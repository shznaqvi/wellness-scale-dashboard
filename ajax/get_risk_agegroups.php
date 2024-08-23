<?php
include_once '../dbconfig_mysql.php';

// Query to get risk stratification counts for different age groups
$query = "
SELECT 
  CASE
    WHEN a104 BETWEEN 18 AND 24 THEN '18-24 years old'
    WHEN a104 BETWEEN 25 AND 34 THEN '25-34 years old'
    WHEN a104 BETWEEN 35 AND 54 THEN '35-54 years old'
    WHEN a104 BETWEEN 55 AND 64 THEN '55-64 years old'
    WHEN a104 >= 65 THEN '65 and above'
  END AS age_group,
  riskOutcome,
  COUNT(*) AS risk_count
FROM familymember
where  username LIKE '%lhw%'
GROUP BY age_group, riskOutcome
ORDER BY age_group";

$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
