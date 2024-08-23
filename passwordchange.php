<?php
include_once 'config.php';


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $username = $_POST["username"];
    $currentPassword = $_POST["currentPassword"];
    $newPassword = $_POST["newPassword"];

    // Validate input fields (e.g., check if username and passwords are not empty)

    // Retrieve the salted and hashed password from the database based on the username
    // Assuming you have a function to fetch user data from the database, adapt it here
    $userData = getUserDataFromDatabase($username, $con);

    if ($userData) {
        // Extract salt and hashed password from the retrieved user data
        $storedSaltedHash = $userData['passwordEnc'];
        
        // Extract the salt from the stored salted hash
        $storedSalt = substr($storedSaltedHash, 0, 16); // Assuming salt length is 16 bytes

        // Hash the old password with the stored salt to compare with the stored password hash
        //$hashedOldPassword = UserAuth::generatePassword($currentPassword, $storedSalt); // Use UserAuth::generatePassword()

        // Compare the hashed old password with the stored password hash
        if (UserAuth::checkPassword($currentPassword, $storedSaltedHash)) {
            // Old password matches, proceed to update the password with the new one

            // Generate salt and hash for the new password
            $newSaltedHash = UserAuth::generatePassword($newPassword, null); // Use UserAuth::generatePassword()

            // Update the password in the database with the new salted and hashed password
            // Assuming you have a function to update user data in the database, adapt it here
            $updateSuccess = updateUserPassword($username, $newSaltedHash, $con);

            if ($updateSuccess) {
                // Password updated successfully
                $response = array("success" => true, "message" => "Password updated successfully.");
                echo json_encode($response);
                exit;
            } else {
                // Failed to update password in the database
                $response = array("success" => false, "message" => "Failed to update password.");
                echo json_encode($response);
                exit;
            }
        } else {
            // Old password does not match
            $response = array("success" => false, "message" => "Old password is incorrect.");
            echo json_encode($response);
            exit;
        }
    } else {
        // User not found in the database
        $response = array("success" => false, "message" => "User not found.");
        echo json_encode($response);
        exit;
    }
}

// Function to fetch user data from the database
function getUserDataFromDatabase($username, $con)
{
    // Prepare and execute the SELECT query to retrieve user data based on username
    $query = "SELECT * FROM appuser WHERE username = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if any rows are returned
    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the user data from the result set
        $userData = mysqli_fetch_assoc($result);
        return $userData;
    } else {
        // No user found with the given username
        return null;
    }
}


// Function to update user password in the database
function updateUserPassword($username, $newPasswordEnc, $con)
{
    // Prepare and execute the UPDATE query to update the password
    $query = "UPDATE appuser SET passwordEnc = ?, isNewUser = '0' WHERE username = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $newPasswordEnc, $username);

    if (mysqli_stmt_execute($stmt)) {
        // Password updated successfully
        return true;
    } else {
        // Error occurred while updating password
        return false;
    }
}
?>
