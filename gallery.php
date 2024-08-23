<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .modal-dialog {
            height: 90vh; /* 90% of viewport height */
            margin: auto;
            max-width: 90vw; /* Max width of the modal is 90% of viewport width */
        }
        .modal-content {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .modal-body {
            overflow: hidden; /* Hide overflow to avoid scrollbars */
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1; /* Allow body to take available space */
            position: relative;
        }
        #modalImage {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* Ensure image fits within modal */
            transition: transform 0.3s ease; /* Smooth rotation transition */
        }
        .modal-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            padding: 10px;
            display: flex;
            justify-content: center;
        }
        .rotate-button, .nav-button {
            color: #fff;
            background: #007bff;
            border: none;
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
        }
        .nav-button {
            background: #28a745; /* Different color for navigation buttons */
        }
        .nav-button.disabled {
            background: #6c757d; /* Disabled state */
            cursor: not-allowed;
        }
        .nav-controls {
            display: flex;
            justify-content: space-between;
            position: absolute;
            top: 50%;
            width: 100%;
            transform: translateY(-50%);
            pointer-events: none; /* Allows clicking through the controls */
        }
        .nav-controls button {
            pointer-events: all; /* Re-enables clicking on the buttons */
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row" id="gallery">
            <!-- Photo gallery will be dynamically inserted here -->
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="nav-controls">
                        <button id="prevPhoto" class="nav-button disabled">&lt; Previous</button>
                        <button id="nextPhoto" class="nav-button">Next &gt;</button>
                    </div>
                    <img id="modalImage" class="img-fluid" alt="Enlarged Image">
                </div>
                <div class="modal-footer">
                    <button id="rotateLeft" class="rotate-button">Rotate Left</button>
                    <button id="rotateRight" class="rotate-button">Rotate Right</button>
                    <button id="resetRotation" class="rotate-button">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let rotationAngle = 0;
            let images = [];
            let currentIndex = 0;

            $.ajax({
                url: './ajax/get_images.php', // Path to the PHP script
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    images = data;
                    let gallery = $('#gallery');
                    images.forEach(function(image) {
                        let col = $('<div class="col-md-3 mb-4"></div>');
                        let img = $('<img class="img-fluid img-thumbnail" alt="Gallery Image">').attr('src', 'wellnessscale/'+image).data('index', images.indexOf(image));
                        col.append(img);
                        gallery.append(col);
                    });

                    // Attach click event to images
                    $('#gallery img').on('click', function() {
                        currentIndex = $(this).data('index');
                        showImage(currentIndex);
                        $('#imageModal').modal('show');
                    });
                },
                error: function() {
                    alert('Failed to load images.');
                }
            });

            function showImage(index) {
                if (index < 0 || index >= images.length) return;
                $('#modalImage').attr('src', 'wellnessscale/' + images[index]);
                $('#modalImage').css('transform', 'rotate(0deg)'); // Reset rotation
                rotationAngle = 0;

                // Update navigation buttons
                $('#prevPhoto').toggleClass('disabled', index === 0);
                $('#nextPhoto').toggleClass('disabled', index === images.length - 1);
            }

            // Rotation functionality
            $('#rotateLeft').on('click', function() {
                rotationAngle -= 90; // Rotate left by 90 degrees
                $('#modalImage').css('transform', `rotate(${rotationAngle}deg)`);
            });

            $('#rotateRight').on('click', function() {
                rotationAngle += 90; // Rotate right by 90 degrees
                $('#modalImage').css('transform', `rotate(${rotationAngle}deg)`);
            });

            $('#resetRotation').on('click', function() {
                rotationAngle = 0; // Reset angle
                $('#modalImage').css('transform', 'rotate(0deg)');
            });

            $('#prevPhoto').on('click', function() {
                if (currentIndex > 0) {
                    currentIndex--;
                    showImage(currentIndex);
                }
            });

            $('#nextPhoto').on('click', function() {
                if (currentIndex < images.length - 1) {
                    currentIndex++;
                    showImage(currentIndex);
                }
            });
        });
    </script>
</body>
</html>
