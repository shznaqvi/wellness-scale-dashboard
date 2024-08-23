<?php
include_once 'config.php';


$key = hashKey(WS_KEY, 8, 32); // substr(KEY, 12, 44) in JAVA
$iv_len = openssl_cipher_iv_length($cipher = "aes-256-gcm");

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
    $passwordEnc = genPassword($_POST["password"]);
    $fullName = $_POST["full_name"];
    $designation = $_POST["designation"];
    $enabled = '1';
    $isNewUser = '1';
    $pwdExpiry = date('Y-m-d H:i:s', strtotime('+90 days'));
    $distId = $_POST["district"];

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

// Function to encrypt using OpenSSL
function encrypt_openssl($textToEncrypt, $key) {
    global $iv_len, $cipher;
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag_length = 16;
    $tag = ""; // will be filled by openssl_encrypt

    $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
    $encrypted = base64_encode($iv . $ciphertext . $tag);

    return $encrypted;          
}

// Function to decrypt using OpenSSL
function decrypt_openssl($textToDecrypt, $key) {
    $encrypted = base64_decode($textToDecrypt);
    global $iv_len, $cipher;
    $iv = substr($encrypted, 0, $iv_len);
    $tag_length = 16;
    $tag = substr($encrypted, -$tag_length);

    $ciphertext = substr($encrypted, $iv_len, -$tag_length);
    $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv, $tag);

    return $decrypted;
}

// Function to generate hashed key
function hashKey($key, $offset, $length) {
    $hash = base64_encode(hash('sha384', $key, true));
    $hashedkey = substr($hash, $offset, $length);
    return $hashedkey;        
}

// Function to generate hashed password
function genPassword($password)
{
    $key_length = 16;
    $saltSize = 16;
    $iterations = 1000;
    $salt = random_bytes(16);
    
    if (!isset($algorithm) || $algorithm == '') {
        $algorithm = 'sha1'; // sha1 OR sha512
    }

    $output = hash_pbkdf2(
        $algorithm,
        $password,
        $salt,
        $iterations,
        $key_length / 8,
        true // IMPORTANT
    );

    return base64_encode($salt . $output);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Wellness Scale: Login</title>
<link rel="apple-touch-icon" sizes="180x180" href="i/ico/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="i/ico/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="i/ico/favicon-16x16.png">
<link rel="manifest" href="i/ico/site.webmanifest">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.7/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.7/js/dataTables.bootstrap4.min.js"></script>

<style>
    /* Your existing styles here */
</style>

</head>
<body class="authentication-bg">
<nav class="navbar navbar-expand-lg navbar-dark ">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Wellness Scale App</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Password Change Modal -->
<div class="modal fade" id="passwordChangeModal" tabindex="-1" role="dialog" aria-labelledby="passwordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordChangeModalLabel">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <!-- Your form for changing password -->
                        <form id="changePasswordForm">
                            <!-- Input fields for current password, new password, and confirm password -->
                            <div class="form-group">
                                <label for="currentPassword">Current Password:</label>
                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                            </div>
                            <div class="form-group">
                                <label for="newPassword">New Password:</label>
                                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password:</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            </div>
                            <!-- Error message area -->
                            <div class="alert alert-danger" id="passwordChangeError" style="display: none;"></div>
                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Optional footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End of Password Change Modal -->

<div class="container-fluid authentication-bg  pt-2 pt-sm-5 pb-4 pb-sm-5">
    <div class="row justify-content-center">
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8">
                    <div class="card shadow-lg">
                        <div class="pt-4 pb-4 text-center card-primary-color card-header"><a href="/"><span><img src=".\i\wellness_health_care.png" alt="" height="48"></span></a></div>
                        <div class="card-body p-5 bg-light rounded-3">
                            <div class="alert alert-danger" id="errorAlert" style="display: none;"></div>
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center mt-0 fw-bold">Sign In</h4>
                                <p class="text-muted mb-4">Enter your username and password to access admin panel.</p>
                            </div>    
                            <?php if (isset($insertSuccess) && $insertSuccess) : ?>
                                <div class="alert alert-success" role="alert">
                                    User created successfully!
                                </div>
                            <?php endif; ?>

                            <?php if (isset($insertError)) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $insertError; ?>
                                </div>
                            <?php endif; ?>

                            <form id="loginForm">
                                <div class="form-group mb-3">
                                    <label>Username:</label>
                                    <input type="text" class="form-control" name="username" id="username" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password">Password:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="input-group-append pl-1">
                                            <button class="btn btn btn-dark-overlay-color" type="button" id="showPassword">
                                                <i class="fa fa-eye fa-icon-class text-primary-color" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary text-center">Login</button>
                                </div>
                            </form>
                            <div class="text-center mt-4">
                                <a href="#" data-toggle="modal" data-target="#passwordChangeModal">Forgot Password?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <footer>
        <div class="row">
            <div class="col-md-6">
                <p>Copyright &copy; 2023 Wellness Scale App</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="#" class="text-dark">Terms of Use</a>
                <span class="text-muted mx-2">|</span>
                <a href="#" class="text-dark">Privacy Policy</a>
            </div>
        </div>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Submit login form
    $('#loginForm').submit(function(event) {
        event.preventDefault();
        var username = $('#username').val();
        var password = $('#password').val();

        $.ajax({
            type: 'POST',
            url: 'login-session.php', // Your PHP script to handle login
            data: {
                username: username,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.isNewUser === '1') {
                        $('#passwordChangeModal').modal('show');
                    } else {
                        // Redirect to the referring URL or dashboard.php if no referrer
                        var referringUrl = document.referrer;
                        window.location.href = referringUrl || 'dashboard.php';
                    }
                } else {
                    $('#errorAlert').text(response.message).show();
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#errorAlert').text('An error occurred. Please try again later.').show();
            }
        });
    });

    // Submit password change form (using .on() to handle multiple submissions)
    $('#passwordChangeModal').on('submit', function(event) {
        event.preventDefault();
        var username = $('#username').val(); // Capture the username
        var newPassword = $('#newPassword').val();
        var currentPassword = $('#currentPassword').val();
        var confirmPassword = $('#confirmPassword').val();

        // Perform validation checks here if necessary
var alphabetRegex = /[a-zA-Z]/;
    var numberRegex = /\d/;

        if (newPassword == currentPassword) {
            $('#passwordChangeError').text('New password cannot be same as current password. Please try again.').show();
            return;
        } else if (newPassword !== confirmPassword){
			            $('#passwordChangeError').text('Passwords do not match. Please try again.').show();
            return;
		}  else if (newPassword.length < 8) {
        console.log("Password must be at least 8 characters in length.");
					            $('#passwordChangeError').text('Password must be at least 8 characters in length.').show();
								return;
		} 
			// Check for at least one alphabet
    
    else if (!alphabetRegex.test(newPassword)) {
        console.log("Password must have at least one alphabet.");
        // Display error message or handle validation error
$('#passwordChangeError').text('Password must have at least one alphabet.').show();
								return;    } 
								// Check for at least one number
    else if (!numberRegex.test(newPassword)) {
        console.log("Password must have at least one number.");
        // Display error message or handle validation error
       $('#passwordChangeError').text('Password must have at least one number.').show();
								return; 
    } // Check if the password is the same as the username
    else if (newPassword === username) {
        console.log("Username and Password cannot be the same.");
        // Display error message or handle validation error
  $('#passwordChangeError').text('Username and Password cannot be the same.').show();
								return;     }
								

	

        $.ajax({
            type: 'POST',
            url: 'passwordchange.php', // Your PHP script to handle password change
            data: {
                username: username, // Include the username field
                newPassword: newPassword,
                currentPassword: currentPassword
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#passwordChangeError').text(response.message).show();
                    $('#errorAlert').text('Please login using new password.').show();
                } else {
                    $('#passwordChangeError').text(response.message).show();
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                $('#passwordChangeError').text('An error occurred. Please try again later.').show();
            }
        });
    });
});

</script>

</body>
</html>
