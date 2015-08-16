<?php

namespace Configuration;

error_reporting(0);

class Configuration
{
    //Domain Name
    const DOMAIN_NAME="http://yoantonioroldan.esy.es";

    //Database connection parameters
    const DATABASE_HOST = "localhost";
    const DATABASE_USER = "u985107780_user";
    const DATABASE_PASSWORD = "123456";
    const DATABASE_NAME = "a";
    const DATABASE_PREFIX = "u985107780_";
    const DATABASE_PORT = "3306";

    //PHPMailer Configuration
    const PHPMAILER_SMTP_SERVER = 'smtp.gmail.com';
    const PHPMAILER_USER = 'airp0001@red.ujaen.es';
    const PHPMAILER_PASSWORD = '1973Pinkfloyd';
    const PHPMAILER_SMTPAUTH = true;
    const PHPMAILER_SMTPSECURE = 'tls';
    const PHPMAILER_PORT = 587;
    const PHPMAILER_FROM_EMAIL = 'fulanito@example.com';
    const PHPMAILER_FROM_NAME = 'fulanito';
    const PHPMAILER_SUBJECT = 'Password Recovery';
}
