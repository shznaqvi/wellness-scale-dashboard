<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// SQL query to get average age
$sql = "SELECT ROUND(AVG(a104)) AS average_age FROM familymember WHERE username NOT LIKE '%test%' and username LIKE '%lhw%' ";

$result = $con->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $averageAge = $row['average_age'];
} else {
    $averageAge = 0; // Handle error or no results
}

$con->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(array('average_age' => $averageAge));
?>
