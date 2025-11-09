<?php
session_start();
include 'connection.php';
include 'functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = sanitize($con, $_GET['id']);

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        mysqli_begin_transaction($con);

        $deleteQuery = "DELETE FROM hotels WHERE hotel_id='$id'";
        mysqli_query($con, $deleteQuery);

        mysqli_commit($con);

        $_SESSION['hotel_msg'] = [
            "text" => "Hotel deleted successfully!",
            "type" => "success"
        ];
    } 
    catch (mysqli_sql_exception $e) {
    mysqli_rollback($con);

    // Friendly message for the user
    $_SESSION['hotel_msg'] = [
        "text" => "Cannot delete hotel! It has bookings linked to it.",
        "type" => "error"
    ];

    // (Optional) If you want to debug, log the real error to a file
    // error_log("Delete Hotel Error: " . $e->getMessage());
}


    header("Location: admin_dashboard.php");
    exit();
}
?>
