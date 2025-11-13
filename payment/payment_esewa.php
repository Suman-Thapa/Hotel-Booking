<?php
session_start();
include '../includes/connection.php';

if (!isset($_POST['booking_id']) || !isset($_POST['total_amount'])) {
    die("Invalid request!");
}

$booking_id = (int) $_POST['booking_id'];
$total_amount = number_format((float) $_POST['total_amount'], 2, '.', ''); // ensures correct format

// eSewa Sandbox credentials
$epay_url = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";
$product_code = "EPAYTEST";
$secret_key = "8gBm/:&EnhH.1/q";

// ✅ Generate unique transaction ID per attempt
$transaction_uuid = "BKNG_" . uniqid();

// ✅ Prepare signature using correct variable
$data_to_sign = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";
$signature = base64_encode(hash_hmac('sha256', $data_to_sign, $secret_key, true));

// ✅ Update payments table with new transaction UUID and pending status
mysqli_query($con, "UPDATE payments SET transaction_id='$transaction_uuid', payment_status='pending' WHERE booking_id=$booking_id");

// Success / failure URLs (keep clean)
$success_url = "http://localhost/Hotel-Booking/payment/success_esewa.php?booking_id=$booking_id";
$failure_url = "http://localhost/Hotel-Booking/payment/failure_esewa.php?booking_id=$booking_id";
?>

<form id="esewaForm" action="<?= $epay_url; ?>" method="POST">
    <input type="text" name="amount" value="<?= $total_amount - 10; ?>" required>
    <input type="text" name="tax_amount" value="10" required>
    <input type="text" name="total_amount" value="<?= $total_amount; ?>" required>
    <input type="text" name="transaction_uuid" value="<?= $transaction_uuid; ?>" required>
    <input type="text" name="product_code" value="<?= $product_code; ?>" required>
    <input type="text" name="product_service_charge" value="0" required>
    <input type="text" name="product_delivery_charge" value="0" required>
    <input type="text" name="success_url" value="<?= $success_url; ?>" required>
    <input type="text" name="failure_url" value="<?= $failure_url; ?>" required>
    <input type="text" name="signed_field_names" value="total_amount,transaction_uuid,product_code" required>
    <input type="text" name="signature" value="<?= $signature; ?>" required>
</form>

<script>
document.getElementById('esewaForm').submit();
</script>
