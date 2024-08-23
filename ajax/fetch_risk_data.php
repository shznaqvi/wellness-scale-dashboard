<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// SQL query to fetch risk data
$query = "SELECT u.full_name AS lhw, MAX(f.sysdate) AS sysdate, f.kno, MAX(a102d) AS area, riskoutcome, COUNT(*) AS risk_count, MAX(xac) AS xac, MAX(xdt) AS xdt, MAX(xlg) AS xlg, MAX(xlt) AS xlt
          FROM ws_app.forms f
          LEFT JOIN appuser u ON f.username = u.username
          LEFT JOIN familymember fm ON f._uid = fm._uuid AND f.sysdate = fm.sysdate
          WHERE f.username LIKE '%lhw%' AND xac != 0
          GROUP BY riskoutcome, f.kno, u.full_name
          ORDER BY u.full_name, f.kno, riskoutcome";

// Execute query
$result = mysqli_query($con, $query);

// Fetch data into associative array
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

// Close connection
mysqli_close($con);

// Output data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
