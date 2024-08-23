<?php
$dir = '../api/uploads'; // Change this to your image folder path
$images = array();
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), array('jpg', 'jpeg', 'png', 'gif'))) {
                $images[] = $dir . '/' . $file;
            }
        }
        closedir($dh);
    }
}
echo json_encode($images);
?>
