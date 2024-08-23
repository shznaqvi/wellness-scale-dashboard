<?php
//ini_set('display_errors', 1);
date_default_timezone_set('Asia/Karachi');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300);

$host = "localhost";
$username = "app";
$password = "abcd1234";
$database = "ws_app";

// Create a MySQLi connection
$con = mysqli_connect($host, $username, $password, $database);

// Check the connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL query to select all records from the APPuser table
$sql = "SELECT * FROM APPuser";

// Execute the query
$result = mysqli_query($con, $sql);

// Check if any rows were returned
if (mysqli_num_rows($result) > 0) {
    // Start the HTML table
    echo "<table border='1'>";
    
    // Table headers based on the columns returned by the query
    echo "<tr>";
    while ($fieldinfo = mysqli_fetch_field($result)) {
        echo "<th>" . $fieldinfo->name . "</th>";
    }
    echo "</tr>";
    
    // Output data of each row
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . $value . "</td>";
        }
        echo "</tr>";
    }
    // Close the table
    echo "</table>";
} else {
    echo "0 results";
}

// Close the connection
mysqli_close($con);
?>
