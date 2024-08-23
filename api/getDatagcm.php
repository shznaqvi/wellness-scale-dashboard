<?php
include_once '../dbconfig_mysql.php';
include_once '../../encids.php';

$key = hashKey(WS_KEY, 8, 32); // substr(KEY, 12, 44) in JAVA
//echo $key;die();

//echo "Die"; die();
// $ivlen = openssl_cipher_iv_length($cipher="aes-128-cbc");
// $iv = openssl_random_pseudo_bytes($ivlen);  

$iv_len = openssl_cipher_iv_length($cipher = "aes-256-gcm");

$data = json_decode(decrypt_openssl(file_get_contents('php://input'), $key), true);
//echo $key."\r\n";
//var_dump($data);die();

// {"table":"villages","filter":"seem_vid like '3%'"}
//$data = json_decode(file_get_contents('php://input'), true);
$json = array();

if (isset($data['table']) && $data['table'] != '') {
    $table = $data['table'];

    if ($table == "versionApp") {
        $json = json_decode(file_get_contents($data['folder'] . '/output-metadata.json'), true);
    } else {
        $sql = "SELECT ";

        if (isset($data['limit']) && $data['limit'] != '') {
            $sql .= "LIMIT " . intval($data['limit']);
        }

        if (isset($data['select']) && $data['select'] != '') {
            $sql .= $data['select'] . " FROM $table";
        } else {
            $sql .= " * FROM $table";
        }

        if (isset($data['filter']) && $data['filter'] != '') {
            $sql .= " WHERE " . $data['filter'];
        }

        if (isset($data['orderby']) && $data['orderby'] != '') {
            $sql .= " ORDER BY " . $data['orderby'];
        }

        $result = mysqli_query($con, $sql);

        if (!$result) {
            $json[] = array('status' => 0, 'message' => 'Query error.', 'error' => 1);
        } else {
            if (mysqli_num_rows($result) == 0) {
                $json[] = array('status' => 0, 'message' => 'No record found.', 'error' => 1);
            } else {
                while ($r = mysqli_fetch_assoc($result)) {
                    $json[] = $r;
                }
            }
        }
    }
} else {
    $json[] = 'Invalid Table';
}

mysqli_close($con);
/* header('Content-Type: application/json');
$json = json_encode($json, JSON_UNESCAPED_UNICODE);

if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
    $json[] = array('status' => 0, 'message' => 'JSON encoding error.', 'error' => 1);
}

echo json_encode($json, JSON_UNESCAPED_UNICODE); */

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
echo encrypt_openssl($json, $key);


function encrypt_openssl($textToEncrypt, $key)
{
    // $cipher = 'aes-256-gcm';
    // $iv_len = openssl_cipher_iv_length($cipher);
    global $iv_len, $cipher;
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag_length = 16;
    $tag = ""; // will be filled by openssl_encrypt

    $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
    $encrypted = base64_encode($iv . $ciphertext . $tag);

    return $encrypted;
}

function decrypt_openssl($textToDecrypt, $key)
{
    $encrypted = base64_decode($textToDecrypt);
    // $cipher = 'aes-256-gcm';
    // $iv_len = openssl_cipher_iv_length($cipher);
    global $iv_len, $cipher;
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