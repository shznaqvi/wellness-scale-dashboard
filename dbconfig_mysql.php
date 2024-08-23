<?php
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);

$host = "localhost";
$username = "app";
$password = "abcd1234";
$database = "ws_app";

// Create a MySQLi connection
$con = mysqli_connect($host, $username, $password, $database);

// Check the connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
} else {
//	echo "Success!";
}
?>