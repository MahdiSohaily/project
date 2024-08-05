<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['updateUserLabel'])) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
    echo updateUserLabel($_POST);
}

function updateUserLabel($data)
{
    $cartable_pos = $data['cartable-pos'];
    $phone = $data['phone'];

    $label = "";
    if (isset($data['label']) || !empty($data['label'])) {
        foreach ($data['label'] as $selectedLabel) {
            $label .= $selectedLabel . ",";
        }
    } else {
        $label = NULL;
    }

    $user = "";
    if (isset($data['userSelector']) || !empty($data['userSelector'])) {
        foreach ($data['userSelector'] as $selectedUser) {
            $user .= $selectedUser . ",";
        }
    } else {
        $user = NULL;
    }

    // Prepare the SQL query with placeholders
    $sql = "UPDATE callcenter.customer SET cartable=:cartable_pos, label=:label, user=:user WHERE phone LIKE :phone";

    // Prepare the statement
    $stmt = PDO_CONNECTION->prepare($sql);

    // Bind the values to the placeholders
    $stmt->bindParam(':cartable_pos', $cartable_pos);
    $stmt->bindParam(':label', $label);
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':phone', $phone);

    // Execute the prepared statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

if (isset($_POST['saveContact'])) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization");
    echo saveContact($_POST);
}

function saveContact($data)
{
    $name = $data['name'];
    $family = $data['family'];
    $phone = $data['phone'];
    $vin = $data['vin'];
    $description = $data['des'];
    $address = $data['address'];
    $kind = $data['kind'];
    $car = $data['car'];
    $callInfo = $data['callInfo'];
    $id =  $_SESSION["id"];
    $isOld = $data['isOld'];
    $pin = empty($data['pin']) ? 'unpin' : 'pin';

    if (strlen($callInfo) > 0) {
        if (saveCallRecord($phone, $callInfo, $id, $pin)) {
        }
    }
    if ($isOld == 0) {
        return insertCustomer($name, $family, $phone, $vin, $description, $address, $kind, $car);
    }

    if ($isOld == 1) {
        return updateCustomer($name, $family, $phone, $vin, $description, $address, $kind, $car);
    }
}

function saveCallRecord($phone, $callInfo, $id, $pin)
{
    // Prepare the SQL query with placeholders
    $sql = "INSERT INTO callcenter.record (phone, callinfo, user, pin) VALUES (:phone, :callInfo, :user, :pin)";

    // Prepare the statement
    $stmt = PDO_CONNECTION->prepare($sql);

    // Bind the values to the placeholders
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':callInfo', $callInfo);
    $stmt->bindParam(':user', $id);
    $stmt->bindParam(':pin', $pin);

    // Execute the prepared statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function insertCustomer($name, $family, $phone, $vin, $description, $address, $kind, $car)
{
    // Prepare the SQL query with placeholders
    $sql = "INSERT INTO callcenter.customer (name, family, phone, vin, des, address, kind, car) VALUES (:name, :family, :phone, :vin, :description, :address, :kind, :car)";

    // Prepare the statement
    $stmt = PDO_CONNECTION->prepare($sql);

    // Bind the values to the placeholders
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':family', $family);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':vin', $vin);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':kind', $kind);
    $stmt->bindParam(':car', $car);

    // Execute the prepared statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function updateCustomer($name, $family, $phone, $vin, $description, $address, $kind, $car)
{
    // Prepare the SQL query with placeholders
    $sql = "UPDATE callcenter.customer SET name = :name, family = :family, vin = :vin, des = :description, address = :address, kind = :kind, car = :car WHERE phone LIKE :phone";

    // Prepare the statement
    $stmt = PDO_CONNECTION->prepare($sql);

    // Bind the values to the placeholders
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':family', $family);
    $stmt->bindParam(':vin', $vin);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':kind', $kind);
    $stmt->bindParam(':car', $car);
    $stmt->bindParam(':phone', $phone);

    // Execute the prepared statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
