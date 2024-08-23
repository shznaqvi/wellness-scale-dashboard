<?php
class UserAuth2 {
    private const TAG = "UserAuth";
    private const PBKDF2_NAME = "PBKDF2WithHmacSHA1";
    private const HASH_BYTE_SIZE = 20; // SHA-1 produces 20-byte hash
    private const SALT_BYTE_SIZE = 16;
    private const ITERATIONS = 1000;

    public static function generatePassword($password, $oldsalt = null) {
        $salt = $oldsalt ? $oldsalt : self::getSalt();
		
        $hash = hash_pbkdf2(self::PBKDF2_NAME, $password, $salt, self::ITERATIONS, self::HASH_BYTE_SIZE, true);
        return base64_encode($salt . $hash);
    }

    public static function checkPassword($password, $oldPassword) {
        $decodedOldPassword = base64_decode($oldPassword);
        $salt = substr($decodedOldPassword, 0, self::SALT_BYTE_SIZE);

        $generatedPassword = self::generatePassword($password, $salt);

        return $generatedPassword === $oldPassword;
    }




    public static function getSalt() {
        $salt = openssl_random_pseudo_bytes(self::SALT_BYTE_SIZE);

        return $salt;
    }
}
?>
