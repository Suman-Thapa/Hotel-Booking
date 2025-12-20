<?php
session_start();
include '../includes/connection.php';

if (!isset($_GET['booking_id'])) {
    die("Booking ID not provided!");
}

$booking_id = (int)$_GET['booking_id'];

// Update payment_status to 'failed'
$update = mysqli_query($con, "UPDATE payments SET payment_status='failed' WHERE booking_id=$booking_id");

if (!$update) {
    die("Database update failed: " . mysqli_error($con));
}

// Redirect back to invoice
header("Location: ../user/invoice.php?booking_id=$booking_id");
exit;
