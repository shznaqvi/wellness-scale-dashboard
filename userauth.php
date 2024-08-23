<?php
class UserAuth {
    private const TAG = "UserAuth";
    private const PBKDF2_NAME = "sha1"; // Using SHA1 for HMAC
    private const HASH_BYTE_SIZE = 16; // SHA-1 produces 20-byte hash
    private const SALT_BYTE_SIZE = 16;
    private const ITERATIONS = 1000;

public static function generatePassword($password, $oldsalt) {
     $salt = $oldsalt ? $oldsalt : self::getSalt();

 /*   $hash = hash_pbkdf2(self::PBKDF2_NAME, $password, $salt, self::ITERATIONS, self::HASH_BYTE_SIZE/8, true);
    
    // Concatenate salt and hash
    $saltAndHash = $salt . $hash;
    
    // Encode concatenated byte array
    return base64_encode($saltAndHash); */
	  $key_length = 16;
    $saltSize = 16;
    $iterations = 1000;
    //$salt = random_bytes(16);
	
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

    public static function checkPassword($password, $oldPassword) {
        $decodedOldPassword = base64_decode($oldPassword);
        $salt = substr($decodedOldPassword, 0, self::SALT_BYTE_SIZE);

        $generatedPassword = self::generatePassword($password, $salt);
/* 		echo 'oldPassword: '.$oldPassword;
echo "\nsalt: ". $salt;
echo "\ngenPass:". $generatedPassword;
echo "\n"; */
        return $generatedPassword === $oldPassword;
    }

    private static function getSalt() {
        return random_bytes(self::SALT_BYTE_SIZE);
    }
}
?>
