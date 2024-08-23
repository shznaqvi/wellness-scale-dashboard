<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Query to get LHW visit counts within the last 7 days
$query = "SELECT 
            l.full_name AS LHW,
            COUNT(*) AS Visits_Count
          FROM forms f 
          LEFT JOIN appuser l ON f.username = l.username
          WHERE f.sysdate >= DATE_SUB(CURDATE(), INTERVAL 7 month) and   l.username like '%lhw%'
          GROUP BY f.username, l.full_name
		  order by count(*) desc";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
