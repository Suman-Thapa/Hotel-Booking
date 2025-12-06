<?php
include 'connection.php';

function sanitize($con, $data) {
    return mysqli_real_escape_string($con, trim($data));
}

function check_login() {
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: /Hotel-Booking/login/login.php");
        exit();
        
    }
}
?>
