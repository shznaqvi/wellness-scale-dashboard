<!DOCTYPE html>
<html>
<head>
  <title>Risk Map</title>
  <style>
    /* CSS styles for the map container */
    #map {
      height: 100%; /* Full height */
      width: 100%; /* Full width */
    }
    html, body {
      height: 100%; /* Full height */
      margin: 0;
      padding: 0;
    }
  </style>
</head>
<body>
  <h1>Risk Map</h1>
  <div id="map"></div>
  
  <!-- Include jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <!-- Load the Google Maps API asynchronously with the 'loading=async' parameter -->
  <script async defer 
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBYyxs5020Ymmc6NMPDc_hclRHFDZcWNwU&libraries=marker&callback=initMap">
  </script>

  <script>
    let map;

  function initMap() {
    var mapOptions = {
        zoom: 10,
        center: new google.maps.LatLng(24.8607, 67.0011),  // Example coordinates
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById('map'), mapOptions);

    fetchMarkers().then(function(markers) {
        markers.forEach(function(markerData) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(markerData.lat, markerData.lng),
                map: map,
                title: markerData.title
            });
        });
    }).catch(function(error) {
        console.error('Error initializing map with markers:', error);
    });
}

  function fetchMarkers() {
    $.ajax({
        url: 'your-endpoint-url',  // Replace with your endpoint
        method: 'GET',
        success: function(data) {
            console.log('Markers fetched:', data);
            // Ensure data is in the correct format and non-empty
            if (data && data.length) {
                // Process markers
            } else {
                console.error('No markers found or invalid data format.');
            }
        },
        error: function(error) {
            console.error('Error fetching markers:', error);
        }
    });
}
  </script>

</body>
</html>

