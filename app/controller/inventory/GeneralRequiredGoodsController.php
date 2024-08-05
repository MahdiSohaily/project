<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$relationALL = PDO_CONNECTION->prepare("SELECT pattern_id, original, fake 
                            FROM shop.good_limit_all
                            WHERE pattern_id IS NOT NULL AND nisha_id IS NULL");
$relationALL->execute();
$relations = $relationALL->fetchAll(PDO::FETCH_ASSOC);

$needToMove = array();
foreach ($relations as $relation) {
    $patter_id = $relation['pattern_id'];
    $original = $relation['original'];
    $fake = $relation['fake'];

    $similar = PDO_CONNECTION->prepare("SELECT nisha_id FROM shop.similars WHERE pattern_id= :id");

    $similar->bindParam(':id', $patter_id);
    $similar->execute();
    $records = $similar->fetchAll(PDO::FETCH_ASSOC);

    $goods = array_column($records, 'nisha_id');

    if (count($goods) > 0) {
        $existing = getStockInfo($goods);
        // Initialize a variable to store the sum
        $sumOriginal = 0;
        $sumFake = 0;
        foreach ($existing as $item) {
            $sumOriginal += intval($item['original']);
            $sumFake += intval($item['fake']);
        }

        if ($sumOriginal < $original || $sumFake < $fake) {

            $needToMove[$patter_id]['goods'] = $existing;
            $needToMove[$patter_id]['original'] = $original;
            $needToMove[$patter_id]['fake'] = $fake;
            $needToMove[$patter_id]['sumOriginal'] = $sumOriginal;
            $needToMove[$patter_id]['sumFake'] = $sumFake;
            $needToMove[$patter_id]['IsSingle'] = false;
        }
    }
}



$singleGoods = PDO_CONNECTION->prepare("SELECT nisha_id, original, fake 
                                        FROM shop.good_limit_all 
                                        WHERE pattern_id IS  NULL AND nisha_id IS NOT NULL");
$singleGoods->execute();
$goods = $singleGoods->fetchAll(PDO::FETCH_ASSOC);

$singleItems = array();
foreach ($goods as $good) {
    $patter_id = $good['nisha_id'];
    $original = $good['original'];
    $fake = $good['fake'];
    $existing = getStockInfo([$good['nisha_id']]);

    $sumOriginal = intval(current($existing)['original']);
    $sumFake = intval(current($existing)['fake']);

    if ($sumOriginal < $original || $sumFake < $fake) {
        $needToMove[$patter_id]['goods'] = $existing;
        $needToMove[$patter_id]['original'] = $original;
        $needToMove[$patter_id]['fake'] = $fake;
        $needToMove[$patter_id]['sumOriginal'] = $sumOriginal;
        $needToMove[$patter_id]['sumFake'] = $sumFake;
        $needToMove[$patter_id]['IsSingle'] = true;
    }
}

function getStockInfo($codes)
{
    $statement = PDO_CONNECTION->prepare("SELECT * FROM yadakshop.nisha WHERE id = :id");

    $goods = array();
    foreach ($codes as $code) {
        $statement->bindParam(':id', $code);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $item = $result;
            $ids = array($result['id']);
            $goods[$code] = getEntranceRecord($ids);
        }
    }

    return $goods;
}


function getEntranceRecord($partNumbers)
{
    global $stock;

    $statement = PDO_CONNECTION->prepare("SELECT qtybank.id, codeid, brand.name AS brand_name, qty,
                                                invoice_date, seller.name AS seller_name
                                            FROM (( $stock.qtybank 
                                            INNER JOIN yadakshop.brand ON brand.id = qtybank.brand )
                                            INNER JOIN yadakshop.seller ON seller.id = qtybank.seller)
                                            WHERE codeid = :code_id");

    $data = array();
    foreach ($partNumbers as $partNumber) {
        $statement->bindParam(':code_id', $partNumber);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $record) {
            array_push($data, $record);
        }
    }

    return getExitRecords($data);
}


function getExitRecords($entrance)
{
    global $stock;
    $statement = PDO_CONNECTION->prepare("SELECT qty FROM $stock.exitrecord WHERE qtyid = ?");

    $data = array();
    foreach ($entrance as &$record) {
        $statement->bindParam(1, $record['id']);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $quantity = 0;
        foreach ($result as $row) {
            $quantity += $row['qty'];
        }
        $record['qty'] -= $quantity;
        if ($record['qty'] > 0) {
            array_push($data, $record);
        }
        // getFinalAmount($result, $record['qty']);
    }
    $derived = getFinalAmount($data);

    $GEN = isset($derived['GEN']) ? $derived['GEN'] : 0;
    $MOB = isset($derived['MOB']) ? $derived['MOB'] : 0;

    $original = $GEN + $MOB;
    $fake = array_sum($derived) - $original;

    return ['original' => $original, 'fake' => $fake];
}


function getFinalAmount($data)
{
    // Create an associative array to store the sum of qty for each brand_name
    $brandQtySum = array();

    // Iterate through the data and sum the qty for each brand_name
    foreach ($data as $record) {
        $brandName = $record["brand_name"];
        $qty = $record["qty"];
        if (array_key_exists($brandName, $brandQtySum)) {
            $brandQtySum[$brandName] += $qty;
        } else {
            $brandQtySum[$brandName] = $qty;
        }
    }

    uasort($brandQtySum, "sortByBrandNameQTY");

    return $brandQtySum;
}

function sortByBrandNameQTY($a, $b)
{
    return $b - $a;
}

function getPartNumber($id)
{
    $statement = PDO_CONNECTION->prepare("SELECT partnumber FROM nisha WHERE id = :id");
    $statement->bindParam(":id", $id);
    $statement->execute();
    $result = $statement->setFetchMode(PDO::FETCH_ASSOC);

    $result = $statement->fetch();
    return $result['partnumber'];
}

function getRelationInfo($id)
{
    $statement = PDO_CONNECTION->prepare("SELECT name FROM shop.patterns WHERE id = :id");
    $statement->bindParam(":id", $id);
    $statement->execute();
    $result = $statement->setFetchMode(PDO::FETCH_ASSOC);

    $result = $statement->fetch();
    return array_key_exists('name', $result) ? $result['name'] : 'Hello';
}
