<?php
/**
 * PostEx API - Fetch Un-booked Orders (https://api.postex.pk/services/integration/api/order/v2/get-unbooked-orders)
 * 
 * Retrieves un-booked order details from the PostEx API within a specified date range and optionally filtered by city.
 *
 * @param string $startDate The start date for the orders to fetch.
 * @param string $endDate The end date for the orders to fetch.
 * @param string|null $cityName Optional city name to filter the orders.
 * @return array The un-booked orders or an error message.
 */
function fetchApiData($startDate, $endDate, $cityName = null) {
    // Base API URL for un-booked orders
    $endPoint = 'https://api.postex.pk/services/integration/api/order/v2/get-unbooked-orders';
    
    // API token for authentication
    $token = 'YOUR_API_TOKEN'; // Replace with your actual API token

    // Prepare query parameters
    $queryParams = [
        'startDate' => $startDate,
        'endDate' => $endDate,
        'cityName' => $cityName
    ];
    $queryParams = array_filter($queryParams); // Remove null values
    $queryString = http_build_query($queryParams);

    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endPoint . '?' . $queryString,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'token: ' . $token
        ]
    ]);

    $response = curl_exec($curl);
    $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Check for cURL errors
    if (curl_errno($curl)) {
        curl_close($curl);
        return [
            'success' => false,
            'error' => 'Curl error: ' . curl_error($curl)
        ];
    }

    // Close cURL
    curl_close($curl);

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Log the raw response for debugging
    error_log("HTTP Status: " . $httpStatus);
    error_log("API Response: " . print_r($responseData, true));

    // Check if the response contains statusCode
    if (isset($responseData['statusCode']) && $responseData['statusCode'] === "200") {
        // Process each order response
        $orders = $responseData['dist'];
        return [
            'success' => true,
            'orders' => $orders
        ];
    } else {
        // Handle error status and message
        return [
            'success' => false,
            'error' => isset($responseData['statusMessage']) ? 'Error: ' . $responseData['statusMessage'] : 'Unknown error'
        ];
    }
}

// Fetch orders based on the provided dates
$startDate = '2024-07-01';
$endDate = '2024-07-31';

$result = fetchApiData($startDate, $endDate);

// Output results
if ($result['success']) {
    $orders = $result['orders'];
    if (!empty($orders)) {
        echo "<h2>Un-booked Orders</h2>";
        echo "<table border='1'>";
        echo "<thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Delivery Address</th>
                    <th>Invoice Payment</th>
                    <th>Order Reference Number</th>
                    <th>Transaction Date</th>
                    <th>Merchant Name</th>
                    <th>Tracking Number</th>
                    <th>City Name</th>
                    <th>Transaction Status</th>
                    <th>Balance Payment</th>
                    <th>Items</th>
                    <th>Invoice Division</th>
                </tr>
              </thead>
              <tbody>";

        foreach ($orders as $order) {
            echo "<tr>
                    <td>" . htmlspecialchars($order['customerName']) . "</td>
                    <td>" . htmlspecialchars($order['customerPhone']) . "</td>
                    <td>" . htmlspecialchars($order['deliveryAddress']) . "</td>
                    <td>" . number_format($order['invoicePayment'], 2) . "</td>
                    <td>" . htmlspecialchars($order['orderRefNumber']) . "</td>
                    <td>" . htmlspecialchars($order['transactionDate']) . "</td>
                    <td>" . htmlspecialchars($order['merchantName']) . "</td>
                    <td>" . htmlspecialchars($order['trackingNumber']) . "</td>
                    <td>" . htmlspecialchars($order['cityName']) . "</td>
                    <td>" . htmlspecialchars($order['transactionStatus']) . "</td>
                    <td>" . number_format($order['balancePayment'], 2) . "</td>
                    <td>" . htmlspecialchars($order['items']) . "</td>
                    <td>" . htmlspecialchars($order['invoiceDivision']) . "</td>
                  </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>No orders found.</p>";
    }
} else {
    echo "<h2>Error</h2>";
    echo "<p>API call failed: " . htmlspecialchars($result['error']) . "</p>";
}
?>
