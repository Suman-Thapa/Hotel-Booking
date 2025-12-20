<?php
session_start();
include '../includes/connection.php';

if (!isset($_GET['booking_id']) || !isset($_GET['amount'])) {
    die("Missing booking information");
}

$booking_id = (int)$_GET['booking_id'];
$amount = (int)$_GET['amount'] * 100; // Khalti amount in paisa
$name   = $_GET['name'] ?? '';
$email  = $_GET['email'] ?? '';

// Temporary transaction ID
$transaction_id = "Booking{$booking_id}_" . time();

// Update payment method and transaction ID in DB
mysqli_query($con, "
    UPDATE payments SET 
        transaction_id='$transaction_id',
        payment_method='khalti'
    WHERE booking_id='$booking_id'
");

// ---------- Step 1: Payment Callback ----------
if (isset($_GET['pidx'])) {
    $pidx = $_GET['pidx'];

    // Lookup API request to verify payment
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://dev.khalti.com/api/v2/epayment/lookup/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([
            "pidx" => $pidx
        ]),
        CURLOPT_HTTPHEADER => [
            "Authorization: key c1baae33db524a35a67d4641c84f9077", // sandbox secret key
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    // Debug: print lookup response
    echo "<pre>";
    echo "Khalti Lookup Response:\n";
    print_r($data);
    echo "</pre>";

    $status = $data['status'] ?? '';
    $transaction_id = $data['transaction_id'] ?? '';
    $paid_amount = isset($data['total_amount']) ? $data['total_amount'] / 100 : 0;

    // Update DB based on status
    if ($status === "Completed") {
        mysqli_query($con, "
            UPDATE payments SET
                payment_status='paid',
                amount='$paid_amount',
                transaction_id='$transaction_id',
                payment_method='khalti'
            WHERE booking_id='$booking_id'
        ");
    } else {
        mysqli_query($con, "
            UPDATE payments SET
                payment_status='failed'
            WHERE booking_id='$booking_id'
        ");
    }

    // Redirect to invoice
    header("Location: ../user/invoice.php?booking_id=$booking_id");
    exit;
}

// ---------- Step 2: Initiate Payment ----------
$init_curl = curl_init();
curl_setopt_array($init_curl, [
    CURLOPT_URL => "https://dev.khalti.com/api/v2/epayment/initiate/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        "return_url" => "http://localhost/Hotel-Booking/payment/payment_khalti.php?booking_id=$booking_id",
        "website_url" => "http://localhost/Hotel-Booking",
        "amount" => $amount,
        "purchase_order_id" => $booking_id,
        "purchase_order_name" => "Hotel Booking",
        "customer_info" => [
            "name" => $name,
            "email" => $email
        ]
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: key c1baae33db524a35a67d4641c84f9077", // sandbox secret key
        "Content-Type: application/json"
    ],
]);

$init_response = curl_exec($init_curl);
curl_close($init_curl);

$init_data = json_decode($init_response, true);

// Debug: show initiate response
echo "<pre>===== KHALTI INITIATE DEBUG =====\n";
print_r($init_data);
echo "</pre>";

if (isset($init_data['payment_url'])) {
    header("Location: " . $init_data['payment_url']);
    exit;
} else {
    die("❌ Khalti initiate error: " . $init_response);
}
