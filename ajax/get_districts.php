<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

$query = "SELECT * FROM districts";
$result = mysqli_query($con, $query);

$districts = array();
while ($row = mysqli_fetch_assoc($result)) {
    $districts[] = array(
        'districtCode' => $row['districtCode'],
        'districtName' => $row['districtName']
    );
}

$response = array('districts' => $districts);
header('Content-Type: application/json');
echo json_encode($response);
?>
