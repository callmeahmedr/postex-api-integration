<?php
/**
 * PostEx API - Single Order Tracking (https://api.postex.pk/services/integration/api/order/v1/track-order/)
 *
 * Retrieves detailed shipment information from the PostEx API.
 *
 * @param string $trackingNumber The tracking number of the shipment.
 * @return array The detailed status of the shipment or an error message.
 */
function callAPIAndGetStatus($trackingNumber) {
    // API URL for tracking
    $endPoint = 'https://api.postex.pk/services/integration/api/order/v1/track-order/' . urlencode($trackingNumber);

    // API token for authentication
    $token = 'YOUR_API_TOKEN'; // Replace with your actual API token

    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endPoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'token: ' . $token
        ],
    ]);

    // Execute the request
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

    // Check if the response contains statusCode
    if (isset($responseData['statusCode']) && $responseData['statusCode'] === "200") {
        // Check if the response contains the expected data
        if (isset($responseData['dist'])) {
            $dist = $responseData['dist'];
            return [
                'success' => true,
                'customerName' => $dist['customerName'],
                'customerPhone' => $dist['customerPhone'],
                'deliveryAddress' => $dist['deliveryAddress'],
                'invoicePayment' => $dist['invoicePayment'],
                'orderDetail' => $dist['orderDetail'],
                'orderRefNumber' => $dist['orderRefNumber'],
                'transactionTax' => $dist['transactionTax'],
                'transactionFee' => $dist['transactionFee'],
                'trackingNumber' => $dist['trackingNumber'],
                'transactionDate' => $dist['transactionDate'],
                'upfrontPayment' => $dist['upfrontPayment'],
                'merchantName' => $dist['merchantName'],
                'transactionStatus' => $dist['transactionStatus'],
                'reversalTax' => $dist['reversalTax'],
                'reversalFee' => $dist['reversalFee'],
                'reservePayment' => $dist['reservePayment'],
                'reservePaymentDate' => $dist['reservePaymentDate'],
                'balancePayment' => $dist['balancePayment'],
                'cityName' => $dist['cityName'],
                'transactionNotes' => $dist['transactionNotes'],
                'transactionStatusHistory' => $dist['transactionStatusHistory']
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Status information not found in response'
            ];
        }
    } else {
        // Handle error status and message
        return [
            'success' => false,
            'error' => isset($responseData['statusMessage']) ? 'Error: ' . $responseData['statusMessage'] : 'Unknown error'
        ];
    }
}

// Example usage
$trackingNumber = 'example_tracking_number'; // Replace with actual tracking number
$result = callAPIAndGetStatus($trackingNumber);
if ($result['success']) {
    echo "Customer Name: " . $result['customerName'] . "<br>";
    echo "Customer Phone: " . $result['customerPhone'] . "<br>";
    echo "Delivery Address: " . $result['deliveryAddress'] . "<br>";
    echo "Invoice Payment: " . $result['invoicePayment'] . "<br>";
    echo "Order Detail: " . $result['orderDetail'] . "<br>";
    echo "Order Ref Number: " . $result['orderRefNumber'] . "<br>";
    echo "Transaction Tax: " . $result['transactionTax'] . "<br>";
    echo "Transaction Fee: " . $result['transactionFee'] . "<br>";
    echo "Tracking Number: " . $result['trackingNumber'] . "<br>";
    echo "Transaction Date: " . $result['transactionDate'] . "<br>";
    echo "Upfront Payment: " . $result['upfrontPayment'] . "<br>";
    echo "Merchant Name: " . $result['merchantName'] . "<br>";
    echo "Transaction Status: " . $result['transactionStatus'] . "<br>";
    echo "Reversal Tax: " . $result['reversalTax'] . "<br>";
    echo "Reversal Fee: " . $result['reversalFee'] . "<br>";
    echo "Reserve Payment: " . $result['reservePayment'] . "<br>";
    echo "Reserve Payment Date: " . $result['reservePaymentDate'] . "<br>";
    echo "Balance Payment: " . $result['balancePayment'] . "<br>";
    echo "City Name: " . $result['cityName'] . "<br>";
    echo "Transaction Notes: " . $result['transactionNotes'] . "<br>";
    echo "Transaction Status History: <br>";
    foreach ($result['transactionStatusHistory'] as $status) {
        echo "- " . $status['transactionStatusMessage'] . " (Code: " . $status['transactionStatusMessageCode'] . ")<br>";
    }
} else {
    echo "Errors occurred:\n" . $result['error'];
}
?>
