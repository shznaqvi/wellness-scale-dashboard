<?php
// Include your database connection script
include_once '../dbconfig_mysql.php';

 $sql = "
       SELECT 
    l.full_name AS fullname,
    COUNT(DISTINCT f._uid) AS households_visited,
    COUNT(DISTINCT fm._uid) AS participants_interviewed,
    if(SUM(CASE WHEN household_counts.participants between 3 and 4 THEN 1 ELSE 0 END)>0,(SUM(CASE WHEN household_counts.participants between 3 and 4 THEN 1 ELSE 0 END)/SUM(CASE WHEN household_counts.participants between 3 and 4 THEN 1 ELSE 0 END)),0) AS households_with_3_or_more,
    if(SUM(CASE WHEN household_counts.participants >= 5 THEN 1 ELSE 0 END)>0,(SUM(CASE WHEN household_counts.participants >= 5 THEN 1 ELSE 0 END)/SUM(CASE WHEN household_counts.participants >= 5 THEN 1 ELSE 0 END)),0) AS households_with_5_or_more
FROM 
    ws_app.forms AS f
LEFT JOIN 
    ws_app.familymember AS fm ON f._uid = fm._uuid AND f.sysdate = fm.sysdate
LEFT JOIN 
    appuser AS l ON f.username = l.username 
LEFT JOIN (
    SELECT 
        f._uid,
        COUNT(fm._uid) AS participants
    FROM 
        ws_app.forms AS f
    LEFT JOIN 
        ws_app.familymember AS fm ON f._uid = fm._uuid AND f.sysdate = fm.sysdate
    GROUP BY 
        f._uid
) AS household_counts ON f._uid = household_counts._uid
WHERE 
    f.username LIKE '%LHW%'
GROUP BY 
    l.full_name;
    ";
    
    $result = $con->query($sql);
    
    $lhwData = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $lhwData[] = $row;
        }
    }
    

$con->close();

header('Content-Type: application/json');
echo json_encode($lhwData);
?>