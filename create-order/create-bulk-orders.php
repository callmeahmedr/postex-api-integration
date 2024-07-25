<?php
/**
 * PostEx API - Bulk Order Booking (https://api.postex.pk/services/integration/api/order/v3/create-order)
 *
 * Processes multiple orders by sending data to the PostEx API.
 *
 * @param array $orders Array of orders, each containing details required by the API.
 * @param string $apiToken API token for authentication.
 * @return array Results of the API calls, including success status and tracking information for each order.
 */
function processBulkOrders(array $orders, $apiToken) {
    $results = []; // Array to store results of each order processing
    $errors = [];  // Array to store any errors

    foreach ($orders as $order) {
        // Prepare data for API request
        $requestData = [
            'cityName' => $order['city'],
            'customerName' => $order['customerName'],
            'customerPhone' => $order['customerPhone'],
            'deliveryAddress' => $order['deliveryAddress'],
            'invoicePayment' => $order['invoicePayment'],
            'orderDetail' => $order['orderDetail'],
            'orderRefNumber' => $order['orderRefNumber'],
            'orderType' => $order['orderType'],
            'pickupAddressCode' => $order['pickupAddressCode'],
            'bookingWeight' => $order['bookingWeight'],
            'transactionNotes' => $order['transactionNotes']
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
            $errors[] = 'Curl error for Order Ref ' . $order['orderRefNumber'] . ': ' . curl_error($curl);
            $results[$order['orderRefNumber']] = ['success' => false, 'errors' => [$errors[count($errors) - 1]]];
        } else {
            // Decode the JSON response from the API
            $responseData = json_decode($apiResponse, true);

            // Check if the response contains the tracking number
            if (isset($responseData['dist']['trackingNumber'])) {
                // API call succeeded
                $trackingNumber = $responseData['dist']['trackingNumber'];
                $orderStatus = $responseData['dist']['orderStatus'];
                $results[$order['orderRefNumber']] = [
                    'success' => true,
                    'trackingNumber' => $trackingNumber,
                    'orderStatus' => $orderStatus
                ];
            } else {
                $errors[] = 'API did not return tracking number for Order Ref ' . $order['orderRefNumber'];
                $results[$order['orderRefNumber']] = ['success' => false, 'errors' => [$errors[count($errors) - 1]]];
            }
        }

        // Close cURL session
        curl_close($curl);
    }

    // Return all results and errors
    return [
        'results' => $results,
        'errors' => $errors
    ];
}

// Sample bulk orders data
$apiToken = 'YOUR_API_TOKEN'; // Replace with your actual API token
$orders = [
    [
        'city' => 'Karachi',
        'customerName' => 'John Doe',
        'customerPhone' => '1234567890',
        'deliveryAddress' => '123 Main St, Karachi (1234567890)',
        'invoicePayment' => 1500.00,
        'orderDetail' => '[2 x Product A, 1 x Product B]',
        'orderRefNumber' => 'SM-123',
        'orderType' => 'Normal',
        'pickupAddressCode' => '004',
        'bookingWeight' => 2.5,
        'transactionNotes' => 'Fragile items'
    ],
    // Add more orders here...
];

// Process the bulk orders
$result = processBulkOrders($orders, $apiToken);

// Output the results
foreach ($result['results'] as $orderRef => $info) {
    if ($info['success']) {
        echo "Order Ref $orderRef processed successfully! Tracking Number: " . $info['trackingNumber'] . " Status: " . $info['orderStatus'] . "\n";
    } else {
        echo "Errors occurred for Order Ref $orderRef:\n" . implode("\n", $info['errors']) . "\n";
    }
}

// Output all errors
if (!empty($result['errors'])) {
    echo "Global errors:\n" . implode("\n", $result['errors']) . "\n";
}
?>
