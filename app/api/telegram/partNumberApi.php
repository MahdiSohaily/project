<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';


if (isset($_POST['searchPartNumbers'])) {
    $pattern = $_POST['pattern'];
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

    echo json_encode(searchPartNumbers($pattern));
}

function searchPartNumbers($search)
{
    $sql = "SELECT * FROM telegram.goods_for_sell WHERE partnumber LIKE :search";
    $stmt = PDO_CONNECTION->prepare($sql);
    $search = '%' . $search . '%';
    $stmt->bindParam(':search', $search);
    $stmt->execute();
    $partNumbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $partNumbers;
}

if (isset($_POST['search'])) {
    $pattern = $_POST['pattern'];
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

    echo json_encode(getMatchedPartNumbers($pattern));
}

function getMatchedPartNumbers($search)
{
    $sql = "SELECT * FROM yadakshop.nisha WHERE partnumber LIKE :search";
    $stmt = PDO_CONNECTION->prepare($sql);
    $search = '%' . $search . '%';
    $stmt->bindParam(':search', $search);
    $stmt->execute();
    $partNumbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $partNumbers;
}

if (isset($_POST['addPartNumber'])) {
    $addPartNumber = $_POST['addPartNumber'];
    $selectedPartNumber = json_decode($_POST['selectedPartNumber']);

    // Set content type to JSON
    header("Content-Type: application/json"); // Allow requests from any origin
    echo json_encode(addGoodsForSell($selectedPartNumber));
}

function checkIfAlreadyExist($partNumber)
{
    // Prepare the SQL statement
    $sql = "SELECT * FROM telegram.goods_for_sell WHERE partNumber = :partNumber";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->bindParam(':partNumber', $partNumber);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    return count($result) > 0;
}

function addGoodsForSell($good)
{
    if (checkIfAlreadyExist($good->partNumber)) {
        return "exists";
    }

    $nishaId = findRelation($good->id);

    if (!$nishaId) {
        // If good_id does not exist, proceed with insertion
        $sql = "INSERT INTO telegram.goods_for_sell (good_id, partNumber) VALUES (:good_id , :partNumber)";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':good_id', $good->id);
        $stmt->bindParam(':partNumber', $good->partNumber);

        // Execute the prepared statement
        if ($stmt->execute()) {
            return 'true'; // Insertion successful
        } else {
            return 'false'; // Insertion failed
        }
    } else {
        $relatedItems = getInRelationItems($nishaId);
        if ($relatedItems) {
            foreach ($relatedItems as $item) {
                if (checkIfAlreadyExist($item['partnumber'])) {
                    continue; //
                }
                $sql = "INSERT INTO telegram.goods_for_sell (good_id, partNumber) VALUES (:good_id, :partNumber)";
                $stmt = PDO_CONNECTION->prepare($sql);
                $stmt->bindParam(':good_id', $item['id']);
                $stmt->bindParam(':partNumber', $item['partnumber']);
                $stmt->execute();
            }
            return 'true'; // Insertion successful
        }
    }
}

function findRelation($id)
{
    // Prepare and execute the SQL query
    $sql = "SELECT pattern_id FROM shop.similars WHERE nisha_id = :nisha_id LIMIT 1";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':nisha_id', $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if there are any rows returned
    if ($result && count($result) > 0) {
        return (int) $result['pattern_id']; // Convert to integer and return
    } else {
        // No rows found, return false
        return false;
    }
}

function getInRelationItems($nisha_id)
{
    // Fetch similar items based on the provided nisha_id
    $sql = "SELECT nisha_id FROM shop.similars WHERE pattern_id = :nisha_id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':nisha_id', $nisha_id);
    $stmt->execute();
    $goods = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $all_ids = array_column($goods, 'nisha_id');

    if (count($all_ids) == 0) {
        return false;
    }

    // Prepare the list of IDs to use in the IN clause of the next query
    $idList = implode(',', $all_ids);

    // Fetch part numbers of the related items
    $partNumberSQL = "SELECT id, partnumber FROM yadakshop.nisha WHERE id IN (:idList)";
    $partNumberResult = PDO_CONNECTION->prepare($partNumberSQL);
    $partNumberResult->bindParam(':idList', $idList);
    $partNumberResult->execute();

    return $partNumberResult->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['deleteGood'])) {
    $deleteGood = $_POST['deleteGood'];
    $id = $_POST['id'];

    // Set content type to JSON
    header("Content-Type: application/json"); // Allow requests from any origin
    echo json_encode(deleteGoodsForSell($id));
}

function deleteGoodsForSell($id)
{
    $sql = "DELETE FROM telegram.goods_for_sell WHERE id = :id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':id', $id);

    // Execute the prepared statement
    if ($stmt->execute()) {
        return 'true'; // Deletion successful
    } else {
        return 'false'; // Deletion failed
    }
}

if (isset($_POST['getPartialsSelectedGoods'])) {
    $page = $_POST['page'];

    header('Content-Type: application/json');
    echo getPartialsSelectedGoods($page);
}

function getPartialsSelectedGoods($page)
{
    $offset = ($page - 1) * 50;
    $sql = "SELECT * FROM telegram.goods_for_sell LIMIT 50 OFFSET :offset";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statement->execute();
    $goods = $statement->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($goods);
}

if (isset($_POST['getSelectedGoodsCount'])) {
    header('Content-Type: application/json');
    echo getSelectedGoodsCount();
}

function getSelectedGoodsCount()
{
    $sql = "SELECT COUNT(id) AS total FROM telegram.goods_for_sell";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->execute();
    $goods = $statement->fetch();
    return json_encode($goods['total']);
}
