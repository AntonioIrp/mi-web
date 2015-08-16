<?php
error_reporting(0);

require_once "./vendor/autoload.php";

session_start();

if (isset($_POST['csrf_token']) && ($_POST['csrf_token'] == "true")) {
    $csrfToken = sha1(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
    $_SESSION['csrf_token'] = $csrfToken;
    echo $csrfToken;
} elseif (
    isset($_POST['password']) &&
    isset($_POST['length']) &&
    isset($_POST['email']) &&
    isset($_POST['csrf_token']) &&
    ($_POST['csrf_token'] == $_SESSION['csrf_token'])
) {
    $password = trim($_POST['password']);
    $length = trim($_POST['length']);
    $userEmail = trim($_POST['email']);

    $myValidator = new \Validators\ValidatorService();

    $validationStatus = $myValidator->loginValidator(
        $userEmail,
        $password,
        $length);

    $response = new stdClass();

    if ($validationStatus === 1) {
        $response->code_operation = "login";
        $response->status = "false";
        $response->description = $myValidator->statusMessage;
        echo json_encode($response);
    } elseif ($validationStatus === 0) {
        $newLogin = new \PhpServicesKit\LoginKit\LogIn();
        $responseMessage = $newLogin->doLogin($userEmail, $password);
        echo json_encode($responseMessage);
    } else {
        $response->code_operation = "login";
        $response->status = "false";
        $response->description = \Messages\MessageService::DEFAULT_ERROR;
        echo json_encode($response);
    }
} else {
    $response->code_operation = "login";
    $response->status = "false";
    $response->description = \Messages\MessageService::DEFAULT_ERROR;
    echo json_encode($response);
}
