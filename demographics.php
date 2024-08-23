<?php
include_once 'config.php';
include_once 'inc/index.php'
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wellness Scale Monitoring & Evaluation Dashboard</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="i/ico/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="i/ico/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="i/ico/favicon-16x16.png">
    <link rel="manifest" href="i/ico/site.webmanifest">
    <!-- CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.7/css/dataTables.bootstrap5.min.css">
	    <link rel="stylesheet" href="css/app.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://d3js.org/d3.v7.min.js"></script>

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
        width: 18px; /* Set the icon width to 24px */
		}
.chart-wrapper {
    width: 100%;
    height: 380px; /* Adjust height as needed */
    position: relative;
	  padding-bottom: 20px;


#lineChart {
    width: 100%;
    height: 100%;
}

h4.todayText {
    color: #666; /* Gray color */
    font-size: 14px; /* Adjust font size as needed */
    /* Add any additional styling properties here */
}


h4.toprow{
	color: var(--primary-color) !important;
}
    </style>
</head>

<body>
	<div id="wrapper" style="display: flex; flex-direction: column; min-height: 100vh;">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Wellness Scale App</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="#">Home <span class="visually-hidden">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a> <!-- Logout link -->
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Modals -->
        <!-- Add district modal -->
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


        <!-- Confirmation modal -->
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
				<nav data-simplebar="init" id="sidebar" style="max-height: 100%; padding-top: 24px;" class="col-md-2  d-md-block bg-dark sidebar">
					<div class="position-sticky">
						<ul class="nav flex-column">
							<li class="side-nav-item">
								<a class="sub-nav-link-ref sub-nav-link nav-link" data-menu-key="dashboard-overview" href="dashboard.php">
									<i class="fas fa-chart-line fa-lg text-white mr-2 icon-fixed-width"></i>
									<span>Dashboard Overview</span>
								</a>
								<ul class="sub-nav flex-column pl-4">
									<li class="side-nav-item">
										<a class="sub-nav-link-ref sub-nav-link nav-link" data-menu-key="demographics" href="demographics.php">
											<i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
											<span>Demographics</span>
										</a>
									</li>
						
					
					 <li class="side-nav-item">
                        <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="lhw_performance" href="lhw_performance.php">
    <i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
                            <span> LHW Performance </span>
                        </a>
                    </li>
					
									 <li class="side-nav-item">
                        <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="daily_activity" href="daily_activity.php">
    <i class="fas fa-chart-pie fa-sm text-gray-400 mr-2 ml-4"></i>
                            <span> Daily Activity </span>
                        </a>
                    </li>
									<!-- Other sublinks -->
								</ul>
							</li>
							<li class="side-nav-item">
								<a class="nav-link-ref sub-nav-link nav-link" data-menu-key="user-management" href="users.php">
									<i class="fas fa-users fa-lg text-white mr-2 icon-fixed-width"></i>
									<span>User Management</span>
								</a>
							</li>
							<li class="side-nav-item">
								<a class="nav-link-ref sub-nav-link nav-link" data-menu-key="data-analytics" href="/data.php">
									<i class="fas fa-chart-bar fa-lg text-white mr-2 icon-fixed-width"></i>
									<span>Data Analytics</span>
								</a>
							</li>
							<li class="side-nav-item">
								<a class="nav-link-ref sub-nav-link nav-link" data-menu-key="settings" href="/settings">
									<i class="fas fa-cog fa-lg text-white mr-2 icon-fixed-width"></i>
									<span>Settings</span>
								</a>
							</li>
							<li class="side-nav-item">
								<a class="nav-link-ref sub-nav-link nav-link" href="app.php">
									<i class="fas fa-download fa-lg text-white mr-2 icon-fixed-width"></i>
									<span>Download App</span>
								</a>
							</li>
							<!-- Add more sidebar items here -->
						</ul>
					</div>
				</nav>

				<!-- Main Content -->
				   <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-top: 20px;">
					<h3> Demographics </h3>
				<hr>

<div class="container-fluid">
           <div class="row mb-4">
            <!-- Participant Count Card -->
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Participant Counts</h5>
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
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body bg-light rounded-3 chart-container">
                <!-- Favicon -->
<i class="fas fa-venus-mars fa-2x" style="color: #3498db; float: right;"></i>
				            <h5 class="card-title">Gender Distribution</h5>

                <h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
                <p id="totalHouseholdsValue" class="display-3"></p>
                <!-- Chart Placeholder -->
				                <div class="chart-wrapper">

                <canvas id="genderDonutChart" ></canvas>
				</div>
            </div>
        </div>
    </div>
    <!-- Age Groups Card -->
      <div class="col-md-4">
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
	<!-- Risk Strata by Age Groups Card -->
      <div class="col-md-4">
        <div class="card shadow-sm ">
            <div class="card-body bg-light rounded-3 chart-container">
                <!-- Favicon -->
            <i class="fas fa-suitcase  fa-2x" style="color: #f9e94f; float: right;"></i>
				            <h5 class="card-title">Risk Stratification by Age Group</h5>

                <h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
                <p id="totalHouseholdsValue" class="display-3"></p>
                <!-- Chart Placeholder -->
				                <div class="chart-wrapper">

                <canvas id="riskStratificationChart"></canvas>
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
            <i class="fas fa-graduation-cap fa-2x" style="color: #db3498db; float: right;"></i>
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
            <i class="fas fa-suitcase  fa-2x" style="color: #f9e94f; float: right;"></i>
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
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>


<script>
$(document).ready(function() {
	
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
    success: function(response) {
        var genders = response.map(item => item.gender);
		console.log("Gender: "+ genders);
        var counts = response.map(item => item.count);
       // var backgroundColors = generateRandomColors(genders.length); // Function to generate random colors

        // Update the chart with the new data
        genderDonutChart.data.labels = genders;
        genderDonutChart.data.datasets[0].data = counts;
       // genderDonutChart.data.datasets[0].backgroundColor = backgroundColors;

        genderDonutChart.update();
    },
    error: function(xhr, status, error) {
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
    success: function(response) {
        var ageGroups = Object.keys(response);
        var femaleCounts = [];
        var maleCounts = [];
        var otherCounts = [];

        ageGroups.forEach(function(ageGroup) {
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
    error: function(xhr, status, error) {
        console.error('Error fetching age and gender stack data: ' + error);
    }
});

// Function to fetch data and update chart
      
            $.ajax({
                url: 'ajax/get_risk_agegroups.php', // Replace with your PHP script path
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Prepare data for chart
					
                    var ageGroups = ['18-24 years old', '25-34 years old','35-54 years old', '55-64 years old', '65 and above'];
                    var riskOutcomes = ['LOW RISK', 'MEDIUM RISK', 'HIGH RISK'];
                    var chartData = {
                        labels: ageGroups,
                        datasets: riskOutcomes.map((risk, index) => ({
                            label: risk,
                            data: ageGroups.map(ageGroup => {
                                var found = data.find(d => d.age_group === ageGroup && d.riskOutcome === risk);
                                return found ? found.risk_count : 0;
                            }),
                            backgroundColor: ['rgba(63, 81, 181, 0.5)', 'rgba(255, 193, 7, 0.5)', 'rgba(244, 67, 54, 0.5)'][index],
                            borderColor: ['rgba(63, 81, 181, 1)', 'rgba(255, 193, 7, 1)', 'rgba(244, 67, 54, 1)'][index],
                            borderWidth: 1
                        }))
                    };

                    // Get chart context and create Chart.js instance
                    var ctx = document.getElementById('riskStratificationChart').getContext('2d');
                    var riskStratificationChart = new Chart(ctx, {
                        type: 'bar',
                        data: chartData,
                        options: {
 						        maintainAspectRatio: false,
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
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
            label: 'Education Status',
            data: [],
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)', // Red
                'rgba(54, 162, 235, 0.5)', // Blue
                'rgba(255, 206, 86, 0.5)', // Yellow
                'rgba(75, 192, 192, 0.5)', // Green
                'rgba(153, 102, 255, 0.5)', // Purple
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

// Custom order array
var customOrder = ["No formal education", "Primary education", "Secondary education", "Higher secondary", "Graduation or Higher"];

// Make an AJAX call to fetch education status data
$.ajax({
    type: 'GET',
    url: 'ajax/get_education_status.php', // Replace with the actual URL
    dataType: 'json',
    success: function(response) {
        // Sort response based on custom order
        response.sort((a, b) => customOrder.indexOf(a.education_status) - customOrder.indexOf(b.education_status));

        var educationLabels = response.map(item => item.education_status);
        var counts = response.map(item => item.count);

        // Update the chart with the new data
        educationBarChart.data.labels = educationLabels;
        educationBarChart.data.datasets[0].data = counts;

        educationBarChart.update();
    },
    error: function(xhr, status, error) {
        console.error('Error fetching education status data: ' + error);
    }
});


// Initialize the pie chart variable
   var ctx = document.getElementById('employmentPieChart').getContext('2d');
    var employmentPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Employed', 'Not Employed'],
            datasets: [{
                data: [0, 0], // Initial data
                backgroundColor: ['#36a2eb', '#ff6384'],
                borderColor: ['#fff', '#fff'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
			 						        maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    anchor: 'end',
                    align: 'start',
                    offset: -10,
                    borderWidth: 2,
                    borderColor: '#fff',
                    borderRadius: 25,
                    backgroundColor: function(context) {
                        return context.dataset.backgroundColor;
                    },
                    font: {
                        weight: 'bold',
                        size: '14'
                    },
                    formatter: function(value, context) {
                        return value;
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

// Make an AJAX call to fetch employment status data
   $.ajax({
            type: 'GET',
            url: 'ajax/get_employment_status.php', // Replace with the actual URL
            dataType: 'json',
            success: function(response) {
                var employedCount = 0;
                var notEmployedCount = 0;

                // Loop through the response data to get the counts
                response.forEach(function(item) {
                    if (item.employment_status === 'Yes') {
                        employedCount = item.count;
                    } else if (item.employment_status === 'No') {
                        notEmployedCount = item.count;
                    }
                });

                // Update the pie chart data with the counts
                employmentPieChart.data.datasets[0].data = [employedCount, notEmployedCount];
                employmentPieChart.update();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching employment status data:', error);
            }
        });

    $.ajax({
        url: 'ajax/get_participant_count.php', // Path to your PHP script
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Update card content with fetched data
            $('#participantCount').text(data.total_participants);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
            // Handle error gracefully (e.g., display an error message)
        }
    });
	 $.ajax({
        url: 'ajax/get_average_age.php', // Path to your PHP script
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Update card content with fetched data
            $('#averageAge').text(data.average_age+' years');
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
            // Handle error gracefully (e.g., display an error message)
        }
    });
  $.ajax({
        url: 'ajax/get_gender_ratio.php', // Path to your PHP script
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Update card content with fetched data
            $('#maleCount').text(data.male_count);
            $('#femaleCount').text(data.female_count);
			var totalCount = parseInt(data.male_count) + parseInt(data.female_count); 
			      var male= Math.round(((data.male_count / totalCount) * 100).toFixed(2));
                  var female= Math.round(((data.female_count / totalCount) * 100).toFixed(2));
				              $('#genderRatio').text(male+'% m - '+ female +'% f');

        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
            // Handle error gracefully (e.g., display an error message)
        }
    });
	// Fetch High Risk Count
    $.ajax({
        url: 'ajax/get_high_risk_count.php', // Path to your PHP script for high risk count
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Update high risk count card content
            $('#highRiskCount').text(data.high_risk_count);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error: ' + status + ' - ' + error);
        }
    });

</script>

</body>
</html>