<?php 

$data = "total_amount=110,transaction_uuid=241028,product_code=EPAYTEST";

$key= "8gBm/:&EnhH.1/q";

$s = hash_hmac('sha256', $data, $key, true);
echo base64_encode($s); 
