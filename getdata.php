<?php
include_once 'dbconfig_mysql.php';


$sql = "SELECT sysdate, distCode,  u.full_name, kno, xac gps_accuracy, xdt gps_date_time,  xlg longitude, xlt latitude FROM ws_app.forms f
left join appuser u on f.username = u.username where f.username like '%lhw%' and xac != 0 order by f.username, right('0000'+kno,3);";
$result = $con->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
$con->close();
?>
