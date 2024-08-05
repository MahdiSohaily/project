<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}
require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';
require_once '../../../utilities/jdf.php';
require_once '../../../utilities/inventory/InventoryHelpers.php';
require_once '../../../utilities/inventory/ExistingHelper.php';

if (isset($_POST['searchGoods'])) {
    $pattern = $_POST['pattern'];
    $purchasedGoods = getPurchaseReport($pattern);
    // Output the existing goods
    if (!empty($purchasedGoods)) :
        foreach ($purchasedGoods as $index => $good) :
            if ($good['remaining_qty'] > 0) : ?>
                <tr class="<?= $good['is_transfered'] ? 'bg-orange-400' : 'even:bg-sky-100' ?>">
                    <td class="text-xs text-center w-2.5"><?= $index + 1 ?></td>
                    <td class="relative p-2 text-center text-lg text-white font-semibold uppercase <?= $good['sellerName'] == 'کاربر دستوری' ? 'bg-orange-400' : 'bg-sky-500' ?>">
                        <?= $good['sellerName'] == 'کاربر دستوری' ? '
                        <img class=" absolute right-12 shake" src="./assets/icons/warning.svg" />'
                            : '' ?>
                        <?= $good["partNumber"]; ?>
                    </td>
                    <td class="p-2 text-center text-sm text-white <?= getBrandBackground(strtoupper($good["brandName"])) ?> font-semibold uppercase"><?= $good["brandName"] ?></td>
                    <td class="p-2 text-center text-sm font-bold bg-yellow-100"><?= $good['remaining_qty'] ?></td>
                    <td class="p-2 text-sm uppercase text-center font-semibold <?= $item["seller_id"] == 61 ? 'text-white bg-red-600' : 'bg-yellow-500' ?>"><?= $good["sellerName"] ?></td>
                    <td class="p-2 text-center text-sm font-bold"><?= $good["pos1"] ?></td>
                    <td class="p-2 text-center text-sm font-bold"><?= $good["pos2"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold"><?= $good["quantityDescription"] ?></td>
                    <td class="p-2 text-xs text-gray-700 text-center font-semibold">
                        <?php
                        $stock = trim($good["stockName"]);
                        $theme = '';
                        if ($stock == "خاوران") {
                            $theme = 'bg-red-500';
                        } elseif ($stock == 'یدک شاپ') {
                            $theme = 'bg-green-700';
                        } elseif ($stock == 'انبار مشترک') {
                            $theme = 'bg-cyan-700';
                        } elseif ($stock == 'فرشاد') {
                            $theme = 'bg-blue-500';
                        } elseif ($stock == 'دوبی') {
                            $theme = 'bg-gray-800';
                        } elseif ($stock == 'انبار 2') {
                            $theme = 'bg-sky-600"';
                        } elseif ($stock == 'لنتور') {
                            $theme = 'bg-cyan-500';
                        } elseif ($stock == 'چین') {
                            $theme = 'bg-cyan-500';
                        }
                        ?>
                        <p class='text-xs py-2 mx-2 rounded text-white <?= $theme ?>'> <?= $stock ?> </p>
                    </td>
                </tr>
        <?php
            endif;
        endforeach;
    else : ?>
        <tr>
            <td colspan="9" class="text-center text-sm text-white p-3 bg-red-500 font-semibold">هیچ کالایی با این شماره یافت نشد</td>
        </tr>
<?php endif;
    exit();
}
