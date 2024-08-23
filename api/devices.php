<?php
include_once '../dbconfig.php';
$data = file_get_contents('php://input');
$today = date("Y-m-d H:i:s");
$file_dt = date("Y-m-d");
$file = 'E:/PortalFiles/JSONS/biofort/devices-' . $file_dt . '.json';
file_put_contents($file, $today . " - " . $data . "\r\n", FILE_APPEND | LOCK_EX);
$data = json_decode($data, true);
//print_r($data); IH--12
$imei = $data["imei"];
$appversion = $data["appversion"];
$appname = $data["appname"];
$dist_id = $data["dist_id"];
$tblname = "devices";
file_put_contents($file, $today . " - " . $imei . " - " . $appversion . " - " . $dist_id . "\r\n", FILE_APPEND | LOCK_EX);
$json = array();
if ($imei != '') {
    $sql = "UPDATE " . $tblname . " set appversion = '" . $appversion . "', appname = '" . $appname . "', dist_id = '" . $dist_id . "', updt_date = GETDATE()  where imei like '%" . $imei . "%';";
    //var_dump($sql); die();

    $param = array("appversion" => "appversion", "appname" => "appname", "imei" => "imei");

    $result = sqlsrv_query($con, $sql);


    $rows_affected = sqlsrv_rows_affected($result);
    if ($rows_affected == 1) {
        $sql1 = "SELECT tag FROM " . $tblname . " where imei = '" . $imei . "';";
        //var_dump($sql1); die();					$result=sqlsrv_query($con, $sql1);
        $result = sqlsrv_query($con, $sql1);
        while ($r = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $json[] = $r;

        }
    } else {
        //var_dump($sql);
        //die( print_r( sqlsrv_errors(), true));
        $row = $data;
        $table = $tblname;
        //var_dump($data); die();
        //		foreach($value as $row){


        $array_keys = array_keys($row);
        $array_values = array_values($row);


        $params_ins = $array_values;


        $sql = "INSERT INTO " . $table . "(" . implode(",", array_map('trim', $array_keys)) . ") SELECT '" . implode("','", $array_values) . "'";
        //var_dump($sql); die();
        if ($stmt = sqlsrv_query($con, $sql, $params_ins)) {


            if (sqlsrv_rows_affected($stmt) == 1) {

                $json[] = array("error" => 1, "message" => "Please register your device.", "status" => 0, "imei" => $imei);
            }

        } else {

            $json[] = array("error" => 1, "message" => sqlsrv_errors(), "status" => 0, "imei" => $imei);

        }
        $formdate = 'sysdate';
        //}
    }
    //$sql="SELECT tag FROM " . $tblname . " where imei != '$imei';";
    //$sql="SELECT * FROM family_members where dss_id_hh like '$area%' and member_type != 'h'";
    //$result=sqlsrv_query($con,$sql);
} else {

    $json[] = array("error" => 1, "message" => "imei error", "status" => 0, "imei" => $imei);
}
/* determine our thread id */
//$thread_id = mysqli_thread_id($con);
/* Kill connection */
//mysqli_kill($con, $thread_id);
//mysqli_close($con);
sqlsrv_close($con);
header('Content-Type: application/json');
echo json_encode($json);
die();
?>