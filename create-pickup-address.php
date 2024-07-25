<?php
/**
 * PostEx API - Create Pickup Address (https://api.postex.pk/services/integration/api/order/v2/create-merchant-address)
 * 
 * Creates a new pickup address in the PostEx system.
 *
 * @param string $address The warehouse physical address.
 * @param int $addressTypeId The type of address (1 for Return, 2 for Pickup).
 * @param string $cityName The city of the warehouse.
 * @param string $contactPersonName The contact person's name at the warehouse.
 * @param string $phone1 The primary phone number of the warehouse.
 * @param string $phone2 The secondary phone number of the warehouse.
 * @param string $phone3 (Optional) The manager's phone number.
 * @param string $wareHouseManagerName (Optional) The warehouse manager's name.
 * @return array The response from the API or an error message.
 */
function createPickupAddress($address, $addressTypeId, $cityName, $contactPersonName, $phone1, $phone2, $phone3 = null, $wareHouseManagerName = null) {
    // API URL for creating pickup address
    $endPoint = 'https://api.postex.pk/services/integration/api/order/v2/create-merchant-address';
    
    // API token for authentication
    $token = 'YOUR_API_TOKEN'; // Replace with your actual API token
    
    // Prepare the request payload
    $payload = [
        'address' => $address,
        'addressTypeId' => $addressTypeId,
        'cityName' => $cityName,
        'contactPersonName' => $contactPersonName,
        'phone1' => $phone1,
        'phone2' => $phone2,
        'phone3' => $phone3,
        'wareHouseManagerName' => $wareHouseManagerName
    ];
    
    // Initialize cURL
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endPoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'token: ' . $token
        ]
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

    // Log only on error
    if (isset($responseData['statusCode']) && $responseData['statusCode'] !== 200) {
        error_log('Error Response: ' . print_r($responseData, true)); // Log error details
        return [
            'success' => false,
            'error' => isset($responseData['statusMessage']) ? 'Error: ' . $responseData['statusMessage'] : 'Unknown error'
        ];
    }

    // Log success response with detail
    if (isset($responseData['statusCode']) && $responseData['statusCode'] === 200) {
        error_log('Success Response: ' . print_r($responseData, true)); // Log success details
        return [
            'success' => true,
            'message' => $responseData['statusMessage'],
            'pickupAddressCode' => isset($responseData['dist']['pickupAddressCode']) ? $responseData['dist']['pickupAddressCode'] : null
        ];
    }

    // Handle unexpected response
    error_log('Unexpected Response: ' . print_r($responseData, true)); // Log unexpected details
    return [
        'success' => false,
        'error' => 'Unexpected response format'
    ];
}

// Example usage
$address = '123 Warehouse St';
$addressTypeId = 2; // 2 for Pickup or 1 for Return
$cityName = 'Lahore';
$contactPersonName = 'Muhammad Ahmed';
$phone1 = '1234567890';
$phone2 = '0987654321';
$phone3 = '1122334455'; // Optional
$wareHouseManagerName = 'Awais Jahaz'; // Optional

$result = createPickupAddress($address, $addressTypeId, $cityName, $contactPersonName, $phone1, $phone2, $phone3, $wareHouseManagerName);

if ($result['success']) {
    echo "Pickup address created successfully: " . $result['message'];
    echo "Pickup Address Code: " . $result['pickupAddressCode'];
} else {
    echo $result['error'];
}
?>
