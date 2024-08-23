<?php
include_once 'dbconfig_mysql.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) {
$passwordEnc = trim($_POST["newPassword"]);
$userID = trim($_POST["userID"]);


$query = "UPDATE ws_app.appuser SET passwordEnc = ?, isNewUser = 1, pwdExpiry = DATE_ADD(pwdExpiry, INTERVAL 90 DAY) WHERE username = ?";
$stmt = $con->prepare($query);

if (!$stmt) {
    $response = array(
        'success' => false,
        'message' => 'Error in preparing the statement: ' . $con->error
    );
} else {
    $stmt->bind_param("ss", $passwordEnc, $userID);

    if ($stmt->execute()) {
        $response = array(
            'success' => true,
            'message' => 'User password reset successfully!'
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Error updating user password: ' . $stmt->error
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