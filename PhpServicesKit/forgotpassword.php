<?php
error_reporting(0);

require_once "./vendor/autoload.php";

session_start();

if ((isset($_POST['csrf']))) {
    $phpSessId = sha1(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
    $_SESSION['phpsessid'] = $phpSessId;;
    echo $phpSessId;

} elseif (
    ($_POST['phpsessid'] == $_SESSION['phpsessid']) &&
    isset($_POST['email']) &&
    isset($_POST['phpsessid'])

) {
    $userEmail = trim($_POST['email']);
    $valideEmail=new \Validators\ValidatorService();
    $validationStatus=$valideEmail->ValidaMail($userEmail);

    $response = new stdClass();

    if ($validationStatus === 1) {
        $response->code_operation = "recovery";
        $response->status = "false";
        echo json_encode($response);
    } elseif ($validationStatus === 0) {
        $dbConnection = new \PhpServicesKit\DbConnection\DbConnection();
        $checkUserEmailStatus = $dbConnection->checkUserEmail($userEmail);

        if ($checkUserEmailStatus === 1) {
            $response->code_operation = "recovery";
            $response->status = "false";
            echo json_encode($response);
        } elseif ($checkUserEmailStatus === 0) {

            $newRecoveryRequest = new \PhpServicesKit\PasswordRecovery\RecoveryService();
            $newPasswordRequest = $newRecoveryRequest->createNewPasswordRequest($userEmail);

            if ($newPasswordRequest === 0) {
                $response->code_operation = "recovery";
                $response->status = "true";
                echo json_encode($response);
            } elseif ($newPasswordRequest === 1) {
                $response->code_operation = "recovery";
                $response->status = "false";
                echo json_encode($response);
            }
        }

    } else {
        $response->code_operation = "recovery";
        $response->status = "false";
        echo json_encode($response);
    }

} else {
    $response->code_operation = "recovery";
    $response->status = "false";
    echo json_encode($response);
}
