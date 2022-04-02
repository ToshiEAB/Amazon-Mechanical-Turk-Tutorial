<?php

// Receive data from Javascript
$filecount = $_POST['filecount'];
$ID = $_POST['ID'];
$group = $_POST['GROUP'];
$dollar = $_POST['dollar'];
$code = $_POST['code'];
$a1 = $_POST['a1'];
$a2 = $_POST['a2'];
$a3 = $_POST['a3'];
$a4 = $_POST['a4'];
$a5 = $_POST['a5'];
$a6 = $_POST['a6'];
$a7 = $_POST['a7'];
$a8 = $_POST['a8'];
$a9 = $_POST['a9'];
$a10 = $_POST['a10'];
$a11 = $_POST['a11'];
$a12 = $_POST['a12'];
$a12extra = $_POST['a12extra'];
$a13 = $_POST['a13'];
$a14 = $_POST['a14'];
$a15 = $_POST['a15'];
$a16 = $_POST['a16'];
$a17 = $_POST['a17'];
$a18 = $_POST['a18'];
$a19 = $_POST['a19'];

// Remove line-break code (i.e., "\n", "\r", "\r\n"), an illegal code for file name, that might be added automatically at some point
$filecount = str_replace(array("\n", "\r", "\r\n"), '', $filecount);
$group = str_replace(array("\n", "\r", "\r\n"), '', $group);

// Set the current working directory 
$directory = getcwd() ."/results/Survey/"; // ***** Change this to a directory/folder in which you wanna save survey-data files. This sample directory is unsafe, actually. See Step 11 in Appendix A of our paper for better security.

// Create a /results/RawData/ directory if it does not exist
if (!is_dir($directory)) {
    mkdir($directory, 0700, true); // see permission levels at https://chmodcommand.com/chmod-700/
}

// Save a text data file on this server computer
$filename = "Participant" . str_pad($filecount, 3, 0, STR_PAD_LEFT) . "-" . $group . "-Survey";
$filepath = $directory . $filename . ".txt";

$f = fopen($filepath, 'w');

fwrite($f, "filename: " . $filename . "\n");
fwrite($f, "\n");
fwrite($f, "ID: " . $ID . "\n");
fwrite($f, "Group: " . $group . "\n");
fwrite($f, "code: " . $code . "\n");
fwrite($f, "Dollar: " . $dollar . "\n");
fwrite($f, "\n");
fwrite($f, "Q1: " . $a1 . "\n");
fwrite($f, "Q2: " . $a2 . "\n");
fwrite($f, "Q3: " . $a3 . "\n");
fwrite($f, "Q4: " . $a4 . "\n");
fwrite($f, "Q5: " . $a5 . "\n");
fwrite($f, "Q6: " . $a6 . "\n");
fwrite($f, "Q7: " . $a7 . "\n");
fwrite($f, "Q8: " . $a8 . "\n");
fwrite($f, "Q9: " . $a9 . "\n");
fwrite($f, "Q10: " . $a10 . "\n");
fwrite($f, "Q11: " . $a11 . "\n");
fwrite($f, "Q12: " . $a12 . "\n");
fwrite($f, "   Text: " . $a12extra . "\n");
fwrite($f, "Q13: " . $a13 . "\n");
fwrite($f, "Q14: " . $a14 . "\n");
fwrite($f, "Q15: " . $a15 . "\n");
fwrite($f, "Q16: " . $a16 . "\n");
fwrite($f, "Q17: " . $a17 . "\n");
fwrite($f, "Q18: " . $a18 . "\n");
fwrite($f, "Q19: " . $a19 . "\n");
fwrite($f, "\n");
fwrite($f, "\n");
fwrite($f, "\n");

fclose($f);

exit;
