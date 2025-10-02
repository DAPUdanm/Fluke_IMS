<?php
require_once 'dbconnection.php';
require_once 'proposal_functions.php';

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set the content type to JSON
header('Content-Type: application/json');

try {
    // Log the raw input
    $rawInput = file_get_contents('php://input');
    error_log("Received data: " . $rawInput);

    // Decode JSON input
    $data = json_decode($rawInput, true);
    
    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }

    // Validate required fields
    $requiredFields = ['customerName', 'customerAddress', 'contactNumber', 'validityDate', 'terms', 'items'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            throw new Exception("Missing required field: {$field}");
        }
    }

    // Validate items array
    if (!is_array($data['items']) || empty($data['items'])) {
        throw new Exception('At least one item is required');
    }

    // Log the decoded data
    error_log("Processed data: " . print_r($data, true));

    // Create the proposal
    $result = createProposal(
        $data['customerName'],
        $data['customerAddress'],
        $data['contactNumber'],
        $data['validityDate'],
        $data['items'],
        $data['terms']
    );

    // Log the result
    error_log("Creation result: " . print_r($result, true));

    if (!$result['success']) {
        throw new Exception($result['message'] ?? 'Unknown error occurred');
    }

    echo json_encode($result);

} catch (Exception $e) {
    // Log the error
    error_log("Proposal creation error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());

    // Send error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_details' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
