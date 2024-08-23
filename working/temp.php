<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Community Health Dashboard</title>
  <!-- Include Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Community Health Dashboard</a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav">
      <div class="nav-item text-nowrap">
        <a class="nav-link px-3" href="#">Sign out</a>
      </div>
    </div>
  </header>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <!-- Sidebar content -->
		 <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
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

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <!-- Main content -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Collapsible Sidebar Example</title>
  <!-- Include Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-gray-200 w-64 h-screen">
      <div class="p-4">
        <h1 class="text-xl font-semibold">Collapsible Sidebar</h1>
      </div>
      <div class="py-4">
        <a href="#" class="block p-4 bg-white hover:bg-gray-100">Dashboard</a>
        <a href="#" class="block p-4 bg-white hover:bg-gray-100">Profile</a>
        <a href="#" class="block p-4 bg-white hover:bg-gray-100">Settings</a>
        <a href="#" class="block p-4 bg-white hover:bg-gray-100">Help</a>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper" class="w-full">
      <nav class="flex items-center justify-between bg-white p-4 border-b">
        <button class="text-xl" id="menu-toggle">
          &#9776;
        </button>
      </nav>
      <div class="container mx-auto p-4">
        <!-- Page content goes here -->
        <h1 class="text-2xl font-semibold">Collapsible Sidebar Example</h1>
        <p class="mt-2">This is an example of a collapsible sidebar using Tailwind CSS.</p>
      </div>
    </div>
    <!-- /#page-content-wrapper -->
  </div>
  <!-- /#wrapper -->

  <script>
    // Toggle the sidebar
    document.getElementById("menu-toggle").addEventListener("click", function(e) {
      e.preventDefault();
      document.getElementById("wrapper").classList.toggle("toggled");
    });
  </script>
</body>
</html>

        <div class="row">
          <!-- Visual 1: Data Collection Progress - Line Chart -->
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-4">
                <h5 class="card-title">Data Collection Progress</h5>
                <div id="dataCollectionProgressChart"></div>
              </div>
            </div>
          </div>

          <!-- Visual 2: Tool Usage Distribution - Pie Chart -->
          <div class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body p-4">
                <h5 class="card-title">Tool Usage Distribution</h5>
                <div id="toolUsageDistributionChart"></div>
              </div>
            </div>
          </div>

          <!-- Add more cards for other visuals -->
        </div>
      </main>
    </div>
  </div>

  <!-- Include D3.js -->
  <script src="https://d3js.org/d3.v7.min.js"></script>
  <!-- Include Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- D3.js code for each visualization -->
  <script>
    // D3.js code for each of the suggested visuals with dummy data
    // Please add your D3.js code here for each chart
  </script>
</body>
</html>
