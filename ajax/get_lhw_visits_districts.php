<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

$query = "SELECT 
    d.districtName,
    SUM(CASE 
            WHEN f.sysdate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 
            ELSE 0 
        END) AS Visits_Count_Last_7_Days,
    SUM(CASE 
            WHEN f.sysdate BETWEEN DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND DATE_SUB(CURDATE(), INTERVAL 8 DAY) THEN 1 
            ELSE 0 
        END) AS Visits_Count_Previous_7_Days,
    SUM(CASE 
            WHEN f.sysdate >= DATE_SUB(CURDATE(), INTERVAL 2 DAY) THEN 1 
            ELSE 0 
        END) AS Visits_Count_Last_2_Days,
    SUM(CASE 
            WHEN f.sysdate BETWEEN DATE_SUB(CURDATE(), INTERVAL 5 DAY) AND DATE_SUB(CURDATE(), INTERVAL 3 DAY) THEN 1 
            ELSE 0 
        END) AS Visits_Count_Previous_2_Days
FROM 
    forms f
JOIN 
    district d ON f.distCode = d.districtCode
	where  f.username LIKE '%lhw%' 
GROUP BY 
    d.districtCode;";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
