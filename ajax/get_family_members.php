<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

$data = []; // Initialize an empty array for storing data

if (isset($_GET['uid'])) {
    $uuid = $_GET['uid']; // Sanitize input to prevent SQL injection
    $uuid = $con->real_escape_string($uuid); // Sanitize input using real_escape_string

    // SQL query to fetch family member details using prepared statement
    $sql = "SELECT a103 AS member_name,
                   CONCAT(a104, 'y') AS age,
                   CASE WHEN a105 = 1 THEN 'Male' WHEN a105 = 2 THEN 'Female' END AS gender,
				       if(diabetesResult = 1, 'Yes', 'No') diabetesResult,
                   if(strokeResult= 1, 'Yes', 'No') strokeResult,
                   if(anginaResult= 1, 'Yes', 'No') anginaResult,
                   if(mentalResult= 1, 'Yes', 'No') mentalResult,
                   if(hypertensionResult= 1, 'Yes', 'No') hypertensionResult,
                   riskoutcome
            FROM familymember
            WHERE _uuid = '$uuid' and  familymember.username LIKE '%lhw%' ";

    $result = $con->query($sql);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    } else {
        // Handle query error
        echo json_encode(['error' => 'Query failed: ' . $con->error]);
        http_response_code(500); // Internal Server Error
        exit;
    }
}

$con->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
