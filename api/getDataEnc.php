<?php
include_once '../dbconfig.php';
include_once '../../../ENCID/encids.php';
//require_once("testid.php");

// DEFINE KEY NAME HERE
$key = substr(F4HE_KEY, 16, 16); // substr(KEY, 16,32) in JAVA
//echo $key;die();

//echo "Die"; die();
$ivlen = openssl_cipher_iv_length($cipher = "aes-128-cbc");
$iv = openssl_random_pseudo_bytes($ivlen);
$data = json_decode(decrypt_openssl(file_get_contents('php://input'), $key), true);
//echo $key."\r\n";
//var_dump($data);die();

// {"table":"villages","filter":"seem_vid like '3%'"}
//$data = json_decode(file_get_contents('php://input'), true);
$json = array();
if (isset($data['table']) && $data['table'] != '') {
    $table = $data['table'];
    if ($table == "versionApp") {

        $json = json_decode(file_get_contents('../app/output.json'), true);
    } else {

        // select top 10 * from [yourtable] order by newid()
        $sql = "SELECT ";

        if (isset($data['limit']) && $data['limit'] != '') {

            $sql .= " top " . $data['limit'];
        }

        if (isset($data['select']) && $data['select'] != '') {

            $sql .= $data['select'] . " from $table";

        } else {

            $sql .= " * from $table";

        }

        if (isset($data['filter']) && $data['filter'] != '') {

            $sql .= " where " . $data['filter'];
        }

        if (isset($data['orderby']) && $data['orderby'] != '') {

            $sql .= " order by " . $data['orderby'];
        }
        $sql .= " ; ";


        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        // echo $sql; die();
        $result = sqlsrv_query($con, $sql, $params, $options);
        if ($result === false) {
            echo "Error in query preparation/execution.\n";
            die(print_r(sqlsrv_errors(), true));
        } else {

            $row_count = sqlsrv_num_rows($result);
            //var_dump(sqlsrv_num_rows( $result ));die();

            if ($row_count == 0) {
                $json[] = array('status' => 0, 'message' => 'No record found.', 'error' => 1);
            } else {
                //echo $row_count;die();
                while ($r = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                    $json[] = $r;
                }
            }
        }
    }
} else {
    $json[] = 'Invalid Table';
}
// var_dump($json);die();
sqlsrv_close($con);
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


function encrypt_openssl($msg, $key, $iv)
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

}

?>