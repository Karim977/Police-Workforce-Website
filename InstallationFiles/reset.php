<?php
function redirect($url)
{
    header('Location: ' . $url);
    die();
}
session_start();
$username = $_SESSION['username'];
$reporter_ID = $_SESSION['reporter_ID'];
// Stackoverflow comment
// Unsetting all session variables
$helper = array_keys($_SESSION);
    foreach ($helper as $key){
        unset($_SESSION[$key]);
    }
$_SESSION['username'] = $username;
$_SESSION['reporter_ID'] = $reporter_ID;
$destination = $_GET["dest"];
redirect($destination)


?>