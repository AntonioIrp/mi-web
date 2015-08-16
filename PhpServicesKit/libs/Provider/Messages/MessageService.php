<?php

namespace Messages;

error_reporting(0);

class MessageService
{
    const DATABASE_CONNECTION_ERROR = "can't connect to db";
    const DATABASE_CONNECTION_OK = "bd is ok";
    const DUPLICATE_USERNAME = "duplicate username";
    const DUPLICATE_EMAIL = "duplicated e-mail";
    const SIGNEDIN_SUCCESSFULLY = "sign up successful";
    const BD_DEFAULT_ERROR = "bd error";
    const USER_NOT_FOUND = "user not found";
    const USER_FOUND = "user found";
    const USER_BLOCKED = "user blocked";
    const USER_NOT_BLOCKED = "user not blocked";
    const INSERTED_NEW_USER = "inserted new user";
    const INSERTED_BAD_LOGIN_ATTEMPT = "invalid credentials";

    const EMPTY_USERNAME="name can't be empty";
    const EMPTY_EMAIL="e-mail can¡t be empty";
    const ERROR_EMAIL="invalid e-mail";
    const EMPTY_PASSWORD="password is necessary";
    const EMPTY_VERIFIED_PASSWORD="check your password";
    const PASSWORDS_MISMATCH="passwords mismatch";
    const WEAK_PASSWORDS="passwords too weak";

    const NEW_RECOVERTY_TOKEN_CREATED = "new recovery token created";

    const DEFAULT_ERROR="Error";

}
