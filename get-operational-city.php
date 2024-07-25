<?php
/**
 * PostEx API - Get Operational Cities (https://api.postex.pk/services/integration/api/order/v2/get-operational-city)
 * 
 * Fetch Operational Cities from PostEx API
 * 
 * Retrieves a list of operational cities from the PostEx API.
 *
 * @return array|false List of operational city names or an error message.
 */
function fetchOperationalCities() {
    // API endpoint and token
    $apiEndpoint = 'https://api.postex.pk/services/integration/api/order/v2/get-operational-city';
    $apiToken = 'YOUR_API_TOKEN'; // Replace with your actual API token

    // Parameters for the GET request
    $params = [
        'type' => 'ALL'  // Adjust this as needed, or remove if not required
    ];

    // Create the query string from parameters
    $queryString = http_build_query($params);

    // Initialize cURL
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiEndpoint . '?' . $queryString,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'token: ' . $apiToken,
            'Content-Type: application/json'
        ]
    ]);

    // Execute the cURL request
    $apiResponse = curl_exec($curl);

    // Check for cURL errors
    if ($apiResponse === false) {
        curl_close($curl);
        return 'Curl error: ' . curl_error($curl);
    }

    // Decode the JSON response
    $responseData = json_decode($apiResponse, true);

    // Close cURL resource
    curl_close($curl);

    // Check if response data is valid
    if (json_last_error() === JSON_ERROR_NONE) {
        // Return the full JSON response
        return $responseData;
    } else {
        return 'Failed to decode JSON response: ' . json_last_error_msg();
    }
}

// Fetch the operational cities data
$response = fetchOperationalCities();

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
?>
