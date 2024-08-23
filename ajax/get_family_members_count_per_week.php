<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Query to get forms count per week with starting and end dates
$query = "SELECT date(MIN(sysdate)) AS weekStart, date(MAX(sysdate)) AS weekEnd, COUNT(*) AS Counts 
          FROM familymember where   username LIKE '%lhw%' 
          GROUP BY DAY(sysdate)
		  		  ORDER by max(sysdate) 
";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>