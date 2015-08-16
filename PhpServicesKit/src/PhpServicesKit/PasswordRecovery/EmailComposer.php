<?php

namespace PhpServicesKit\PasswordRecovery;

use PHPMailer;
use Configuration\Configuration;

error_reporting(0);

class EmailComposer
{
    public function composeEmail($email, $token)
    {
        $mail=new PHPMailer();

        //$mail->SMTPDebug = 3; //Verbose Mode
        $mail->isSMTP();
        $mail->Host = Configuration::PHPMAILER_SMTP_SERVER;
        $mail->SMTPAuth = Configuration::PHPMAILER_SMTPAUTH;
        //$mail->AuthType = "LOGIN";
        $mail->Username = Configuration::PHPMAILER_USER;
        $mail->Password = Configuration::PHPMAILER_PASSWORD;
        $mail->SMTPSecure = Configuration::PHPMAILER_SMTPSECURE;
        $mail->Port = Configuration::PHPMAILER_PORT;

        $mail->From = Configuration::PHPMAILER_FROM_EMAIL;
        $mail->FromName = Configuration::PHPMAILER_FROM_NAME;
        $mail->addAddress($email);

        $mail->isHTML(true);

        $mail->Subject = Configuration::PHPMAILER_SUBJECT;

        $message = '
            This message has been generated because an email recovery request has been submited.
            <br>
            <b>If you have not ordered to recover your password, please ignore and delete this email.</b>
            <br>
            If you want to recover your access password, please check the link below:

            <a href="'.Configuration::DOMAIN_NAME.'/PhpServicesKit/passwordrecover.php?token='.$token.'">
                Restore my password now!
            </a>

            This is auto-generated message, please do not reply.
        ';

        $mail->msgHTML($message);
        $mail->AltBody = $message;

        if (!$mail->send()) {
            return 1;
        } else {
            return 0;
        }

    }
}
