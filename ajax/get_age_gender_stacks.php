<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Query to get age count within health-specific groups
$query = "SELECT 
    CASE 
    when a104 BETWEEN 18 AND 24 THEN '18-24 years old'
    WHEN a104 BETWEEN 25 AND 34 THEN '25-34 years old'
	WHEN a104 BETWEEN 35 AND 54 THEN '35-54 years old'
    WHEN a104 BETWEEN 55 AND 64 THEN '55-64 years old'
    WHEN a104 >= 65 THEN '65 and above'
    END AS `age_group`,
    SUM(CASE WHEN `a105` = 2 THEN 1 ELSE 0 END) AS `Female`,
    SUM(CASE WHEN `a105` = 1 THEN 1 ELSE 0 END) AS `Male`,
    SUM(CASE WHEN `a105` NOT IN (1, 2) THEN 1 ELSE 0 END) AS `Other`
FROM `familymember` 
WHERE  username LIKE '%lhw%' 
GROUP BY `age_group`
ORDER BY `age_group`;";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $ageGroup = $row['age_group'];
    unset($row['age_group']);
    $data[$ageGroup] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>