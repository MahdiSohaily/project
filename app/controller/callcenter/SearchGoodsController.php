<?php
if (!isset($dbname)) {
    header("Location: ../../../views/auth/403.php");
}

$rates = getRates();

function getRates()
{
    $stmt = PDO_CONNECTION->prepare("SELECT * FROM shop.rates ORDER BY amount ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchedGoods($pattern)
{
    $stmt = PDO_CONNECTION->prepare("SELECT * FROM yadakshop.nisha WHERE partnumber LIKE :pattern");
    $pattern = $pattern . '%';
    $stmt->bindParam(':pattern', $pattern, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_http_response_code($url)
{
    ini_set('user_agent', 'Mozilla/5.0');
    $headers = get_headers($url);
    return substr($headers[0], 9, 3);
}

function checkMobis($mobis, $good)
{
    $context = stream_context_create(array("http" => array("header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36")));

    $item = [];

    if (get_http_response_code("https://partsmotors.com/products/$mobis") != "200") {
        $price = '-';
        UpdateMobisPrice($mobis, $price);
        return $item;
    } else {
        require_once 'simple_html_dom.php'; // A php file which converts the response text to HTML DOM

        $html = file_get_contents("https://partsmotors.com/products/$mobis", false, $context);

        $html = str_get_html($html);
        $price = null;
        foreach ($html->find('meta[property=og:price:amount]') as $element) {
            $price = $element->content;
        }

        $price = str_replace(",", "", $price);
        // Updating the current item mobis
        updateMobisPrice($mobis, $price);

        $item = [
            'id' => $good['id'],
            'partNumber' => $good['partnumber'],
            'price' => $price,
            'avgPrice' => round($price * 100 / 243.5 * 1.1),
        ];
    }
    return $item;
}

function updateMobisPrice($partNumber, $price)
{
    $update_sql = "UPDATE yadakshop.nisha SET mobis = :mobis WHERE partnumber = :partNumber";
    $stmt = PDO_CONNECTION->prepare($update_sql);
    $stmt->bindParam(':mobis', $price, PDO::PARAM_STR);
    $stmt->bindParam(':partNumber', $partNumber, PDO::PARAM_STR);
    $stmt->execute();
}
