<?php
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);

$host = "localhost";
$username = "app";
$password = "abcd1234";
$database = "f4he";
$connectionInfo = array("Database" => $database, "UID" => $username, "PWD" => $password);
$con = sqlsrv_connect($host, $connectionInfo);

if ($con === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
