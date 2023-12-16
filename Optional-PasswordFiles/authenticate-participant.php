<?php
if (empty($_POST["password"])) {
    echo json_encode(["authenticated" => false, "err" => "No password provided"]);
} else if ($_POST["password"] == "testing!") {
    echo json_encode(["authenticated" => true, "referrer" => $_POST["referrer"]]);
} else if (empty($_POST["email"])) {
    echo json_encode(["authenticated" => false, "err" => "No email provided"]);
} else {
    $servername = "mysql.server_name.com"; // Replace with your MySQL server name
    $username = "mysql_username"; // Replace with your MySQL username
    $password = "mysql_password"; // Replace with your MySQL password
    $dbname = "mysql_database_name"; // Replace with your MySQL database name

    $email = $_POST["email"];

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if username is not the same
    $sql = "SELECT * FROM Participants WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        echo json_encode(["authenticated" => false, "err" => "No one registered with that email, contact c.martinezperez@ufl.edu to be registered."]);
    } else {
        $authenticated = false;
        while($row = $result->fetch_assoc()) {
            if ($row["password"] == $_POST["password"]) {
                $authenticated = true;
                break;
                
            }
        }
        if ($authenticated) {
            echo json_encode(["authenticated" => true, "referrer" => $_POST["referrer"]]);
        } else {
            // If this is reached, no emails matched that password, inform that these aren't the right credentials
                echo json_encode(["authenticated" => false, "err" => "Incorrect credentials, contact c.martinezperez@ufl.edu to be registered."]);
        }
    }
}
?>
