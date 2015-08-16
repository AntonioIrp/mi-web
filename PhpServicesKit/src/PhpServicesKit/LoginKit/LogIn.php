<?php

namespace PhpServicesKit\LoginKit;

use PhpServicesKit\DbConnection\DbConnection;

error_reporting(0);

class LogIn
{
    public function doLogin($userEmail, $userPassword)
    {
        $response = new \stdClass();
        $response->code_operation = "login";
        $response->description = null;
		$response->user_id = null;
		$response->user_name = null;
		$response-> user_email = null;
        $dbConnection = new DbConnection();

        $statusCode = $dbConnection->checkUserForLogin($userEmail);

        if ($statusCode === 1) {
            $response->status = "false";
            $response->description = $dbConnection->statusMessage;

            return $response;
        } elseif ($statusCode === 0) {
            $statusCode = $dbConnection->checkBruteForce($dbConnection->userId);
            if ($statusCode === 1) {
                $response->status = "false";
                $response->description = $dbConnection->statusMessage;

                return $response;
            } elseif ($statusCode === 0) {
                if (password_verify($userPassword, $dbConnection->dbPassword)) {
                    $response->status = "true";
                    $response->description = $dbConnection->statusMessage;

                    $userId = preg_replace("/[^0-9]+/", "", $dbConnection->userId);
                    $userName = $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $dbConnection->userName );
                    $sessionHash = hash('sha512', $dbConnection->dbPassword.$userId.$userName.$userEmail);

                    $_SESSION['sessionData']=$sessionHash;
					$_SESSION['user_id']=$userId;
					$_SESSION['user_email']=$userEmail;
					$_SESSION['user_name']=$userName;
					
					$response->user_id = $userId;
					$response->user_email = $userEmail;
					$response->user_name = $userName;
					
                    setcookie("sessionData", $sessionHash);

                    return $response;
                } else {
                    $dbConnection->insertBadLoginAttempt($dbConnection->userId);
                    $response->status = "false";
                    $response->description = $dbConnection->statusMessage;

                    return $response;
                }
            } else {
                $response->status = "false";

                return $response;
            }
        } else {
            $response->status = "false";

            return $response;
        }
    }
}
