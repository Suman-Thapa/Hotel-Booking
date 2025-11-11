<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['token'], $data['amount'], $data['booking_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request data"]);
    exit;
}

$token = $data['token'];
$amount = $data['amount'];
$booking_id = $data['booking_id'];

$secret_key = "test_secret_key_d067b4bfa9c74302a22c0dbaf5b16464";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://a.khalti.com/api/v2/payment/verify/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        "token" => $token,
        "amount" => $amount
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Key $secret_key"
    ],
]);

$response = curl_exec($curl);
curl_close($curl);

$res = json_decode($response, true);

if (isset($res['idx'])) {
    include 'connection.php';
    $stmt = $con->prepare("UPDATE payments SET payment_status='paid', amount=? WHERE booking_id=?");
    $stmt->bind_param("di", $amount, $booking_id);
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Payment verified successfully", "khalti_response" => $res]);
} else {
    echo json_encode([
        "success" => false,
        "message" => $res['detail'] ?? "Verification failed",
        "response" => $res
    ]);
}
?>
