<?php
error_reporting(0);

session_start();

if(isset($_COOKIE['sessionData']) &&
    ($_SESSION['sessionData'] == $_COOKIE['sessionData'])
){

    echo "variable de sesion encontrada";

} else {
    echo "variable de sesion no disponible";
}
