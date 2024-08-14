<?php
$completeCode = $_POST['code'];
$dateTime = convertPersianToEnglish(jdate('Y/m/d'));
$explodedCodes = explode("\n", $completeCode);

$nonExistingCodes = [];

$explodedCodes = array_filter($explodedCodes, function ($code) {
    return strlen($code) > 6;
});

// Cleaning and filtering codes
$sanitizedCodes = array_map(function ($code) {
    return strtoupper(preg_replace('/[^a-z0-9]/i', '', $code));
}, $explodedCodes);

// Remove duplicate codes
$explodedCodes = array_unique($sanitizedCodes);

$existing_code = []; // This array will hold the id and partNumber of the existing codes in DB

// Prepare SQL statement outside the loop for better performance
$sql = "SELECT id, partnumber FROM yadakshop.nisha WHERE partnumber LIKE :partNumber";
$stmt = PDO_CONNECTION->prepare($sql);

foreach ($explodedCodes as $code) {
    $param = $code . '%';
    $stmt->bindParam(':partNumber', $param, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        $existing_code[$code] = $result;
    } else {
        $nonExistingCodes[] = $code;
    }
}

$goodDetails = [];
$relation_id = [];
$codeRelationId = [];
foreach ($explodedCodes as $code) {
    if (!in_array($code, $nonExistingCodes)) {
        foreach ($existing_code[$code] as $item) {
            $relation_exist = isInRelation($item['id']);

            if ($relation_exist) {
                if (!in_array($relation_exist, $relation_id)) {
                    array_push($relation_id, $relation_exist);
                    $goodDescription = relations($relation_exist, true);
                    $goodDetails[$item['partnumber']]['goods'] = $goodDescription['goods'][$item['partnumber']];
                    $goodDetails[$item['partnumber']]['existing'] = $goodDescription['existing'];
                    $goodDetails[$item['partnumber']]['givenPrice'] = givenPrice(array_keys($goodDescription['goods']), $relation_exist);
                    break;
                }
            } else {
                $goodDescription = relations($item['partnumber'], false);
                $goodDetails[$item['partnumber']]['goods'] = $goodDescription['goods'][$item['partnumber']];
                $goodDetails[$item['partnumber']]['existing'] = $goodDescription['existing'];
                $goodDetails[$item['partnumber']]['givenPrice'] = givenPrice(array_keys($goodDescription['goods']));
            }
        }
    }
}

foreach ($goodDetails as $partNumber => $goodDetail) {
    $brands = [];
    foreach ($goodDetail['existing'] as $item) {
        if (count($item)) {
            array_push($brands, array_keys($item));
        }
    }
    $brands = [...array_unique(array_merge(...$brands))];
    $goodDetails[$partNumber]['brands'] = addRelatedBrands($brands);
    $goodDetails[$partNumber]['finalPrice'] = getFinalSanitizedPrice($goodDetail['givenPrice'], $goodDetails[$partNumber]['brands']);
}

$factorItems = [];
foreach ($goodDetails as $partNumber => $goodDetail) {
    $factorItems[] = [
        "id" => $goodDetail['goods']['id'],
        "partName" => getItemName($goodDetail['goods'], $goodDetail['brands']),
        "price_per" => getItemPrice($goodDetail['finalPrice']),
        "quantity" => 1,
        "max" => "undefined",
        "partNumber" => $partNumber
    ];
}

$factorInfo = [
    'customer_id' => 0,
    'bill_number' => 0,
    'quantity' => 0,
    'discount' => 0,
    'tax' => 0,
    'withdraw' => 0,
    'total' => 0,
    'date' => $dateTime,
    'partner' => 0,
    'totalInWords' => null
];

foreach ($factorItems as $item) {
    $factorInfo['quantity'] += $item['quantity'];
    $factorInfo['total'] += $item['price_per'] * $item['quantity'];
}

$incompleteBillId = createBill($factorInfo);

$incompleteBillDetails = createBillItemsTable(
    $incompleteBillId,
    json_encode($factorItems)
);

header('location: /views/factor/checkIncompleteSell.php?factor_number=' . $incompleteBillId);