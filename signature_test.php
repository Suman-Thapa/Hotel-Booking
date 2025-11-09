<?php

$secret_key = "8gBm/:&EnhH.1/q";
$data_to_sign = "total_amount=100,transaction_uuid=11-201-13,product_code=EPAYTEST";
$signature = base64_encode(
    hash_hmac('sha256', $data_to_sign, $secret_key, true)
);

echo $signature;

echo "<br>4Ov7pCI1zIOdwtV2BRMUNjz1upIlT/COTxfLhWvVurE=";