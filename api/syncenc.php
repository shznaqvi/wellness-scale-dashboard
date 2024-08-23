<?php
include_once '../dbconfig.php';
include_once '../../../ENCID/encids.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$projectName = 'F4HE';
$key = substr(F4HE_KEY, 16, 16); // substr(KEY, 16,32) in JAVA
$ivlen = openssl_cipher_iv_length($cipher = "aes-128-cbc");
$iv = openssl_random_pseudo_bytes($ivlen);

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
                $create_sql = "SET ANSI_NULLS ON SET QUOTED_IDENTIFIER ON if not exists (select * from sysobjects where name='$table' and xtype='U') ";
                $create_sql .= " CREATE TABLE $table ( [col_id] [int] IDENTITY(1,1) NOT NULL, [col_dt] [datetime] DEFAULT getdate(), ";
                foreach ($array_keys_1 as $field) {
                    if ($fld_count >= count($array_keys_1)) {
                        $create_sql .= "[" . $field . "] varchar(900) ";
                    } else {
                        $create_sql .= "[" . $field . "] varchar(900), ";
                    }
                }
                $create_sql .= " CONSTRAINT [PK_$table] PRIMARY KEY CLUSTERED  (  [col_id] ASC )WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]  ) ON [PRIMARY]";
                $params_sel = $array_keys_1;
                if ($stmt = sqlsrv_query($con, $create_sql, $array_keys_1)) {
                } else {
                    $err_message = '';
                    if (($errors = sqlsrv_errors()) != null) {
                        foreach ($errors as $error) {
                            $err_message = $error['message'];
                            if (strpos($err_message, "Warning")) {
                                $json[] = array("error" => $error['code'], "message" => "Upload data again. If problem persist than report to your supervisor", "status" => 0);
                            } else {
                                $json[] = array("error" => $error['code'], "message" => $err_message, "status" => 0);
                            }
                        }
                    }
                }

                foreach ($value as $row) {
                    unset($row['synced']);
                    unset($row['syncedDate']);
                    $row = flatten($row);
                    $array_keys = array_keys($row);
                    $array_values = array_values($row);
                    $params_ins = $array_values;
                    /*$sql = "INSERT INTO " . $table . "(" . implode(",", array_map('trim', $array_keys)) . ") SELECT '" . implode("','", $array_values) . "'";
                    $sql .= " WHERE NOT EXISTS ( SELECT $deviceid, $_id, $formdate FROM " . $table . " WHERE $_id = '" . $row[$_id] . "' and $deviceid = '" . $row[$deviceid] . "' and $formdate = '" . $row[$formdate] . "');";*/

                    $sql = "INSERT INTO " . $table . "(" . implode(",", array_map('trim', $array_keys)) . ") SELECT '" . implode("',N'", mb_convert_encoding($array_values, 'ISO-8859-1')) . "'";
                    $sql .= " WHERE NOT EXISTS ( SELECT $deviceid, $_id, $formdate FROM " . $table . " WHERE $_id = '" . $row[$_id] . "' and $deviceid = '" . $row[$deviceid] . "' and $formdate = '" . $row[$formdate] . "');";

                    if ($stmt = sqlsrv_query($con, $sql, $params_ins)) {
                        if (sqlsrv_rows_affected($stmt) == 1) {
                            $json[] = array("error" => 0, "message" => "Successfully Saved!", "status" => 1, "id" => $row[$_id]);
                        } else {
                            $json[] = array("error" => 0, "message" => "Duplicate Sample ID. Data Uploaded.", "status" => 2, "id" => $row[$_id]);
                        }
                    } else {
                        if (($errors = sqlsrv_errors()) != null) {
                            foreach ($errors as $error) {
                                $json[] = array("error" => 1, "message" => $error['message'], "status" => 0, "id" => $row[$_id]);
                            }
                        }
                    }
                }
            } else {
                $json[] = array("error" => 1, "message" => 'CData is Null [] OR Key missing!', "status" => 0);
            }
        }
        sqlsrv_close($con);
    } else {
        $json[] = array("error" => 1, "message" => 'Vital information is missing.', "status" => 0);
    }
} else {
    $today = date("Y-m-d H:i:s");
    $file_dt = date("Y-m-d");
    if ($_SERVER['SERVER_NAME'] == 'f38158' || $_SERVER['SERVER_NAME'] == 'f48605' || $_SERVER['SERVER_NAME'] != 'vcoe1.aku.edu') {
        $upload_location = 'json/';
        $file = $upload_location . 'ERROR-' . $file_dt . '.json';
    } else {
        $upload_location = "E:/PortalFiles/JSONS/" . $projectName;
        $file = $upload_location . 'ERROR-' . $file_dt . '.json';
    }
    if (!is_dir($upload_location)) {
        mkdir($upload_location, 0777, TRUE);
    }
    file_put_contents($file, $today . " - " . $data . "\r\n", FILE_APPEND | LOCK_EX);
    $json[] = array("error" => 1, "message" => 'This is a bot!', "status" => 0);
}

header('Content-Type: application/json');
$json = json_encode($json);
echo encrypt_openssl($json, $key, $iv);

//} else {
//  generate401();
//}

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

function encrypt_openssl($msg, $key, $iv)
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

}

function generate401()
{
    header("HTTP/1.1 401 Unauthorized");
}

?>