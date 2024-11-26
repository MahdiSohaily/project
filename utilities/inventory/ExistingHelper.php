<?php
function getPurchaseReportById($id)
{
    global $stock;

    // Construct the base SQL query with LEFT JOIN to exitrecord and necessary calculations
    $sql = "SELECT nisha.partnumber AS partNumber, stock.name AS stockName, stock.id AS stockId,
                nisha.id AS goodId, seller.id AS seller_id, seller.name AS sellerName, brand.id AS brandId, brand.name AS brandName,
                qtybank.des AS quantityDescription, qtybank.id AS quantityId,
                qtybank.qty AS quantity,
                qtybank.is_transfered,
                qtybank.pos1,
                qtybank.pos2,
                IFNULL(SUM(exitrecord.qty), 0) AS total_sold,
                qtybank.qty - IFNULL(SUM(exitrecord.qty), 0) AS remaining_qty
            FROM $stock.qtybank
            LEFT JOIN nisha ON qtybank.codeid = nisha.id
            LEFT JOIN seller ON qtybank.seller = seller.id
            LEFT JOIN stock ON qtybank.stock_id = stock.id
            INNER JOIN brand ON qtybank.brand = brand.id
            LEFT JOIN $stock.exitrecord ON qtybank.id = exitrecord.qtyid
            WHERE qtybank.id = :id
            GROUP BY qtybank.id, nisha.partnumber, stock.name, stock.id, nisha.id, seller.id, seller.name, brand.id, brand.name, qtybank.des, qtybank.qty, qtybank.is_transfered, qtybank.pos1, qtybank.pos2
            HAVING remaining_qty > 0";

    try {
        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch the results
        $purchasedGoods = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $purchasedGoods;
    } catch (PDOException $e) {
        // Handle any errors
        echo 'Error: ' . $e->getMessage();
        return [];
    }
}

function getPurchaseReport($pattern = null)
{
    global $stock;

    // Construct the base SQL query with LEFT JOIN to exitrecord and necessary calculations
    $sql = "SELECT nisha.partnumber AS partNumber, stock.name AS stockName, stock.id AS stockId,
                nisha.id AS goodId, seller.id AS seller_id, seller.name AS sellerName, brand.id AS brandId, brand.name AS brandName,
                qtybank.des AS quantityDescription, qtybank.id AS quantityId,
                qtybank.qty AS quantity,
                qtybank.is_transfered,
                qtybank.pos1,
                qtybank.pos2,
                IFNULL(SUM(exitrecord.qty), 0) AS total_sold,
                qtybank.qty - IFNULL(SUM(exitrecord.qty), 0) AS remaining_qty
            FROM $stock.qtybank
            LEFT JOIN nisha ON qtybank.codeid = nisha.id
            LEFT JOIN seller ON qtybank.seller = seller.id
            LEFT JOIN stock ON qtybank.stock_id = stock.id
            INNER JOIN brand ON qtybank.brand = brand.id
            LEFT JOIN $stock.exitrecord ON qtybank.id = exitrecord.qtyid 
            WHERE qtybank.sold_out = 0 ";

    // Append the pattern condition if provided
    if ($pattern) {
        $sql .= " AND nisha.partnumber LIKE :pattern";
    }

    // Group by necessary fields to allow aggregate functions
    $sql .= " GROUP BY qtybank.id, nisha.partnumber, stock.name, stock.id, nisha.id, seller.id, seller.name, brand.id, brand.name, qtybank.des, qtybank.qty, qtybank.is_transfered, qtybank.pos1, qtybank.pos2";

    // Add HAVING clause to filter out items with no remaining quantity
    $sql .= " HAVING remaining_qty > 0 ORDER BY nisha.partnumber DESC";

    try {
        // Prepare the statement
        $stmt = PDO_CONNECTION->prepare($sql);

        // Bind the pattern parameter if provided
        if ($pattern) {
            $stmt->bindValue(':pattern', '%' . $pattern . '%', PDO::PARAM_STR);
        }

        // Execute the statement
        $stmt->execute();

        // Fetch the results
        $purchasedGoods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $purchasedGoods;
    } catch (PDOException $e) {
        // Handle any errors
        echo 'Error: ' . $e->getMessage();
        return [];
    }
}
