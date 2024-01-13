<?php
if (isset($_GET['filename'])) {
    $filename = $_GET['filename'];
    if (file_exists($filename)) {
        unlink($filename);
    }
}
?>
