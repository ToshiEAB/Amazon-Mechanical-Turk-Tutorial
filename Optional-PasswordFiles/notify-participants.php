<?php
include '../../../wp-load.php';

// Created:         03/13/2023
// Last Edited:     03/13/2023
// Created by:      Matthew Lamperski
// Email:           matthew.lamperski@gmail.com


function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

header('Content-Type: application/json');

$req = file_get_contents('php://input');
$data = json_decode($req, true);

$status = array();

$email = "your_email@email.com"; // Replase with your email address using for an experiment
$subject = "your_subject";
$headers = 'From: '. $email . "\r\n" .
    'Reply-To: ' . $email . "\r\n";

// Set MySQL connection

$servername = "mysql.server_name.com"; // Replace with your MySQL server name
$username = "mysql_username"; // Replace with your MySQL username
$password = "mysql_password"; // Replace with your MySQL password
$dbname = "mysql_database_name"; // Replace with your MySQL database name

$conn = new mysqli($servername, $username, $password, $dbname);
$conn_status = "Not Connected";
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);

} else {
    $conn_status = "Connection established";
}

// For each participant
for ($i = 0; $i < count($data["participants"]); $i++) {
    $curr_participant = $data["participants"][$i];
    $first_name = $curr_participant["first_name"];
    $last_name = $curr_participant["last_name"];
    $to = $curr_participant["email"];

    // Create random password.

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    // Password will be 9 chars
    $password = generateRandomString(9);

    $email_success = false;

    // Insert email/password/first name into DB to allow for sign in
    $sql = "INSERT INTO Participants (email, first_name, last_name, password) 
            VALUES ('{$to}', '{$first_name}', '{$last_name}', '{$password}')";

    $insert_status = "uninitialized_message";
    $insert_result = $conn->query($sql);
    if ($conn->error) {
        $insert_status = $conn->error;
    } else {
        // Only email them if their password could be saved, otherwise it's pointless (this prevents duplicates)
        $message = "Hello {$first_name},\n\nThank you for signing up to participate in an experiment. When you begin the experiment, you will be prompted for a password. Please enter the following:\n\nPassword: {$password}";
        $message .= "\n\nThank you";

        $insert_status = "Successfully saved password";
        $email_success = wp_mail($to, $subject, $message);
    }

    $status[] = ["email_success" => $email_success, "email" => $to, "password_saved" => $insert_status];
}



echo json_encode(["status" => $status, "mysql_connection" => $conn_status]);

