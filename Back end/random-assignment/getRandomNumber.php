<?php

$filepath = '/home/username/_________.com/public_html/wp-content/themes/luxech/random-assignment/list.txt'; // ***** Change this to your URL where you uploaded "list.txt"

$firstLine = 0;
$line;
$arrLine = array();
$count = 0;
$selectedValue = 0;
$error = 0;
$output;

$f = fopen($filepath, 'r');

if ($f) {
    // Read the first line
    if (($line = fgets($f)) != false) {
       $firstLine = (int)$line;
    }

   // Read the rest
    while (($line = fgets($f)) !== false) {
       $count++; 
       $arrLine[$count] = $line;
    }
  
   // Just in case
   if ($firstLine > $count) {
      $firstLine = 1;
   }

   // Select a number
   $selectedValue = $arrLine[$firstLine];

   // Set a string output
   $output = strval($firstLine) . ',' . strval($selectedValue);

   fclose($f);

} else {
    $error = 1;
} 

// Write an updated list
$f = fopen($filepath, 'w');

if ($f) {
   $firstLine++;
   fwrite($f, $firstLine . "\n");
   for($i = 1; $i <= $count; $i++) {
      fwrite($f, $arrLine[$i]);
   }

   fclose($f);

} else {
    $error = 1;
}


if ($error) {
  //echo "error";
} else {
  echo $output;
}

exit;