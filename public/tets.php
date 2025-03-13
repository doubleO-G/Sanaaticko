<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer uvrU5HwU1U8ORdVcY0DWCHHJgPgb',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, 1);

// Create the data array
$data = [
    "BusinessShortCode" => 174379,
    "Password" => "MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjUwMzEzMTQxMjU2",
    "Timestamp" => "20250313141256",
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => 1,
    "PartyA" => 254719516641,
    "PartyB" => 174379,
    "PhoneNumber" => 254719516641,
    "CallBackURL" => "https://truelightproperties.co.ke/testaroo/callback.php",
    "AccountReference" => "aroo test",
    "TransactionDesc" => "Payment of tests"
];

// Encode the data as a JSON string
$jsonData = json_encode($data);

curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
