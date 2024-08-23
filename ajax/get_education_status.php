<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Query to get education status count with labels
$query = "SELECT 
    CASE `a107`
        WHEN '1' THEN 'No formal education'
        WHEN '2' THEN 'Primary education'
        WHEN '3' THEN 'Secondary education'
        WHEN '4' THEN 'Higher secondary'
        WHEN '5' THEN 'Graduation or Higher'
        ELSE 'Unknown'
    END AS `education_status`,
    COUNT(*) AS `count`
FROM `familymember`
WHERE username LIKE '%lhw%' 
GROUP BY `education_status`;";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
