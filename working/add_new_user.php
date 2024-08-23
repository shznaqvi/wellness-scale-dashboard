
<?php
include_once '../dbconfig_mysql.php';
include_once '../encids.php';

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
<html>
<head>
    <title>Create New User - Wellness Scale App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Your existing styles here */
        
        /* Sidebar Styles */
        /* ... */

        .container {
            margin-left: 250px;
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
    </style>
</head>
<body>



    <nav class="navbar navbar-expand-lg navbar-dark ">
    <div class="container">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
  <main class="container-fluid">
    <div class="row">
	  <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <!-- Sidebar content -->
 <div class="position-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" href="#">
                <span data-feather="home"></span>
                Dashboard Overview
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <span data-feather="user"></span>
                User Management
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <span data-feather="bar-chart-2"></span>
                Data Analytics
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <span data-feather="settings"></span>
                Settings
              </a>
            </li>
          </ul>
        </div>
		</nav>
		
<div class="container mt-5">
    <h2>Create New User</h2>
    
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
        $query = "SELECT * FROM districts";
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
                    <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#addDistrictModal">Add New District</button>
                </div>
            </div>

        <button type="submit" class="btn btn-primary">Create User</button>
    </form>    
</div>
</main>
</div>
</div>
<footer class="footer text-white text-center py-3">
    <div class="container">
        <p>&copy; 2023 Wellness Scale App. All rights reserved.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Handle form submission using AJAX
    $('#addDistrictBtn').click(function() {
        var districtName = $('#districtName').val();

// Client-side validation
        if (!isValidDistrictName(districtName)) {
            alert('Please enter a valid district name.');
            return;
        }
		
        $.ajax({
            type: 'POST',
            url: 'process_district.php', // Replace with the actual URL
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
  

   
});
</script>
</body>
</html>
