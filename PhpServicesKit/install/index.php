<?php

require_once "../vendor/autoload.php";
use Configuration\Configuration;

error_reporting(0);

echo
"
<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta name='viewport' content='width=device-width'/>
<title>Login & Signup Kit Database Installation</title>
</head>

<body>
<h1>Login & Signup Kit Database Installation</h1>
";

if (isset($_GET["install"])) {
    $dbPassword =  Configuration::DATABASE_PASSWORD;
    $dbUser = Configuration::DATABASE_USER;
    $dbHost = Configuration::DATABASE_HOST;
    $dbPort = Configuration::DATABASE_PORT;
    $dbDatabase = Configuration::DATABASE_NAME;
    $dbDatabasePrefix = Configuration::DATABASE_PREFIX;
    $completeDatabaseName=$dbDatabasePrefix.$dbDatabase;

    $dbInstall = false;

    $mysqli = new mysqli();
    $mysqli->mysqli(
        $dbHost,
        $dbUser,
        $dbPassword,
        $completeDatabaseName,
        $dbPort
    );

    if ($mysqli->connect_error) {
        $cfg_result = "<p>Cant connect to MySQL server. Check MySQL configuration file (DbConnection.php)</p>";
    } else {

        $databaseCreate = "
        CREATE DATABASE IF NOT EXISTS ".$completeDatabaseName." DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
        ";

        $loginAttempts = "
	    CREATE TABLE IF NOT EXISTS login_attempts (
        user_id int(11) NOT NULL,
        time varchar(30) NOT NULL,
        attemp_id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        time_stamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
	    ";

        $membersTable = "
        CREATE TABLE IF NOT EXISTS members (
        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        username varchar(30) NOT NULL,
        email varchar(50) NOT NULL,
        password char(128) NOT NULL
        ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
	    ";

        $password='$2y$15$BTm77jO53MqbZXHNCHBtsujee0BcaqLPOQTVtILGlDfhOHKEmJmzW';
        $membersData = "
        INSERT INTO members (username, email, password) VALUES
        ('test', 'test@test.com','".$password."');
        ";

        $passwordRequest = "
        CREATE TABLE IF NOT EXISTS password_requests (
        id_request int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_email varchar(50) NOT NULL,
        time_stamp varchar(100) NOT NULL,
        token varchar(150) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

        ";

        echo '<h2>Installer Log:</h2>';

        $stmt = $mysqli->prepare($databaseCreate);
        if ($stmt->execute()) {
            $cfg_result = "<p style='color: green'>New ".$completeDatabaseName." database created successfully</p>";
        } else {
            $cfg_result = "<p>Error creating ".$completeDatabaseName." database</p>";
            $dbInstall = true;
        }
        echo $cfg_result;

        $mysqli->select_db($completeDatabaseName);

        if ($result = $mysqli->query("SELECT DATABASE()")) {
            $row = $result->fetch_row();
            echo "<p style='color: green'>Using ". $row[0] . " database now</p>";
            $result->close();
        } else {
            echo "<p style='color: red'>Error using ".$completeDatabaseName." database</p>";
        }

        $stmt = $mysqli->prepare($loginAttempts);
        if ($stmt->execute()) {
            $cfg_result = "<p style='color: green'>login_attempts table created successfully</p>";
        } else {
            $cfg_result = "<p style='color: red'>Error creating login_attempts table</p>";
            $dbInstall = true;
        }

        echo $cfg_result;

        $stmt = $mysqli->prepare($membersTable);
        if ($stmt->execute()) {
            $cfg_result = "<p style='color: green'>Members table created successfully</p>";
        } else {
            $cfg_result = "<p style='color: red'>Error creating members table.</p>";
            $dbInstall = true;
        }

        echo $cfg_result;

        $stmt = $mysqli->prepare($membersData);
        if ($stmt->execute()) {
            $cfg_result = "
                <p style='color: green'>Added test member-> user:test@test.com password:12345678
                only for test purposes!!</p>
                ";
        } else {
            $cfg_result = "<p style='color: red'>Cannot insert test member</p>";
            $dbInstall = true;
        }

        echo $cfg_result;

        $stmt = $mysqli->prepare($passwordRequest);
        if ($stmt->execute()) {
            $cfg_result = "<p style='color: green'>password_recovery table created successfully</p>";
        } else {
            $cfg_result = "<p style='color: red'>Error creating password_recovery table</p>";
            $dbInstall = true;
        }

        echo $cfg_result;

        if (!$dbInstall) {
            echo "<h1>Database setup complete, please delete the install folder.</h1>";
        } else {
            echo "<p><a href='./index.php?install=true'>Errors ocurred. Please try again.
            Or check manually your database.</a></p>";
        }
    }
} else {
    echo "
        <p>Please check /config/Configuration.php database params:</p>
        <br>
        <p>const HOST = 'your_host_name'</p>
        <p>const USER = 'your_database_user'</p>
        <p>const PASSWORD = 'your_mysql_password'</p>
        <p>const DATABASE = 'specify_a_new_database_name'</p>
        <p>const DATABASE_PREFIX = 'specify_the_database_prefix_case_mysql_use_it'</p>
        <p>const PORT = 'if_needed_specify_mysql_port_or_leave_default'</p>
        <br>
        <p><a href='./index.php?install=true'>Click here to start database installation</a></p>
        ";
}

echo "</body> </html>";
