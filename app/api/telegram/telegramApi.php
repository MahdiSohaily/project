<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';

if (isset($_POST['selectedGoodForMessage'])) {
    $partNumber  = $_POST['partNumber'];

    $goodID = getGoodID($partNumber);
    echo addSelectedGoodForMessage($goodID, $partNumber);
}

function getGoodID($partNumber)
{
    // Prepare the SQL statement
    $sql = "SELECT id FROM yadakshop.nisha WHERE partnumber = :partNumber LIMIT 1";

    // Prepare the statement
    $statement = PDO_CONNECTION->prepare($sql);

    // Bind parameters and execute the statement
    $statement->bindParam(":partNumber", $partNumber);
    $statement->execute();

    // Fetch the result
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    // Return the ID or null if no row found
    return $row ? $row['id'] : null;
}

function addSelectedGoodForMessage($goodID, $partNumber)
{
    if (checkIfAlreadyExist($partNumber)) {
        return false;
    }
    $nishaId = findRelation($goodID);

    if (!$nishaId) {
        // If good_id does not exist, proceed with insertion
        $sql = "INSERT INTO telegram.goods_for_sell (good_id, partNumber) VALUES (?, ?)";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->execute([$goodID, $partNumber]);

        // Return the result of execution
        return $stmt->rowCount() > 0; // If insertion was successful, rowCount will be greater than 0
    } else {
        $relatedItems = getInRelationItems($nishaId);
        if ($relatedItems) {
            foreach ($relatedItems as $item) {
                if (checkIfAlreadyExist($item['partnumber'])) {
                    continue;
                }

                $sql = "INSERT INTO telegram.goods_for_sell (good_id, partNumber) VALUES (?, ?)";
                $stmt = PDO_CONNECTION->prepare($sql);
                $stmt->execute([$item['id'], $item['partnumber']]);
            }
            return true; // Insertion successful
        }
    }
    return false; // Insertion failed
}

function checkIfAlreadyExist($partNumber)
{
    // Prepare the SQL statement
    $sql = "SELECT * FROM telegram.goods_for_sell WHERE partNumber = ?";

    // Prepare the statement
    $statement = PDO_CONNECTION->prepare($sql);

    // Bind parameters and execute the statement
    $statement->execute([$partNumber]);

    // Store result
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    // Check if any rows were returned
    return $result !== false; // If result is not false, the row exists
}

function findRelation($id)
{
    // Prepare the SQL statement
    $sql = "SELECT pattern_id FROM shop.similars WHERE nisha_id = ? LIMIT 1";

    // Prepare the statement
    $statement = PDO_CONNECTION->prepare($sql);

    // Bind parameters and execute the statement
    $statement->execute([$id]);

    // Fetch the result
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    // Check if any rows were returned
    if ($row !== false) {
        // If row exists, return the pattern_id
        return (int)$row['pattern_id']; // Convert to integer and return
    } else {
        // No rows found, return false
        return false;
    }
}

function getInRelationItems($nisha_id)
{
    // Fetch similar items based on the provided nisha_id
    $sql = "SELECT nisha_id FROM shop.similars WHERE pattern_id = ?";
    $statement = PDO_CONNECTION->prepare($sql);
    $statement->execute([$nisha_id]);
    $goods = $statement->fetchAll(PDO::FETCH_ASSOC);
    $all_ids = array_column($goods, 'nisha_id');

    if (count($all_ids) == 0) {
        return false;
    }

    // Prepare the list of IDs to use in the IN clause of the next query
    $idList = implode(',', $all_ids);

    // Fetch part numbers of the related items
    $partNumberSQL = "SELECT id, partnumber FROM yadakshop.nisha WHERE id IN ($idList)";
    $partNumberStatement = PDO_CONNECTION->query($partNumberSQL);
    $partNumbers = $partNumberStatement->fetchAll(PDO::FETCH_ASSOC);

    return $partNumbers;
}

if (isset($_POST['deleteGood'])) {
    $partNumber = $_POST['partNumber'];
    $goodID = getGoodID($partNumber);
    $nishaId = findRelation($goodID);

    if (!$nishaId) {
        echo deleteGood($partNumber);
        return;
    } else {
        $relatedItems = getInRelationItems($nishaId);
        if ($relatedItems) {
            foreach ($relatedItems as $item) {
                $sql = "DELETE FROM telegram.goods_for_sell WHERE partNumber = ?";
                $stmt = PDO_CONNECTION->prepare($sql);
                $stmt->execute([$item['partnumber']]);
            }
        }
        echo true;
    }
}

function deleteGood($partNumber)
{
    // Prepare the SQL statement
    $sql = "DELETE FROM telegram.goods_for_sell WHERE partNumber = ?";

    // Prepare the statement
    $statement = PDO_CONNECTION->prepare($sql);

    // Bind parameters and execute the statement
    $statement->execute([$partNumber]);

    // Return true if the execution was successful, false otherwise
    return $statement->rowCount() > 0;
}
