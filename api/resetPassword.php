<?php


include_once '../dbconfig_mysql.php'; // Include your database configuration here
include_once '../../encids.php'; // Include encryption functions here

$key = hashKey(WS_KEY, 8, 32); // substr(KEY, 12, 44) in JAVA

$iv_len = openssl_cipher_iv_length($cipher = "aes-256-gcm");
$iv = openssl_random_pseudo_bytes($iv_len);

$data = json_decode(decrypt_openssl(file_get_contents('php://input'), $key), true);
$json = array();

if (isset($data['userName']) && $data['userName'] != '') {
    $userName = $data['userName'];
    $oldPassword = $data['oldPassword'];
    $newPassword = $data['newPassword'];

    $sql = "UPDATE appuser SET passwordenc = ?,  pwdExpiry = DATE_ADD(pwdExpiry, INTERVAL 90 DAY), isNewUser = '0' WHERE username = ? AND passwordenc = ?";


    try {
        // Prepare and execute the query
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $newPassword, $userName, $oldPassword);

        if (mysqli_stmt_execute($stmt)) {
            $row_count = mysqli_stmt_affected_rows($stmt);
            if ($row_count == 0) {
                $json[] = array('status' => 0, 'message' => 'Username or Password did not match.', 'error' => 1);
            } else {
                $json[] = array('userName' => $userName, 'message' => 'Password updated successfully.', 'error' => 0);
            }
        } else {
            $json[] = array('status' => 0, 'message' => 'Error in query execution.', 'error' => 1);
        }
    } catch (mysqli_sql_exception $e) {
        // Handle the exception
        $errorMessage = $e->getMessage();
        // ... Handle the error logging and reporting
    } finally {
        mysqli_stmt_close($stmt); // Close the prepared statement
    }
} else {
    $json[] = "Invalid user.";
}

mysqli_close($con);
header('Content-Type: application/json');
$json = json_encode($json);

if ($json === null && json_last_error() !== JSON_ERROR_NONE) {

    switch (json_last_error()) {
        case JSON_ERROR_NONE:

            break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
            $json[] = array('status' => 0, 'message' => ' - Maximum stack depth exceeded', 'error' => 1);
            break;
        case JSON_ERROR_STATE_MISMATCH:
            //echo ' - Underflow or the modes mismatch';
            $json[] = array('status' => 0, 'message' => ' - Underflow or the modes mismatch', 'error' => 1);
            break;
        case JSON_ERROR_CTRL_CHAR:
            //echo ' - Unexpected control character found';
            $json[] = array('status' => 0, 'message' => ' - Unexpected control character found', 'error' => 1);

            break;
        case JSON_ERROR_SYNTAX:
            //echo ' - Syntax error, malformed JSON';
            $json[] = array('status' => 0, 'message' => ' - Syntax error, malformed JSON', 'error' => 1);


            break;
        case JSON_ERROR_UTF8:
            //echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            $json[] = array('status' => 0, 'message' => ' - Malformed UTF-8 characters, possibly incorrectly encoded', 'error' => 1);

            break;
        default:
            //echo ' - Unknown error';
            $json[] = array('status' => 0, 'message' => ' - Unknown error', 'error' => 1);


            break;
    }
    $json = json_encode($json);
}
//echo $json;
echo encrypt_openssl($json, $key, $iv);


/* function encrypt_openssl($msg, $key, $iv)
{
    $encryptedMessage = openssl_encrypt($msg, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encryptedMessage);
}

function decrypt_openssl($data, $key)
{
    $data = base64_decode($data);
    //$data = $data;
    $iv_size = openssl_cipher_iv_length('AES-128-CBC');
    $iv = substr($data, 0, $iv_size);
    $data = substr($data, $iv_size);
    return openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

} */
function encrypt_openssl($msg, $key)
{

    $cipher = 'aes-256-gcm';
    $iv_len = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag_length = 16;
    $tag = ""; // will be filled by openssl_encrypt

    $ciphertext = openssl_encrypt($msg, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
    $encrypted = base64_encode($iv . $ciphertext . $tag);

    return $encrypted;
}

function decrypt_openssl($textToDecrypt, $key)
{

    $encrypted = base64_decode($textToDecrypt);
    $cipher = 'aes-256-gcm';
    $iv_len = openssl_cipher_iv_length($cipher);
    $iv = substr($encrypted, 0, $iv_len);
    $tag_length = 16;
    $tag = substr($encrypted, -$tag_length);

    $ciphertext = substr($encrypted, $iv_len, -$tag_length);
    $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv, $tag);

    return $decrypted;
}

function hashKey($key, $offset, $length)
{

    $hash = base64_encode(hash('sha384', $key, true));
    $hashedkey = substr($hash, $offset, $length);

    return $hashedkey;
}

?>

