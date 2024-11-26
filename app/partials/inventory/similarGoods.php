<?php

function getSimilarGoods($factorItems, $billId, $customer, $factorNumber)
{
    $selectedGoods = [];
    foreach ($factorItems as $item) {
        $factorItemParts = explode('-', $item->partName);

        $goodNameBrand = trim($factorItemParts[1]);
        $goodNamePart = trim(explode(' ', $factorItemParts[0])[0]);

        $ALLOWED_BRANDS = [$goodNameBrand];

        if ($goodNameBrand == 'اصلی' || $goodNameBrand == 'GEN' || $goodNameBrand == 'MOB') {
            $ALLOWED_BRANDS[] = 'GEN';
            $ALLOWED_BRANDS[] = 'MOB';
        }

        if ($goodNameBrand == 'شرکتی') {
            $ALLOWED_BRANDS[] = 'IRAN';
        }

        if ($goodNameBrand == 'متفرقه' || $goodNameBrand == 'چین') {
            $ALLOWED_BRANDS[] = 'CHINA';
        }

        if ($goodNameBrand == 'کره' || $goodNameBrand == 'کره ای') {
            $ALLOWED_BRANDS[] = 'KOREA';
        }

        $ALLOWED_BRANDS =  addRelatedBrands($ALLOWED_BRANDS);
        $goods = getGoodsSpecification($goodNamePart, $ALLOWED_BRANDS);

        $inventoryGoods = isset($goods['goods']) ? $goods['goods'] : [];

        $billItemQuantity = $item->quantity;
        $counter = 1;
        $totalQuantity = getTotalQuantity($inventoryGoods, $ALLOWED_BRANDS);

        $index = 0; // Counter to track the current index
        foreach ($inventoryGoods as $good) {
            if ($billItemQuantity == 0) {
                break;
            }

            if (in_array($good['brandName'], $ALLOWED_BRANDS)) {
                if ($totalQuantity >= $billItemQuantity && $billItemQuantity > 0) {
                    $sellQuantity = $billItemQuantity;
                    if ($billItemQuantity >= $good['remaining_qty']) {
                        $sellQuantity = $good['remaining_qty'];
                        $billItemQuantity -= $good['remaining_qty'];
                        addToBillItems($good, $sellQuantity, $selectedGoods, $item->id);
                    } else {
                        $sellQuantity = $billItemQuantity;
                        addToBillItems($good, $sellQuantity, $selectedGoods, $item->id);
                        break;
                    }
                }
            } else {
                if ($index === count($inventoryGoods) - 1) {
                    if ($goodNameBrand == 'اصلی') {
                        $goodNameBrand = 'GEN یا MOB';
                    }
                }
            }
            $counter++;
            $index++;
        }
    }

    $billItemsDescription = [$item->id => []];

    sendSellsReportMessage($selectedGoods, $customer, $factorNumber);

    if (hasPreSellFactor($billId)) {
        update_pre_bill($billId, json_encode($selectedGoods), json_encode($billItemsDescription));
    } else {
        save_pre_bill($billId, json_encode($selectedGoods), json_encode($billItemsDescription));
    }
}

function sendSellsReportMessage($goods, $customer, $factorNumber)
{
    $name = $_SESSION['user']['name'] ?? '';
    $family = $_SESSION['user']['family'] ?? '';
    $fullName = $name . ' ' . $family;
    $template = "{$customer->displayName} {$customer->family} \nکاربر : {$fullName} \nشماره فاکتور : {$factorNumber} \n";

    foreach ($goods as $good) {
        $template .= PHP_EOL . $good['partNumber'] . ' ' . $good['brandName'] . ' ' . $good['quantity'] . ' ' . $good['pos1'] . ' ' . $good['pos2'] . PHP_EOL;
        $template .= "-----------------------------" . PHP_EOL;
    }

    // Prepare data for POST request
    $postData = array(
        "sendMessage" => "sellsReport",
        "message" => $template,
    );

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, "http://auto.yadak.center/");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL request
    $result = curl_exec($ch);

    // Close cURL session
    curl_close($ch);
}

function getGoodsSpecification($completeCode, $allAllowedBrands)
{

    $similarCodes = setup_loading($completeCode);

    $relatedGoods =  $similarCodes['existing'] ? current(current($similarCodes['existing'])) : [];

    if ($relatedGoods) {
        $stockInfo = $relatedGoods['relation']['stockInfo'];

        $existingGoods = array_filter($stockInfo, function ($item) {
            return count($item) > 0;
        });

        $MatchedGoods = fetchCodesWithInfo($existingGoods, $allAllowedBrands, $completeCode);

        return $MatchedGoods;
    } else {
        return null;
    }
}

function setup_loading($completeCode)
{
    $explodedCodes = explode("\n", $completeCode);

    $results_array = [
        'not_exist' => [],
        'existing' => [],
    ];

    $explodedCodes = array_map(function ($code) {
        if (strlen($code) > 0) {
            return  strtoupper(preg_replace('/[^a-z0-9]/i', '', $code));
        }
    }, $explodedCodes);

    $explodedCodes = array_filter($explodedCodes, function ($code) {
        if (strlen($code) > 6) {
            return  $code;
        }
    });

    // Remove duplicate codes from results array
    $explodedCodes = array_unique($explodedCodes);

    $existing_code = []; // this array will hold the id and partNumber of the existing codes in DB

    foreach ($explodedCodes as $code) {
        $sql = "SELECT id, partnumber FROM yadakshop.nisha WHERE partnumber LIKE :partNumber";
        $stmt = PDO_CONNECTION->prepare($sql);
        $param = $code . '%';
        $stmt->bindParam(':partNumber', $param, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $existing_code[$code] = $result;
        } else {
            $results_array['not_exist'][] = $code; // Adding nonexisting codes to the final result array's not_exist index
        }
    }

    $itemDetails = [];
    $relation_id = [];
    $codeRelationId = [];
    foreach ($explodedCodes as $code) {
        if (!in_array($code, $results_array['not_exist'])) {
            $itemDetails[$code] = [];
            foreach ($existing_code[$code] as $item) {
                $relation_exist = isInRelation($item['id']);

                if ($relation_exist) {
                    $codeRelationId[$code] =  $relation_exist;
                    if (!in_array($relation_exist, $relation_id)) {
                        array_push($relation_id, $relation_exist); // if a new relation exists -> put it in the result array

                        $itemDetails[$code][$item['partnumber']]['information'] = info($relation_exist);
                        $itemDetails[$code][$item['partnumber']]['relation'] = relations($relation_exist, true);
                        $itemDetails[$code][$item['partnumber']]['givenPrice'] = givenPrice(array_keys($itemDetails[$code][$item['partnumber']]['relation']['goods']), $relation_exist);
                    }
                } else {
                    $codeRelationId[$code] =  'not' . rand();
                    $itemDetails[$code][$item['partnumber']]['information'] = info();
                    $itemDetails[$code][$item['partnumber']]['relation'] = relations($item['partnumber'], false);
                    $itemDetails[$code][$item['partnumber']]['givenPrice'] = givenPrice(array_keys($itemDetails[$code][$item['partnumber']]['relation']['goods']));
                }
            }
        }
    }

    foreach ($itemDetails as &$record) {

        uasort($record, 'resultCustomSort'); // Sort the inner array by values
    }

    return ([
        'explodedCodes' => $explodedCodes,
        'not_exist' => $results_array['not_exist'],
        'existing' => $itemDetails,
    ]);
}

// Custom comparison function to sort inner arrays by values in descending order
function resultCustomSort($a, $b)
{
    $sumA = array_sum($a['relation']['sorted']); // Calculate the sum of values in $a
    $sumB = array_sum($b['relation']['sorted']); // Calculate the sum of values in $b

    // Compare the sums in descending order
    if ($sumA == $sumB) {
        return 0;
    }
    return ($sumA > $sumB) ? -1 : 1;
}

function fetchCodesWithInfo($existingGoods, $allowedBrands, $completeCode)
{
    $CODES_INFORMATION = [
        'goods' => [],
        'codes' => []
    ];

    $specifiedCode = [];
    $similarCodes = [];

    foreach ($existingGoods as $key => $code) {
        foreach ($code as $item) {
            if (in_array(trim($item['brandName']), $allowedBrands)) {
                $item['partNumber'] = $key;
                if ($key == $completeCode) {
                    $specifiedCode[] = $item;
                } else {
                    $similarCodes[] = $item;
                }
                $CODES_INFORMATION['codes'][] = $key;
                $CODES_INFORMATION['codes'] = array_unique($CODES_INFORMATION['codes']);
            }
        }
    }

    $YadakShopInventory = [];
    $otherInventory = [];

    foreach ($specifiedCode as $good) {
        if ($good['stockId'] == 9) {
            $YadakShopInventory[] = $good;
        } else {
            $otherInventory[] = $good;
        }
    }

    $specifiedCode = array_merge($YadakShopInventory, $otherInventory);

    $YadakShopInventory = [];
    $otherInventory = [];

    foreach ($similarCodes as $good) {
        if ($good['stockId'] == 9) {
            $YadakShopInventory[] = $good;
        } else {
            $otherInventory[] = $good;
        }
    }

    // usort($specifiedCode, function ($a, $b) {
    //     // Convert date strings to timestamps for comparison
    //     return strtotime($a['invoice_date']) - strtotime($b['invoice_date']);
    // });

    // usort($YadakShopInventory, function ($a, $b) {
    //     // Convert date strings to timestamps for comparison
    //     return strtotime($b['invoice_date']) - strtotime($a['invoice_date']);
    // });

    // usort($otherInventory, function ($a, $b) {
    //     // Convert date strings to timestamps for comparison
    //     return strtotime($b['invoice_date']) - strtotime($a['invoice_date']);
    // });

    // Merge inventories
    $CODES_INFORMATION['goods'] = array_merge($specifiedCode, $YadakShopInventory, $otherInventory);

    // No need to filter empty goods, all goods will have data
    $CODES_INFORMATION['goods'] = array_filter($CODES_INFORMATION['goods'], function ($item) {
        return !empty($item); // Ensuring that goods are non-empty
    });

    // Return unique codes instead of array keys
    return ($CODES_INFORMATION);
}

function getTotalQuantity($goods = [], $brandsName = [])
{
    $totalQuantity = 0;

    foreach ($goods as $good) {
        if (in_array($good['brandName'], $brandsName)) {
            $totalQuantity += $good['remaining_qty'];
        }
    }
    return $totalQuantity;
}


function addToBillItems($good, $quantity, &$selectedGoods, $index)
{
    // Check if the item already exists in billItems
    if (array_key_exists($good['goodId'], $selectedGoods)) {
        // If the item exists, sum the quantities
        $selectedGoods[$good['goodId']]['quantity'] += $quantity;
    } else {
        // If the item does not exist, add it to billItems
        $selectedGoods[$good['goodId']] = [
            'quantityId' => $good['quantityId'],
            'id' => $index,
            'goodId' => $good['goodId'],
            'partNumber' => $good['partNumber'],
            'stockId' => $good['stockId'],
            'purchase_Description' => $good['purchase_Description'],
            'stockName' => $good['stockName'],
            'brandName' => $good['brandName'],
            'sellerName' => $good['seller_name'],
            'quantity' => $quantity,
            'pos1' => $good['pos1'],
            'pos2' => $good['pos2']
        ];
    }
}

function save_pre_bill($billId, $billItems, $billItemsDescription)
{
    try {
        // Prepare the SQL statement with correct placeholders
        $sql = "INSERT INTO factor.pre_sell(bill_id, selected_items, details) 
                VALUES (:billId, :billItems, :billItemsDescription)";

        $stmt = PDO_CONNECTION->prepare($sql);

        // Ensure the data is serialized if necessary
        $billItems = json_encode($billItems);
        $billItemsDescription = json_encode($billItemsDescription);

        // Bind values to the placeholders
        $stmt->bindValue(":billId", $billId);
        $stmt->bindValue(":billItems", $billItems);
        $stmt->bindValue(":billItemsDescription", $billItemsDescription);

        // Execute the statement and handle the result
        if ($stmt->execute()) {
            echo json_encode(array('status' => 'create', 'message' => 'Bill saved successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to save bill'));
        }
    } catch (\Throwable $th) {
        echo json_encode(array('status' => 'error', 'message' => 'An error occurred: ' . $th->getMessage()));
    }
}

function update_pre_bill($billId, $billItems, $billItemsDescription)
{
    try {
        // Prepare the SQL statement with correct placeholders
        $sql = "UPDATE factor.pre_sell SET selected_items = :billItems, details = :billItemsDescription WHERE bill_id = :billId";

        $stmt = PDO_CONNECTION->prepare($sql);

        // Ensure the data is serialized if necessary
        $billItems = json_encode($billItems);
        $billItemsDescription = json_encode($billItemsDescription);

        // Bind values to the placeholders
        $stmt->bindValue(":billId", $billId);
        $stmt->bindValue(":billItems", $billItems);
        $stmt->bindValue(":billItemsDescription", $billItemsDescription);

        // Execute the statement and handle the result
        if ($stmt->execute()) {
            echo json_encode(array('status' => 'update', 'message' => 'Bill updated successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to update bill'));
        }
    } catch (\Throwable $th) {
        echo json_encode(array('status' => 'error', 'message' => 'An error occurred: ' . $th->getMessage()));
    }
}

function hasPreSellFactor($factorId)
{
    $sql = "SELECT * FROM factor.pre_sell WHERE bill_id = :billId";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(":billId", $factorId);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
