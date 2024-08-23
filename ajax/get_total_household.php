<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

// Get today's date in YYYY-MM-DD format
$today = date('Y-m-d');

// Query to get the total number of households and households done today
$query = "SELECT 
            COUNT(*) AS totalHouseholds, 
            SUM(CASE WHEN DATE(sysdate) = '$today' THEN 1 ELSE 0 END) AS householdsDoneToday 
          FROM 
            ws_app.forms
		WHERE  username LIKE '%lhw%' ";
$result = mysqli_query($con, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalHouseholds = $row['totalHouseholds'];
    $householdsDoneToday = $row['householdsDoneToday'];
} else {
    // Handle the error if the query fails
    $totalHouseholds = 0;
    $householdsDoneToday = 0;
}

$response = array(
    'totalHouseholds' => $totalHouseholds,
    'householdsDoneToday' => $householdsDoneToday
);
header('Content-Type: application/json');
echo json_encode($response);
?>
