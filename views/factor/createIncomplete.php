<?php
$pageTitle = "ویرایش فاکتور";
$iconUrl = 'factor.svg';
require_once './components/header.php';
require_once '../../app/models/factor/Bill.php';
$dateTime = convertPersianToEnglish(jdate('Y/m/d'));

if ($_GET['phone']) {
    $customer = getCustomer($_GET['phone']);
    $incompleteBillId = null;
    $type = $_GET['type'] ?? 0;

    if ($customer) {
        $incompleteBillId = createBill([
            'customer_id' => $customer,
            'bill_number' => 0,
            'quantity' => 0,
            'discount' => 0,
            'tax' => 0,
            'withdraw' => 0,
            'total' => 0,
            'date' => $dateTime,
            'partner' => $type,
            'totalInWords' => null
        ]);
    } else {
        $customerId = createCustomer($_GET['phone']);
        $incompleteBillId = createBill([
            'customer_id' => $customerId,
            'bill_number' => 0,
            'quantity' => 0,
            'discount' => 0,
            'tax' => 0,
            'withdraw' => 0,
            'total' => 0,
            'date' => $dateTime,
            'partner' => $type,
            'totalInWords' => null
        ]);
    }

    $incompleteBillDetails = createBillItemsTable(
        $incompleteBillId,
        '[{
        "id": 5892295,
        "partName": "اسم قطعه",
        "price_per": 0,
        "quantity": 1,
        "max": "undefined",
        "partNumber": "NOTPART"
        }]'
    );
    $phone = $_GET['phone'];
    header("Location: /YadakShop-APP/views/factor/incomplete.php?factor_number=$incompleteBillId");
}

function convertPersianToEnglish($string)
{
    $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    return str_replace($persianDigits, $englishDigits, $string);
}

function createBill($billInfo)
{
    try {
        $sql = "INSERT INTO factor.bill 
                (customer_id, bill_number, quantity, discount, tax, withdraw, total, bill_date, user_id, status, partner) 
                VALUES (:customer_id, :bill_number, :quantity, :discount, :tax, :withdraw, :total, :bill_date, :user_id, :status, :partner)";


        $status = 0;
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':customer_id', $billInfo['customer_id'], PDO::PARAM_INT);
        $stmt->bindParam(':bill_number', $billInfo['bill_number'], PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $billInfo['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':discount', $billInfo['discount'], PDO::PARAM_STR);
        $stmt->bindParam(':tax', $billInfo['tax'], PDO::PARAM_STR);
        $stmt->bindParam(':withdraw', $billInfo['withdraw'], PDO::PARAM_STR);
        $stmt->bindParam(':total', $billInfo['total'], PDO::PARAM_STR);
        $stmt->bindParam(':bill_date', $billInfo['date'], PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':partner', $billInfo['partner'], PDO::PARAM_INT);

        $stmt->execute();

        $lastInsertedId = PDO_CONNECTION->lastInsertId();
        $stmt->closeCursor();

        return $lastInsertedId;
    } catch (PDOException $e) {
        return false;
    }
}

function createBillItemsTable($billId, $billItems)
{
    try {
        $sql = "INSERT INTO factor.bill_details (bill_id, billDetails) VALUES (?, ?)";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->execute([$billId, $billItems]);
        $stmt->closeCursor();
    } catch (PDOException $e) {
        // Handle exception here, if needed
    }
}

function getCustomer($phone)
{
    try {
        $sql = "SELECT id FROM callcenter.customer WHERE phone = :phone LIMIT 1";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($customer)
            return $customer['id'];
        else
            return false;
    } catch (PDOException $e) {
        return false;
    }
}

function createCustomer($phone)
{
    $name = '';
    try {
        $sql = "INSERT INTO callcenter.customer (phone, name) VALUES (:phone, :name)";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $lastInsertedId = PDO_CONNECTION->lastInsertId();
        $stmt->closeCursor();
        return $lastInsertedId;
    } catch (PDOException $e) {
        return false;
    }
}
