<?php
// Receive the callback response
$response = file_get_contents('php://input');

// Log the response for debugging
file_put_contents('stkpush_callback.log', $response . PHP_EOL, FILE_APPEND);

// Process the response
$data = json_decode($response, true);

if (isset($data['Body']['stkCallback']['ResultCode']) && $data['Body']['stkCallback']['ResultCode'] == 0) {
  // Transaction successful
  $mpesaReceiptNumber = $data['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
  echo "Transaction successful. Receipt number: " . $mpesaReceiptNumber;
} else {
  // Transaction failed
  echo "Transaction failed. Error: " . $data['Body']['stkCallback']['ResultDesc'];
}
?>
