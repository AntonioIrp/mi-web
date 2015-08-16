<?php

error_reporting(0);

require_once "./vendor/autoload.php";

if (
        isset($_POST["password"]) &&
        isset($_POST['v_password']) &&
        isset($_POST['v_password_strength']) &&
        isset($_POST['password_strength']) &&
        isset($_POST['password_strength']) &&
        isset($_POST['username']) &&
        isset($_POST['email'])
) {
    $passwordForm = trim($_POST['password']);
    $vPasswordForm = trim($_POST['v_password']);
    $vPasswordFormStrength = trim($_POST['v_password_strength']);
    $passwordFormStrength = trim($_POST['password_strength']);
    $userName = trim($_POST['username']);
    $userEmail = trim($_POST['email']);

    $myValidator=new \Validators\ValidatorService();

    $validationStatus = $myValidator->registerValidator(
        $userName,
        $userEmail,
        $passwordForm,
        $vPasswordForm,
        $passwordFormStrength,
        $vPasswordFormStrength);

    $response = new stdClass();

    if ($validationStatus === 1) {
        $response->code_operation = "signup";
        $response->status = "false";
        $response->description = $myValidator->statusMessage;
        echo json_encode($response);
    } elseif ($validationStatus === 0) {
        $newSignup = new \PhpServicesKit\SignupKit\SignUp();
        $responseMessage = $newSignup->doSignup($passwordForm, $userName, $userEmail);
        echo json_encode($responseMessage);
    } else {
        $response->code_operation = "signup";
        $response->status = "false";
        $response->description = \Messages\MessageService::DEFAULT_ERROR;
        echo json_encode($response);
    }

} else {
        $response->code_operation = "signup";
        $response->status = "false";
        $response->description = \Messages\MessageService::DEFAULT_ERROR;
        echo json_encode($response);
}
