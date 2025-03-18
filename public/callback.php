<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read the raw POST data
$inputData = file_get_contents("php://input");

// Decode the JSON data into an array
$callbackData = json_decode($inputData, true);

// Log the callback data (for debugging purposes)
file_put_contents('callback_log.txt', print_r($callbackData, true), FILE_APPEND);

// Check if the 'Body' key exists
if (isset($callbackData['Body'])) {
    $body = $callbackData['Body'];

    // Safaricom sends the transaction details in the 'stkCallback' array
    if (isset($body['stkCallback'])) {
        $stkCallback = $body['stkCallback'];

        // Extract the result code and description
        $resultCode = $stkCallback['ResultCode'];
        $resultDesc = $stkCallback['ResultDesc'];

        if ($resultCode == 0) {
            // Payment was successful
            $amount = $stkCallback['CallbackMetadata']['Item'][0]['Value'];
            $mpesaReceiptNumber = $stkCallback['CallbackMetadata']['Item'][1]['Value'];
            $transactionDate = $stkCallback['CallbackMetadata']['Item'][3]['Value'];
            $phoneNumber = $stkCallback['CallbackMetadata']['Item'][4]['Value'];

            // You can now process this data and store it in the database
            // For example, save transaction details:
            // saveTransaction($amount, $mpesaReceiptNumber, $transactionDate, $phoneNumber);

            // Respond to Safaricom that the callback was processed successfully
            echo json_encode([
                'status' => 'success',
                'message' => 'Payment successful',
            ]);
        } else {
            // Payment failed
            echo json_encode([
                'status' => 'error',
                'message' => 'Payment failed: ' . $resultDesc,
            ]);
        }
    } else {
        // Invalid callback format
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid callback format',
        ]);
    }
} else {
    // If 'Body' key is not found in the callback data
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing Body key in callback data',
    ]);
}
