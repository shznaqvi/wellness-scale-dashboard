<?php
include_once '../dbconfig_mysql.php';

// Fetch the timestamp of the last refresh from the client
$last_refresh_time = isset($_GET['last_refresh_time']) ? $_GET['last_refresh_time'] : null;

if ($last_refresh_time) {
    // Convert the last refresh time to a datetime format for the query
    $last_refresh_time = date('Y-m-d H:i:s', strtotime($last_refresh_time));

    // Query to count new forms since the last refresh
    $query = "SELECT COUNT(*) AS new_forms_count FROM forms WHERE username like '%.lhw%' and col_dt > '$last_refresh_time'";
    $result = mysqli_query($con, $query);
    $data = mysqli_fetch_assoc($result);

    echo json_encode(['new_forms_count' => $data['new_forms_count']]);
} else {
    echo json_encode(['new_forms_count' => 0]);
}
?>
