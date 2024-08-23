<?php
// Initialize database connection
$con = mysqli_connect("hostname", "username", "password", "database_name");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Initialize variables
$username = "";
$password = "";
$fullName = "";
$designation = "";
$enabled = "";
$isNewUser = "";
$pwdExpiry = "";
$distId = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $username = $_POST["username"];
    $passwordEnc = $_POST["password"];
    $fullName = $_POST["full_name"];
    $designation = $_POST["designation"];
    $enabled = $_POST["enabled"];
    $isNewUser = $_POST["isNewUser"];
    $pwdExpiry = $_POST["pwdExpiry"];
    $distId = $_POST["dist_id"];

    // Prepare and execute the INSERT query
    $query = "INSERT INTO appuser (username, passwordEnc, full_name, designation, enabled, isNewUser, pwdExpiry, dist_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssssssss", $username, $passwordEnc, $fullName, $designation, $enabled, $isNewUser, $pwdExpiry, $distId);
    
    if (mysqli_stmt_execute($stmt)) {
        $insertSuccess = true;
    } else {
        $insertError = "Error: " . mysqli_error($con);
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New User</title>
</head>
<body>

<h2>Create New User</h2>

<?php if (isset($insertSuccess) && $insertSuccess) : ?>
    <p>User created successfully!</p>
<?php endif; ?>

<?php if (isset($insertError)) : ?>
    <p><?php echo $insertError; ?></p>
<?php endif; ?>

<form method="post">
    <label>Username:</label>
    <input type="text" name="username" required><br>

    <label>Password:</label>
    <input type="password" name="password" required><br>

    <label>Full Name:</label>
    <input type="text" name="full_name"><br>

    <label>Designation:</label>
    <input type="text" name="designation"><br>

    <label>Enabled:</label>
    <select name="enabled">
        <option value="Yes">Yes</option>
        <option value="No">No</option>
    </select><br>

    <label>Is New User:</label>
    <select name="isNewUser">
        <option value="Yes">Yes</option>
        <option value="No">No</option>
    </select><br>

    <label>Password Expiry:</label>
    <input type="text" name="pwdExpiry"><br>

    <label>Dist ID:</label>
    <input type="text" name="dist_id"><br>

    <input type="submit" value="Create User">
</form>

</body>
</html>
