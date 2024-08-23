<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Query to get gender counts
$query = "SELECT 
            CASE 
                WHEN `a105` = 1 THEN 'Male'
                WHEN `a105` = 2 THEN 'Female'
                ELSE 'Other'
            END AS gender,
            COUNT(*) AS count 
          FROM `familymember` 
		  where  username LIKE '%lhw%' 
          GROUP BY `a105`";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>