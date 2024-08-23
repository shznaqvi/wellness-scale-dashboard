<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bootstrap Responsive Layout Example</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.7/css/dataTables.bootstrap4.min.css">

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
.footer,
.navbar {
    background-color: var(--primary-color);
    color: white;
}
.icon-fixed-width {
    width: 24px; /* Set the icon width to 24px */
}
#app.card {
    max-width: 500px;
    margin: 0 auto;
    margin-top: 32px;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    border-radius: 5px;
    position: relative; /* Add relative positioning */
}
#app h1 {
    text-align: center;
    color: #4d4d4d;
    margin-top:32px;
}
#app p {
    color: #4d4d4d;
}
#app ul {
    list-style: none;
    margin: 0;
    padding: 0;
}
#app li {
    margin-bottom: 10px;
}
#app a.button {
    display: inline-block;
    padding: 8px 16px;
    background-color: #0077cc;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
}
#app a.button:hover {
    background-color: #d1397e;
}
/* Add styles for the crown image */
#crown-img {
    width: 48px;
    height: auto;
}

/* Add styles for the crown image container */
#crown-img-container {
	position:absolute;
    background-color: #f35ba0; /* Set the background color */
    border-radius: 12px; /* Set the corner radius */
    padding: 16px; /* Add padding */
    display: flex;
	top:-24px;
    justify-content: center;
    align-items: center;
    left: calc(50% - 48px); /* Center horizontally */
    margin-bottom: 16px; /* Add margin to separate from the heading */
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark ">
<div class="container-fluid">
    <a class="navbar-brand" href="#">Wellness Scale App</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Are you sure you want to toggle the user's status?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelToggle">Cancel</button>
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
                        <i class="fas fa-chart-line fa-lg text-white me-2 icon-fixed-width"></i>
                        <span> Dashboard Overview </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="user-management" href="users.php">
                        <i class="fas fa-users fa-lg text-white me-2 icon-fixed-width"></i>
                        <span> User Management </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="data-analytics" href="/data.php">
                        <i class="fas fa-chart-bar fa-lg text-white me-2 icon-fixed-width"></i>
                        <span> Data Analytics </span>
                    </a>
                </li>
                <li class="side-nav-item">
                    <a class="nav-link-ref sub-nav-link nav-link" data-menu-key="settings" href="/settings">
                        <i class="fas fa-cog fa-lg text-white me-2 icon-fixed-width"></i>
                        <span> Settings </span>
                    </a>
                </li>
                <!-- Add more sidebar items here -->
            </ul>
        </div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div id="app" class="card shadow-sm">
                        <div id="crown-img-container">
                            <img id="crown-img" src="./i/wellness_health_care.png" alt="Crown">
                        </div>
                        <h1>WellnessScale v<span id="versionName"></span>.<span id="versionCode"></span></h1>
                        <p>Welcome to the download page for WellnessScale version <span id="versionName"></span>.<span id="versionCode"></span>! You can download the app using the links below:</p>
                        <ul>
                            <li><a id="android-download-button" href="#" class="button">Download for Android</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr>
            <footer>
                <div class="row">
                    <div class="col-md-6">
                        <p>Copyright &copy; 2024 Wellness Scale App</p>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
// Fetch the output-metadata.json file using AJAX
$.ajax({
    url: './app/output-metadata.json',
    dataType: 'json',
	cache: false,
    success: function(metadata) {
        // Extract versionName and versionCode from metadata
        const { versionName, versionCode, outputFile } = metadata.elements[0];

        // Replace placeholders in HTML with actual values
        $('#versionName').text(versionName);
        $('#versionCode').text(versionCode);
        $('#android-download-button').attr('href', './app/' + outputFile);
    },
    error: function(xhr, status, error) {
        console.error('Error fetching metadata:', error);
    }
});
</script>

</body>
</html>
