<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $shortcode = '174379';  // Business short code
        $password = 'MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjUwMzEzMTYyNDU0';  // Password for API
        $accessToken = '1A9gG9GFxbPscnR9GGkP7AXUZAXi';  // Bearer Token for authorization (this should be securely retrieved)

        // Prepare the request payload
        $data = [
            "BusinessShortCode" => $shortcode,
            "Password" => $password,
            "Timestamp" => now()->format('YmdHis'),  // Current timestamp in the required format
            "TransactionType" => "CustomerPayBillOnline",  // Transaction type
            "Amount" => 1,  // Payment amount
            "PartyA" => 254708374149,  // Phone number of the payer
            "PartyB" => $shortcode,  // Business short code
            "PhoneNumber" => 254708374149,  // Phone number of the payer
            "CallBackURL" => "https://mydomain.com/path",  // Your callback URL
            "AccountReference" => "CompanyXLTD",  // Account reference
            "TransactionDesc" => "Payment of X"  // Description of the transaction
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
}
