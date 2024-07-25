<?php
/**
 * PostEx API - Bulk Order Tracking (https://api.postex.pk/services/integration/api/order/v1/track-bulk-order/)
 * 
 * Retrieves detailed shipment information for multiple tracking numbers from the PostEx API.
 *
 * @param array $trackingNumbers An array of tracking numbers.
 * @return array The detailed status of each shipment or an error message.
 */
function callBulkAPIAndGetStatus($trackingNumbers) {
    // Base API URL for bulk tracking
    $endPoint = 'https://api.postex.pk/services/integration/api/order/v1/track-bulk-order';
    
    // API token for authentication (use environment variable for security)
    $token = 'YOUR_API_TOKEN'; // Replace with your actual API token
    
    // Check if token is set
    if (!$token) {
        return [
            'success' => false,
            'error' => 'API token is not set.'
        ];
    }
    
    // Prepare the query parameters
    $queryParams = http_build_query([
        'TrackingNumbers' => implode(',', $trackingNumbers) // Convert array to comma-separated string
    ]);
    
    // Construct the full URL with query parameters
    $url = $endPoint . '?' . $queryParams;

    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true, // Use GET method
        CURLOPT_HTTPHEADER => [
            'token: ' . $token,
            'Content-Type: application/json'
        ],
    ]);

    $response = curl_exec($curl);

    // Check for cURL errors
    if ($response === false) {
        curl_close($curl); // Close cURL resource
        return [
            'success' => false,
            'error' => 'Curl error: ' . curl_error($curl)
        ];
    }

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Close cURL
    curl_close($curl);

    // Log the raw response for debugging
    error_log(print_r($responseData, true)); // Log response to error log for inspection

    // Check if the response contains statusCode
    if (isset($responseData['statusCode']) && $responseData['statusCode'] === "200") {
        // Process each tracking response
        $trackingResponses = $responseData['dist'];
        $results = [];

        foreach ($trackingResponses as $response) {
            if (isset($response['trackingResponse'])) {
                $trackingResponse = $response['trackingResponse'];
                $results[] = [
                    'trackingNumber' => $response['trackingNumber'],
                    'customerName' => $trackingResponse['customerName'],
                    'customerPhone' => $trackingResponse['customerPhone'],
                    'deliveryAddress' => $trackingResponse['deliveryAddress'],
                    'invoicePayment' => $trackingResponse['invoicePayment'],
                    'orderDetail' => $trackingResponse['orderDetail'],
                    'orderRefNumber' => $trackingResponse['orderRefNumber'],
                    'transactionTax' => $trackingResponse['transactionTax'],
                    'transactionFee' => $trackingResponse['transactionFee'],
                    'transactionDate' => $trackingResponse['transactionDate'],
                    'upfrontPayment' => $trackingResponse['upfrontPayment'],
                    'merchantName' => $trackingResponse['merchantName'],
                    'transactionStatus' => $trackingResponse['transactionStatus'],
                    'reversalTax' => $trackingResponse['reversalTax'],
                    'reversalFee' => $trackingResponse['reversalFee'],
                    'cityName' => $trackingResponse['cityName'],
                    'transactionNotes' => $trackingResponse['transactionNotes'],
                    'balancePayment' => $trackingResponse['balancePayment'],
                    'items' => $trackingResponse['items'],
                    'invoiceDivision' => $trackingResponse['invoiceDivision'],
                    'message' => $response['message']
                ];
            } else {
                $results[] = [
                    'trackingNumber' => $response['trackingNumber'],
                    'error' => 'Tracking response not found'
                ];
            }
        }

        return [
            'success' => true,
            'results' => $results
        ];
    } else {
        // Handle error status and message
        return [
            'success' => false,
            'error' => isset($responseData['statusMessage']) ? 'Error: ' . $responseData['statusMessage'] : 'Unknown error'
        ];
    }
}

// Example usage
$trackingNumbers = [
    'TrackingNumber1',
    'TrackingNumber2',
    'TrackingNumber3'
];

$result = callBulkAPIAndGetStatus($trackingNumbers);

if ($result['success']) {
    foreach ($result['results'] as $trackingInfo) {
        if (isset($trackingInfo['error'])) {
            echo "Error for Tracking Number " . $trackingInfo['trackingNumber'] . ": " . $trackingInfo['error'] . "<br>";
        } else {
            echo "Tracking Number: " . $trackingInfo['trackingNumber'] . "<br>";
            echo "Customer Name: " . $trackingInfo['customerName'] . "<br>";
            echo "Customer Phone: " . $trackingInfo['customerPhone'] . "<br>";
            echo "Delivery Address: " . $trackingInfo['deliveryAddress'] . "<br>";
            echo "Invoice Payment: " . $trackingInfo['invoicePayment'] . "<br>";
            echo "Order Detail: " . $trackingInfo['orderDetail'] . "<br>";
            echo "Order Ref Number: " . $trackingInfo['orderRefNumber'] . "<br>";
            echo "Transaction Tax: " . $trackingInfo['transactionTax'] . "<br>";
            echo "Transaction Fee: " . $trackingInfo['transactionFee'] . "<br>";
            echo "Transaction Date: " . $trackingInfo['transactionDate'] . "<br>";
            echo "Upfront Payment: " . $trackingInfo['upfrontPayment'] . "<br>";
            echo "Merchant Name: " . $trackingInfo['merchantName'] . "<br>";
            echo "Transaction Status: " . $trackingInfo['transactionStatus'] . "<br>";
            echo "Reversal Tax: " . $trackingInfo['reversalTax'] . "<br>";
            echo "Reversal Fee: " . $trackingInfo['reversalFee'] . "<br>";
            echo "City Name: " . $trackingInfo['cityName'] . "<br>";
            echo "Transaction Notes: " . $trackingInfo['transactionNotes'] . "<br>";
            echo "Balance Payment: " . $trackingInfo['balancePayment'] . "<br>";
            echo "Items: " . $trackingInfo['items'] . "<br>";
            echo "Invoice Division: " . $trackingInfo['invoiceDivision'] . "<br>";
            echo "Message: " . $trackingInfo['message'] . "<br>";
            echo "<hr>";
        }
    }
} else {
    echo "Errors occurred:\n" . $result['error'];
}
?>
