<?php
include_once '../dbconfig_mysql.php';
include_once '../../encids.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$projectName = 'HFP';


$key = hashKey(WS_KEY, 8, 32); // substr(KEY, 12, 44) in JAVA
//echo $key;die();
	
//echo "Die"; die();
// $ivlen = openssl_cipher_iv_length($cipher="aes-128-cbc");
// $iv = openssl_random_pseudo_bytes($ivlen);  

$iv_len = openssl_cipher_iv_length($cipher="aes-256-gcm");
$iv = openssl_random_pseudo_bytes($iv_len);

$_id = "_id";
$deviceid = "deviceid";
$formdate = "sysdate";

$data = decrypt_openssl(file_get_contents('php://input'), $key);
//var_dump($data); die();
//if (strlen(strstr($_SERVER['HTTP_USER_AGENT'], DEVICE_AGENT)) > 0 || isset($data['check'])) {
$data = preg_replace('/[\x00-\x1F\x7F]/', '', $data);
$data = str_replace("\0", "", $data);
$data = str_replace("'", " ", $data);
$value = json_decode($data, true);
if ($value[0] != null || $value[1] != null) {
    $table = $value[0];
    $value = $value[1];
    $json = array();

    if ($table != null) {
        $table = $table['table'];
        $today = date("Y-m-d H:i:s");
        $file_dt = date("Y-m-d");
        if ($_SERVER['SERVER_NAME'] == 'f38158' || $_SERVER['SERVER_NAME'] == 'f48605' || $_SERVER['SERVER_NAME'] != 'vcoe1.aku.edu') {
            $upload_location = 'json/';
            $file = $upload_location . $table . '-' . $file_dt . '.json';
        } else {
            $upload_location = "E:/PortalFiles/JSONS/" . $projectName . "/";
            $file = $upload_location . $table . '-' . $file_dt . '.json';
        }
        if (!is_dir($upload_location)) {
            mkdir($upload_location, 0777, TRUE);
        }

        file_put_contents($file, $today . " - " . $data . "\r\n", FILE_APPEND | LOCK_EX);
        if ($value === null && json_last_error() !== JSON_ERROR_NONE) {
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
        } else {
            if ($value != null) {
                $row_1 = $value[0];
                $row_1 = flatten($row_1);
                $fld_count = 0;
                unset($row_1['synced']);
                unset($row_1['syncedDate']);
                $array_keys_1 = array_keys($row_1);
                sort($array_keys_1);

                $create_sql = "CREATE TABLE IF NOT EXISTS `$table` ( `col_id` int(11) NOT NULL AUTO_INCREMENT, `col_dt` datetime DEFAULT CURRENT_TIMESTAMP, ";
                
                foreach ($array_keys_1 as $field) {
                    $create_sql .= "`$field` text COLLATE utf8mb4_unicode_ci, ";
                }
                
                $create_sql .= "PRIMARY KEY (`col_id`), UNIQUE KEY col_id_UNIQUE (`col_id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

                if (mysqli_query($con, $create_sql)) {
                    // Table creation successful
                } else {
                    $json[] = array("error" => 1, "message" => mysqli_error($con), "status" => 0);
                }

                foreach ($value as $row) {
                    unset($row['synced']);
                    unset($row['syncedDate']);

                    $row = flatten($row);
                    $array_keys = array_keys($row);
                    $array_values = array_values($row);

                    $sql = "INSERT INTO $table (`".implode("`,`", array_map('trim', $array_keys))."`) SELECT '".implode("','", $array_values)."'";
                    $sql .= " WHERE NOT EXISTS ( SELECT `$deviceid`, `$_id`, `$formdate` FROM $table WHERE `$_id` = '".$row[$_id]."' and `$deviceid` = '".$row[$deviceid]."' and `$formdate` = '".$row[$formdate]."') LIMIT 1;";

    try {
        // Execute the INSERT query
        $result = mysqli_query($con, $sql);

        if ($result) {
                        if (mysqli_affected_rows($con) == 1) {
                            $json[] = array("error" => 0, "message" => "", "status" => 1, "id" => $row[$_id]);
                        } else {
                            $json[] = array("error" => 0, "message" => "duplicate record", "status" => 2, "id" => $row[$_id]);
                        }
                    } else {
                        $errfile = "errors/".$table . '-' . $file_dt . '.json';
                        file_put_contents($errfile, $table." ".$today." [".mysqli_error($con)."] ".$sql."\r\n", FILE_APPEND | LOCK_EX);
                        $json[] = array("error" => 1, "message" => mysqli_error($con), "status" => 0, "id" => $row[$_id]);
                    }
					 } catch (mysqli_sql_exception $e) {
        // Handle the exception
        $errorMessage = $e->getMessage();
        $errorFile = "errors/" . $table . '-' . $file_dt . '.json';
        file_put_contents($errorFile, $table . " " . $today . " [" . $errorMessage . "] " . $sql . "\r\n", FILE_APPEND | LOCK_EX);
        $json[] = array("error" => 1, "message" => $errorMessage, "status" => 0, "id" => $row[$_id]);
    }
                }
            } else {
                $json[] = array("error" => 1, "message" => 'CData is Null [] OR Key missing!', "status" => 0);
            }
        }

        mysqli_close($con);
    } else {
        $json[] = array("error" => 1, "message" => 'Vital information is missing.', "status" => 0);
    }
} else {
    $json[] = array("error" => 1, "message" => 'This is a bot!', "status" => 0);
}

header('Content-Type: application/json');
//echo json_encode($json);
    $json = json_encode($json);

echo encrypt_openssl($json, $key);


function flatten($array)
{
    $result = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $result = $result + flatten($value);
        } else {
            $result[$key] = $value;
        }
    }
    return $result;
}



/* function encrypt_openssl($msg, $key, $iv)
{
    $encryptedMessage = openssl_encrypt($msg, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encryptedMessage);
}

function decrypt_openssl($data, $key)
{
    $data = base64_decode($data);
    $iv_size = openssl_cipher_iv_length('AES-128-CBC');
    $iv = substr($data, 0, $iv_size);
    $data = substr($data, $iv_size);
    return openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

} */

function encrypt_openssl($textToEncrypt, $key) {
                // $cipher = 'aes-256-gcm';
                // $iv_len = openssl_cipher_iv_length($cipher);
				global $iv_len, $cipher;
                $iv = openssl_random_pseudo_bytes($iv_len);
                $tag_length = 16;
                $tag = ""; // will be filled by openssl_encrypt

                $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
                $encrypted = base64_encode($iv.$ciphertext.$tag);

                return $encrypted;          
}

function decrypt_openssl($textToDecrypt, $key) {
                $encrypted = base64_decode($textToDecrypt);
                // $cipher = 'aes-256-gcm';
                // $iv_len = openssl_cipher_iv_length($cipher);
				global $iv_len, $cipher;
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