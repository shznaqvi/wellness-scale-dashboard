<?php
include_once '../dbconfig_mysql.php'; // Include your database connection script

// Validate district name
$districtName = trim($_POST["districtName"]);
if (empty($districtName)) {
    $response = array('success' => false, 'message' => 'Please enter a district name.');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$query = "INSERT INTO districts (districtName) VALUES (?)";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $districtName);
$result = $stmt->execute();

if ($result) {
    $response = array('success' => true, 'message' => 'New district created successfully!');
} else {
    $response = array('success' => false, 'message' => 'Error: ' . mysqli_error($con));
}

$stmt->close(); // Close the prepared statement
$con->close(); // Close the database connection

header('Content-Type: application/json');
echo json_encode($response);
?>
