<?php
namespace Validators;

use Messages\MessageService;

error_reporting(0);

class ValidatorService
{
    public $statusMessage=null;

    public function registerValidator(
        $username,
        $email,
        $password,
        $verifiedPassword,
        $passwordStrength,
        $verifiedPasswordStrength)
    {

        if (empty($username)) {
            $this->statusMessage = MessageService::EMPTY_USERNAME;

            return 1;
        } elseif (empty($email)) {
            $this->statusMessage = MessageService::EMPTY_EMAIL;

            return 1;
        } elseif ($this->ValidaMail($email) == 1) {
            $this->statusMessage = MessageService::ERROR_EMAIL;

            return 1;
        } elseif (empty($password)) {
            $this->statusMessage = MessageService::EMPTY_PASSWORD;

            return 1;
        } elseif (empty($verifiedPassword)) {
            $this->statusMessage = MessageService::EMPTY_VERIFIED_PASSWORD;

            return 1;
        } elseif ($password !== $verifiedPassword) {
            $this->statusMessage = MessageService::PASSWORDS_MISMATCH;

            return 1;
        } elseif ((intval($passwordStrength) || intval($verifiedPasswordStrength)) < 8) {
            $this->statusMessage = MessageService::WEAK_PASSWORDS;

            return 1;
        } else {
            return 0;
        }
    }

    public function loginValidator($email, $password, $length)
    {
        if (empty($email)) {
            $this->statusMessage = MessageService::EMPTY_EMAIL;

            return 1;
        } elseif ($this->ValidaMail($email) == 1) {
            $this->statusMessage = MessageService::ERROR_EMAIL;

            return 1;
        } elseif (empty($password)) {
            $this->statusMessage = MessageService::EMPTY_PASSWORD;

            return 1;
        } elseif ($length < 8) {
            $this->statusMessage = MessageService::WEAK_PASSWORDS;

            return 1;
        } else {
            return 0;
        }
    }

    public function recoveryValidator($email, $password, $vPassword)
    {
        if (empty($email)) {
            $this->statusMessage = MessageService::EMPTY_EMAIL;

            return 1;
        } elseif ($this->ValidaMail($email) == 1) {
            $this->statusMessage = MessageService::ERROR_EMAIL;

            return 1;
        } elseif (empty($password)) {
            $this->statusMessage = MessageService::EMPTY_PASSWORD;

            return 1;
        } elseif (empty($vPassword)) {
            $this->statusMessage = MessageService::EMPTY_VERIFIED_PASSWORD;

            return 1;
        } elseif ($password !== $vPassword) {
            $this->statusMessage = MessageService::PASSWORDS_MISMATCH;

            return 1;
        } elseif ((strlen($password) || strlen($vPassword)) < 8) {
            $this->statusMessage = MessageService::WEAK_PASSWORDS;

            return 1;
        } else {
            return 0;
        }
    }

    public function ValidaMail($email)
    {
        if (empty($email)) {
            return 1;
        } elseif (preg_match(
            "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@+([_a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]{2,200}\.[a-zA-Z]{2,6}$/", $email )
        ) {
            return 0;
        } else {
            return 1;
        }
    }
}
