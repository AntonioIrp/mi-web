<?php

error_reporting(0);

session_start();

if(isset($_COOKIE['sessionData']) &&
    ($_SESSION['sessionData'] == $_COOKIE['sessionData'])
){
    session_destroy();
    echo "session destroyed";
}
