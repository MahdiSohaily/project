<?php
require_once './config/constants.php';
require_once './database/db_connect.php';
require_once './utilities/callcenter/DollarRateHelper.php';
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

if (isset($_POST['code'])) {
    //remove all the special characters from the user input
    $code = [htmlspecialchars($_POST['code'])];

    $completeCode = $code;
    $finalResult = (setup_loading($completeCode));

    $explodedCodes = &$finalResult['explodedCodes'];
    $not_exist = &$finalResult['not_exist'];
    $existing = &$finalResult['existing'];
    $completeCode = &$finalResult['completeCode'];
    $relation_ids = &$finalResult['relation_id'];

    foreach ($explodedCodes as $code_index => &$code) {
        if (array_key_exists($code, $existing)) {
            foreach ($existing[$code] as &$item) {
                if ($item['givenPrice'] !== null && count($item['givenPrice']) > 0) {
                    foreach ($item['givenPrice'] as &$price) {
                        $priceDate = $price['created_at'];

                        if (checkDateIfOkay($applyDate, $priceDate) && $price['price'] !== 'موجود نیست') {
                            $rawGivenPrice = $price['price'];
                            $price['price'] = applyDollarRate($rawGivenPrice, $priceDate);
                        }
                    }
                    unset($price); // Unset the reference to avoid any unintended modifications outside the loop
                }
            }
            unset($item); // Unset the reference to avoid any unintended modifications outside the loop
        }
    }
    unset($code); // Unset the reference to avoid any unintended modifications outside the loop



    if (!empty($finalResult)) {
        // Assuming everything went well
        $response = [
            'success' => true,
            'message' => 'Form data received successfully.',
            'data' => $finalResult,
        ];
        // Send the JSON response
        echo json_encode($response);
    }
}

function setup_loading($completeCode)
{
    $explodedCodes = $completeCode;
    // echo json_encode($explodedCodes) . "\n";

    $results_array = [
        'not_exist' => [],
        'existing' => [],
    ];

    $explodedCodes = array_map(function ($code) {
        if (strlen($code) > 0) {
            return  preg_replace('/[^a-z0-9]/i', '', $code);
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

    return ([
        'explodedCodes' => $explodedCodes,
        'not_exist' => $results_array['not_exist'],
        'existing' => $itemDetails,
        'completeCode' => $completeCode,
        'relation_id' => $codeRelationId
    ]);
}

function isInRelation($id)
{
    $sql = "SELECT pattern_id FROM shop.similars WHERE nisha_id = :nisha_id";
    $stmt = PDO_CONNECTION->prepare($sql);
    $stmt->bindParam(':nisha_id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['pattern_id'];
    }

    return false;
}

/**
 * @param Connection to the database
 * @return array of rates selected to be used in the goods report table
 */
function getSelectedRates()
{

    $sql = "SELECT amount, status FROM shop.rates WHERE selected = '1' ORDER BY amount ASC";
    $result = PDO_CONNECTION->query($sql);
    $rates = $result->fetchAll(PDO::FETCH_ASSOC);
    return $rates;
}

/**
 * @param Connection to the database
 * @param int $id is the id of specified good
 * @return int $relation_exist
 * @return array of information about the good
 */
function info($relation_exist = null)
{
    $info = false;
    $cars = [];

    if ($relation_exist) {
        // Get pattern info
        $sql = "SELECT * FROM shop.patterns WHERE id = :relation_exist";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':relation_exist', $relation_exist, PDO::PARAM_INT);
        $stmt->execute();
        $info = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($info && $info['status_id'] !== 0) {
            $sql = "SELECT patterns.*, status.name AS status_name 
                    FROM shop.patterns 
                    INNER JOIN shop.status ON status.id = patterns.status_id 
                    WHERE patterns.id = :relation_exist";
            $stmt = PDO_CONNECTION->prepare($sql);
            $stmt->bindParam(':relation_exist', $relation_exist, PDO::PARAM_INT);
            $stmt->execute();
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Get cars info
        $sql = "SELECT cars.name 
                FROM shop.patterncars 
                INNER JOIN shop.cars ON cars.id = patterncars.car_id 
                WHERE patterncars.pattern_id = :relation_exist";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':relation_exist', $relation_exist, PDO::PARAM_INT);
        $stmt->execute();
        $cars = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    return $info ? ['relationInfo' => $info, 'cars' => $cars] : false;
}

function relations($id, $condition)
{
    $relations = [];
    $limit_id = '';

    if ($condition) {
        $sql = "SELECT yadakshop.nisha.* 
                FROM yadakshop.nisha 
                INNER JOIN shop.similars ON similars.nisha_id = nisha.id 
                WHERE similars.pattern_id = :id";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $limit_id = $id . '-r';
    } else {
        $sql = "SELECT * FROM yadakshop.nisha WHERE partnumber = :id";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $limit_id = end($relations)['id'] . '-s';
    }

    $existing = [];
    $stockInfo = [];
    $sortedGoods = [];
    $unique_goods = [];

    foreach ($relations as $relation) {
        if (!array_key_exists($relation['partnumber'], $unique_goods)) {
            $unique_goods[$relation['partnumber']] = [$relation['id']];
        } else {
            $unique_goods[$relation['partnumber']][] = $relation['id'];
        }
        $sortedGoods[$relation['partnumber']] = $relation;
    }


    foreach ($unique_goods as $key => $relation_ids) {
        $data = exist($relation_ids);
        $existing[$key] = $data['brands_info'];
        $stockInfo[$key] = $data['stockInfo'];
    }

    arsort($existing);
    $sorted = [];

    $max = 0;
    foreach ($existing as $key => $value) {
        $sorted[$key] = getMax($value);
        $max += $sorted[$key];
    }

    arsort($sorted);

    return [
        'goods' => $sortedGoods,
        'existing' => $existing,
        'sorted' => $sorted,
        'stockInfo' => $stockInfo,
        'limit_alert' => $limit_id
    ];
}

function givenPrice($codes, $relation_exist = null)
{
    // Filter and lowercase the codes
    $codes = array_map('strtolower', array_filter($codes, 'trim'));
    $ordered_price = [];

    if ($relation_exist) {
        // Query to get the price and created_at for the given pattern
        $sql = "SELECT price, created_at FROM shop.patterns WHERE id = :relation_exist";
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindParam(':relation_exist', $relation_exist, PDO::PARAM_INT);
        $stmt->execute();
        $ordered_price = $stmt->fetch(PDO::FETCH_ASSOC);
        $ordered_price['ordered'] = true;
    }

    // Query to get prices based on the provided codes
    if (!empty($codes)) {
        // Create placeholders for each code
        $placeholders = implode(',', array_fill(0, count($codes), '?'));
        $sql = "SELECT prices.id, prices.price, prices.partnumber, customer.name, customer.id AS customerID, 
                       customer.family, users.id AS userID, prices.created_at
                FROM shop.prices
                INNER JOIN callcenter.customer ON customer.id = prices.customer_id
                INNER JOIN yadakshop.users ON users.id = prices.user_id
                WHERE partnumber IN ($placeholders)
                ORDER BY created_at DESC LIMIT 7";

        $stmt = PDO_CONNECTION->prepare($sql);

        // Execute the statement with the array of codes
        $stmt->execute($codes);
        $givenPrices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ensure givenPrices is not empty
        $givenPrices = array_filter($givenPrices);

        // Prepare final data array
        $final_data = $relation_exist ? array_merge([$ordered_price], $givenPrices) : $givenPrices;

        // Sort final data by created_at if relation_exist is true
        if ($relation_exist) {
            usort($final_data, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        // Filter out items without price
        $filtered_data = array_filter($final_data, function ($item) {
            return isset($item['price']) && $item['price'] !== '';
        });

        return $filtered_data;
    }

    return [];
}

function exist($ids)
{
    global $stock;
    $stock = 'stock_1403';

    // Prepare the base SQL query with LEFT JOIN to exitrecord and necessary calculations
    $base_sql = "SELECT qtybank.id AS quantityId, codeid AS goodId, brand.name AS brandName, qtybank.qty AS quantity,
                    create_time AS invoice_date, seller.name AS seller_name, brand.id AS brandId,
                    IFNULL(SUM(exitrecord.qty), 0) AS total_sold,
                    qtybank.qty - IFNULL(SUM(exitrecord.qty), 0) AS remaining_qty
                FROM {$stock}.qtybank
                LEFT JOIN yadakshop.brand ON brand.id = qtybank.brand
                LEFT JOIN yadakshop.seller ON seller.id = qtybank.seller
                LEFT JOIN $stock.exitrecord ON qtybank.id = exitrecord.qtyid";

    // Append the condition based on the number of IDs
    if (count($ids) == 1) {
        $data_sql = $base_sql . " WHERE codeid = :id
                                  GROUP BY qtybank.id, codeid, brand.name, qtybank.qty, create_time, seller.name, brand.id
                                  HAVING remaining_qty > 0";

        // Prepare and execute the SQL statement
        $stmt = PDO_CONNECTION->prepare($data_sql);
        $stmt->bindParam(':id', $ids[0], PDO::PARAM_INT);
    } else {
        // Prepare placeholders for multiple IDs
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $data_sql = $base_sql . " WHERE codeid IN ($placeholders)
                                  GROUP BY qtybank.id, codeid, brand.name, qtybank.qty, create_time, seller.name, brand.id
                                  HAVING remaining_qty > 0";

        // Prepare and execute the SQL statement
        $stmt = PDO_CONNECTION->prepare($data_sql);
        // Bind each ID to the corresponding placeholder
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
    }

    try {
        // Execute the statement
        $stmt->execute();
        $incoming = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filter and process the incoming data
        $existingGoods = ($incoming);

        // Calculate brand totals
        $brands_info = [];
        foreach ($existingGoods as $item) {
            $brandName = $item['brandName'];
            $remainingQuantity = $item['remaining_qty'];
            if (isset($brands_info[$brandName])) {
                $brands_info[$brandName] += $remainingQuantity;
            } else {
                $brands_info[$brandName] = $remainingQuantity;
            }
        }

        // Sort brands by total quantity
        arsort($brands_info);

        return ['stockInfo' => $existingGoods, 'brands_info' => $brands_info];
    } catch (PDOException $e) {
        // Handle any errors
        echo 'Error: ' . $e->getMessage();
        return [];
    }
}

function getMax($array)
{
    $max = 0;
    foreach ($array as $k => $v) {
        $max = $max < $v ? $v : $max;
    }
    return $max;
}
