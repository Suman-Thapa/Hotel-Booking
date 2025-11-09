<?php
// eSewa verification API
$verify_url = "https://rc-epay.esewa.com.np/api/epay/transaction/status/";
$secret_key = "8gBm/:&EnhH.1/q";
$product_code = "EPAYTEST001";

// eSewa sends these fields in the POST request
$refId = $_POST['refId'] ?? '';
$amount = $_POST['total_amount'] ?? '';
$transaction_uuid = $_POST['transaction_uuid'] ?? '';

if ($refId && $amount && $transaction_uuid) {
    // Verify payment with eSewa
    $data = array(
        'product_code' => $product_code,
        'total_amount' => $amount,
        'transaction_uuid' => $transaction_uuid
    );

    $ch = curl_init($verify_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['status']) && $result['status'] === 'COMPLETE') {
        echo "<h2>✅ Payment Successful!</h2>";
        echo "<p>Reference ID: $refId</p>";
        echo "<p>Transaction UUID: $transaction_uuid</p>";
    } else {
        echo "<h2>⚠️ Payment Verification Failed!</h2>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
} else {
    echo "<h2>❌ Invalid request — no data received!</h2>";
}
?>
