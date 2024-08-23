<?php
function genPassword($password, $salt, $algorithm = '')
{
    $key_length = 16;
    $saltSize = 16;
    $iterations = 1000;
    $salt = random_bytes(16);
	
    if (!isset($algorithm) || $algorithm == '') {
        $algorithm = 'sha1'; // sha1 OR sha512
    }

    $output = hash_pbkdf2(
        $algorithm,
        $password,
        $salt,
        $iterations,
        $key_length / 8,
        true // IMPORTANT
    );

    return base64_encode($salt . $output);
}

// Example usage
$password = "user_password";
 // You should generate a random salt
$hashedPassword = genPassword($password, $salt, 'sha1');
echo "Hashed Password: " . $hashedPassword . PHP_EOL;
?>
