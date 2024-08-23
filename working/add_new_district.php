
<?php
include_once '../dbconfig_mysql.php';
include_once '../encids.php';

$key = hashKey(WS_KEY, 8, 32); // substr(KEY, 12, 44) in JAVA
//echo $key;die();

//echo "Die"; die();
// $ivlen = openssl_cipher_iv_length($cipher="aes-128-cbc");
// $iv = openssl_random_pseudo_bytes($ivlen);  

$iv_len = openssl_cipher_iv_length($cipher="aes-256-gcm");

/* 
// Initialize database connection
$con = mysqli_connect("hostname", "username", "password", "database_name");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
} */

// Initialize variables
$username = "";
$password = "";
$fullName = "";
$designation = "";
$enabled = "";
$isNewUser = "";
$pwdExpiry = "";
$distId = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // $districtCode = $_POST["districtCode"];
    $districtName = $_POST["districtName"];

    // Insert the data into the districts table
    $query = "INSERT INTO districts (districtName) VALUES ('$districtName')";
    if (mysqli_query($con, $query)) {
        $insertSuccess = true;
    } else {
        $insertError = "Error: " . mysqli_error($con);
    }
}

// Close connection
mysqli_close($con);




function encrypt_openssl($textToEncrypt, $key) {
                // $cipher = 'aes-256-gcm';
                // $iv_len = openssl_cipher_iv_length($cipher);
				global $iv_len, $cipher;
                $iv = openssl_random_pseudo_bytes($iv_len);
                $tag_length = 16;
                $tag = ""; // will be filled by openssl_encrypt

                $ciphertext = openssl_encrypt($textToEncrypt, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag, "", $tag_length);
                $encrypted = base64_encode($iv.$ciphertext.$tag);

                return $encrypted;          
}

function decrypt_openssl($textToDecrypt, $key) {
                $encrypted = base64_decode($textToDecrypt);
                // $cipher = 'aes-256-gcm';
                // $iv_len = openssl_cipher_iv_length($cipher);
				global $iv_len, $cipher;
                $iv = substr($encrypted, 0, $iv_len);
                $tag_length = 16;
                $tag = substr($encrypted, -$tag_length);

                $ciphertext = substr($encrypted, $iv_len, -$tag_length);
                $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv, $tag);

                return $decrypted;
}

function hashKey($key, $offset, $length) {
                
                $hash = base64_encode(hash('sha384', $key, true));
                $hashedkey = substr($hash, $offset, $length);

                return $hashedkey;        
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Create New User - Wellness Scale App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Your existing styles here */
        
        /* Sidebar Styles */
        /* ... */

        .container {
            margin-left: 250px;
            padding: 20px;
            margin-bottom: 40px; /* Add margin to the bottom */
        }
    </style>
</head>
<body>



    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Wellness Scale App</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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


<div class="container mt-5">
    <h2>Create New District</h2>
    
 <?php if (isset($insertSuccess) && $insertSuccess) : ?>
        <div class="alert alert-success" role="alert">
            User created successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($insertError)) : ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $insertError; ?>
        </div>
    <?php endif; ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
  
            <div class="form-group">
                <label for="districtName">District Name</label>
                <input type="text" class="form-control" id="districtName" name="districtName" required>
            </div>
            <button type="submit" class="btn btn-primary">Add District</button>
        </form>
</div>
</div>
<footer class="footer bg-primary text-white text-center py-3">
    <div class="container">
        <p>&copy; 2023 Wellness Scale App. All rights reserved.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
