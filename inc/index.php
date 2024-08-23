<?php

// Check if the user is authenticated
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Encryption and Decryption Functions
function encrypt_openssl($textToEncrypt, $key) {
    $cipher = 'aes-256-gcm';
    $iv_len = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag_length = 16;
    $tag = "";
    $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
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

// Key Hashing Function
function hashKey($key, $offset, $length) {
    $hash = base64_encode(hash('sha384', $key, true));
    $hashedkey = substr($hash, $offset, $length);
    return $hashedkey;
}

// Password Hashing Function
function genPassword($password) {
    $key_length = 16;
    $saltSize = 16;
    $iterations = 1000;
    $salt = random_bytes($saltSize);
    $algorithm = 'sha1'; // sha1 OR sha512
    $output = hash_pbkdf2($algorithm, $password, $salt, $iterations, $key_length / 8, true);
    return base64_encode($salt . $output);
}

?>
