<?php
include_once 'dbconfig_mysql.php';
include_once '../encids.php';

// Set session parameters
ini_set('session.gc_maxlifetime', 900); // 15 minutes


// Start session
session_save_path('F:/htdocs/iisses/');
session_start();

// Other configuration settings goes here
// Get the session ID

$session_id = session_id();
$last_8_chars = substr($session_id, -8)





?>