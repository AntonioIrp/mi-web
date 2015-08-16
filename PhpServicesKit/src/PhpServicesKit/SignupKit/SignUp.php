<?php

namespace PhpServicesKit\SignupKit;

//require_once "../../../vendor/autoload.php";
use PhpServicesKit\DbConnection\DbConnection;


error_reporting(0);

class SignUp
{
    public function doSignup($passwordForm, $userName, $userEmail)
    {
        $response = new \stdClass();

        $randomSalt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);

        $passwordHashOptions = array('cost' => 15, 'salt' => $randomSalt);

        $securePassword = password_hash($passwordForm, PASSWORD_BCRYPT, $passwordHashOptions);

        $myDb = new DbConnection();

        if ($myDb->checkUserForSignup($userName, $userEmail) == 1) {
            $response->code_operation = "signup";
            $response->status = "false";
            $response->description = $myDb->statusMessage;

            return $response;
        } elseif ($status = $myDb->addNewUser($userName, $userEmail, $securePassword) == 1) {
            $response->code_operation = "signup";
            $response->status = "false";
            $response->description = $myDb->statusMessage;

            return $response;
        } elseif ($status == 0) {
            $response->code_operation = "signup";
            $response->status = "true";
            $response->description = $myDb->statusMessage;

            return $response;
        } else {
            $response->code_operation = "signup";
            $response->status = "false";
            $response->description = $myDb->statusMessage;

            return $response;
        }
    }
}
