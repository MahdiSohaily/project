<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';


if (isset($_POST['edit_purchase'])) {

    echo saveChanges($_POST);
}
function cleanInput($input)
{
    // Trim whitespace from the beginning and end
    $input = trim($input);

    // Remove line breaks (both Unix and Windows style)
    $input = str_replace(["\r", "\n"], '', $input);

    // Optionally remove unwanted characters (e.g., special characters)
    // Here, I'm removing non-alphanumeric characters, but you can customize this
    $input = preg_replace('/[^A-Za-z0-9\- ]/', '', $input);

    return $input;
}
function saveChanges($data)
{
    global $stock;
    $record_id = $data['id'];
    $field = cleanInput($data['field']);
    $value = cleanInput($data['value']);

    $base_query = "UPDATE $stock.qtybank SET $field = :value WHERE id = :record_id";

    // Assuming PDO_CONNECTION is a PDO instance
    $statement = PDO_CONNECTION->prepare($base_query);
    $statement->bindParam(':value', $value);
    $statement->bindParam(':record_id', $record_id);

    // Execute the statement
    $statement->execute();

    // Check for success
    if ($statement->rowCount() > 0) {
        return true;
    } else {
        return false;
    }
}
