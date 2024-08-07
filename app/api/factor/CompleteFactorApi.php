<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';


// START ------------------ UPDATE THE COMPLETE BILL -----------------------------
if (isset($_POST['GenerateCompleteFactor'])) {
    $customerInfo = json_decode($_POST['customerInfo']);
    $factorInfo = json_decode($_POST['factorInfo']);
    $factorItems = json_decode($_POST['factorItems']);

    $customer_id = getCustomerId($customerInfo) ? getCustomerId($customerInfo) : null;
    $success = true; // Initialize success variable

    try {
        PDO_CONNECTION->beginTransaction();
        if (!$customer_id) {
            $customer_id = createCustomer($customerInfo);
        } else {
            updateCustomer($customerInfo, $customer_id);
        }

        if ($customer_id == null) {
            throw new Exception("Customer ID is null");
        }

        $factorNumber = getFactorNumber();
        registerFactorNumber($factorNumber, $customerInfo);

        CreateCompleteBill($factorInfo, $customer_id, $factorNumber);
        CreateBillItems($factorInfo, $factorItems);
        PDO_CONNECTION->commit();
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        PDO_CONNECTION->rollback();
        $success = false; // Set success to false if an error occurred
    }

    // Output based on transaction success
    if ($success) {
        echo "true";
    } else {
        echo "false";
    }
}

if (isset($_POST['updateCompleteFactor'])) {
    $customerInfo = json_decode($_POST['customerInfo']);
    $factorInfo = json_decode($_POST['factorInfo']);
    $factorItems = json_decode($_POST['factorItems']);

    $customer_id = getCustomerId($customerInfo) ? getCustomerId($customerInfo) : null;
    $success = true; // Initialize success variable

    try {
        PDO_CONNECTION->beginTransaction();

        if (!$customer_id) {
            $customer_id = createCustomer($customerInfo);
        } else {
            updateCustomer($customerInfo, $customer_id);
        }

        if ($customer_id == null) {
            throw new Exception("Customer ID is null");
        }
        updateRegisteredFactorNumber($factorInfo->billNO, $customerInfo);
        UpdateCompletedBill($factorInfo, $customer_id);
        CreateBillItems($factorInfo, $factorItems);

        PDO_CONNECTION->commit();
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        PDO_CONNECTION->rollback();
        $success = false; // Set success to false if an error occurred
    }
    echo $success;
    // Output based on transaction success
    if ($success) {
        echo "true";
    } else {
        echo "false";
    }
}

function getCustomerId($customer)
{
    $sql = "SELECT id FROM callcenter.customer WHERE phone = :phone ORDER BY id DESC LIMIT 1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(":phone", $customer->phone);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        return $result['id'];
    } else {
        return false;
    }
}

function createCustomer($customer)
{
    // Extract customer details with fallback to empty strings if properties are not set
    $name = $customer->name ?? "";
    $family = $customer->family ?? "";
    $phone = $customer->phone ?? "";
    $address = $customer->address ?? "";
    $car = $customer->car ?? "";

    // Prepare the SQL query
    $sql = "INSERT INTO callcenter.customer (name, family, phone, address, car) VALUES (:name, :family, :phone, :address, :car)";

    try {
        // Prepare the PDO statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':family', $family, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':car', $car, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Return the last inserted ID
        return PDO_CONNECTION->lastInsertId();
    } catch (PDOException $e) {
        // Handle any errors (e.g., log them, rethrow them, return a specific error message, etc.)
        // For demonstration purposes, we'll just rethrow the exception
        throw $e;
    }
}

function updateCustomer($customer)
{
    // Extract customer details with fallback to empty strings if properties are not set
    $name = $customer->name ?? "";
    $family = $customer->family ?? "";
    $phone = $customer->phone ?? "";
    $address = $customer->address ?? "";
    $car = $customer->car ?? "";
    $id = $customer->id ?? "";

    // Prepare the SQL query
    $sql = "UPDATE callcenter.customer
                    SET name = :name, family = :family, phone = :phone,
                    address = :address, car = :car
                    WHERE id = :id";

    try {
        // Prepare the PDO statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':family', $family, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);
        $stmt->bindParam(':car', $car, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();
    } catch (PDOException $e) {
        // Handle any errors (e.g., log them, rethrow them, return a specific error message, etc.)
        // For demonstration purposes, we'll just rethrow the exception
        throw $e;
    }
}

function UpdateCompletedBill($billInfo, $customerId)
{
    try {
        $user_id = $_SESSION['id'];

        $sql = "UPDATE factor.bill SET 
                customer_id = :customerId,
                quantity = :quantity,
                discount = :discount,
                tax = :tax,
                withdraw = :withdraw,
                total = :totalPrice,
                bill_date = :billDate,
                user_id = :userId,
                description = :description,
                status = 1
                WHERE id = :billId";

        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':customerId', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $billInfo->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':discount', $billInfo->discount, PDO::PARAM_STR);
        $stmt->bindParam(':tax', $billInfo->tax, PDO::PARAM_STR);
        $stmt->bindParam(':withdraw', $billInfo->withdraw, PDO::PARAM_STR);
        $stmt->bindParam(':totalPrice', $billInfo->totalPrice, PDO::PARAM_STR);
        $stmt->bindParam(':billDate', $billInfo->date, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $billInfo->description, PDO::PARAM_STR);
        $stmt->bindParam(':billId', $billInfo->id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Check if the update was successful
        $success = $stmt->rowCount() > 0;

        // Return success status
        return $success;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function CreateCompleteBill($billInfo, $customerId, $factorNumber)
{
    try {
        $user_id = $_SESSION['id'];

        $sql = "UPDATE factor.bill SET 
                customer_id = :customerId,
                bill_number = :billNumber,
                quantity = :quantity,
                discount = :discount,
                tax = :tax,
                withdraw = :withdraw,
                total = :totalPrice,
                bill_date = :billDate,
                user_id = :userId,
                description = :description,
                status = 1,
                created_at = CURRENT_TIMESTAMP
                WHERE id = :billId";

        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':customerId', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':billNumber', $factorNumber, PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $billInfo->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':discount', $billInfo->discount, PDO::PARAM_STR);
        $stmt->bindParam(':tax', $billInfo->tax, PDO::PARAM_STR);
        $stmt->bindParam(':withdraw', $billInfo->withdraw, PDO::PARAM_STR);
        $stmt->bindParam(':totalPrice', $billInfo->totalPrice, PDO::PARAM_STR);
        $stmt->bindParam(':billDate', $billInfo->date, PDO::PARAM_STR);
        $stmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $billInfo->description, PDO::PARAM_STR);
        $stmt->bindParam(':billId', $billInfo->id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Check if the update was successful
        $success = $stmt->rowCount() > 0;

        // Return success status
        return $success;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function CreateBillItems($billInfo, $billItems)
{
    try {
        // Convert bill items to JSON
        $billItemsJson = json_encode($billItems);

        // SQL query with named placeholders
        $sql = "UPDATE factor.bill_details
                SET billDetails = :billItems
                WHERE bill_id = :billId";

        // Prepare the PDO statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':billItems', $billItemsJson, PDO::PARAM_STR);
        $stmt->bindParam(':billId', $billInfo->id, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Optionally, check if the update was successful
        $success = $stmt->rowCount() > 0;

        // Return success status (optional)
        return $success;
    } catch (PDOException $e) {
        echo $e->getMessage();
        // Handle the exception (log it, return a specific error code, etc.)
        return false;
    }
}

function getFactorNumber()
{
    try {
        // Prepare the SQL statement
        $stmt = PDO_CONNECTION->prepare("SELECT shomare FROM factor.shomarefaktor ORDER BY id DESC LIMIT 1");

        // Execute the statement
        $stmt->execute();

        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if a row was fetched
        if ($row) {
            $shomare = $row['shomare'];
            return $shomare + 1;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // Handle the exception (log it, return a specific error code, etc.)
        echo $e->getMessage();
        return false;
    }
}

function registerFactorNumber($factorNumber, $customer)
{
    try {
        $user_id = $_SESSION['id'];
        $name = $customer->name ?? '';
        $family = $customer->family ?? '';

        $fullName = $name . ' ' . $family;

        // Prepare the SQL statement with placeholders
        $sql = "INSERT INTO factor.shomarefaktor (shomare, kharidar, user)
                VALUES (:shomare, :kharidar, :user)";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameters to the statement
        $stmt->bindParam(':shomare', $factorNumber);
        $stmt->bindParam(':kharidar', $fullName);
        $stmt->bindParam(':user', $user_id);

        // Execute the statement
        $success = $stmt->execute();

        // Check if the execution was successful
        return $success;
    } catch (PDOException $e) {
        // Handle the exception (log it, return a specific error code, etc.)
        echo $e->getMessage();
        return false;
    }
}

function updateRegisteredFactorNumber($factorNumber, $customer)
{
    try {
        $name = $customer->name ?? '';
        $family = $customer->family ?? '';

        $fullName = $name . ' ' . $family;

        // Prepare the SQL statement with placeholders
        $sql = "UPDATE factor.shomarefaktor 
                SET kharidar = :kharidar
                WHERE shomare = :shomare";

        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind parameters to the statement
        $stmt->bindParam(':kharidar', $fullName);
        $stmt->bindParam(':shomare', $factorNumber);

        // Execute the statement
        $stmt->execute();
    } catch (PDOException $e) {
        // Handle the exception (log it, return a specific error code, etc.)
        echo $e->getMessage();
    }
}