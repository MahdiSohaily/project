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
                    $data = getPrice($codes);
                    $data = getFinalPrice($data);
                    $template = '';
                    $conversation = '';
                    $index = rand(0, count($separators) - 1);

                    if ($data) {
                        foreach ($data as $item) {
                            if (trim($item['price']) == 'موجود نیست') {
                                echo $item['partnumber'] . " does not exist";
                                continue;
                            }
                            $template .= $item['partnumber'] . $separators[$index] . $item['price'] . "\n";
                            $conversation .= $item['partnumber'] . $separators[$index] . $item['price'] . "\n";
                            saveConversation($sender, $item['partnumber'], $conversation);
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

function getFinalPrice($prices)
{
    $explodedCodes = $prices['explodedCodes'];
    $existing = $prices['existing'];
    $displayPrices = [];

    foreach ($explodedCodes as $code) {
        // Check if the code exists in the $existing array
        if (!isset($existing[$code])) {
            continue;
        }

        $existingCodes = array_values($existing[$code]);
        $max = 0;

        foreach ($existingCodes as $item) {
            $max += max(array_values($item['relation']['sorted']));
        }

        if ($max <= 0) {
            return false;
        }

        // Ensure 'givenPrice' key exists and is an array
        $givenPrice = $existingCodes[0]['givenPrice'] ?? [];

        // Check if 'givenPrice' is an associative array
        if (!is_array($givenPrice)) {
            continue;
        }

        // If 'givenPrice' is a single price array, wrap it in another array
        if (isset($givenPrice['price'])) {
            $givenPrice = [$givenPrice];
        }
        $givenPrice = array_values($givenPrice);

        // Ensure there are prices in the givenPrice array
        if (count($givenPrice) > 0) {
            $displayPrices[] = [
                'partnumber' => $code,
                'price' => $givenPrice[0]['price'] ?? null,
                'created_at' => $givenPrice[0]['created_at'],
            ];
        } else {
            return false;
        }
    }

    return $displayPrices;
}


// boot();

$response = '{
    "1310670940":
        {"info":[
            {"code":"553113K130\n361203c150\n361203c150\n","message":"58101-3SA26","date":1710235467}],
        "name":["Azizi -Diakopar"],
        "userName":[1310670940],
        "profile":["1310670940_x_4.jpg"]}
    }';
$response = json_decode($response, true);
// print_r($response);
// $response = array_values($response);

validateMessages($response);
