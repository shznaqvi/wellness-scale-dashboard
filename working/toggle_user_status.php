<?php
include_once '../dbconfig_mysql.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) {
$enabled = trim($_POST["enabled"]);
$userID = trim($_POST["userID"]);


$query = "UPDATE ws_app.appuser SET enabled = ? WHERE username = ?";
$stmt = $con->prepare($query);

if (!$stmt) {
    $response = array(
        'success' => false,
        'message' => 'Error in preparing the statement: ' . $con->error
    );
} else {
    $stmt->bind_param("ss", $enabled, $userID);

    if ($stmt->execute()) {
        $response = array(
            'success' => true,
            'message' => 'User status toggled successfully!'
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error updating user status: ' . $stmt->error
        );
    }

    $stmt->close();
}

// Convert the response array to JSON
$jsonResponse = json_encode($response);

// Set appropriate headers
header('Content-Type: application/json');
header('Content-Length: ' . strlen($jsonResponse));

// Send the JSON response
echo $jsonResponse;
}
?>	 