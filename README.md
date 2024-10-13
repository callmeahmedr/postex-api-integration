# PostEx API Integration

Welcome to the PostEx API Integration repository! 🚀 This project provides PHP scripts to seamlessly integrate with the PostEx API for handling both order creation and tracking functionalities.

## Overview
This repository includes PHP scripts designed to interact with the PostEx API, specifically covering endpoints for:

- **create-order**
  - create-single-order.php
  - create-bulk-orders.php
- **track-order**
  - single-order-tracking.php
  - bulk-order-tracking.php
- **create-pickup-address.php**
- **get-operational-city.php**
- **get-unbooked-orders.php**

These scripts are based on the PostEx Merchant API Integration Guide V4.1.9. For detailed API documentation, refer to the [Merchant API Integration Guide](https://merchant-api-guide.s3.ap-south-1.amazonaws.com/PostEx-COD_API_Integration_Guide_V4.1.9.pdf).

## Features
- **Order Creation**: Create single or bulk orders with their details.
- **Order Tracking**: Track single or multiple orders at once and get detailed status updates.
- **Creating Pickup/Return Address**: Creates a new pickup or return address in the PostEx system.
- **Getting List of Operational Cities**: Fetch Operational Cities from PostEx system
- **Fetch Un-booked Orders**: Retrieves un-booked order details within a specified date range and optionally filtered by city.

## Dependencies
- PHP 7.4 or higher
- cURL extension enabled (ensure it's installed and enabled in your PHP configuration)
- Access to PostEx API with a valid API token

## Contribution
We welcome suggestions and contributions to improve this integration! If you have ideas or enhancements, feel free to open an issue or submit a pull request.

Happy Coding ❤️