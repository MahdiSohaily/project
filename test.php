<?php
require_once './config/constants.php';
require_once './database/db_connect.php';
require_once './utilities/callcenter/DollarRateHelper.php';
require_once './app/controller/telegram/AutoMessageController.php';


function validateMessages($messages)
{
    $separators = [" ", "  ", " - ", " : ", " = ", " \n", " \n\n", " => "];
    foreach ($messages as $sender => $message) {
        if (!checkIfValidSender($sender)) {
            continue;
        }

        $latestRequests = getReceiverLatestRequests($sender);

        array_walk($latestRequests, function (&$request) {
            $request = explode(' ', $request);
        });

        $latestRequests = array_merge(...$latestRequests);

        $allMessages = $message['info'];


        foreach ($allMessages as $message) {

            // Step 1: Explode the message codes into an array
            $rawCodes = explode("\n", $message['code']);

            // Step 2: Remove the last element of the array
            array_pop($rawCodes);
            // Step 3: Convert all codes to uppercase
            $rawCodes = array_map('strtoupper', $rawCodes);

            // Step 4: Trim whitespace from each code
            $rawCodes = array_map('trim', $rawCodes);

            // Step 5: Ensure all codes are unique
            $rawCodes = array_unique($rawCodes);


            // Step 6: Remove codes that are already in the latest requests
            $rawCodes = array_diff($rawCodes, $latestRequests);

            if (!count($rawCodes)) {
                continue;
            }

            $codes = isGoodSelected($rawCodes);
            // Now $codes contains the filtered codes
            if (count($codes)) {
                try {
                    $completeCode = implode("\n", $codes);
                    $data = getSpecification($completeCode);
                    $template = '';
                    $conversation = '';
                    $index = rand(0, count($separators) - 1);

                    if ($data) {
                        foreach ($data as $code => $item) {

                            if (trim($item['finalPrice']) == 'موجود نیست' || empty($item['finalPrice'])) {
                                echo $code . "  قیمت نهایی موجود نیست " . "\n";
                                continue;
                            }
                            $template .= $code . $separators[$index] . $item['finalPrice'] . "\n";
                            $conversation .= $code . $separators[$index] . $item['finalPrice'] . "\n";
                            saveConversation($sender, $code, $conversation);
                            $conversation = '';
                        }
                    }

                    echo $template;
                } catch (Exception $error) {
                    echo 'Error fetching price: ' . $error->getMessage();
                }
            } else {
                if (count($rawCodes) > 0) {
                    echo implode(', ', $rawCodes);
                    echo " کد مدنظر اضافه نشده " . "\n";
                }
            }
        }
    }
}

function getSpecification($completeCode)
{
    // $dateTime = convertPersianToEnglish(jdate('Y/m/d'));
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
    foreach ($explodedCodes as $code) {
        if (!in_array($code, $nonExistingCodes)) {
            foreach ($existing_code[$code] as $item) {
                $relation_exist = isInRelation($item['id']);

                if ($relation_exist) {
                    if (!in_array($relation_exist, $relation_id)) {
                        array_push($relation_id, $relation_exist);
                        $goodDescription = relations($relation_exist, true);
                        // $goodDetails[$item['partnumber']]['goods'] = $goodDescription['goods'][$item['partnumber']];
                        $goodDetails[$item['partnumber']]['existing'] = $goodDescription['existing'];
                        $goodDetails[$item['partnumber']]['givenPrice'] = givenPrice(array_keys($goodDescription['goods']), $relation_exist);
                        break;
                    }
                } else {
                    $goodDescription = relations($item['partnumber'], false);
                    // $goodDetails[$item['partnumber']]['goods'] = $goodDescription['goods'][$item['partnumber']];
                    $goodDetails[$item['partnumber']]['existing'] = $goodDescription['existing'];
                    $goodDetails[$item['partnumber']]['givenPrice'] = givenPrice(array_keys($goodDescription['goods']));
                }
            }
        }
    }

    $finalResult = [];

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
        $finalResult[$partNumber]['finalPrice'] = getFinalSanitizedPrice($goodDetail['givenPrice'], $goodDetails[$partNumber]['brands']);
    }

    return $finalResult;
}

// boot();

$response = '{
    "1310670940":
        {"info":[
            {"code":"546602B200\n548132D000\n","message":"58101-3SA26","date":1710235467}],
        "name":["Azizi -Diakopar"],
        "userName":[1310670940],
        "profile":["1310670940_x_4.jpg"]}
    }';
$response = json_decode($response, true);
validateMessages($response);
