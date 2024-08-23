<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

// Get today's date
$today = date("Y-m-d");

// Fetch the total number of surveys and surveys done today
$queryTotal = "SELECT 
                    COUNT(*) as totalSurveys,
                    SUM(CASE WHEN DATE(sysdate) = '$today' THEN 1 ELSE 0 END) AS todaySurveys,
                    SUM(CASE WHEN DATE(sysdate) = '$today' and istatus = 4 THEN 1 ELSE 0 END) AS todayRefused,
                    SUM(CASE WHEN  istatus = 4 THEN 1 ELSE 0 END) AS totalRefused
               FROM forms  where username LIKE '%lhw%' ";
$resultTotal = mysqli_query($con, $queryTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalSurveys = $rowTotal['totalSurveys'];
$todaySurveys = $rowTotal['todaySurveys'];
$todayRefused = $rowTotal['todayRefused'];
$totalRefused = $rowTotal['totalRefused'];

// Fetch the number of completed surveys and completed surveys done today
$queryCompleted = "SELECT 
                        COUNT(*) as completedSurveys,
                        SUM(CASE WHEN DATE(sysdate) = '$today' THEN 1 ELSE 0 END) AS todayCompleted
                   FROM forms WHERE iStatus = '1' and username LIKE '%lhw%' ";
$resultCompleted = mysqli_query($con, $queryCompleted);
$rowCompleted = mysqli_fetch_assoc($resultCompleted);
$completedSurveys = $rowCompleted['completedSurveys'];
$todayCompleted = $rowCompleted['todayCompleted'];

// Calculate the completion rate
$completionRate = ($totalSurveys > 0) ? round(($completedSurveys / $totalSurveys) * 100) : 0;

// Calculate the completion rate for today
$completionRateToday = ($todaySurveys > 0) ? round(($todayCompleted / $todaySurveys) * 100) : 0;

$response = array(
    'completionRate' => $completionRate,
    'completionRateToday' => $completionRateToday,
    'todayRefused' => $todayRefused,
    'totalRefused' => $totalRefused
);

header('Content-Type: application/json');
echo json_encode($response);
?>
