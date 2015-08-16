<?php
error_reporting(0);

require_once './libs/csrf-magic/csrf-magic.php';
require_once './vendor/autoload.php';

//if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) {
//    // request is not using SSL, redirect to https, or fail
//    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
//    exit();
//}else
if (
    $_SERVER['REQUEST_METHOD'] == 'POST' &&
    csrf_check() === true &&
    isset($_POST['userEmail']) &&
    isset($_POST['newPassword']) &&
    isset($_POST['vPassword']) &&
    isset($_GET['token'])

) {
    echo "<br>";
    echo "restoring password...";
    echo "<br>";

    $password=trim($_POST['newPassword']);
    $vPassword=trim($_POST['vPassword']);
    $userEmail=trim($_POST['userEmail']);
    $token = trim($_GET['token']);

    $passwordValidationService = new \Validators\ValidatorService();
    $validationStatus = $passwordValidationService->recoveryValidator($userEmail, $password, $vPassword);

    if ($validationStatus === 0) {
        $restoreService = new \PhpServicesKit\PasswordRecovery\RecoveryService();
        $resetPassword = $restoreService->restoreNewPassword($userEmail, $password, $token);

        if ($resetPassword === 0) {
            echo "
            <meta name='viewport' content='width=device-width'/>
            <p style='color: lawngreen'>Password has been restored successfully</p>
            ";
        } else {
            echo "
            <meta name='viewport' content='width=device-width'/>
            <p style='color: red'>Error restoring password. Try again</p>
            ";
        }

    } else {
        echo "
            <meta name='viewport' content='width=device-width'/>
            <p style='color: red'>Missing data, incorrect or password too weak. You need at least 8 char password</p>
            ";
    }

 } elseif (isset($_GET['token'])) {
    echo '
        <!DOCTYPE html>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no"/>
        <title>Password Recovery</title>
        </head>

        <body>

        <noscript>
            <b>This page needs JavaScript activated to work.</b>
            You will need activate Javascript in your browser in order to continue.
            <style>div { display:none; }</style>
        </noscript>

        <script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha512.js"></script>

        <script>
            function submitForm()
            {
                var a = document.getElementById("password");
                var b = document.getElementById("vPassword");

                if ((a.value.length && b.value.length)>=8) {
                    var c = CryptoJS.SHA512(a.value);
                    var d = CryptoJS.SHA512(b.value);
                    a.value = c;
                    b.value = d;
                    var form = document.getElementById("recoveryForm");
                    form.submit();
                } else {
                    document.write("Passwords too weak");
                }
            }
        </script>

        <div id="pageContainer">
            <h3>Password Recovery Process</h3>
            <p>This page will let you restore your missing password.</p>
            <form id="recoveryForm" action="" method="post">
                <label for="email">Email: </label>
                <input id="email" type="email" name="userEmail" required="required"/>
                <br>
                <br>
                <label for="password">Password: </label>
                <input id="password" type="password" name="newPassword"
                    pattern=".{8,}" required title="8 characters minimum" required="required"/>
                <br>
                <br>
                <label for="vPassword">Verify your password: </label>
                <input id="vPassword" type="password" name="vPassword"
                    pattern=".{8,}" required title="8 characters minimum" required="required"/>
                <br>
                <br>
                <input type="button"  value="Restore Password" onclick="submitForm(); return true;"  />
            </form>
        </div>


        </body>
        </html>
    ';
}
