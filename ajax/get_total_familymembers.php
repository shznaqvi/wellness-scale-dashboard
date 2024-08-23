<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

// Get today's date in the correct format for MySQL
$today = date("Y-m-d");

// Query to get the total number of family members interviewed and those done today
$query = "SELECT 
            COUNT(*) AS totalFamilyMembers,
            SUM(CASE WHEN DATE(sysdate) = '$today' THEN 1 ELSE 0 END) AS familymemberDoneToday 
          FROM familymember
		  where	 username LIKE '%lhw%' ";
$result = mysqli_query($con, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalFamilyMembers = $row['totalFamilyMembers'];
    $familymemberDoneToday = $row['familymemberDoneToday'];

    // Prepare the response array
    $response = array(
        'totalFamilyMembers' => $totalFamilyMembers,
        'familymemberDoneToday' => $familymemberDoneToday
    );

    // Send the JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle the case where the query fails
    $error = mysqli_error($con);
    echo json_encode(array('error' => $error));
}

// Close the database connection
mysqli_close($con);
?>
