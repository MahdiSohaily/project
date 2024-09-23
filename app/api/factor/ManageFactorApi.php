<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['action']) && $_POST['action'] == 'filterFactors') {
    $customer = $_POST['customer'];
    $factor = $_POST['factor'];
    $startDate = $_POST['startDate'];

    $customers = null;
    $factors = null;

    if (!empty($customer)) {
        $customer = trim($customer);
        $customers = getMatchedCustomers($customer);
    }

    if (!empty($factor)) {
        $factor = trim($factor);
        $factors = getMatchedBillDetails($factor);
        print_r($factors);
    }
    
    $getMatchedFactors = getMatchedFactors($customers, $factor, $startDate);
}

function getMatchedCustomers($pattern)
{
    try {
        $parts = mb_split(' ', $pattern);
        $name = $parts[0];
        $family = isset($parts[1]) ? $parts[1] : '';

        $sql = "SELECT id FROM callcenter.customer WHERE (name LIKE :namePattern OR family LIKE :namePattern)";

        if (!empty($family)) {
            $sql .= " AND (family LIKE :familyPattern)";
        }

        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindValue(':namePattern', '%' . $name . '%', PDO::PARAM_STR);

        if (!empty($family)) {
            $stmt->bindValue(':familyPattern', '%' . $family . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        $customersId = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $customersId;
        
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return [];
    }
}

function getMatchedBillDetails($factor)
{
    try {
        // Decode the Unicode escape sequences in the search term
        $decodedFactor = json_decode('"' . $factor . '"');

        // Prepare the SQL query with LIKE operator for substring search
        $sql = "SELECT bill_id FROM factor.bill_details
                WHERE billDetails LIKE :factor COLLATE utf8mb4_general_ci";

        // Debugging: Print the SQL query to ensure it looks correct
        error_log('SQL query: ' . $sql);

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Add wildcards for partial matching
        $factor = '%' . addslashes($decodedFactor) . '%';

        // Debugging: Print the factor after adding wildcards
        error_log('Search factor with wildcards: ' . $factor);

        // Bind the parameter
        $stmt->bindParam(':factor', $factor, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Fetch the results
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debugging: Print the fetched data
        error_log('Fetched data: ' . print_r($data, true));

        // Return the bill_ids
        return array_column($data, 'bill_id');
    } catch (PDOException $e) {
        // Handle the exception (log it, rethrow it, etc.)
        error_log('Database query error: ' . $e->getMessage());
        return [];
    }
}




function getMatchedFactors($customer, $factor, $startDate)
{
    try {
        $baseSql = "SELECT customer.name, customer.family, bill.id, bill.bill_number, bill.quantity, bill.bill_date, bill.total, bill.user_id
                    FROM factor.bill
                    INNER JOIN callcenter.customer ON customer_id = customer.id
                    WHERE DATE(bill.created_at) = :startDate
                    AND status = 1";

        if ($customer !== 'all') {
            $baseSql .= " AND bill.user_id = :customer";
        }

        if ($factor !== 'all') {
            $baseSql .= " AND bill.bill_number = :factor";
        }

        $baseSql .= " ORDER BY bill.created_at DESC";

        $stmt = PDO_CONNECTION->prepare($baseSql);
        $stmt->bindParam(':startDate', $startDate);

        if ($customer !== 'all') {
            $stmt->bindParam(':customer', $customer);
        }

        if ($factor !== 'all') {
            $stmt->bindParam(':factor', $factor);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    } catch (PDOException $e) {
        // Log the error and handle it appropriately
        error_log('Database error: ' . $e->getMessage());
    }
}
