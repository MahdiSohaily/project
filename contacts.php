<?php
require_once './config/constants.php';
require_once './database/db_connect.php';
require_once './utilities/callcenter/DollarRateHelper.php';
// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

// Allow specified HTTP methods
header("Access-Control-Allow-Methods:POST");

// Allow specified headers
header("Access-Control-Allow-Headers: Content-Type");

// Allow credentials (cookies, authorization headers, etc.)
header("Access-Control-Allow-Credentials: true");

// Set content type to JSON
header("Content-Type: application/json"); // Allow requests from any origin

if (isset($_POST['contacts'])) {

    $allContacts = getAllContacts();
    echo json_encode($allContacts);
}

function getAllContacts()
{
    try {
        // Prepare SQL query
        $stmt = PDO_CONNECTION->prepare("SELECT name, family, phone FROM callcenter.customer");

        // Execute the query
        $stmt->execute();

        // Fetch results as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if any results were found
        if ($result) {
            return $result;
        } else {
            return []; // Return empty array if no results
        }
    } catch (PDOException $e) {
        // Handle database error
        error_log("Database error: " . $e->getMessage()); // Log error to file
        return ['error' => 'Unable to fetch contacts at this time.']; // Return error message
    }
}
