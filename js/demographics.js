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
            backgroundColor:  [
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
    success: function(response) {
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
    success: function(response) {
        var employedCount = 0;
        var notEmployedCount = 0;

        // Loop through the response data to count employed and not employed
        response.forEach(function(item) {
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
    error: function(xhr, status, error) {
        console.error('Error fetching employment status data: ' + error);
    }
});
