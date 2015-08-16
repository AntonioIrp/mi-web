<?php

namespace PhpServicesKit\PasswordRecovery;

use PhpServicesKit\DbConnection\DbConnection;

error_reporting(0);

class RecoveryService
{
    public function createNewPasswordRequest($userEmail)
    {
        $newRecoveryPasswordToken = hash('sha512', mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));

        $newDbConnection = new DbConnection();
        $saveUserAndToken = $newDbConnection->userWantToChangePassword($userEmail, $newRecoveryPasswordToken);

        if ($saveUserAndToken === 0) {
            $composeEmailService = new EmailComposer();
            $sendNewEmail = $composeEmailService->composeEmail($userEmail, $newRecoveryPasswordToken);

            if ($sendNewEmail === 0) {
                return 0;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

    public function restoreNewPassword($email, $password, $token)
    {
        $dataBaseServices = new DbConnection();
        $checkEmailStatus = $dataBaseServices->checkUserEmail($email);

        if ($checkEmailStatus === 0) {
            $chekTokenStatus = $dataBaseServices->checkValidToken($email, $token);

            if ($chekTokenStatus === 0) {
                $signedPassword=$this->signNewPassword($password);
                $changePasswordStatus = $dataBaseServices->changeUserPassword($email, $signedPassword);

                if ($changePasswordStatus === 0) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

    private function signNewPassword($password)
    {
        $randomSalt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
        $passwordHashOptions = array('cost' => 15, 'salt' => $randomSalt);
        $securePassword = password_hash($password, PASSWORD_BCRYPT, $passwordHashOptions);

        return $securePassword;
    }
}
