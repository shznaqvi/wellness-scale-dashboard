<?php
include_once 'config.php';
require 'UserAuth.php';

// Function to check if the session has expired
function isSessionExpired() {
    $sessionStartTime = isset($_SESSION['sessionStartTime']) ? $_SESSION['sessionStartTime'] : 0;
    return (time() - $sessionStartTime) > (15 * 60); // 15 minutes in seconds
}

// Function to handle invalid credentials
function handleInvalidCredentials() {
    if (!isset($_SESSION['failed_attempts'])) {
        $_SESSION['failed_attempts'] = 0;
    } else {
        $_SESSION['failed_attempts']++;
    }
}

// Check if the session has expired, and if so, unset the session variables
if (isset($_SESSION['sessionStartTime']) && isSessionExpired()) {
    session_unset();
    session_destroy();

}

// Check if username and password are provided via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $plaintextPassword = $_POST['password'];

    // Retrieve the stored password, role, isNewUser, and enabled status from the database using the provided username
    $query = "SELECT passwordEnc, role, isNewUser, enabled FROM appuser WHERE username = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $storedPassword = $row['passwordEnc'];
        $userRole = $row['role'];
        $isNewUser = $row['isNewUser'];
        $enabled = $row['enabled'];

        // Check if the user has been blocked
        if ($enabled != 1) {
            $response = array('success' => false, 'message' => 'User has been blocked!', 'isNewUser' => 0);
        } else {
            // Check if there are too many failed attempts
            if (isset($_SESSION['failed_attempts']) && $_SESSION['failed_attempts'] >= 5) {
                $response = array('success' => false, 'message' => 'Too many failed attempts. Please try again later.', 'isNewUser' => 0);
            } else {
                // Compare the provided plaintext password with the stored password hash
                if (UserAuth::checkPassword($plaintextPassword, $storedPassword)) {
                    // Passwords match, reset failed attempts and set session variables
					            session_regenerate_id(true);

                    unset($_SESSION['failed_attempts']);
                    $_SESSION['sessionStartTime'] = time(); // Store the session start time
                    $_SESSION['username'] = $username; // Store username in session
                    $_SESSION['role'] = $userRole; // Store user role in session
                    
                    // Proceed with further authentication or tasks
                    if ($userRole === 'admin' || $userRole === 'user') {
                        // Redirect admin and regular users to the dashboard
                        $response = array('success' => true, 'redirect' => 'dashboard.php', 'isNewUser' => $isNewUser);
                    } else {
                        // Handle other roles or unauthorized access
                        $response = array('success' => false, 'message' => 'Unauthorized access', 'isNewUser' => 0);
                    }
                } else {
                    // Invalid credentials, increment failed attempts
                    handleInvalidCredentials();
                    $response = array('success' => false, 'message' => 'Invalid credentials', 'isNewUser' => 0);
                }
            }
        }
    } else {
        // User not found
        $response = array('success' => false, 'message' => 'User not found', 'isNewUser' => 0);
    }

    mysqli_stmt_close($stmt);
} else {
    // Invalid request method or missing username/password
    $response = array('success' => false, 'message' => 'Invalid request method or missing username/password', 'isNewUser' => 0);
}

header('Content-Type: application/json');
echo json_encode($response);
?>
