<?php
require_once './config/constants.php';
require_once './database/db_connect.php';
require_once './utilities/callcenter/DollarRateHelper.php';
require_once './app/controller/telegram/AutoMessageController.php';
$status = getStatus();
function boot()
{
    $now = date('H:i:s');
    echo "\n\n*************** Cron job started ( $now ) ************************\n\n";
    // API endpoint URL
    $apiUrl = 'http://auto.yadak.center/';

    $postData = [
        'getMessagesAuto' => 'getMessagesAuto'
    ];

    // Initialize curl
    $curl = curl_init();

    // Set the curl options
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true, // Return response as a string instead of outputting it
        CURLOPT_FOLLOWLOCATION => true, // Follow redirects
        CURLOPT_MAXREDIRS => 10, // Maximum number of redirects to follow
        CURLOPT_TIMEOUT => 600, // Timeout in seconds
        CURLOPT_POST => true, // Set as POST request
        CURLOPT_POSTFIELDS => http_build_query($postData), // Encode data as URL-encoded format
        CURLOPT_HTTPHEADER => [ // Optional headers
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    // Execute the request
    $response = curl_exec($curl);

    // Check for errors
    if (curl_errno($curl)) {
        $errorMessage = curl_error($curl);
        // Handle the error
        echo "cURL error: $errorMessage";
    } else {
        // Handle the response` 
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Get HTTP status code
        if ($statusCode >= 200 && $statusCode < 300) {

            // Request was successful
            $response = json_decode($response, true);
            validateMessages($response);
        } else {
            // Request failed
            echo "Request failed with status code $statusCode";
            // You can handle different status codes here
        }
    }

    // Close curl
    curl_close($curl);
}

function validateMessages($messages)
{
    $separators = [
        " ",
        "  ",
        "     ",
        "           ",
        " - ",
        " -- ",
        " : ",
        " = ",
        " == ",
        " \n",
        " \n\n",
        " \n\n\n",
        " => ",
        " / ",
        " __ ",
        " **** ",
    ];
    
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
                                echo $item['partnumber'] . "قیمت موجود نیست برای این کد ذخیره شده است";
                                continue;
                            }
                            $template .= $item['partnumber'] . $separators[$index] . $item['price'] . "\n";
                            $conversation .= $item['partnumber'] . $separators[$index] . $item['price'] . "\n";
                            saveConversation($sender, $item['partnumber'], $conversation);
                            $conversation = '';
                        }
                    }

                    echo $template;
                    if ($template !== '') {
                        sendMessageWithTemplate($sender, $template);
                    }
                } catch (Exception $error) {
                    echo 'Error fetching price: ' . $error->getMessage();
                }
            } else {
                if (count($rawCodes) > 0) {
                    $codes = implode(', ', $rawCodes);
                    echo $codes . " کد مدنظر اضافه نشده " . "\n";
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

if ($status) {
    boot();
} else {
    echo 'ارسال پیام خودکار غیرفعال است' . "\n";
}
