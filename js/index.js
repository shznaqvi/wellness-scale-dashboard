$(document).ready(function() {
    // Function to format large numbers with commas
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
	
   function getMySQLDateTime() {
        var date = new Date();
        return date.getFullYear() + '-' + 
               ('0' + (date.getMonth() + 1)).slice(-2) + '-' + 
               ('0' + date.getDate()).slice(-2) + ' ' + 
               ('0' + date.getHours()).slice(-2) + ':' + 
               ('0' + date.getMinutes()).slice(-2) + ':' + 
               ('0' + date.getSeconds()).slice(-2);
    }

    var lastRefreshTime = getMySQLDateTime();
	
	console.log('lastRefreshTime: '+ lastRefreshTime);
    function updateNewFormsCount() {
        $.ajax({
            url: 'ajax/get_new_forms_count.php',
            type: 'GET',
            data: {
                last_refresh_time: lastRefreshTime
            },
            dataType: 'json',
            success: function(data) {
                $('#newFormsCount').text(data.new_forms_count);
                console.log('New Forms: ' + data.new_forms_count + ' after ' + lastRefreshTime);

                // Update lastRefreshTime after successful fetch
                //lastRefreshTime = new Date().toISOString();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching new forms count: ' + error);
            }
        });
    }

    // Update the count every 15 seconds
    setInterval(updateNewFormsCount, 15000);

    // Initial fetch
    updateNewFormsCount();

    // AJAX call to fetch total households
    $.ajax({
        type: 'GET',
        url: 'ajax/get_total_household.php',
        dataType: 'json',
        success: function(response) {
            var totalHouseholds = response.totalHouseholds;
            var householdsDoneToday = response.householdsDoneToday;

            // Update total households card
            $('#totalHouseholdsCardTitle').text('Total Households');
            $('#totalHouseholdsValue').text(formatNumber(totalHouseholds));
            $('#totalHouseholdsTotalValue').text("(" + formatNumber(householdsDoneToday) + " done today)");
        },
        error: function(xhr, status, error) {
            console.error('Error fetching total households: ' + error);
        }
    });

    // AJAX call to fetch total family members interviewed
    $.ajax({
        type: 'GET',
        url: 'ajax/get_total_familymembers.php',
        dataType: 'json',
        success: function(response) {
            var totalFamilyMembers = response.totalFamilyMembers;
            var familymemberDoneToday = response.familymemberDoneToday;

            // Update total family members card
            $('#totalFamilyMembersCardTitle').text('Total Family Members');
            $('#totalFamilyMembersValue').text(formatNumber(totalFamilyMembers));
            $('#familymemberDoneToday').text("(" + formatNumber(familymemberDoneToday) + " done today)");
        },
        error: function(xhr, status, error) {
            console.error('Error fetching total family members: ' + error);
        }
    });

    // AJAX call to fetch total active LHWS
    $.ajax({
        type: 'GET',
        url: 'ajax/get_total_activelhws.php',
        dataType: 'json',
        success: function(response) {
            var totalActiveLHWS = response.totalActiveLHWS;
            var activeLHWsToday = response.activeLHWsToday;

            // Update total active LHWS card
            $('#totalActiveLHWSCardTitle').text('Total Active LHWS');
            $('#totalActiveLHWSValue').text(formatNumber(totalActiveLHWS));
            $('#activeLHWsToday').text("(" + formatNumber(activeLHWsToday) + " active today)");
        },
        error: function(xhr, status, error) {
            console.error('Error fetching total active LHWS: ' + error);
        }
    });

    // AJAX call to fetch completion rate of surveys
    $.ajax({
        type: 'GET',
        url: 'ajax/get_completion_rate.php',
        dataType: 'json',
        success: function(response) {
            var completionRate = response.completionRate;
            var completionRateToday = response.completionRateToday;
            var todayRefused = response.todayRefused;
            var totalRefused = response.totalRefused;

            // Update completion rate card
            $('#completionRateCardTitle').text('Completion Rate');
            $('#completionRateValue').text(completionRate + '%');
            $('#completionRateToday').text("(" + completionRateToday + '% today) Total refused: ' + totalRefused + ' | ' + todayRefused + ' today');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching completion rate: ' + error);
        }
    });

    // Initialize householdsChart variable
    var householdsChart = new Chart(document.getElementById('householdsChart'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Households per District',
                data: [],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // AJAX call to fetch number of households per district
    $.ajax({
        type: 'GET',
        url: 'ajax/get_households_per_district.php',
        dataType: 'json',
        success: function(response) {
            var districtLabels = response.districtLabels;
            var householdCountData = response.householdCountData;
            var backgroundColors = generateRandomColors(districtLabels.length);

            // Update households chart
            householdsChart.data.labels = districtLabels;
            householdsChart.data.datasets[0].data = householdCountData;
            householdsChart.data.datasets[0].backgroundColor = backgroundColors;
            householdsChart.update();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching households per district: ' + error);
        }
    });

    // Initialize familyMembersChart variable
    var familyMembersChart = new Chart(document.getElementById('familyMembersChart'), {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Family Members per District',
                data: [],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // AJAX call to fetch number of family members per district
    $.ajax({
        type: 'GET',
        url: 'ajax/get_family_members_per_district.php',
        dataType: 'json',
        success: function(response) {
            var districtLabels = response.districtLabels;
            var familyMemberCountData = response.familyMemberCountData;
            var backgroundColors = generateRandomColors(districtLabels.length);

            // Update family members chart
            familyMembersChart.data.labels = districtLabels;
            familyMembersChart.data.datasets[0].data = familyMemberCountData;
            familyMembersChart.data.datasets[0].backgroundColor = backgroundColors;
            familyMembersChart.update();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching family members per district: ' + error);
        }
    });

  // Initialize lineChart variable
var lineChart = new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Daily Household Visits',
            data: [],
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            fill: false
        },{
            label: 'Family Member Interviewed',
            data: [],
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            fill: false
        },
        {
            label: 'Daily Active LHWs',
            data: [],
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 2,
            fill: false
        }
		
        ]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// AJAX call to fetch forms count per week data
$.ajax({
    type: 'GET',
    url: 'ajax/get_forms_count_per_week.php',
    dataType: 'json',
    success: function(response) {
        var labels = response.map(function(item) {
            return item.weekStart + ' to ' + item.weekEnd;
        });
        var countsData = response.map(function(item) {
            return item.Counts;
        });
        var lhwCountsData = response.map(function(item) {
            return item.LHW_Counts;
        });

        // Update line chart data
        lineChart.data.labels = labels;
        lineChart.data.datasets[0].data = countsData;
        lineChart.data.datasets[2].data = lhwCountsData;
        lineChart.update();
    },
    error: function(xhr, status, error) {
        console.error('Error fetching forms count per week data: ' + error);
    }
});

// AJAX call to fetch family member count per week data
$.ajax({
    type: 'GET',
    url: 'ajax/get_family_members_count_per_week.php',
    dataType: 'json',
    success: function(response) {
        var familyMemberCountsData = response.map(function(item) {
            return item.Counts;
        });

        // Ensure that the labels remain consistent
        // If labels are already populated, no need to update again, this is for initial load.
        if (lineChart.data.labels.length === 0) {
            var labels = response.map(function(item) {
                return item.weekStart + ' to ' + item.weekEnd;
            });
            lineChart.data.labels = labels;
        }

        // Update line chart data for family members
        lineChart.data.datasets[1].data = familyMemberCountsData;
        lineChart.update();
    },
    error: function(xhr, status, error) {
        console.error('Error fetching family member count per week data: ' + error);
    }
});

	
	 

  // Initialize Risk Stratification Chart
var riskStratificationCtx = document.getElementById('riskStratificationChart').getContext('2d');

// Fetch data and render Risk Stratification Chart
$.ajax({
    url: 'ajax/get_risk_data.php',
    method: 'GET',
    dataType: 'json',
    success: function(data) {
        console.log("Data: " + JSON.stringify(data));

        // Prepare data structure for Chart.js
        var labels = []; // Labels for x-axis (districts)
        var datasets = {
            'LOW RISK': [],
            'MEDIUM RISK': [],
            'HIGH RISK': []
        };

        // Organize data by district and risk outcome
        data.forEach(item => {
            var districtName = item.districtName;
            var riskOutcome = item.riskOutcome;
            var riskCount = parseInt(item.risk_count);

            // Add district to labels if not already added
            if (!labels.includes(districtName)) {
                labels.push(districtName);
            }

            // Add data for the current district and risk outcome
            if (datasets[riskOutcome]) {
                datasets[riskOutcome].push({
                    district: districtName,
                    count: riskCount
                });
            }
        });

        // Convert datasets object into an array of datasets in the specified order
        var chartDatasets = ['LOW RISK', 'MEDIUM RISK', 'HIGH RISK'].map(function(riskOutcome) {
            return {
                label: riskOutcome,
                data: labels.map(function(district) {
                    var dataPoint = datasets[riskOutcome].find(function(data) {
                        return data.district === district;
                    });
                    return dataPoint ? dataPoint.count : 0;
                }),
                backgroundColor: getBackgroundColor(riskOutcome),
                borderColor: getBorderColor(riskOutcome),
                borderWidth: 1
            };
        });

        // Render Risk Stratification Chart
        var riskStratificationChart = new Chart(riskStratificationCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: chartDatasets
            },
            options: {
                responsive: true,
                indexAxis: 'x', // Use districts as labels on x-axis
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false, // Do not stack bars
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value); // Format y-axis ticks with commas
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + formatNumber(tooltipItem.raw);
                            }
                        }
                    },
                    // Custom plugin to display counts inside the top of each bar
                    drawBarValues: {
                        enabled: true
                    }
                }
            }
        });

        // Custom plugin definition to draw values inside top of bars
        Chart.register({
            id: 'drawBarValues',
            afterDraw: function(chart, args, options) {
                var ctx = chart.ctx;

                // Loop through all datasets and bars to display values
                chart.data.datasets.forEach(function(dataset, i) {
                    var meta = chart.getDatasetMeta(i);
                    if (!meta.hidden) {
                        meta.data.forEach(function(element, index) {
                            // Display value only if it's greater than 0
                            var data = dataset.data[index];
                            if (data !== null && data !== undefined && data !== 0) {
                                var fontSize = 12;
                                var fontStyle = 'normal';
                                var fontFamily = 'Arial';
                                var value = formatNumber(data);
                                var textWidth = ctx.measureText(value).width;
                                var padding = 4;
                                var position = element.tooltipPosition();
                                var xPos = element.x - textWidth / 2;
                                var yPos = position.y - (fontSize / 2) - padding;
                                ctx.font = Chart.helpers.fontString(fontSize, fontStyle, fontFamily);
                                ctx.fillStyle = dataset.borderColor;
                                ctx.fillText(value, xPos, yPos);
                            }
                        });
                    }
                });
            }
        });

    },
    error: function(xhr, status, error) {
        console.error('Error fetching risk data:', error);
    }
});

// Function to generate random background color based on risk outcome
function getBackgroundColor(riskOutcome) {
    switch (riskOutcome) {
        case 'LOW RISK':
            return 'rgba(54, 162, 235, 0.6)';
        case 'MEDIUM RISK':
            return 'rgba(255, 206, 86, 0.6)';
        case 'HIGH RISK':
            return 'rgba(255, 99, 132, 0.6)';
        default:
            return 'rgba(75, 192, 192, 0.6)';
    }
}

// Function to generate border color based on risk outcome
function getBorderColor(riskOutcome) {
    switch (riskOutcome) {
        case 'LOW RISK':
            return 'rgba(54, 162, 235, 1)';
        case 'MEDIUM RISK':
            return 'rgba(255, 206, 86, 1)';
        case 'HIGH RISK':
            return 'rgba(255, 99, 132, 1)';
        default:
            return 'rgba(75, 192, 192, 1)';
    }
}

// Function to format numbers with commas
function formatNumber(value) {
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
