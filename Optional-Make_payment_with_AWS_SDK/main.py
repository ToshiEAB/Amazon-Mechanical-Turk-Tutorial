"""
    Copyright: Toshikazu Kuroda (Huckle Co., Ltd., Aichi Bunkyo University)
    Date: April, 2022
    License: Apache License 2.0

    WARNING:
        The copyright holder of this program has no liability regarding issues resulting from potential bugs in this program.
        You could pay a bonus to a wrong MTurk worker or pay a wrong bonus amount.
        Thus, use this program at your own risk!
        If you can take the risk, then I recommend starting with a small set of .csv data files.

    Prerequisites for using this program
        - Follow "Instructions for how to use"

    How to use:
        - Adjust the value of BASE_PAY_IN_DOLLAR to match your own base pay, which applies to all MTurk workers who have completed a task regardless of their performance
            - Bonus amount = TotalPayment - BASE_PAY_IN_DOLLAR
        - Set TEST = False when ready for actually making payments
"""

import boto3
import configparser
import glob
import os
import datetime
import time
from csv import reader
from decimal import Decimal, ROUND_HALF_UP

TEST = True                 # Change this to False when ready for actually making payment
BASE_PAY_IN_DOLLAR = 0.50   # This amount will be subtracted from TotalPayment, assuming every MTurk worker received the base pay by default
INTERVAL = 1                # To give the AWS webhook enough time to process payments (Adjust this value as needed)

class MTurkApp():
    client = None
    hitID = None
    arrAssignmentID = []
    arrWorkerID = []
    arrSurveyCode = []
    arrCompletionCode = []
    arrTotalPayment = []
    arrFileName = []
    matches = []
    match_indices = []
    unmatches = []
    unmatch_indices = []

    # Constructor
    def __init__(self):
        # Create a client
        self.client = boto3.client('mturk')

        # Read config.ini for HIT ID
        try:
            ini = configparser.ConfigParser()
            ini.read('./config.ini', 'UTF-8')
            self.hitID = ini['hit_info']['hitID']
        except:
            print('config.ini is missing from the directory containing main.py')
            exit(-1)

    # How much you have left in your accound
    def getAccountBalance(self):
        response = self.client.get_account_balance()
        balance = response['AvailableBalance']
        print('Available balance: ' + balance)
        return balance

    # Obtain relevant data from your HIT account
    def getHITdata(self):
        searchWord1 = '<FreeText>'
        searchWord2 = '</FreeText>'
        charCount = len(searchWord1)

        nextToken = None
        while 1:
            try:
                if nextToken is None:
                    # First set (SDK has a limit of 100)
                    response = self.client.list_assignments_for_hit(
                        HITId = self.hitID,
                        MaxResults = 100
                    )
                else:
                    # Subsequent sets
                    response = self.client.list_assignments_for_hit(
                        HITId = self.hitID,
                        MaxResults = 100,
                        NextToken = nextToken
                    )
            except:
                print('Your HIT ID specified in config.ini does not exist')
                exit(-1)

            assignments = response['Assignments']
            for i in range(len(assignments)):
                assignmentID = assignments[i]['AssignmentId']
                workerID = assignments[i]['WorkerId']
                answer = assignments[i]['Answer']

                # Edit "answer" to obtain surveyCode
                pos = answer.find(searchWord1)
                answer = answer[pos + charCount:]
                pos = answer.find(searchWord2)
                answer = answer[:pos]
                surveyCode = answer.replace(" ", "")
                #print(surveyCode)

                self.arrAssignmentID.append(assignmentID)
                self.arrWorkerID.append(workerID)
                self.arrSurveyCode.append(surveyCode)

            if 'NextToken' in response:
                nextToken = response['NextToken']
            else:
                nextToken = None

            if nextToken is None:
                break

    # Obtain completion codes from .csv files stored in the "mturk_data" folder
    def getCompletionCode(self):

        # The following two texts need to match those generated by SaveDataFile.php
        code = 'completion code: '
        pay = 'TotalPayment: '

        codeCount = len(code)
        payCount = len(pay)

        # Check if the "mturk_data" folder exists; otherwise, create it.
        dirName = os.getcwd() + '/mturk_data/'
        if not os.path.exists(dirName):
            os.makedirs(dirName)
            print('A new folder named "mturk_data" has been created\nMove .csv data files into this folder')
            exit(0)

        # Get all .csv files from the folder
        csv_files = glob.glob(os.path.join(dirName, "*.csv"))

        fileCount = 0
        for csv_file in csv_files:
            tmp_CompletionCode = None
            tmp_TotalPayment = None

            with open(csv_file, 'r') as f:
                read = reader(f)
                for row in read:
                    txt = ''.join(s for s in row) # Convert list to str
                    if txt.find(code) == 0:
                        tmp_CompletionCode = txt[codeCount:]
                    if txt.find(pay) == 0:
                        value = txt[payCount:]
                        rounded = Decimal(str(value)).quantize(Decimal('0.01'), rounding=ROUND_HALF_UP)
                        tmp_TotalPayment = str(rounded)

                # Only when a .csv file has both "completion code" and "TotalPayment"
                if (tmp_CompletionCode is not None) and (tmp_TotalPayment is not None):
                    self.arrCompletionCode.append(tmp_CompletionCode)
                    self.arrTotalPayment.append(tmp_TotalPayment)
                    self.arrFileName.append(os.path.basename(csv_file))
                    fileCount += 1

        if fileCount == 0:
            print('No .csv files in the "mturk_data" folder')
            exit(0)

    # Compare a list of survey codes from your HIT page and a list of completion codes from .csv files
    def findMatch(self):
        self.matches = set(self.arrSurveyCode).intersection(self.arrCompletionCode)
        self.match_indices = [self.arrSurveyCode.index(x) for x in self.matches]
        self.match_indices.sort()

        # # Optional
        # self.unmatches = set(self.arrSurveyCode).difference(self.arrCompletionCode)
        # self.unmatch_indices = [self.arrSurveyCode.index(x) for x in self.unmatches]
        # self.unmatch_indices.sort()

    # Make payments after subtracting a base pay from total payment
    def sendBonus(self):
        global TEST, INTERVAL, BASE_PAY_IN_DOLLAR

        if len(self.match_indices) == 0:
            print('No matches in survey/completion codes')
            exit(0)

        with open('./PaymentResults.txt', 'a') as f:
            dt_now = datetime.datetime.now()
            dt_now = dt_now.replace(microsecond=0)
            init_text = '(AWS) AssignmentID / WorkerID / SurveyCode : (CSV) FileName / CompletionCode / TotalPayment'
            f.write(str(dt_now) + '\n')
            f.write(init_text + ' / Status\n')

            print('\nAttempting to pay for the following workers\nIndex: ' + init_text + '\n')
            for i in self.match_indices:
                # Look for the index for matched codes
                csv_i = self.arrCompletionCode.index(self.arrSurveyCode[i])
                text = '(AWS) ' + self.arrAssignmentID[i] + ' / ' + self.arrWorkerID[i] + ' / ' + self.arrSurveyCode[i] \
                       + ' : (CSV) ' + self.arrFileName[csv_i] + ' / ' + self.arrCompletionCode[csv_i] + ' / $' + self.arrTotalPayment[csv_i]
                print(str(i) + ': ' + text)

                assignmentID = self.arrAssignmentID[i]
                workerID =  self.arrWorkerID[i]
                totalPayment = self.arrTotalPayment[csv_i]

                #  Check whether payment has been made previously (Assuming each assignment ID is unique)
                try:
                    response = self.client.list_bonus_payments(
                            AssignmentId = assignmentID
                        )
                    bonus = response['BonusPayments']
                    #print(bonus[0]['BonusAmount'])

                    if bonus:
                        status = 'Paid previously\n'
                    else:
                        # Proceed to payment only when your account has an enough balance for making a payment
                        if float(totalPayment) <= float(self.getAccountBalance()):
                            if not TEST:
                                # Exclude the base pay from total payment
                                bonus_amount = float(totalPayment) - BASE_PAY_IN_DOLLAR
                                bonus_amount = "{:.2f}".format(bonus_amount)

                                self.client.send_bonus(
                                    WorkerId = workerID,
                                    BonusAmount = bonus_amount,
                                    AssignmentId = assignmentID,
                                    Reason = 'Completed a task' #,
                                    #UniqueRequestToken = 'string'
                                )
                                status = 'Paid\n'
                            else:
                                status = 'Not Paid for TEST mode being in effect\n'
                        else:
                            status = 'Insufficient account balance\n'
                    f.write(text + ' / ' + status)
                    print('Status: ' + status)

                except:
                    status = 'Payment failed due to some error\n'
                    f.write(text + ' / ' + status)
                    print('Status: ' + status)

                time.sleep(INTERVAL)
            f.write('\n\n')

# Entry point of the program
if __name__ == '__main__':
    app = MTurkApp()
    #app.getAccountBalance() # Activate this code if you want to check your balance in the beginning
    app.getHITdata()
    app.getCompletionCode()
    app.findMatch()
    app.sendBonus()