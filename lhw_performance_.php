
<?php
include_once 'dbconfig_mysql.php';
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
<title>Wellness Scale: Dashboard: LHW Performance</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.7/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.11.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.7/js/dataTables.bootstrap4.min.js"></script>
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- Chart.js with Moment.js adapter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.2/dist/chartjs-adapter-moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@^3"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@^2"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@^1"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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
            *background-color: var(--primary-color);
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
		.chart-wrapper {
    width: 100%;
    height: 380px; /* Adjust height as needed */
    position: relative;
	  padding-bottom: 20px;

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



<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Sidebar -->
		
<nav data-simplebar="init" id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="side-nav-item">
                <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="dashboard-overview" href="dashboard.php">
                    <i class="fas fa-chart-line fa-lg text-white mr-2 icon-fixed-width"></i>
                    <span> Dashboard Overview </span>
                </a>
                <ul class="nav flex-column pl-4">
                    <li class="side-nav-item">
                        <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="demographics" href="demographics.php">
    <i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
                            <span> Demographics </span>
                        </a>
                    </li>
					
					 <li class="side-nav-item">
                        <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="lhw_performance" href="lhw_performance.php">
    <i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
                            <span> LHW Performance </span>
                        </a>
                    </li>
                    <!-- Other sublinks -->
                </ul>
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
<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body bg-light rounded-3">
            <h5 id="visitsLast7Days_TestDistrictKarachi" class="card-title">Last 7 Days</h5>
            <h5 id="visitsLast7Days_TestDistrictKarachi" class="card-title">Test District Karachi</h5>
			<Div class="row align-items-center pl-3">
            <p id="visitsCountLast7Days_TestDistrictKarachi" class="display-4"></p>
            <p class="display-5 mb-0">visits</p>
            <i id="7DaysChangeIndicator_TestDistrictKarachi" class="fas fa-chart-bar fa-xs px-3 " style="color: #3498db; float: right;"></i>
			</div>
              <div class="text-center col-auto">
                <div class="d-flex justify-content-center align-items-center col-auto"> <!-- Adding left and right padding -->
                    <div>
                        <p class="display-6 text-secondary mb-0 mx-3">Change:</p>
                    </div>
                    <div>
                        <H5 id="changeStatus7Days_TestDistrictKarachi" class=" mb-0 ">4</H5>
						     <p class="display-5  mb-0 text-muted mx-1"> Visits </p>

                    </div>
                    <div>
                        <p class="display-5  mb-0 text-muted mx-1 display-4"> | </p>
                    </div>
                    <div>
                        <H5 id="changePercentage7Days_TestDistrictKarachi" class=" mb-0 ">20%</h5>
						                        <p class="display-5  mb-0 text-muted m1 "> Percentage </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body bg-light rounded-3">
            <h5 id="visitsLast28Days_TestDistrictKarachi" class="card-title">Last 28 Days</h5>
            <h5 id="visitsLast28Days_TestDistrictKarachi" class="card-title">Test District Karachi</h5>
			<Div class="row align-items-center pl-3">
            <p id="visitsCountLast28Days_TestDistrictKarachi" class="display-4"></p>
            <p class="display-5 mb-0">visits</p>
            <i id="28DaysChangeIndicator_TestDistrictKarachi" class="fas fa-chart-bar fa-xs px-3 " style="color: #3498db; float: right;"></i>
			</div>
              <div class="text-center col-auto">
                <div class="d-flex justify-content-center align-items-center col-auto"> <!-- Adding left and right padding -->
                    <div>
                        <p class="display-6 text-secondary mb-0 mx-3">Change:</p>
                    </div>
                    <div>
                        <H5 id="changeStatus28Days_TestDistrictKarachi" class=" mb-0 ">4</H5>
						     <p class="display-5  mb-0 text-muted mx-1"> Visits </p>

                    </div>
                    <div>
                        <p class="display-5  mb-0 text-muted mx-1 display-4"> | </p>
                    </div>
                    <div>
                        <H5 id="changePercentage28Days_TestDistrictKarachi" class=" mb-0 ">20%</h5>
						                        <p class="display-5  mb-0 text-muted m1 "> Percentage </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body bg-light rounded-3">
            <h5 id="visitsLast7Days_TestDistrictHyderabad" class="card-title">Last 7 Days</h5>
            <h5 id="visitsLast7Days_TestDistrictHyderabad" class="card-title">Test District Hyderabad</h5>
			<Div class="row align-items-center pl-3">
            <p id="visitsCountLast7Days_TestDistrictHyderabad" class="display-4"></p>
            <p class="display-5 mb-0">visits</p>
            <i id="7DaysChangeIndicator_TestDistrictHyderabad" class="fas fa-chart-bar fa-xs px-3 " style="color: #3498db; float: right;"></i>
			</div>
              <div class="text-center col-auto">
                <div class="d-flex justify-content-center align-items-center col-auto"> <!-- Adding left and right padding -->
                    <div>
                        <p class="display-6 text-secondary mb-0 mx-3">Change:</p>
                    </div>
                    <div>
                        <H5 id="changeStatus7Days_TestDistrictHyderabad" class=" mb-0 ">4</H5>
						     <p class="display-5  mb-0 text-muted mx-1"> Visits </p>

                    </div>
                    <div>
                        <p class="display-5  mb-0 text-muted mx-1 display-4"> | </p>
                    </div>
                    <div>
                        <H5 id="changePercentage7Days_TestDistrictHyderabad" class=" mb-0 ">20%</h5>
						                        <p class="display-5  mb-0 text-muted m1 "> Percentage </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card shadow-sm">
        <div class="card-body bg-light rounded-3">
            <h5 id="visitsLast28Days_TestDistrictHyderabad" class="card-title">Last 28 Days</h5>
            <h5 id="visitsLast28Days_TestDistrictHyderabad" class="card-title">Test District Hyderabad</h5>
			<Div class="row align-items-center pl-3">
            <p id="visitsCountLast28Days_TestDistrictHyderabad" class="display-4"></p>
            <p class="display-5 mb-0">visits</p>
            <i id="28DaysChangeIndicator_TestDistrictHyderabad" class="fas fa-chart-bar fa-xs px-3 " style="color: #3498db; float: right;"></i>
			</div>
              <div class="text-center col-auto">
                <div class="d-flex justify-content-center align-items-center col-auto"> <!-- Adding left and right padding -->
                    <div>
                        <p class="display-6 text-secondary mb-0 mx-3">Change:</p>
                    </div>
                    <div>
                        <H5 id="changeStatus28Days_TestDistrictHyderabad" class=" mb-0 ">4</H5>
						     <p class="display-5  mb-0 text-muted mx-1"> Visits </p>

                    </div>
                    <div>
                        <p class="display-5  mb-0 text-muted mx-1 display-4"> | </p>
                    </div>
                    <div>
                        <H5 id="changePercentage28Days_TestDistrictHyderabad" class=" mb-0 ">20%</h5>
						                        <p class="display-5  mb-0 text-muted m1 "> Percentage </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
		</div>
	



    <div class="row">
        <!-- Graph Placeholder 1 -->
<!-- LHW Visits Distribution Card -->
<div class="col-md-6">
    <div class="card shadow-sm">
        <div class="card-body bg-light rounded-3 chart-container">
            <i class="fas fa-user-check fa-2x" style="color: #3f51b5; float: right;"></i>
            <h5 class="card-title">LHW Visits</h5>
            <h5 id="totalVisitsCardTitle" class="card-title"></h5>
            <p id="totalVisitsValue" class="display-3"></p>
            <div class="chart-wrapper">
                <canvas id="visitsBarChart"></canvas>
            </div>
        </div>
    </div>
</div>
    <!-- Age Groups Card -->
      <div class="col-md-6">
        <div class="card shadow-sm ">
            <div class="card-body bg-light rounded-3 chart-container">
                <!-- Favicon -->
                <i class="fas fa-home fa-2x" style="color: #9834db; float: right;"></i>
				            <h5 class="card-title">Age Groups</h5>

                <h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
                <p id="totalHouseholdsValue" class="display-3"></p>
                <!-- Chart Placeholder -->
				                <div class="chart-wrapper">

                <canvas id="ageGenderChart"></canvas>
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
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-dark">Terms of Use</a>
                <span class="text-muted mx-2">|</span>
                <a href="#" class="text-dark">Privacy Policy</a>
            </div>
        </div>
		
		</main>
</div>

</div>
    </footer>
	</div>
	

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
$(document).ready(function() {
	
	   // Function to format large numbers with commas
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

$.ajax({
    type: 'GET',
    url: 'ajax/get_lhw_visits_districts.php',
    dataType: 'json',
    success: function(response) {
        response.forEach(function(districtData) {
            if (districtData.districtName === 'Test District Karachi') {
                // Code for Last 7 Days
                $('#visitsCountLast7Days_TestDistrictKarachi').text(districtData.Visits_Count_Last_7_Days);
                var diff7Days = districtData.Visits_Count_Last_7_Days - districtData.Visits_Count_Previous_7_Days;
                $('#changeStatus7Days_TestDistrictKarachi').text(diff7Days);

                var iconElement7Days = $('#7DaysChangeIndicator_TestDistrictKarachi');
                if (diff7Days > 0) {
                    iconElement7Days.removeClass().addClass('fas fa-arrow-up px-3').css('color', '#00FF00');
                    $('#changeStatus7Days_TestDistrictKarachi').css('color', '#00ef00');
                    $('#changePercentage7Days_TestDistrictKarachi').css('color', '#00ef00');
                } else if (diff7Days < 0) {
                    iconElement7Days.removeClass().addClass('fas fa-arrow-down px-3').css('color', '#FF0000');
                    $('#changeStatus7Days_TestDistrictKarachi').css('color', '#ef0000');
                    $('#changePercentage7Days_TestDistrictKarachi').css('color', '#ef0000');
                } else {
                    iconElement7Days.removeClass().addClass('fas fa-equals px-3').css('color', '#000000');
                }

                var previousCount7Days = districtData.Visits_Count_Previous_7_Days;
                var percentageChange7Days = previousCount7Days !== 0 ? ((diff7Days / previousCount7Days) * 100).toFixed(2) : 0;
                $('#changePercentage7Days_TestDistrictKarachi').text(percentageChange7Days !== 'Infinity' ? percentageChange7Days + '%' : '0%');

                // Code for Last 28 Days
                $('#visitsCountLast28Days_TestDistrictKarachi').text(districtData.Visits_Count_Last_28_Days);
                var diff28Days = districtData.Visits_Count_Last_28_Days - districtData.Visits_Count_Previous_28_Days;
                $('#changeStatus28Days_TestDistrictKarachi').text(diff28Days);

                var iconElement28Days = $('#28DaysChangeIndicator_TestDistrictKarachi');
                if (diff28Days > 0) {
                    iconElement28Days.removeClass().addClass('fas fa-arrow-up px-3').css('color', '#00FF00');
                    $('#changeStatus28Days_TestDistrictKarachi').css('color', '#00ef00');
                    $('#changePercentage28Days_TestDistrictKarachi').css('color', '#00ef00');
                } else if (diff28Days < 0) {
                    iconElement28Days.removeClass().addClass('fas fa-arrow-down px-3').css('color', '#FF0000');
                    $('#changeStatus28Days_TestDistrictKarachi').css('color', '#ef0000');
                    $('#changePercentage28Days_TestDistrictKarachi').css('color', '#ef0000');
                } else {
                    iconElement28Days.removeClass().addClass('fas fa-equals px-3').css('color', '#000000');
                }

                var previousCount28Days = districtData.Visits_Count_Previous_28_Days;
                var percentageChange28Days = previousCount28Days !== 0 ? ((diff28Days / previousCount28Days) * 100).toFixed(2) : 0;
                $('#changePercentage28Days_TestDistrictKarachi').text(percentageChange28Days !== 'Infinity' ? percentageChange28Days + '%' : '0%');
            }
			
			   if (districtData.districtName === 'Test District Hyderabad') {
                // Code for Last 7 Days
                $('#visitsCountLast7Days_TestDistrictHyderabad').text(districtData.Visits_Count_Last_7_Days);
                var diff7Days = districtData.Visits_Count_Last_7_Days - districtData.Visits_Count_Previous_7_Days;
                $('#changeStatus7Days_TestDistrictHyderabad').text(diff7Days);

                var iconElement7Days = $('#7DaysChangeIndicator_TestDistrictHyderabad');
                if (diff7Days > 0) {
                    iconElement7Days.removeClass().addClass('fas fa-arrow-up px-3').css('color', '#00FF00');
                    $('#changeStatus7Days_TestDistrictHyderabad').css('color', '#00ef00');
                    $('#changePercentage7Days_TestDistrictHyderabad').css('color', '#00ef00');
                } else if (diff7Days < 0) {
                    iconElement7Days.removeClass().addClass('fas fa-arrow-down px-3').css('color', '#FF0000');
                    $('#changeStatus7Days_TestDistrictHyderabad').css('color', '#ef0000');
                    $('#changePercentage7Days_TestDistrictHyderabad').css('color', '#ef0000');
                } else {
                    iconElement7Days.removeClass().addClass('fas fa-equals px-3').css('color', '#000000');
                }

                var previousCount7Days = districtData.Visits_Count_Previous_7_Days;
                var percentageChange7Days = previousCount7Days !== 0 ? ((diff7Days / previousCount7Days) * 100).toFixed(2) : 0;
                $('#changePercentage7Days_TestDistrictHyderabad').text(percentageChange7Days !== 'Infinity' ? percentageChange7Days + '%' : '0%');

                // Code for Last 28 Days
                $('#visitsCountLast28Days_TestDistrictHyderabad').text(districtData.Visits_Count_Last_28_Days);
                var diff28Days = districtData.Visits_Count_Last_28_Days - districtData.Visits_Count_Previous_28_Days;
                $('#changeStatus28Days_TestDistrictHyderabad').text(diff28Days);

                var iconElement28Days = $('#28DaysChangeIndicator_TestDistrictHyderabad');
                if (diff28Days > 0) {
                    iconElement28Days.removeClass().addClass('fas fa-arrow-up px-3').css('color', '#00FF00');
                    $('#changeStatus28Days_TestDistrictHyderabad').css('color', '#00ef00');
                    $('#changePercentage28Days_TestDistrictHyderabad').css('color', '#00ef00');
                } else if (diff28Days < 0) {
                    iconElement28Days.removeClass().addClass('fas fa-arrow-down px-3').css('color', '#FF0000');
                    $('#changeStatus28Days_TestDistrictHyderabad').css('color', '#ef0000');
                    $('#changePercentage28Days_TestDistrictHyderabad').css('color', '#ef0000');
                } else {
                    iconElement28Days.removeClass().addClass('fas fa-equals px-3').css('color', '#000000');
                }

                var previousCount28Days = districtData.Visits_Count_Previous_28_Days;
                var percentageChange28Days = previousCount28Days !== 0 ? ((diff28Days / previousCount28Days) * 100).toFixed(2) : 0;
                $('#changePercentage28Days_TestDistrictHyderabad').text(percentageChange28Days !== 'Infinity' ? percentageChange28Days + '%' : '0%');
            }
        });
    },
    error: function(xhr, status, error) {
        console.error('Error fetching data: ' + error);
    }
});


	
// Initialize the bar chart variable for LHW visits
var visitsBarChart = new Chart(document.getElementById('visitsBarChart'), {
    type: 'bar',
    data: {
        labels: [],
        datasets: [{
            label: 'LHW Visits',
            data: [],
            backgroundColor: 'rgba(63, 81, 181, 0.5)', // Blue color, adjust as needed
            borderColor: 'rgba(63, 81, 181, 1)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false // Hide the legend key
            }
        }
    }
});

// Make an AJAX call to fetch LHW visit data
$.ajax({
    type: 'GET',
    url: 'ajax/get_lhw_visits.php',
    dataType: 'json',
    success: function(response) {
        var lhwNames = response.map(item => item.LHW || 'Unknown LHW');
        var visitCounts = response.map(item => parseInt(item.Visits_Count));
    
        var backgroundColors = generateRandomColors(visitCounts.length); // Function to generate random colors

        // Update the chart with the new data and random colors
        visitsBarChart.data.labels = lhwNames;
        visitsBarChart.data.datasets[0].data = visitCounts;
        visitsBarChart.data.datasets[0].backgroundColor = backgroundColors;

        visitsBarChart.update();
    },
    error: function(xhr, status, error) {
        console.error('Error fetching LHW visit data: ' + error);
    }
});



// Function to get background colors based on index
function getBackgroundColor(index) {
    // Replace this with your color selection logic based on the index
    const colors = ['#e74c3c', '#3498db', '#9b59b6', '#2ecc71', '#f1c40f'];
    return colors[index % colors.length];
}
// Function to generate random colors
function generateRandomColors(count) {
    var colors = [];
    for (var i = 0; i < count; i++) {
        var randomColor = 'rgba(' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ', 0.5)';
        colors.push(randomColor);
    }
    return colors;
}

});

function createBoxes(districtName, period, currentVisits, previousVisits, container) {
    var boxHtml = `
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body bg-light rounded-3">
                    <i class="fas fa-chart-bar fa-2x" style="color: #3498db; float: right;"></i>
                    <h5 class="card-title">${period}</h5>
                    <p class="display-3" id="visitsCount${districtName}${period.replace(/\s+/g, '')}">${currentVisits}</p>
                    <p class="changeStatus" id="changeStatus${districtName}${period.replace(/\s+/g, '')}">Change: ${currentVisits - previousVisits}</p>
                </div>
            </div>
        </div>
    `;

    container.append(boxHtml);

    // Update icon based on increase/decrease
    var changeStatus = currentVisits - previousVisits;
    var icon = container.find('i');
    if (changeStatus >= 0) {
        icon.addClass('fa-arrow-up');
    } else {
        icon.addClass('fa-arrow-down');
    }
}
</script>

</body>
</html>