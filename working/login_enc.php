<?php
session_save_path('F:/htdocs');
session_start();

include_once '../dbconfig_mysql.php'; // Include your database configuration here
include_once '../encids.php'; // Include encryption functions here

$key = hashKey(WS_KEY, 8, 32); // Assuming WS_KEY is defined elsewhere

// Check if username and password are provided via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = encrypt_openssl($_POST['password'], $key);

    // Validate the user's credentials
    $query = "SELECT * FROM appuser WHERE username = ? AND passwordEnc = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_id'] = $row['id']; // Store user ID in session
        $response = array('success' => true);
    } else {
        $response = array('success' => false, 'message' => 'Invalid credentials');
    }

    mysqli_stmt_close($stmt);
} else {
    $response = array('success' => false, 'message' => 'Invalid request method or missing username/password');
}

header('Content-Type: application/json');
echo json_encode($response);

function encrypt_openssl($msg, $key) {
    $cipher = 'aes-256-gcm';
    $iv_len = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag_length = 16;
    $tag = ""; // will be filled by openssl_encrypt

    $ciphertext = openssl_encrypt($msg, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
    $encrypted = base64_encode($iv.$ciphertext.$tag);

    return $encrypted;          
}

function decrypt_openssl($textToDecrypt, $key) {
    $encrypted = base64_decode($textToDecrypt);
    $cipher = 'aes-256-gcm';
    $iv_len = openssl_cipher_iv_length($cipher);
    $iv = substr($encrypted, 0, $iv_len);
    $tag_length = 16;
    $tag = substr($encrypted, -$tag_length);

    $ciphertext = substr($encrypted, $iv_len, -$tag_length);
    $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag);

    return $decrypted;
}

function hashKey($key, $offset, $length) {
    $hash = base64_encode(hash('sha384', $key, true));
    $hashedkey = substr($hash, $offset, $length);

    return $hashedkey;        
}
?>
