<?php

namespace PhpServicesKit\DbConnection;

use Configuration\Configuration;
use Messages\MessageService;
use mysqli;

error_reporting(0);

class DbConnection
{
    public $statusMessage=null;
    public $userId=null;
    public $dbPassword=null;
    public $userName=null;

    public function testConnection()
    {

        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_OK;

            return 0;
        }
    }

    public function checkUserForSignup($username, $email)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {
            $check_stmt = $mysqli->prepare("SELECT username, email FROM members WHERE username= ? OR email= ? ");
            $check_stmt->bind_param('ss', $username, $email);
            $check_stmt->execute();
            $check_stmt->store_result();
            $check_stmt->bind_result($db_username, $db_email);
            $check_stmt->fetch();
            if ($check_stmt->num_rows >= 1) {
                if ($db_username == $username) {
                    $this->statusMessage = MessageService::DUPLICATE_USERNAME;

                    return 1;
                } elseif ($db_email == $email) {
                    $this->statusMessage = MessageService::DUPLICATE_EMAIL;

                    return 1;
                }
            } else {
                $this->statusMessage = MessageService::USER_NOT_FOUND;

                return 0;
            }
        }

        return 1;
    }

    public function addNewUser($username, $email, $password)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {
            $insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param('sss', $username, $email, $password);
            $insert_stmt->execute();
            $this->statusMessage = MessageService::INSERTED_NEW_USER;

            return 0;
        }
    }

    public function checkUserForLogin($email)
    {
        $userId=null;
        $userName=null;
        $dbPassword=null;

        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {
            $stmt = $mysqli->prepare("SELECT id, username, password FROM members WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($userId, $userName, $dbPassword);
            $stmt->fetch();

            if ($stmt->num_rows == 1) {
                $this->userName = $userName;
                $this->userId = $userId;
                $this->dbPassword = $dbPassword;

                $this->statusMessage = MessageService::USER_FOUND;

                return 0;
            } else {
                $this->statusMessage = MessageService::USER_NOT_FOUND;

                return 1;
            }
        }
    }

    public function checkBruteForce($userId)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        $now = time();

        $validAttempts = $now - (2 * 60 * 60);

        $mysqlQuery=("SELECT time FROM login_attempts WHERE user_id = ? AND time > ?");

        if ($stmt = $mysqli->prepare($mysqlQuery)) {
            $stmt->bind_param('ii', $userId, $validAttempts);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            if ($stmt->num_rows > 4) {
                $this->statusMessage = MessageService::USER_BLOCKED;

                return 1;
            } else {
                $this->statusMessage = MessageService::USER_NOT_BLOCKED;

                return 0;
            }
        }
        $this->statusMessage = MessageService::DEFAULT_ERROR;

        return 1;
    }

    public function insertBadLoginAttempt($userId)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        $now = time();
        $mysqliQuery = "INSERT INTO login_attempts (user_id, time) VALUES (?, ?)";

        if ($stmt = $mysqli->prepare($mysqliQuery)) {
            $stmt->bind_param('ii', $userId, $now);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();
            $this->statusMessage = MessageService::INSERTED_BAD_LOGIN_ATTEMPT;

            return 0;

        }
        $this->statusMessage = MessageService::DEFAULT_ERROR;

        return 1;
    }

    public function checkUserEmail($email)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {
            $check_stmt = $mysqli->prepare("SELECT email FROM members WHERE email= ? ");
            $check_stmt->bind_param('s', $email);
            $check_stmt->execute();
            $check_stmt->store_result();
            $check_stmt->bind_result($db_email);
            $check_stmt->fetch();
            if ($check_stmt->num_rows == 1) {
                $this->statusMessage = MessageService::USER_FOUND;

                return 0;
            } else {
                $this->statusMessage = MessageService::USER_NOT_FOUND;

                return 1;
            }
        }

        return 1;
    }

    public function userWantToChangePassword($userEmail, $recoveryToken)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {
            $now = time();
            //inserta usuario en lista de espera de cambio, email y token
            $insert_stmt = $mysqli->prepare(
                "INSERT INTO password_requests (user_email, token, time_stamp) VALUES (?, ?, ?)"
            );
            $insert_stmt->bind_param('sss', $userEmail, $recoveryToken, $now);
            $insert_stmt->execute();
            $this->statusMessage = MessageService::NEW_RECOVERTY_TOKEN_CREATED;

            return 0;
        }

        return 1;
    }

    public function checkValidToken($userEmail, $token)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {

            $now = time();
            $validTokens = $now - (1 * 60 * 60 * 24);

            $mysqlQuery=(
                "SELECT time_stamp FROM password_requests WHERE user_email = ? AND time_stamp > ? AND token = ?"
            );

            if ($stmt = $mysqli->prepare($mysqlQuery)) {
                $stmt->bind_param('sis', $userEmail, $validTokens, $token);
                $stmt->execute();
                $stmt->store_result();
                $stmt->fetch();
                if ($stmt->num_rows >= 1) {
                    //token valido
                    $deleteTokens=("
                        DELETE FROM password_requests WHERE user_email = ?
                    ");
                    //borrar tokens
                    if ($stmt = $mysqli->prepare($deleteTokens)) {
                        $stmt->bind_param('s', $userEmail);
                        $stmt->execute();

                        return 0;
                    } else {
                        return 1;
                    }
                } elseif ($stmt->num_rows == null) {
                    //token no valido- caducado
                    return 1;
                }
            }
            $this->statusMessage = MessageService::DEFAULT_ERROR;

            return 1;
        }

        return 1;
    }

    public function changeUserPassword($email, $newPassword)
    {
        $mysqli = new mysqli();
        $mysqli->mysqli(
            Configuration::DATABASE_HOST,
            Configuration::DATABASE_USER,
            Configuration::DATABASE_PASSWORD,
            Configuration::DATABASE_PREFIX.Configuration::DATABASE_NAME,
            Configuration::DATABASE_PORT);

        if ($mysqli->connect_error) {
            $this->statusMessage = MessageService::DATABASE_CONNECTION_ERROR;

            return 1;
        } else {
            $validUser = $this->checkUserEmail($email);

            if ($validUser===0) {
                $mysqlQuery=(
                "UPDATE members SET password = ? WHERE email = ?"
                );

                if ($stmt = $mysqli->prepare($mysqlQuery)) {
                    $stmt->bind_param('ss', $newPassword, $email);
                    $stmt->execute();

                    return 0;
                } else {
                    return 1;
                }

            } else {
                return 1;
            }
        }
    }
}
