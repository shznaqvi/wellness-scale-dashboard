<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

$query = "SELECT districtName, count(*) AS householdCount FROM forms f left join district d on f.distCode = d.districtCode where districtname is not null and  f.username LIKE '%lhw%' GROUP BY distCode";

$result = mysqli_query($con, $query);

$districtLabels = array();
$householdCountData = array();

while ($row = mysqli_fetch_assoc($result)) {
    $districtLabels[] = $row['districtName'];
    $householdCountData[] = $row['householdCount'];
}

$response = array(
    'districtLabels' => $districtLabels,
    'householdCountData' => $householdCountData
);

header('Content-Type: application/json');
echo json_encode($response);
?>