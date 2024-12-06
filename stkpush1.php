<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if we have a phone number in session
if (!isset($_SESSION['payment_phone'])) {
    header("Location: payment1.php?error=missing_phone");
    exit();
}

if (!isset($_SESSION['payment_phone']) || $_SESSION['plan_type'] !== 'basic') {
    header("Location: payment1.php?error=invalid_request");
    exit();
}

try {
    // Include Access token
    require_once 'accessToken.php';
    if (!isset($access_token)) {
        throw new Exception('Access token not available');
    }

    // Get and format phone number
    $phoneNumber = $_SESSION['payment_phone'];
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    // Ensure the number starts with 254
    if (substr($phoneNumber, 0, 3) !== '254') {
        $phoneNumber = '254' . ltrim($phoneNumber, '0');
    }

    // Log the formatted phone number
    error_log("Formatted Phone Number: " . $phoneNumber);

    // STK Push parameters
    $BusinessShortCode = '174379';
    $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    $Timestamp = date('YmdHis');
    $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
    $TransactionType = 'CustomerPayBillOnline';
    $Amount = '1000';
    $PartyA = $phoneNumber;
    $PartyB = $BusinessShortCode;
    $PhoneNumber = $phoneNumber;
    $CallBackURL = 'https://your-domain.com/callback.php';
    $AccountReference = 'SIMPLEAV-BASIC';
    $TransactionDesc = 'Payment for Basic Plan';

    // Log the request parameters
    error_log("STK Push Request Parameters: " . json_encode([
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => $TransactionType,
        'Amount' => $Amount,
        'PartyA' => $PartyA,
        'PartyB' => $PartyB,
        'PhoneNumber' => $PhoneNumber,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => $AccountReference,
        'TransactionDesc' => $TransactionDesc
    ]));

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            'BusinessShortCode' => $BusinessShortCode,
            'Password' => $Password,
            'Timestamp' => $Timestamp,
            'TransactionType' => $TransactionType,
            'Amount' => $Amount,
            'PartyA' => $PartyA,
            'PartyB' => $PartyB,
            'PhoneNumber' => $PhoneNumber,
            'CallBackURL' => $CallBackURL,
            'AccountReference' => $AccountReference,
            'TransactionDesc' => $TransactionDesc
        ]),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    // Log raw response
    error_log("STK Push Raw Response: " . $response);

    if ($err) {
        throw new Exception("cURL Error: " . $err);
    }

    $response_data = json_decode($response, true);

    if (!$response_data) {
        throw new Exception("Failed to decode response: " . $response);
    }

    // Log decoded response
    error_log("STK Push Decoded Response: " . print_r($response_data, true));

    if (isset($response_data['ResponseCode']) && $response_data['ResponseCode'] === '0') {
        // Success
        $_SESSION['CheckoutRequestID'] = $response_data['CheckoutRequestID'];
        unset($_SESSION['payment_phone']); // Clear phone from session
        header("Location: thank_you.php");
        exit();
    } else {
        // Get specific error message
        $errorMessage = $response_data['errorMessage'] ??
                       $response_data['ResponseDescription'] ??
                       'Unknown error occurred';
        throw new Exception($errorMessage);
    }

} catch (Exception $e) {
    error_log("Payment Error: " . $e->getMessage());
    header("Location: payment1.php?error=payment_failed&message=" . urlencode($e->getMessage()));
    exit();
}

