<?php
require_once '../../config/constants.php';
require_once '../../database/db_connect.php';
if (stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) {
    header("Content-type: application/xhtml+xml");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
} else {
    header("Content-type: text/html");
} ?>

<YeastarIPPhoneDirectory>
    <?php
    $sql = "SELECT * FROM callcenter.customer WHERE customer.name IS NOT NULL AND name != '' LIMIT 18000,4500 ";
    $stmt = PDO_CONNECTION->query($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) :
        foreach ($result as $row) :
            $name = $row['name'];
            $family = $row['family'];
            $phone = $row['phone']; ?>
            <DirectoryEntry>
                <Name><?= ($name . " " . $family) ?></Name>
                <Telephone label="Mobile Number"><?= $phone ?></Telephone>
            </DirectoryEntry>
    <?php
        endforeach;
    endif; ?>
</YeastarIPPhoneDirectory>