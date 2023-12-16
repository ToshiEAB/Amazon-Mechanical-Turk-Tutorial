<?php

// Connect to the MySQL server
$servername = "mysql.server_name.com"; // Replace with your MySQL server name
$username = "mysql_username"; // Replace with your MySQL username
$password = "mysql_password"; // Replace with your MySQL password
$dbname = "mysql_database_name"; // Replace with your MySQL database name

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get the password and validity from the AJAX request
$password = $_POST['password'];
$validity = $_POST['validity'];

// Prepare the MySQL query
$stmt = $mysqli->prepare("INSERT INTO passwords (password, validity) VALUES (?, ?)");
$stmt->bind_param("si", $password, $validity);

// Execute the query and check for errors
if ($stmt->execute()) {
    echo "Password stored successfully!";
} else {
    echo "Error storing password: " . $stmt->error;
}

// Close the statement and the database connection
$stmt->close();
$mysqli->close();

?>
