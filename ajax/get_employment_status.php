<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Query to fetch employment status data
$query = "SELECT `a108`, COUNT(*) AS `count` FROM `familymember` where  username LIKE '%lhw%'  GROUP BY `a108`";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Assuming `a108` has values 'Yes' and 'No'
    $status = ($row['a108'] == '1') ? 'Yes' : 'No';
    $data[] = array('employment_status' => $status, 'count' => intval($row['count']));
}

header('Content-Type: application/json');
echo json_encode($data);
?>