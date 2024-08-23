<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Fetch high risk count
$sql = "SELECT count(*) AS high_risk_count FROM familymember WHERE riskOutcome = 'HIGH RISK' AND username NOT LIKE '%test%'";
$result = $con->query($sql);

$data = array();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data['high_risk_count'] = $row['high_risk_count'];
} else {
    $data['high_risk_count'] = 0;
}

$con->close();

header('Content-Type: application/json');
echo json_encode($data);
?>