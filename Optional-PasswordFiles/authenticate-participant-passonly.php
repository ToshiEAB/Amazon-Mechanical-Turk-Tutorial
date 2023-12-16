<?php
if (empty($_POST["password"])) {
    echo json_encode(["authenticated" => false, "err" => "No password provided"]);
} else if ($_POST["password"] == "testing!") {
    echo json_encode(["authenticated" => true, "referrer" => $_POST["referrer"]]);
} else {
    $servername = "mysql.server_name.com"; // Replace with your MySQL server name
    $username = "mysql_username"; // Replace with your MySQL username
    $password = "mysql_password"; // Replace with your MySQL password
    $dbname = "mysql_database_name"; // Replace with your MySQL database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if username is not the same
    $sql = "SELECT * FROM passwords WHERE password = '{$_POST["password"]}'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        echo json_encode(["authenticated" => false, "err" => "That password is not valid."]);
    } else {
        $authenticated = false;
        while($row = $result->fetch_assoc()) {
            if ($row["uses"] == $row["validity"]) {
                $authenticated = false;
                break;
            } else {
                $authenticated = true;
                $sqlIncrement = "UPDATE passwords SET uses = uses + 1 WHERE password = '{$_POST["password"]}'";
                $result = $conn->query($sqlIncrement);
                break;
            }
        }
        if ($authenticated) {
            echo json_encode(["authenticated" => true, "referrer" => $_POST["referrer"]]);
        } else {
            // If this is reached, no emails matched that password, inform that these aren't the right credentials
                echo json_encode(["authenticated" => false, "err" => "This password has expired and cannot be used again."]);
        }
        
    }
}
?>
