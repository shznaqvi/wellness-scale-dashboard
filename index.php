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

.todayText {
    color: #666; /* Gray color */
    font-size: 14px; /* Adjust font size as needed */
    /* Add any additional styling properties here */
}
    .badge-container {
            position: relative;
            display: inline-block;
        }
        .badge-container .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 0.75rem;
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
					   <li class="nav-item">
                        <div class="badge-container">
                            <i class="fas fa-bell text-white"></i>
                            <span class="badge badge-danger" id="newFormsCount">0</span>
                        </div>
                    </li>
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
				<main class="col-md-10 ms-sm-auto px-md-4" style="margin-top: 20px;">
				<h3> Main Dashboard </h3>
				<hr>
					<div class="container-fluid h-100">
						<div class="row">
							<!-- Graph Placeholder 1 -->
							<div class="col-md-3">
								<div class="card shadow-sm">
									<div class="card-body bg-light rounded-3">
										<!-- Favicon -->
										<i class="fas fa-chart-bar fa-2x" style="color: #3498db; float: right;"></i>
										<h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
										<p> <span id="totalHouseholdsValue" class="display-3 text-secondary"></span></br><span id="totalHouseholdsTotalValue" class="todayText text-secondary"></span></p>
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
										<p><span id="totalFamilyMembersValue" class="display-3 text-secondary"></span></br><span id="familymemberDoneToday" class="todayText text-secondary"></span></p>
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
										<p><span id="totalActiveLHWSValue" class="display-3"></span></br><span id="activeLHWsToday" class="todayText text-secondary"></span></p>
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
										<p><span id="completionRateValue" class="display-3"></span></br><span id="completionRateToday" class="todayText text-secondary"></span></p>
									</div>
								</div>
							</div>
						</div>

						<div class="row mt-4 ">
							<!-- Graph Placeholder 5 -->
							<div class="col-md-4">
								<div class="card shadow-sm h-100">
									<div class="card-body bg-light rounded-3 chart-container">
										<!-- Favicon -->
										<i class="fas fa-home fa-2x" style="color: #3498db; float: right;"></i>
										<h5 class="card-title">Number of Households per District</h5>

										<h5 id="totalHouseholdsCardTitle" class="card-title"></h5>
										<p id="householdsPerDistValue" class="display-3"></p>
										<!-- Chart Placeholder -->
										<canvas id="householdsChart"></canvas>
									</div>
								</div>
							</div>

							<!-- Graph Placeholder 6 -->
							<div class="col-md-4">
								<div class="card shadow-sm h-100">
									<div class="card-body bg-light rounded-3 chart-container">
										<!-- Favicon -->
										<i class="fas fa-users fa-2x" style="color: #e74c3c; float: right;"></i>
										<h5 class="card-title">Number of Family Members per District</h5>

										<h5 id="totalFamilyMembersCardTitle" class="card-title"></h5>
										<p id="totalFamilyMembersValue" class="display-3"></p>
										<!-- Chart Placeholder -->
										<canvas id="familyMembersChart"></canvas>
									</div>
								</div>
							</div>
							
							 <!-- Risk Stratification Chart -->
							<div class="col-md-4">
								<div class="card shadow-sm h-100">
									<div class="card-body bg-light rounded-3 chart-container">
										<!-- Favicon -->
										<i class="fas fa-exclamation-triangle fa-2x" style="color: #f39c12; float: right;"></i>
										<h5 class="card-title">Risk Stratification</h5>
										<canvas id="riskStratificationChart"></canvas>
									</div>
								</div>
							</div>
						</div>

						<div class="row mt-4">
							<div class="col-md-12">
								<div class="card shadow-sm">
									<div class="card-body bg-light rounded-3 chart-container">
										<!-- Favicon -->
										<i class="fas fa-chart-line fa-2x" style="color: #27ae60; float: right;"></i>
										<h5 id="combinedChartCardTitle" class="card-title">Household Visits and Family Members Interviewed</h5>
										<div class="chart-wrapper">
											<canvas id="lineChart"></canvas>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						  
					</div>
				</main>
			</div>
		</div>
        <!-- Footer -->
        <footer class="mt-auto py-3 bg-dark">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-white">Copyright &copy; 2024 Wellness Scale App</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="text-white">Terms of Use</a>
                        <span class="text-muted mx-2">|</span>
                        <a href="#" class="text-white">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Your custom scripts -->
    <script src="js/index.js"></script>
</body>

</html>
