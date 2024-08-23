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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.7/css/dataTables.bootstrap4.min.css">

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

    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Wellness Scale App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
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

    <main class="container mt-5">
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
                <th>UID</th>
                <th>Synced On</th>
                <th>Details</th>
            </tr>
            </thead>
            <tbody>
            <!-- Data will be inserted here by DataTables -->
            </tbody>
        </table>
    </main>

    <footer class="footer mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted">Copyright &copy; 2023 Wellness Scale App</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-dark">Terms of Use</a>
                    <span class="text-muted mx-2">|</span>
                    <a href="#" class="text-dark">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- Bootstrap 5 JavaScript (Bundle) -->
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
                "url": "get_lhw_data.php",
                "dataSrc": ""
            },
            "columns": [
                {"data": "full_name"},
                {"data": "sysdate"},
                {"data": "districtName"},
                {"data": "area"},
                {"data": "kno"},
                {"data": "istatus"},
                {"data": "_uid"},
                {"data": "synced_on"},
                {
                    "data": "_uid",
                    "render": function (data, type, row, meta) {
                        return `<a href="familyDetails.php?uid=${data}" class="btn btn-primary">Details</a>`;
                    }
                }
            ]
        });
    });
</script>

</body>
</html>
