<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

// Updated query
$sql = "
    SELECT 
        d.districtName,
        fm.riskOutcome,
        COUNT(*) AS risk_count
    FROM 
        ws_app.familymember fm
    LEFT JOIN 
        ws_app.forms f
        ON fm._uuid = f._uid AND fm.sysdate = f.sysdate
    LEFT JOIN 
        ws_app.district d
        ON fm.distCode = d.districtCode
    WHERE 
        fm.username LIKE '%lhw%' AND f.istatus = 1 and riskOutcome is not null
    GROUP BY 
        d.districtName, 
        fm.riskOutcome
    ORDER BY 
        d.districtName, 
        fm.riskOutcome;
";

$result = $con->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo json_encode([]);
    $con->close();
    exit();
}

$con->close();
echo json_encode($data);
?>
