<?php
/**
 * PostEx API - Order Booking (https://api.postex.pk/services/integration/api/order/v3/create-order)
 *
 * Processes an order by sending data to the PostEx API.
 *
 * @param string $city City name for delivery.
 * @param string $customerName Customer's name.
 * @param string $customerPhone Customer's phone number.
 * @param string $deliveryAddress Delivery address including phone number.
 * @param float $invoicePayment Total payment amount for the invoice.
 * @param string $orderDetail Details of the order in a formatted string.
 * @param string $orderRefNumber Reference number for the order.
 * @param string $orderType Type of the order (e.g., 'Normal').
 * @param string $pickupAddressCode Pickup address code.
 * @param float $bookingWeight Weight of the order for booking.
 * @param string $transactionNotes Additional notes for the transaction.
 * @param string $apiToken API token for authentication.
 * @return array Result of the API call including success status and tracking information.
 */
function processPostEx($city, $customerName, $customerPhone, $deliveryAddress, $invoicePayment, $orderDetail, $orderRefNumber, $orderType, $pickupAddressCode, $bookingWeight, $transactionNotes, $apiToken) {
    $errors = []; // Array to store any errors

    // Prepare the data to be sent to the API
    $requestData = [
        'cityName' => $city,
        'customerName' => $customerName,
        'customerPhone' => $customerPhone,
        'deliveryAddress' => $deliveryAddress,
        'invoicePayment' => $invoicePayment,
        'orderDetail' => $orderDetail,
        'orderRefNumber' => $orderRefNumber,
        'orderType' => $orderType,
        'pickupAddressCode' => $pickupAddressCode,
        'bookingWeight' => $bookingWeight,
        'transactionNotes' => $transactionNotes
    ];

    // Define the API endpoint
    $apiEndpoint = 'https://api.postex.pk/services/integration/api/order/v3/create-order';

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($requestData),
        CURLOPT_HTTPHEADER => [
            'token: ' . $apiToken,
            'Content-Type: application/json'
        ]
    ]);

    // Execute the API request
    $apiResponse = curl_exec($curl);

    // Check if cURL executed successfully
    if ($apiResponse === false) {
        $errors[] = 'Curl error: ' . curl_error($curl);
    } else {
        // Decode the JSON response from the API
        $responseData = json_decode($apiResponse, true);

        // Check if the response contains the tracking number
        if (isset($responseData['dist']['trackingNumber'])) {
            // API call succeeded
            $trackingNumber = $responseData['dist']['trackingNumber'];
            $orderStatus = $responseData['dist']['orderStatus'];
            return [
                'success' => true,
                'trackingNumber' => $trackingNumber,
                'orderStatus' => $orderStatus
            ];
        } else {
            $errors[] = 'API did not return tracking number.';
        }
    }

    // Close cURL session
    curl_close($curl);

    // Return result with errors if any
    return [
        'success' => false,
        'errors' => $errors
    ];
}

// Test the function with sample data
$apiToken = 'YOUR_API_TOKEN'; // Replace with your actual API token
$result = processPostEx(
    'Karachi', 
    'John Doe', 
    '1234567890', 
    '123 Main St, Karachi (1234567890)', 
    1500.00, 
    '[2 x Product A, 1 x Product B]', 
    'SM-123', 
    'Normal', 
    '004', 
    2.5, 
    'Fragile items',
    $apiToken // Pass the API token
);

// Output the result of the API call
if ($result['success']) {
    echo "Order processed successfully! Tracking Number: " . $result['trackingNumber'] . " Status: " . $result['orderStatus'];
} else {
    echo "Errors occurred:\n" . implode("\n", $result['errors']);
}
?>
