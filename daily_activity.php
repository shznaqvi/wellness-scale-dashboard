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

<h3> Daily Activity </h3>
				<hr>
<div class="container-fluid">
           <div class="row mb-4 ">
            <h2 class="text-center">Household Forms</h2>
			<div class="card table-responsive-md">
        <table id="dataTable" class="table table-sm table-striped table-bordered compact table-hover stripe">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Entry Date/Time</th>
                    <th>District Name</th>
                    <th>Area</th>
                    <th>Khandan No.</th>
                    <th>Status</th>
                    <th>Synced On</th>
                    <th>App Ver.</th>
                    <th>Family Details</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted here by DataTables -->
            </tbody>
        </table>
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
	

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>


<script>
 $(document).ready(function() {
        $('#dataTable').DataTable({
			"pageLength": 50,
			order: [[6, 'desc']],
			"responsive": true ,
            "ajax": {
                "url": "ajax/get_lhw_data.php",
                "dataSrc": ""
            },
			 
            "columns": [
                { "data": "full_name" },
                { "data": "sysdate" },
                { "data": "districtName" },
                { "data": "area" },
                { "data": "kno" },
                { "data": "istatus" },
                { "data": "synced_on" },
                { "data": "appversion" },
                {
                    "data": "_uid",
                    "render": function(data, type, row, meta) {
                        return `<a href="familyDetails.php?uid=${data}" class="btn btn-primary">Details</a>`;
                    },
                 
                }
            ]
        });
    });

</script>

</body>
</html>