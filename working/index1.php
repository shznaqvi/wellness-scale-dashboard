<!DOCTYPE html>
<html>
<head>
    <title>Wellness Scale App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
   .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #343a40;
            padding-top: 60px;
            transition: left 0.3s;
        }
        .sidebar.active {
            left: 0;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #ffffff;
            display: block;
        }
        .sidebar a:hover {
            background-color: #555;
        }
        .content {
            width: calc(100% - 250px); /* Subtract sidebar width */
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s, width 0.3s;
        }
        .content.active {
            margin-left: 0;
            width: 100%;
        }
		
		.content {
    width: calc(100% - 250px); /* Subtract sidebar width */
    margin-left: 250px;
    padding: 20px;
    transition: margin-left 0.3s, width 0.3s;
}
.content.active {
    margin-left: 0;
    width: 100%;
}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Wellness Scale App</a>
        <button class="navbar-toggler" type="button" id="sidebarToggleBtn">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Rest of the navigation code -->
    </div>
</nav>

<div class="sidebar" id="sidebar">
    <a href="#">Dashboard</a>
    <a href="#">Users</a>
    <a href="#">Settings</a>
    <!-- Add more sidebar links as needed -->
</div>

<div class="content" id="main-content">
    <div class="container mt-5">
        <h2>Welcome to Wellness Scale App</h2>
        <p>This is the main content area of your application.</p>

        <!-- Example Form -->
        <form>
            <!-- Form fields here -->
        </form>

        <!-- Example Table -->
        <table class="table mt-4">
            <!-- Table content here -->
        </table>

        <!-- Add more content here as needed -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');

    sidebarToggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('active');
    });
</script>
</body>
</html>
