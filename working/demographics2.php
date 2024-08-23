<?php
session_save_path('F:/htdocs/iisses/');
session_start();

// Check if the user is not authenticated
if (!isset($_SESSION['username'])) {
    // Redirect the user to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}

include_once '../dbconfig_mysql.php';
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
                <!-- Participant Count Card -->
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Participant Count</h5>
                            <p class="h4 toprow" id="participantCount">-</p>
                        </div>
                    </div>
                </div>
                <!-- Average Age Card -->
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">Average Age</h5>
                            <p class="h4 toprow" id="averageAge">-</p>
                        </div>
                    </div>
                </div>
                <!-- Gender Ratio Card -->
                <div class="col-md-3">
                    <div class="card shadow-sm ">
                        <div class="card-body text-center">
                            <h5 class="card-title">Gender Ratio</h5>
                            <p class="h4 toprow" id="genderRatio">-</p>
                        </div>
                    </div>
                </div>
                <!-- High Risk Count Card -->
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title">High Risk Count</h5>
                            <p class="h4 toprow" id="highRiskCount">-</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Graph Placeholder 1 -->
                <!-- Gender Distribution Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body bg-light rounded-3 chart-container">
                            <!-- Favicon -->
                            <i class="fas fa-venus-mars fa-4x" style="color: #3498db; float: right;"></i>
                            <h5 class="card-title">Gender Distribution</h5>

                            <h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
                            <p id="totalHouseholdsValue" class="display-3"></p>
                            <!-- Chart Placeholder -->
                            <div class="chart-wrapper">

                                <canvas id="genderDonutChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Age Groups Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm ">
                        <div class="card-body bg-light rounded-3 chart-container">
                            <!-- Favicon -->
                            <i class="fas fa-home fa-4x" style="color: #9834db; float: right;"></i>
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


            <div class="row mt-4">
                <!-- Graph Placeholder 1 -->
                <!-- Gender Distribution Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body bg-light rounded-3 chart-container">
                            <i class="fas fa-graduation-cap fa-4x" style="color: #db3498db; float: right;"></i>
                            <h5 class="card-title">Education Status</h5>
                            <h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
                            <p id="totalHouseholdsValue" class="display-3"></p>
                            <div class="chart-wrapper">
                                <canvas id="educationBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Age Groups Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm ">
                        <div class="card-body bg-light rounded-3 chart-container">
                            <!-- Favicon -->
                            <i class="fas fa-suitcase  fa-4x" style="color: #f9e94f; float: right;"></i>
                            <h5 class="card-title">Employment Status</h5>

                            <h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
                            <p id="totalHouseholdsValue" class="display-3"></p>
                            <!-- Chart Placeholder -->
                            <div class="chart-wrapper">

                                <canvas id="employmentPieChart"></canvas>
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
    $(document).ready(function () {

        // Fetching gender data
// Gender Distribution Donut Chart
        var genderDonutChart = new Chart(document.getElementById('genderDonutChart'), {
            type: 'doughnut',
            data: {
                label: 'Gender Distribution',
                datasets: [{
                    label: [],
                    data: [],
                    backgroundColor: ['#e74c3c', '#3498db', '#9b59b6'],
                    borderColor: 'rgba(255, 255, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {

                maintainAspectRatio: false,
                responsive: true
            }
        });

// Make an AJAX call to fetch gender distribution data
        $.ajax({
            type: 'GET',
            url: 'ajax/get_gender_count.php', // Replace with the actual URL
            dataType: 'json',
            success: function (response) {
                var genders = response.map(item => item.gender);
                console.log("Gender: " + genders);
                var counts = response.map(item => item.count);
                // var backgroundColors = generateRandomColors(genders.length); // Function to generate random colors

                // Update the chart with the new data
                genderDonutChart.data.labels = genders;
                genderDonutChart.data.datasets[0].data = counts;
                // genderDonutChart.data.datasets[0].backgroundColor = backgroundColors;

                genderDonutChart.update();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching gender distribution data: ' + error);
            }
        });


// Stacked Bar Chart for Age and Gender
        var ageGenderChart = new Chart(document.getElementById('ageGenderChart'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Female',
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    stack: 'Stack 0',
                    data: []
                },
                    {
                        label: 'Male',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        stack: 'Stack 0',
                        data: []
                    },
                    {
                        label: 'Other',
                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                        stack: 'Stack 0',
                        data: []
                    }]
            },
            options: {
                maintainAspectRatio: false,

                responsive: true,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });

// Make an AJAX call to fetch age and gender stack data
        $.ajax({
            type: 'GET',
            url: 'ajax/get_age_gender_stacks.php',
            dataType: 'json',
            success: function (response) {
                var ageGroups = Object.keys(response);
                var femaleCounts = [];
                var maleCounts = [];
                var otherCounts = [];

                ageGroups.forEach(function (ageGroup) {
                    var data = response[ageGroup];
                    femaleCounts.push(data.Female);
                    maleCounts.push(data.Male);
                    otherCounts.push(data.Other);
                });

                // Update the chart with the new data
                ageGenderChart.data.labels = ageGroups;
                ageGenderChart.data.datasets[0].data = femaleCounts;
                ageGenderChart.data.datasets[1].data = maleCounts;
                ageGenderChart.data.datasets[2].data = otherCounts;

                ageGenderChart.update();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching age and gender stack data: ' + error);
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
                var randomColor = 'rgba(' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ',' + Math.floor(Math.random() * 256) + ', 0.2)';
                colors.push(randomColor);
            }
            return colors;
        }

    });

    // Initialize the horizontal bar chart variable
    var educationBarChart = new Chart(document.getElementById('educationBarChart'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: [],
                data: [],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)', // Red
                    'rgba(54, 162, 235, 0.5)', // Blue
                    'rgba(255, 206, 86, 0.5)', // Yellow
                    'rgba(75, 192, 192, 0.5)', // Green
                    'rgba(153, 102, 255, 0.5)', // Purple
                    'rgba(255, 159, 64, 0.5)' // Orange
                ],
                borderColor: 'rgba(54, 162, 235, 1)',
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

    // Make an AJAX call to fetch education status data
    $.ajax({
        type: 'GET',
        url: 'ajax/get_education_status.php', // Replace with the actual URL
        dataType: 'json',
        success: function (response) {
            var educationLabels = response.map(item => item.education_status);
            var counts = response.map(item => item.count);

            // Update the chart with the new data
            educationBarChart.data.labels = educationLabels;
            educationBarChart.data.datasets[0].data = counts;

            educationBarChart.update();
        },
        error: function (xhr, status, error) {
            console.error('Error fetching education status data: ' + error);
        }
    });

    // Initialize the pie chart variable
    var employmentPieChart = new Chart(document.getElementById('employmentPieChart'), {
        type: 'pie',
        data: {
            labels: ['Employed', 'Not Employed'],
            datasets: [{
                data: [], // Data will be updated dynamically
                backgroundColor: ['#2ecc71', '#e74c3c'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Make an AJAX call to fetch employment status data
    $.ajax({
        type: 'GET',
        url: 'ajax/get_employment_status.php', // Replace with the actual URL
        dataType: 'json',
        success: function (response) {
            var employedCount = 0;
            var notEmployedCount = 0;

            // Loop through the response data to count employed and not employed
            response.forEach(function (item) {
                if (item.employment_status === 'Yes') {
                    employedCount += 1;
                } else {
                    notEmployedCount += 1;
                }
            });

            // Update the pie chart data with the counts
            employmentPieChart.data.datasets[0].data = [employedCount, notEmployedCount];
            employmentPieChart.update();
        },
        error: function (xhr, status, error) {
            console.error('Error fetching employment status data: ' + error);
        }
    });

    $.ajax({
        url: 'ajax/get_participant_count.php', // Path to your PHP script
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Update card content with fetched data
            $('#participantCount').text(data.total_participants);
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
            // Handle error gracefully (e.g., display an error message)
        }
    });
    $.ajax({
        url: 'ajax/get_average_age.php', // Path to your PHP script
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Update card content with fetched data
            $('#averageAge').text(data.average_age + ' years');
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
            // Handle error gracefully (e.g., display an error message)
        }
    });
    $.ajax({
        url: 'ajax/get_gender_ratio.php', // Path to your PHP script
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Update card content with fetched data
            $('#maleCount').text(data.male_count);
            $('#femaleCount').text(data.female_count);
            var totalCount = parseInt(data.male_count) + parseInt(data.female_count);
            var male = Math.round(((data.male_count / totalCount) * 100).toFixed(2));
            var female = Math.round(((data.female_count / totalCount) * 100).toFixed(2));
            $('#genderRatio').text(male + '% m - ' + female + '% f');

        },
        error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
            // Handle error gracefully (e.g., display an error message)
        }
    });
    // Fetch High Risk Count
    $.ajax({
        url: 'ajax/get_high_risk_count.php', // Path to your PHP script for high risk count
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            // Update high risk count card content
            $('#highRiskCount').text(data.high_risk_count);
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
        }
    });

</script>

</body>
</html>