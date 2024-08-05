<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$isValidCustomer = false;
$customer_info = null;
$finalResult = [];

if (isset($_POST['jsonData'])) {
    $jsonData = $_POST['jsonData'];
    $messagesBySender = json_decode($jsonData, true); // Decodes as an associative array
    if (count($messagesBySender) > 0) {
        $isValidCustomer = true;
        // check if a customer is already specified or not !!!! 1 is the ID of the ordered customer!!!
        $customer = empty($_POST['customer']) ? 1 : $_POST['customer'];

        $notification_id = filter_has_var(INPUT_POST, 'notification') ? $_POST['notification'] : null;

        foreach ($messagesBySender as $sender => $message) {
            $fullName = current($message['name']);
            $username = current($message['userName']);
            $profile = current($message['profile']);

            foreach ($message['info'] as $info) {
                $explodedCodes = $info['code'];
                $userMessage = $info['message'];
                $messageDate = $info['date'];
                $finalResult[$sender] = setup_loading($sender, $explodedCodes, $userMessage, $username, $profile, $fullName, $notification_id, $messageDate);
            }
        }
    }
}

function customSort($a, $b)
{
    $sumA = array_sum($a['relation']['sorted']); // Calculate the sum of values in $a
    $sumB = array_sum($b['relation']['sorted']); // Calculate the sum of values in $b

    // Compare the sums in descending order
    if ($sumA == $sumB) {
        return 0;
    }
    return ($sumA > $sumB) ? -1 : 1;
}

function setup_loading($customer, $completeCode,  $userMessage, $username, $profile, $fullName, $notification = null, $messageDate)
{

    $explodedCodes = explode("\n", $completeCode);

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
        $sql = "SELECT id, partnumber FROM yadakshop.nisha WHERE partnumber LIKE :code";
        $stmt = PDO_CONNECTION->prepare($sql);
        $pattern = $code . "%";
        $stmt->bindParam(':code', $pattern, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $all_matched = [];
        if (count($result)) {
            $all_matched = $result;
            $existing_code[$code] = $all_matched;
        } else {
            array_push($results_array['not_exist'], $code); //Adding nonexisting codes to the final result array's not_exist index Line NO: 34
        }
    }


    $itemDetails = [];
    $relation_id = [];
    $codeRelationId = [];
    foreach ($explodedCodes as $code) {
        if (!in_array($code, $results_array['not_exist'])) {
            $itemDetails[$code] = [];
            foreach ($existing_code[$code] as $item) {

                // Check every matched good's Id If they have relationship and,
                // avoid operation for items in the same relationship
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
    // Custom comparison function to sort inner arrays by values in descending order



    foreach ($itemDetails as &$record) {

        uasort($record, 'customSort'); // Sort the inner array by values
    }

    return ([
        'explodedCodes' => $explodedCodes,
        'not_exist' => $results_array['not_exist'],
        'existing' => $itemDetails,
        'customer' => $customer,
        'completeCode' => $completeCode,
        'notification' => $notification,
        'rates' => getSelectedRates(),
        'relation_id' => $codeRelationId,
        'messages' => $userMessage,
        'message_date' => $messageDate,
        'fullName' => $fullName,
        'profile' =>  $profile,
        'username' =>  $username,
    ]);
}