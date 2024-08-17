<?php

use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;

if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

function getDetails($completeCode)
{
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


    $brandsPrices = [];

    foreach ($goodDetails as $partNumber => $goodDetail) {
        $brandsPrices[$partNumber] = getFinalPriceBrands($goodDetail['finalPrice']);
    }

    return $brandsPrices;
}

function getFinalPriceBrands($price)
{
    $brandsPrice = [];
    $addedBrands = [];

    if (empty($price) || $price == 'موجود نیست') {
        return $brandsPrice;
    }

    $pricesParts = explode('/', $price);
    $pricesParts = array_map('trim', $pricesParts);
    $pricesParts = array_map('strtoupper', $pricesParts);

    foreach ($pricesParts as $part) {
        $spaceIndex = strpos($part, ' ');
        if ($spaceIndex !== false) {
            $priceSubStr = substr($part, 0, $spaceIndex);
            $brandSubStr = substr($part, $spaceIndex + 1); // Skip the space
            $brand = trim(explode('(', $brandSubStr)[0]);
            $complexBrands = explode(' ', $brand)[0];

            if (!in_array($brand, $addedBrands) && !empty($brand)) {
                $addedBrands[] = $complexBrands;
                if ($complexBrands == 'MOB' || $complexBrands == 'GEN') {
                    $brandsPrice['اصلی'] = $priceSubStr * 10000;
                    continue;
                }
                $brandsPrice[$complexBrands] = $priceSubStr * 10000;
            }
        } else {
            $brandsPrice['اصلی'] = $part * 10000;
        }
    }
    return $brandsPrice;
}
