<?php
// eSewa Sandbox Credentials
$epay_url = "https://rc-epay.esewa.com.np/api/epay/main/v2/form";
$merchant_code = "EPAYTEST";
$secret_key = "8gBm/:&EnhH.1/q";

// Transaction details
$total_amount = 110; // Test amount
$transaction_uuid = uniqid(); // Unique ID for each transaction
$product_code = "EPAYTEST";

// Prepare data to sign (exact order and no spaces)
$data_to_sign = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";
$signature = base64_encode(hash_hmac('sha256', $data_to_sign, $secret_key, true));
?>

<form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
 <input type="text" id="amount" name="amount" value="100" required>
 <input type="text" id="tax_amount" name="tax_amount" value ="10" required>
 <input type="text" id="total_amount" name="total_amount" value="<?php echo $total_amount; ?>" required>
 <input type="text" id="transaction_uuid" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>" required>
 <input type="text" id="product_code" name="product_code" value ="<?php echo $product_code; ?>" required>
 <input type="text" id="product_service_charge" name="product_service_charge" value="0" required>
 <input type="text" id="product_delivery_charge" name="product_delivery_charge" value="0" required>
 <input type="text" id="success_url" name="success_url" value="http://localhost/Hotel-Booking/payment/success.php" required>
 <input type="text" id="failure_url" name="failure_url" value="http://localhost/Hotel-Booking/payment/failure.php" required>
 <input type="text" id="signed_field_names" name="signed_field_names" value="total_amount,transaction_uuid,product_code" required>
 <input type="text" id="signature" name="signature" value="<?php echo $signature; ?>" required>
 <input value="Submit" type="submit">
 </form>
