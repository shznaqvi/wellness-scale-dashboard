<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

// Get today's date
$today = date("Y-m-d");

// Query to get the total number of active LHWS
$queryActiveLHWS = "SELECT 
count(distinct username) totalLHWS,
      COUNT(DISTINCT CASE WHEN sysdate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN username END) AS totalActiveLHWS,
    COUNT(DISTINCT CASE WHEN DATE(sysdate) = CURDATE() THEN username END) AS activeLHWsToday 
FROM forms
WHERE username LIKE '%lhw%';";
$resultActiveLHWS = mysqli_query($con, $queryActiveLHWS);

$response = array();

if ($resultActiveLHWS) {
    $rowActiveLHWS = mysqli_fetch_assoc($resultActiveLHWS);
    $totalActiveLHWS = $rowActiveLHWS['totalActiveLHWS'].'/'.$rowActiveLHWS['totalLHWS'];
    $activeLHWsToday = $rowActiveLHWS['activeLHWsToday'];

    $response['totalActiveLHWS'] = $totalActiveLHWS;
    $response['activeLHWsToday'] = $activeLHWsToday;
} else {
    // Handle the case where the query for active LHWS fails
    $response['errorActiveLHWS'] = mysqli_error($con);
}

header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
mysqli_close($con);
?>
