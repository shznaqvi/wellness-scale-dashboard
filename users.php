
<?php
include_once 'config.php';


// Check if the user is not authenticated
if (!isset($_SESSION['username'])) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}

// Check if the user has the required role (admin)
if ($_SESSION['role'] !== 'admin') {
    // Redirect the user to the unauthorized access page
    header("Location: unauthorized.php");
    exit(); // Stop further execution
}


$key = hashKey(WS_KEY, 8, 32); // substr(KEY, 12, 44) in JAVA
//echo $key;die();

//echo "Die"; die();
// $ivlen = openssl_cipher_iv_length($cipher="aes-128-cbc");
// $iv = openssl_random_pseudo_bytes($ivlen);  

$iv_len = openssl_cipher_iv_length($cipher="aes-256-gcm");

/* 
// Initialize database connection
$con = mysqli_connect("hostname", "username", "password", "database_name");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
} */

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

// Close connection
//mysqli_close($con);




function encrypt_openssl($textToEncrypt, $key) {
                // $cipher = 'aes-256-gcm';
                // $iv_len = openssl_cipher_iv_length($cipher);
				global $iv_len, $cipher;
                $iv = openssl_random_pseudo_bytes($iv_len);
                $tag_length = 16;
                $tag = ""; // will be filled by openssl_encrypt

                $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
                $encrypted = base64_encode($iv.$ciphertext.$tag);

                return $encrypted;          
}

function decrypt_openssl($textToDecrypt, $key) {
                $encrypted = base64_decode($textToDecrypt);
                // $cipher = 'aes-256-gcm';
                // $iv_len = openssl_cipher_iv_length($cipher);
				global $iv_len, $cipher;
                $iv = substr($encrypted, 0, $iv_len);
                $tag_length = 16;
                $tag = substr($encrypted, -$tag_length);

                $ciphertext = substr($encrypted, $iv_len, -$tag_length);
                $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag);

                return $decrypted;
}

function hashKey($key, $offset, $length) {
                
                $hash = base64_encode(hash('sha384', $key, true));
                $hashedkey = substr($hash, $offset, $length);

                return $hashedkey;        
}

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
<title>Wellness Scale: Admin User Management</title>
<link rel="apple-touch-icon" sizes="180x180" href="i/ico/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="i/ico/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="i/ico/favicon-16x16.png">
<link rel="manifest" href="i/ico/site.webmanifest">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" >
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css" />
  <script src="js/session_timeout.js"></script>

<script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
   <style>
        /* Your existing styles here */
        
        /* Sidebar Styles */
        /* ... */

        .container {
            *margin-left: 250px;
            padding: 20px;
            margin-bottom: 40px; /* Add margin to the bottom */
        }
		
		 /* Custom primary and secondary colors */
        :root {
            --primary-color: #d1397e;
            --secondary-color: #8d33b5;
        }

        /* Update navbar and button colors */
        .navbar {
            background-color: var(--primary-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
		.footer, .navbar {
            background-color: var(--primary-color);
            color: white;
        }
		
		.icon-fixed-width {
        width: 24px; /* Set the icon width to 24px */
    }
    </style>

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
                    <a class="nav-link" href="logout.php">Logout</a> <!-- Logout link -->
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="modal fade" id="addDistrictModal" tabindex="-1" role="dialog" aria-labelledby="addDistrictModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDistrictModalLabel">Add New District</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addDistrictForm">
                    <div class="form-group">
                        <label for="districtName">District Name</label>
                        <input type="text" class="form-control" id="districtName" name="districtName" required>
                    </div>
        <div class="alert alert-danger" id="modalAlert" style="display: none;"></div>
                    <button type="button" class="btn btn-primary" id="addDistrictBtn">Add District</button>
                </form>
            </div>
            <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to toggle the user's status?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelToggle">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmToggle">Confirm</button>
            </div>
        </div>
    </div>
</div>



<!-- Sidebar and Main Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
		
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
            <div class="position-sticky">
                <ul class="nav flex-column">
                    <li class="side-nav-item">
                <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="dashboard-overview" href="dashboard.php">
                    <i class="fas fa-chart-line fa-lg text-white mr-2 icon-fixed-width"></i>
                    <span> Dashboard Overview </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="user-management" href="users.php">
                    <i class="fas fa-users fa-lg text-white mr-2 icon-fixed-width"></i>
                    <span> User Management </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="data-analytics" href="/data.php">
                    <i class="fas fa-chart-bar fa-lg text-white mr-2 icon-fixed-width"></i>
                    <span> Data Analytics </span>
                </a>
            </li>
            <li class="side-nav-item">
                <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="settings" href="/settings">
                    <i class="fas fa-cog fa-lg text-white mr-2 icon-fixed-width"></i>
                    <span> Settings </span>
                </a>
            </li>
			        <li class="side-nav-item">
            <a class="nav-link-ref sub-nav-link nav-link" href="app.php">
                <i class="fas fa-download fa-lg text-white mr-2 icon-fixed-width"></i>
                <span> Download App </span>
            </a>
        </li>
                    <!-- Add more sidebar items here -->
                </ul>
            </div>
        </nav>
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
<div class="container">
  <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-5 bg-light rounded-3">
                    <h5 class="card-title">Create New User</h5>
    
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

                    <form method="post">
                        <div class="form-group">
                            <label>Username:</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>

                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" class="form-control" name="full_name">
                        </div>

                        <div class="form-group">
                            <label>Designation:</label>
                            <input type="text" class="form-control" name="designation">
                        </div>
                    
                        <?php
                        // Assuming you have a database connection established here
                        $query = "SELECT * FROM district";
                        $result = mysqli_query($con, $query);
                        ?>

                        <div class="input-group mb-3">
                            <select class="form-control" id="district" name="district" required>
                                <option value="">Select a district</option>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['districtCode'] . '">' . $row['districtName'] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="input-group-append">
								<button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#addDistrictModal">Add New District</button>

                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mt-5">
        <h2>User Management</h2>
 <table id="userTable" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>User ID</th>
			
            <th>Username</th>
            <th>Role</th>
            <th>Full Name</th>
            <th>Designation</th>
            <th>Enabled</th>
            <th>Is New User</th>
            <th>Password Expiry</th>
            <th>District ID</th>
            <th>Reset Password</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include_once 'dbconfig_mysql.php'; // Adjust the path as needed

        $query = "SELECT a.* , d.districtName
FROM `ws_app`.`appuser` a left join district
 d on a.dist_id = d.districtCode;";
        $result = mysqli_query($con, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['userID']}</td>";
            echo "<td>{$row['username']}</td>";
            echo "<td>{$row['role']}</td>";
            echo "<td>{$row['full_name']}</td>";
            echo "<td>{$row['designation']}</td>";

            echo '<td>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input enable-toggle" id="enableToggle'. $row['username'] .'" '.($row['enabled'] == '1' ? 'checked' : '').' data-userid="'. $row['username'].'">
                    <label class="custom-control-label" for="enableToggle'.$row['username'].'"></label>
                </div>
            </td>';

            echo '<td>' . ($row['isNewUser'] == '1' ? 'Yes' : 'No') . '</td>';
            echo "<td>{$row['pwdExpiry']}</td>";
            echo "<td>{$row['districtName']}</td>";
            echo '<td class="p-0 align-middle text-center">
		
                <button class="btn btn-danger reset-password" data-userid="' . $row['username'] . '"><i class="fas fa-sync-alt"></i> Reset</button>
            </td>';
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

    </div>
    <hr>
    <footer>
        <div class="row">
            <div class="col-md-6">
                <p>Copyright &copy; 2023 Wellness Scale App</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-dark">Terms of Use</a>
                <span class="text-muted mx-2">|</span>
                <a href="#" class="text-dark">Privacy Policy</a>
            </div>
        </div>
    </footer>
</div>
</main>
</div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


<script>
$(document).ready(function() {
	
	 // Toggle Enable/Disable User
$('.enable-toggle').change(function() {
    var userID = $(this).data('userid');
    var enabled = $(this).prop('checked') ? "1" : "0";

    // Show confirmation modal
    $('#confirmationModal').modal('show');

    // Store the checkbox reference and its original state
    var checkbox = $(this);
    var originalState = checkbox.prop('checked');

    $('#confirmToggle').on('click', function() {
        // Close the confirmation modal
        $('#confirmationModal').modal('hide');

        // Send AJAX request to toggle user status
        toggleUserStatus(userID, enabled);
    });

    $('#cancelToggle').on('click', function() {
        // Close the confirmation modal
        $('#confirmationModal').modal('hide');

        // Revert the checkbox state after a slight delay to ensure modal animation completion
checkbox.prop('checked', function(_, checked) {
    return !checked;
});
    });
});

// Function to toggle user status using AJAX
function toggleUserStatus(userID, enabled) {
    $.ajax({
        type: 'POST',
        url: 'toggle_user_status.php',
        data: {
            userID: userID,
            enabled: enabled
        },
        dataType: 'json',
        success: function(response) {
           // alert(JSON.stringify(response)); // Display the JSON response

            if (response.success) {
                alert(response.message);
                // Refresh the user data table
                refreshTableData();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred: ' + error + ' Response: ' + xhr.responseText);
        }
    });
}

	 $('.reset-password').click(function() {
        console.log("Reset password button clicked");

        var userID = $(this).data('userid');
        var newPassword = "LLaQP1kvskDMOQTrAXd1D9lw"; // New password value: password

        // Send AJAX request to reset user's password
        resetUserPassword(userID, newPassword);
    });

    // Function to reset user's password using AJAX
    function resetUserPassword(userID, newPassword) {
        // Send AJAX request to reset password
        $.ajax({
            type: 'POST',
            url: 'reset_password.php', // Replace with the actual URL
            data: {
                userID: userID,
                newPassword: newPassword
            },
            dataType: 'json',
            success: function(response) {
                alert(response.message); // Display the response message
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    }
	
	  $('#addDistrictBtn').click(function() {
        var districtName = $('#districtName').val();

// Client-side validation
        if (!isValidDistrictName(districtName)) {
            alert('Please enter a valid district name.');
            return;
        }
		
        $.ajax({
            type: 'POST',
            url: 'ajax/process_district.php', // Replace with the actual URL
            data: {
                districtName: districtName
            },
            dataType: 'json', // Expect JSON response
            success: function(response) {
                if (response.success) {
                    $('#modalAlert').removeClass('alert-danger').addClass('alert-success').text(response.message).show(); // Display success message within modal
					        refreshDistrictOptions(); // Call the function to refresh district options

                } else {
                    $('#modalAlert').removeClass('alert-success').addClass('alert-danger').text(response.message).show(); // Display error message within modal
                }
            },
            error: function(xhr, status, error) {
                $('#modalAlert').removeClass('alert-success').addClass('alert-danger').text('An error occurred: ' + error).show(); // Display error message within modal
            }
        });
    });
	    $('#addDistrictModal').on('hidden.bs.modal', function() {
        $('#districtCode').val('');
        $('#districtName').val('');
		});
	  
	function isValidDistrictName(name) {
        // Allow letters, numbers, and single spaces
        var regex = /^[a-zA-Z0-9\s]*$/;
        return regex.test(name);
    }
	
	function refreshDistrictOptions() {
    $.ajax({
        type: 'GET',
        url: 'ajax/get_districts.php', // Replace with the actual URL to fetch districts
        dataType: 'json', // Expect JSON response
        success: function(response) {
            var select = $('#district');
            select.empty(); // Clear existing options

            // Add new district options from the response
            $.each(response.districts, function(index, district) {
                select.append($('<option>', {
                    value: district.districtCode,
                    text: district.districtName
                }));
            });
        },
        error: function(xhr, status, error) {
            console.log('An error occurred while fetching districts: ' + error);
        }
    });
}
	
    $('#userTable').DataTable({
        searching: true // Enable search feature
    });
    // Handle form submission using AJAX
  

  function isValidFullName(fullName) {
        var regex = /^[A-Za-z\s]+$/;
        return regex.test(fullName);
    }
  function isValidUsername(username) {
        var regex = /^[a-zA-Z0-9]+$/;
        return regex.test(username);
    }
	
    $('#addUserForm').submit(function(event) {
        var fullName = $('#full_name').val();
var username = $('#username').val();

        if (username === '') {
            event.preventDefault(); // Prevent form submission
            alert('Please enter a username.');
        } else if (!isValidUsername(username)) {
            event.preventDefault(); // Prevent form submission
            alert('Username can only contain letters and numbers.');
        }
        if (!isValidFullName(fullName)) {
            event.preventDefault(); // Prevent form submission
            alert('Please enter a valid full name containing only alphabets and spaces.');
        }
   
        

    });
	

  

 // Handle password reset button click
   

});
</script>

</body>
</html>