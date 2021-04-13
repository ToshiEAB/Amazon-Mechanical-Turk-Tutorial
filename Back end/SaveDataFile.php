<?php

// Receive data from JavaScript program
$ID = $_POST['ID'];
$group = $_POST['GROUP'];
$code = $_POST['code'];
$basePay = $_POST['basePay'];
$dtStart = $_POST['dtStart'];
$dtEnd = $_POST['dtEnd'];
$targetRespCount = $_POST['targetRespCount'];
$altRespCount = $_POST['altRespCount'];
$totalGain = $_POST['totalGain'];
$totalLoss = $_POST['totalLoss'];
$netGain = $_POST['netGain'];
$totalSR = $_POST['totalSR'];
$srPhase1 = $_POST['srPhase1'];
$srPhase2 = $_POST['srPhase2'];
$arrTargetRespCountPhase1 = $_POST['arrTargetRespCountPhase1'];
$arrTargetRespCountPhase2 = $_POST['arrTargetRespCountPhase2'];
$arrAltRespCountPhase1 = $_POST['arrAltRespCountPhase1'];
$arrAltRespCountPhase2 = $_POST['arrAltRespCountPhase2'];
$arrEvents = $_POST['arrEvents'];

$VI_Target = $_POST['VI_Target'];
$VI_Alt = $_POST['VI_Alt'];
$COR = $_POST['COR'];
$lossWeight = $_POST['lossWeight'];
$noLossSign = $_POST['noLossSign'];
$gainWeight = $_POST['gainWeight'];
$initPoint = $_POST['initPoint'];
$terminatePhase1 = $_POST['terminatePhase1'];
$dollarPerPoint = $_POST['dollarPerPoint'];
$intervalPeriodLength = $_POST['intervalPeriodLength'];
$numIntervalsPhase1 = $_POST['numIntervalsPhase1'];
$numIntervalsPhase2 = $_POST['numIntervalsPhase2'];
$symbol = $_POST['symbol'];
$targetSide = $_POST['targetSide'];
$reversal = $_POST['reversal'];

// Set the current working directory 
$directory = getcwd() ."/results/RawData/"; ***** Change this to your URL where you wanna save session-data files. This sample directory is unsafe, actually. See Step 11 in Appendix A of our paper for better security.

$filecount = 0;  
$files = glob( $directory ."*" ); 
  
if( $files ) { 
    $filecount = count($files) + 1; 
} else {
    $filecount = 1;
}


$filename = "Participant" . str_pad($filecount, 3, 0, STR_PAD_LEFT) . "-" . $group;


$filepath = "/home/username/________.com/public_html/wp-content/themes/luxech/results/RawData/" . $filename . ".csv"; // ***** Change this to your URL where you wanna save session-data files. This sample directory is unsafe, actually. See Step 11 in Appendix A of our paper for better security.

$f = fopen($filepath, 'w');

fwrite($f, "Start: " . $dtStart[0] . "/" . $dtStart[1] . "/". $dtStart[2] . " " . $dtStart[3] . ":". $dtStart[4] . ":". $dtStart[5] . "\n");
fwrite($f, "End: " . $dtEnd [0] . "/" . $dtEnd[1] . "/". $dtEnd[2] . " " . $dtEnd[3] . ":". $dtEnd[4] . ":". $dtEnd[5] . "\n");
fwrite($f, "\n");

fwrite($f, "filename: " . $filename . "\n");
fwrite($f, "ID: " . $ID . "\n");
fwrite($f, "Group: " . $group . "\n");
fwrite($f, "completion code: " . $code . "\n");
fwrite($f, "TotalPayment: " . ($netGain * $dollarPerPoint + $basePay) . "\n");
fwrite($f, "\n");
fwrite($f, "PayForPoints: " . ($netGain * $dollarPerPoint) . "\n");
fwrite($f, "BasePay: " . $basePay . "\n");
fwrite($f, "\n");
fwrite($f, "DEPENDENT VARIABLES\n");
fwrite($f, "targetRespCount: " . $targetRespCount . "\n");
fwrite($f, "altRespCount: " . $altRespCount . "\n");
fwrite($f, "totalGain: " . $totalGain . "\n");
fwrite($f, "totalLoss: " . $totalLoss . "\n");
fwrite($f, "netGain: " . $netGain . "\n");
fwrite($f, "totalSR: " . $totalSR . "\n");
fwrite($f, "srPhase1: " . $srPhase1 . "\n");
fwrite($f, "srPhase2: " . $srPhase2 . "\n");
fwrite($f, "\n");

fwrite($f, "INDEPENDENT VARIABLES\n");
fwrite($f, "VI_Target: " . $VI_Target . "\n");
fwrite($f, "VI_Alt: " . $VI_Alt . "\n");
fwrite($f, "COR: " . $COR . "\n");
fwrite($f, "lossWeight: " . $lossWeight . "\n");
fwrite($f, "noLossSign: " . $noLossSign . "\n");
fwrite($f, "gainWeight: " . $gainWeight . "\n");
fwrite($f, "initPoint: " . $initPoint . "\n");
fwrite($f, "terminatePhase1: " . $terminatePhase1 . "\n");
fwrite($f, "dollarPerPoint: " . $dollarPerPoint . "\n");
fwrite($f, "intervalPeriodLength: " . $intervalPeriodLength . "\n");
fwrite($f, "numIntervalsPhase1: " . $numIntervalsPhase1 . "\n");
fwrite($f, "numIntervalsPhase2: " . $numIntervalsPhase2 . "\n");
fwrite($f, "symbol: " . $symbol . "\n");
fwrite($f, "targetSide: " . $targetSide . "\n");
fwrite($f, "reversal: " . $reversal . "\n");
fwrite($f, "\n");


$n = 0;
fwrite($f, "PHASE 1 (Target)\n");
if (!empty($arrTargetRespCountPhase1)) {
   $n = sizeof($arrTargetRespCountPhase1);
   for ($i = 0; $i < $n; $i++) {
      fwrite($f, "Interval " . ($i + 1) . ": " . $arrTargetRespCountPhase1[$i] . "\n");
   }
}
fwrite($f, "\n");

fwrite($f, "PHASE 2 (Target)\n");
if (!empty($arrTargetRespCountPhase2)) {
   $n = sizeof($arrTargetRespCountPhase2);
   for ($i = 0; $i < $n; $i++) {
      fwrite($f, "Interval " . ($i + 1) . ": " . $arrTargetRespCountPhase2[$i] . "\n");
   }
}
fwrite($f, "\n");

fwrite($f, "PHASE 1 (Alt)\n");
if (!empty($arrAltRespCountPhase1)) {
   $n = sizeof($arrAltRespCountPhase1);
   for ($i = 0; $i < $n; $i++) {
      fwrite($f, "Interval " . ($i + 1) . ": " . $arrAltRespCountPhase1[$i] . "\n");
   }
}
fwrite($f, "\n");

fwrite($f, "PHASE 2 (Alt)\n");
if (!empty($arrAltRespCountPhase2)) {
   $n = sizeof($arrAltRespCountPhase2);
   for ($i = 0; $i < $n; $i++) {
      fwrite($f, "Interval " . ($i + 1) . ": " . $arrAltRespCountPhase2[$i] . "\n");
   }
}
fwrite($f, "\n");


fwrite($f, "LIST OF EVENTS:\n");
fwrite($f, "01: Target response\n");
fwrite($f, "02: Alt response\n");
fwrite($f, "03: Response on Left workplace \n");
fwrite($f, "04: Response on Right workplace\n");
fwrite($f, "05: Response on Bar point\n");
fwrite($f, "06: Response on Bar point label\n");
fwrite($f, "07: Response on $US\n");
fwrite($f, "08: Response on Net gain\n");
fwrite($f, "09: Response on Background\n");
fwrite($f, "10: Response on Left star\n");
fwrite($f, "11: Response on Right star\n");
fwrite($f, "12: Response on Left gain point\n");
fwrite($f, "13: Response on Right gain point\n");
fwrite($f, "14: Response on Left loss point\n");
fwrite($f, "15: Response on Right loss point\n");
fwrite($f, "16: Left SR\n");
fwrite($f, "17: Right SR\n");
fwrite($f, "30: End of Phase 1\n");
fwrite($f, "99: End of session\n");
fwrite($f, "\n");

if (!empty($arrEvents)) {
   $n = sizeof($arrEvents);
   for ($i = 0; $i < $n; $i++) {
      fwrite($f, $arrEvents[$i] . "\n");
   }
}
fwrite($f, "\n");
fwrite($f, "\n");
fwrite($f, "\n");

fclose($f);

echo $filecount;

exit;