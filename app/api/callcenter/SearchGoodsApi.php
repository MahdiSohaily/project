<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../views/auth/403.php");
    exit;
}

require_once '../../../config/constants.php';
require_once '../../../database/db_connect.php';
require_once '../../../utilities/jdf.php';
require_once '../../../app/controller/callcenter/SearchGoodsController.php';

if (isset($_POST['pattern'])) {
    $pattern = $_POST['pattern'];
    $matchedGoods = getMatchedGoods($pattern);
    $rates = getRates();

    if (count($matchedGoods) > 0) {
        foreach ($matchedGoods as $item) {
            $partNumber = $item['partnumber'];
            $price = floatval($item['price']);
            $avgPrice = round((intval($price) * 110) / 243.5);
            $weight = round(intval($item['weight']), 2);
            $mobis = floatval($item['mobis']);
            $korea = floatval($item['korea']);
            $status = null;

            if ($mobis == "0.00") {
                $status = "NO-Price";
            } elseif ($mobis == "-") {
                $status = "NO-Mobis";
            } elseif ($mobis == NULL) {
                $status = "Request";
            } else {
                $status = "YES-Mobis";
            } ?>

            <tr class="transition duration-300 ease-in-out hover:bg-neutral-200">
                <td class='whitespace-nowrap text-sm font-semibold bg-blue-900'>
                    <div class='flex gap-1 text-white font-bold'>
                        <?php if ($status == "Request") { ?>
                            <a class='link-s ml-4 Request' target='_blank' href='./goodMobisPrice.php?partNumber=<?= $partNumber ?>'>?</a>
                        <?php
                        } else if ($status == "NO-Price") { ?>
                            <a class='link-s ml-4 NO-Price' target='_blank' href='./goodMobisPrice.php?partNumber=<?= $partNumber ?>'>!</a>
                        <?php
                        } else if ($status == "NO-Mobis") { ?>
                            <a class='link-s ml-4 NO-Mobis' target='_blank' href='./goodMobisPrice.php?partNumber=<?= $partNumber ?>'>x</a>
                        <?php
                        } else { ?> <span class='ml-11'></span>
                        <?php } ?>
                        <?= strtoupper($partNumber) ?>
                    </div>
                </td>
                <td class='whitespace-nowrap text-sm font-semibold text-center px-3 py-3'>
                    <?= round($avgPrice * 1.1) ?>
                </td>
                <td class='orange whitespace-nowrap text-sm font-semibold text-center px-3 py-3 border-black border-l-2'>
                    <?= round($avgPrice * 1.2) ?>
                </td>
                <?php
                if (count($rates) > 0) :
                    foreach ($rates as $rate) : ?>
                        <td class='whitespace-nowrap text-sm font-semibold px-3 py-3 text-center <?= $rate['status'] ?>'>
                            <?= round($avgPrice * $rate['amount'] * 1.2 * 1.2 * 1.3) ?>
                        </td>
                <?php
                    endforeach;
                endif;
                ?>
                <td class='whitespace-nowrap text-sm font-semibold w-24'>
                    <div class='flex justify-center gap-1 items-center px-2'>
                        <a target='_blank' href='https://www.google.com/search?tbm=isch&q=<?= $partNumber ?>'>
                            <img class='w-5 h-auto' src='../../public/img/google.png' alt='google'>
                        </a>
                        <a msg=' <?= $partNumber ?>'>
                            <img class='w-5 h-auto' src='../../public/img/tel.png' alt='part'>
                        </a>
                        <a target='_blank' href='https://partsouq.com/en/search/all?q=<?= $partNumber ?>'>
                            <img class='w-5 h-auto' src='../../public/img/part.png' alt='part'>
                        </a>
                    </div>
                </td>
                <td class='whitespace-nowrap text-sm font-semibold px-3 py-3 kg'>
                    <div class='weight'><?= $weight ?>KG</div>
                </td>
            </tr>
            <?php
            if ($status == "YES-Mobis") :
                $price = $mobis;
                $price = str_replace(",", "", $price);
                $avgPrice = round(($price * 110) / 243.5); ?>
                <tr class='mobis transition duration-400 ease-in-out hover:bg-neutral-500'>
                    <td class='text-white font-bold pr-12'><?= $partNumber ?>-M</td>
                    <td class='font-bold whitespace-nowrap text-sm font-semibold text-center px-3 py-3'><?= round($avgPrice) ?></td>
                    <td class='whitespace-nowrap text-sm font-semibold px-3 py-3 text-center border-black border-l-2'><?= round($avgPrice * 1.1) ?></td>
                    <?php
                    if (count($rates) > 0) :
                        foreach ($rates as $rate) : ?>
                            <td class="whitespace-nowrap text-sm font-semibold px-3 py-3 text-center  b-<?= $rate['status'] ?>">
                                <?= round($avgPrice * $rate['amount'] * 1.25 * 1.3) ?>
                            </td>
                    <?php
                        endforeach;
                    endif;
                    ?>
                    <td></td>
                    <td></td>
                </tr>
            <?php endif;
            if ($korea) :
                $price = $korea;
                $price = str_replace(",", "", $price);
                $avgPrice = round(($price * 110) / 243.5); ?>
                <tr class='mobis transition duration-400 ease-in-out bg-amber-600'>
                    <td class='text-white font-bold pl-12'> <?= $partNumber ?>K</td>
                    <td class='font-bold whitespace-nowrap text-sm font-semibold text-center px-3 py-3'><?= round($avgPrice) ?></td>
                    <td class='whitespace-nowrap text-sm font-semibold px-3 py-3 text-center border-black border-l-2'><?= round($avgPrice * 1.1) ?></td>
                    <?php if (count($rates) > 0) :
                        foreach ($rates as $rate) : ?>
                            <td class='whitespace-nowrap text-sm font-semibold px-3 py-3 text-center  b-<?= $rate['status'] ?>'>
                                <?= round($avgPrice * $rate['amount'] * 1.25 * 1.3) ?>
                            </td>
                    <?php
                        endforeach;
                    endif;
                    ?>
                    <td></td>
                    <td></td>
                </tr>
<?php
            endif;
        }
    }
}

