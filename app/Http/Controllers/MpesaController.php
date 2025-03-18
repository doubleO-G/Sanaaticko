<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;



class MpesaController extends Controller
{
    private function getAccessToken()
    {
        // API URL to retrieve the access token
        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        // // Your Safaricom credentials (Consumer Key and Consumer Secret)
        // $consumerKey = env('MPESA_CONSUMER_KEY'); // You can store this in the .env file
        // $consumerSecret = env('MPESA_CONSUMER_SECRET'); // You can store this in the .env file

         // Your Safaricom credentials (Consumer Key and Consumer Secret)
         $consumerKey = 'KaYJpbHjGw9cfSD7fHy43gR1LRC5ZTYLJ53omutLZ45fewTB' ; // You can store this in the .env file
         $consumerSecret = 'YL4Wc4wcHsYvTzGDcsV3kuDAlMxLGhAQmBuKE31GAEsi5xxRSoWNasP7l6sWahAk' ; // You can store this in the .env file

        // Encode credentials as Base64
        $credentials = base64_encode("$consumerKey:$consumerSecret");
        // or this concatenate
        // $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

        // Send the request to Safaricom to retrieve the access token
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials
        ])->get($url);

        // If the response is successful, return the token
        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        // Handle the error if the token retrieval fails
        return null;
    }




    public function stkPush(Request $request)
    {
        // API URL for the STK Push request
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        // API credentials
        $phone_number = '254719516641'; 
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $shortcode = '174379';  // Business short code
        $accessToken = $this->getAccessToken();  // Access token from the private function

        if (!$accessToken) {
            return response()->json([
                'message' => 'Failed to get access token'
            ], 500);
        }

        // Prepare the request payload
        $timestamp = now()->format('YmdHis');  // Current timestamp in the required format

        // Generate the password (Base64 encoded string)
        $password = base64_encode($shortcode . $passkey . $timestamp);

        // Prepare the request payload
        $data = [
            "BusinessShortCode" => $shortcode,
            "Password" => $password,
            "Timestamp" => $timestamp,  // Current timestamp in the required format
            "TransactionType" => "CustomerPayBillOnline",  // Transaction type
            "Amount" => 1,  // Payment amount
            "PartyA" => $phone_number,  // Phone number of the payer
            "PartyB" => $shortcode,  // Business short code
            "PhoneNumber" => $phone_number,  // Phone number of the payer
            "CallBackURL" => "https://truelightproperties.co.ke/testaroo/callback.php",  // Your callback URL
            "AccountReference" => "pushtolipa",  // Account reference or account number to be shown on push msg
            "TransactionDesc" => "from base"  // Description of the transaction
        ];

        // Make the HTTP POST request to the STK Push API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        // Check if the response is successful
        if ($response->successful()) {
            // Process the response if necessary
            return response()->json([
                'message' => 'Request sent successfully!',
                'response' => $response->json()
            ]);
        } else {
            // Handle error response
            return response()->json([
                'message' => 'Failed to initiate STK Push',
                'error' => $response->body()
            ], $response->status());
        }
    }

    public function handleCallback(Request $request)
    {
        // Dump the callback data for debugging
        // You may remove this after verifying the data structure
        Log::info('M-Pesa Callback Data: ', $request->all());

        // Extract relevant information from the request
        $callbackData = $request->all();

        // For example, Safaricom will send a `Body` key with details:
        if (isset($callbackData['Body'])) {
            $body = $callbackData['Body'];

            // Extract the transaction details from the body
            $stkCallback = $body['stkCallback'];

            // Handle success or failure based on the `ResultCode`
            $resultCode = $stkCallback['ResultCode'];
            $resultDesc = $stkCallback['ResultDesc'];

            if ($resultCode == 0) {
                // Success: Payment was successful
                $amount = $stkCallback['CallbackMetadata']['Item'][0]['Value'];
                $mpesaReceiptNumber = $stkCallback['CallbackMetadata']['Item'][1]['Value'];
                $transactionDate = $stkCallback['CallbackMetadata']['Item'][3]['Value'];

                // You can now save the transaction to the database or process it
                // For example, store it in the database:
                // Transaction::create([
                //     'amount' => $amount,
                //     'mpesa_receipt_number' => $mpesaReceiptNumber,
                //     'transaction_date' => $transactionDate,
                //     'status' => 'success',
                // ]);

                return response()->json([
                    'message' => 'Payment successful',
                    'data' => [
                        'Amount' => $amount,
                        'MpesaReceiptNumber' => $mpesaReceiptNumber,
                        'TransactionDate' => $transactionDate,
                    ]
                ]);
            } else {
                // Failure: Payment failed
                return response()->json([
                    'message' => 'Payment failed',
                    'error' => $resultDesc
                ]);
            }
        } else {
            // If the callback data is not in the expected format
            return response()->json([
                'message' => 'Invalid callback data format',
                'error' => $callbackData
            ], 400);
        }
    }



}
