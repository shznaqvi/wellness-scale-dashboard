<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// SQL query to get gender ratio
$sql = "SELECT 
            SUM(CASE WHEN a105 = 1 THEN 1 ELSE 0 END) AS male_count,
            SUM(CASE WHEN a105 = 2 THEN 1 ELSE 0 END) AS female_count
        FROM familymember
        WHERE username NOT LIKE '%test%' and username LIKE '%lhw%' ";

$result = $con->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $maleCount = $row['male_count'];
    $femaleCount = $row['female_count'];
} else {
    $maleCount = 0; // Handle error or no results
    $femaleCount = 0; // Handle error or no results
}

$con->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(array('male_count' => $maleCount, 'female_count' => $femaleCount));
?>
