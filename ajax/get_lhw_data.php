<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

$sql = "SELECT 
            a.full_name, 
            sysdate, 
            districtName, 
            -- CONCAT(a102c, ', ', a102d) AS area, 
			a102d AS area, 
            kno, 
            CASE 
                WHEN istatus = '1' THEN 'Completed'
                WHEN istatus = '2' THEN 'No Member'
                WHEN istatus = '3' THEN 'Absent'
                WHEN istatus = '4' THEN 'Refused'
                WHEN istatus = '5' THEN 'Vacant'
                WHEN istatus = '6' THEN 'Not Found'
                WHEN istatus = '7' THEN 'No Child'
                WHEN istatus = '96' THEN 'Other'
                ELSE 'Unknown'
            END AS istatus, 
            _uid, 
            col_dt AS synced_on,
			appversion
        FROM 
            ws_app.forms f 
        LEFT JOIN 
            appuser a ON f.username = a.username 
        LEFT JOIN 
            district d ON f.a101a = d.districtCode 
        WHERE 
            a.username NOT LIKE '%test%'  and f.username LIKE '%lhw%' 
		ORDER BY
			col_dt desc";

$result = $con->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$con->close();

header('Content-Type: application/json');
echo json_encode($data);
?>