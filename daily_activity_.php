<?php
session_save_path('F:/htdocs/iisses/');
session_start();

// Check if the user is not authenticated
if (!isset($_SESSION['username'])) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}

include_once 'dbconfig_mysql.php';
include_once '../encids.php';

$key = hashKey(WS_KEY, 8, 32); // substr(KEY, 12, 44) in JAVA
//echo $key;die();

//echo "Die"; die();
// $ivlen = openssl_cipher_iv_length($cipher="aes-128-cbc");
// $iv = openssl_random_pseudo_bytes($ivlen);  

$iv_len = openssl_cipher_iv_length($cipher = "aes-256-gcm");

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


function encrypt_openssl($textToEncrypt, $key)
{
    // $cipher = 'aes-256-gcm';
    // $iv_len = openssl_cipher_iv_length($cipher);
    global $iv_len, $cipher;
    $iv = openssl_random_pseudo_bytes($iv_len);
    $tag_length = 16;
    $tag = ""; // will be filled by openssl_encrypt

    $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
    $encrypted = base64_encode($iv . $ciphertext . $tag);

    return $encrypted;
}

function decrypt_openssl($textToDecrypt, $key)
{
    $encrypted = base64_decode($textToDecrypt);
    // $cipher = 'aes-256-gcm';
    // $iv_len = openssl_cipher_iv_length($cipher);
    global $iv_len, $cipher;
    $iv = substr($encrypted, 0, $iv_len);
    $tag_length = 16;
    $tag = substr($encrypted, -$tag_length);

    $ciphertext = substr($encrypted, $iv_len, -$tag_length);
    $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv, $tag);

    return $decrypted;
}

function hashKey($key, $offset, $length)
{

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
    <title>Wellness Scale: Dashboard: Demographics</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap4.min.css">


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

        .toprow {
            color: var(--primary-color);
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
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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


    <div class="modal fade" id="addDistrictModal" tabindex="-1" role="dialog" aria-labelledby="addDistrictModalLabel"
         aria-hidden="true">
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


    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
         aria-hidden="true">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelToggle">Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmToggle">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid h-100">
        <div class="row h-100">
            <!-- Sidebar -->

            <nav data-simplebar="init" id="sidebar" style="max-height: 100%; padding-top: 24px; height: 100vh;"
                 class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="side-nav-item">
                            <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="dashboard-overview"
                               href="dashboard.php">
                                <i class="fas fa-chart-line fa-lg text-white mr-2 icon-fixed-width"></i>
                                <span> Dashboard Overview </span>
                            </a>
                            <ul class="nav flex-column pl-4">
                                <li class="side-nav-item">
                                    <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="demographics"
                                       href="demographics.php">
                                        <i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
                                        <span> Demographics </span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="lhw_performance"
                                       href="lhw_performance.php">
                                        <i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
                                        <span> LHW Performance </span>
                                    </a>
                                </li>

                                <li class="side-nav-item">
                                    <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="daily_activity"
                                       href="daily_activity.php">
                                        <i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
                                        <span> Daily Activity </span>
                                    </a>
                                </li>
                                <!-- Other sublinks -->
                            </ul>
                        </li>
                        <li class="side-nav-item">
                            <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="user-management"
                               href="users.php">
                                <i class="fas fa-users fa-lg text-white mr-2 icon-fixed-width"></i>
                                <span> User Management </span>
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="data-analytics"
                               href="/data.php">
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


<div class=" container
            ">
            <div class="row mb-4">
                <h2 class="text-center">Household Forms</h2>
                <table id="dataTable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Sysdate</th>
                        <th>District Name</th>
                        <th>Area</th>
                        <th>KNO</th>
                        <th>Status</th>
                        <th>Synced On</th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Data will be inserted here by DataTables -->
                    </tbody>
                </table>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>


<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            "ajax": {
                "url": "ajax/get_lhw_data.php",
                "dataSrc": ""
            },
            "columns": [
                {"data": "full_name"},
                {"data": "sysdate"},
                {"data": "districtName"},
                {"data": "area"},
                {"data": "kno"},
                {"data": "istatus"},
                {"data": "synced_on"},
                {
                    "data": "_uid",
                    "render": function (data, type, row, meta) {
                        return `<a href="familyDetails.php?uid=${data}" class="btn btn-primary">Details</a>`;
                    },

                }
            ]
        });
    });

</script>

</body>
</html>