
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
<title>Bootstrap Responsive Layout Example</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.7/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.7/js/dataTables.bootstrap4.min.js"></script>

   <style>
        /* Your existing styles here */
        
        /* Sidebar Styles */
        /* ... */

        .container {
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
<div id="wrapper" style="display: flex; flex-direction: column; min-height: 100vh;">

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



<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
		
        <nav data-simplebar="init" id="sidebar" style="max-height: 100%; padding-top: 24px" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
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
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-top: 20px;>


<div class="container">
    <div class="row">
        <!-- Graph Placeholder 1 -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body bg-light rounded-3">
                    <!-- Favicon -->
                    <i class="fas fa-chart-bar fa-2x" style="color: #3498db; float: right;"></i>
                    <h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
                    <p id="totalHouseholdsValue" class="display-3"></p>
                </div>
            </div>
        </div>

        <!-- Graph Placeholder 2 -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body bg-light rounded-3">
                    <!-- Favicon -->
                    <i class="fas fa-users fa-2x" style="color: #e74c3c; float: right;"></i>
                    <h5 id="totalFamilyMembersCardTitle" class="card-title"></h5>
                    <p id="totalFamilyMembersValue" class="display-3"></p>
                </div>
            </div>
        </div>

        <!-- Graph Placeholder 3 -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body bg-light rounded-3">
                    <!-- Favicon -->
                    <i class="fas fa-user fa-2x" style="color: #2ecc71; float: right;"></i>
                    <h5 class="card-title" id="totalActiveLHWSCardTitle"></h5>
                    <p id="totalActiveLHWSValue" class="display-3"></p>
                </div>
            </div>
        </div>

        <!-- Graph Placeholder 4 -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body bg-light rounded-3">
                    <!-- Favicon -->
                    <i class="fas fa-check fa-2x" style="color: #f39c12; float: right;"></i>
                    <h5 class="card-title" id="completionRateCardTitle"></h5>
                    <p id="completionRateValue" class="display-3"></p>
                </div>
            </div>
        </div>
    </div>


        <!-- Other content of your dashboard goes here -->
  <div class="row justify-content-center">

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-5 bg-light rounded-3">
                    <h5 class="card-title">Create New User</h5>
    
                  
                </div>
            </div>
        </div>
		<div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-5 bg-light rounded-3">
                    <h5 class="card-title">Create New User</h5>
    
                   
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
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-dark">Terms of Use</a>
                <span class="text-muted mx-2">|</span>
                <a href="#" class="text-dark">Privacy Policy</a>
            </div>
        </div></main>
</div>

</div>
    </footer>
	</div>
	

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script>
$(document).ready(function() {
    // Function to format large numbers with commas
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Make an AJAX call to fetch the total households
    $.ajax({
        type: 'GET',
        url: 'ajax/get_total_household.php', // Replace with the actual URL
        dataType: 'json',
        success: function(response) {
            // Handle the response data
            var totalHouseholds = response.totalHouseholds;

            // Display or use the totalHouseholds value as needed
            console.log('Total Households: ' + totalHouseholds);

            // Format the number with commas and update the card title
            $('#totalHouseholdsCardTitle').text('Total Households');
            $('#totalHouseholdsValue').text(formatNumber(totalHouseholds));
        },
        error: function(xhr, status, error) {
            // Handle the error if the AJAX call fails
            console.error('Error fetching total households: ' + error);
        }
    });
	
	 // Make an AJAX call to fetch the total family members interviewed
    $.ajax({
        type: 'GET',
        url: 'ajax/get_total_familymembers.php', // Replace with the actual URL
        dataType: 'json',
        success: function(response) {
            // Handle the response data
            var totalFamilyMembers = response.totalFamilyMembers;

            // Display or use the totalFamilyMembers value as needed
            console.log('Total Family Members: ' + totalFamilyMembers);

            // Format the number with commas and update the card title and value
            $('#totalFamilyMembersCardTitle').text('Total Family Members');
            $('#totalFamilyMembersValue').text(formatNumber(totalFamilyMembers));
        },
        error: function(xhr, status, error) {
            // Handle the error if the AJAX call fails
            console.error('Error fetching total family members: ' + error);
        }
    });
	

	
// Make an AJAX call to fetch the total active LHWS
$.ajax({
    type: 'GET',
    url: 'ajax/get_total_activelhws.php', // Replace with the actual URL
    dataType: 'json',
    success: function(response) {
        // Handle the response data
        var totalActiveLHWS = response.totalActiveLHWS;

        // Display or use the totalActiveLHWS value as needed
        console.log('Total Active LHWS: ' + totalActiveLHWS);

        // Format the number with commas and update the card title and value
        $('#totalActiveLHWSCardTitle').text('Total Active LHWS');
        $('#totalActiveLHWSValue').text(formatNumber(totalActiveLHWS));
    },
    error: function(xhr, status, error) {
        // Handle the error if the AJAX call fails
        console.error('Error fetching total active LHWS: ' + error);
    }
});

// Make an AJAX call to fetch the completion rate of surveys
$.ajax({
    type: 'GET',
    url: 'ajax/get_completion_rate.php', // Replace with the actual URL
    dataType: 'json',
    success: function(response) {
        // Handle the response data
        var completionRate = response.completionRate;

        // Display or use the completionRate value as needed
        console.log('Completion Rate of Surveys: ' + completionRate + '%');

        // Format the number with commas and update the card title and value
        $('#completionRateCardTitle').text('Completion Rate');
        $('#completionRateValue').text(completionRate + '%');
    },
    error: function(xhr, status, error) {
        // Handle the error if the AJAX call fails
        console.error('Error fetching completion rate: ' + error);
    }
});
});
</script>

</body>
</html>