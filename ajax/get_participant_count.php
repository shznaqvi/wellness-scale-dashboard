<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// SQL query to get total participant count
$sql = "SELECT count(*) as total_participants FROM ws_app.forms f
        LEFT JOIN familymember fm ON f._uid = fm._uuid AND f.sysdate = fm.sysdate
        WHERE f.username NOT LIKE '%test%' and  f.username LIKE '%lhw%' ";

$result = $con->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $totalParticipants = $row['total_participants'];
} else {
    $totalParticipants = 0; // Handle error or no results
}

$con->close();

// Return JSON response
header('Content-Type: application/json');
echo json_encode(array('total_participants' => $totalParticipants));
?>
