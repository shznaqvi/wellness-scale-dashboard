<?php
include_once '../dbconfig_mysql.php';
//include_once '../id.php';

// Assuming you have already connected to the MySQL database using MySQLi
// $con = mysqli_connect($host, $username, $password, $database);

$data = json_decode(file_get_contents('php://input'), true);

if (strlen(strstr($_SERVER['HTTP_USER_AGENT'], "Dalvik")) < 1 && !isset($data['check'])) {
    echo "Failed to authenticate connection!";
    die();
}

$json = array();

if (isset($data['table']) && $data['table'] != '') {
    $table = $data['table'];

    if ($table == "versionApp") {
        $folder = $data['folder'];
        $json = json_decode(file_get_contents('../app/' . $folder), true);
    } else {
        $sql = "SELECT ";

        if (isset($data['limit']) && $data['limit'] != '') {
            $sql .= "LIMIT " . intval($data['limit']);
        }

        if (isset($data['select']) && $data['select'] != '') {
            $sql .= $data['select'] . " FROM $table";
        } else {
            $sql .= " * FROM $table";
        }

        if (isset($data['filter']) && $data['filter'] != '') {
            $sql .= " WHERE 1=1 ";
            $sql .= " AND " . $data['filter'];
        }

        if (isset($data['orderby']) && $data['orderby'] != '') {
            $sql .= " ORDER BY " . $data['orderby'];
        }

        $result = mysqli_query($con, $sql);

        if (!$result) {
            $json[] = array('status' => 0, 'message' => 'Query error.', 'error' => 1);
        } else {
            if (mysqli_num_rows($result) == 0) {
                $json[] = array('status' => 0, 'message' => 'No record found.', 'error' => 1);
            } else {
                while ($r = mysqli_fetch_assoc($result)) {
                    $json[] = $r;
                }
            }
        }
    }
} else {
    $json[] = 'Invalid Table';
}

mysqli_close($con);
header('Content-Type: application/json');
$json = json_encode($json, JSON_UNESCAPED_UNICODE);

if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
    // Handle JSON encoding error
    $json[] = array('status' => 0, 'message' => 'JSON encoding error.', 'error' => 1);
}

echo json_encode($json, JSON_UNESCAPED_UNICODE);
?>
