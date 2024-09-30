<!-- ENd the code info section -->
<div style="direction: ltr !important;" class="w-full bg-white <?= $existingSize ?> overflow-auto shadow-md relative py-2">
    <table style="direction: ltr !important;" class="w-full text-left text-sm font-light custom-table">
        <thead class="font-medium bg-gray-700">
            <tr>
                <th scope="col" class="px-3 py-3 text-white text-center">
                    شماره فنی
                </th>
                <th scope="col" class="px-3 py-3 text-white text-center">
                    موجودی
                </th>
                <th scope="col" class="px-3 py-3 text-white text-center">
                    قیمت به اساس نرخ ارز
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($sorted as $index => $element) : ?>
                <tr>
                    <td class="relative px-1 hover:cursor-pointer" data-part="<?= $goods[$index]['partnumber'] ?>" onmouseleave="hideToolTip(this)" onmouseover="showToolTip(this)">
                        <div class="relative">
                            <?php
                            $not_registered = !is_registered($goods[$index]['partnumber']);
                            $user = $_SESSION['username']; ?>
                            <p onclick="copyPartNumber(this, '<?= strtoupper($goods[$index]['partnumber']) ?>')" class="text-center bold bg-gray-600 <?= $not_registered ? 'text-white' : 'text-green-500' ?>  px-2 py-3">
                                <?php
                                echo strtoupper($goods[$index]['partnumber']);
                                // Calculate initial price and weight
                                $price = floatval($item['price']);
                                $avgPrice = round(($price * 110) / 243.5);
                                $weight = round(floatval($item['weight']), 2);

                                // Convert mobis and korea to floats
                                $mobis = floatval($item['mobis']);
                                $korea = floatval($item['korea']);

                                // Determine status based on mobis
                                $status = null;
                                switch ($mobis) {
                                    case 0.00:
                                        $status = "NO-Price";
                                        break;
                                    case "-":
                                        $status = "NO-Mobis";
                                        break;
                                    case NULL:
                                        $status = "Request";
                                        break;
                                    default:
                                        $status = "YES-Mobis";
                                        break;
                                }

                                // Calculate basePrice and tenPercent for avgPrice
                                $basePrice = round($avgPrice * 1.1);
                                $tenPercent = round($avgPrice * 1.2);

                                // Calculate mobis and mobisTenPercent
                                $mobisAvgPrice = round(($mobis * 110) / 243.5);
                                $mobisTenPercent = round($mobisAvgPrice * 1.1);

                                // Calculate korea and koreaTenPercent
                                $koreaAvgPrice = round(($korea * 110) / 243.5);
                                $koreaTenPercent = round($koreaAvgPrice * 1.1);

                                // Assign updated values to mobis and korea
                                $mobis = $mobisAvgPrice;
                                $korea = $koreaAvgPrice;
                                ?>
                            </p>
                            <div class="ordered-price-tooltip2" id="<?= $goods[$index]['partnumber'] . '-google' ?>">
                                <a target='_blank' href='https://www.google.com/search?tbm=isch&q=<?= $goods[$index]['partnumber'] ?>'>
                                    <img class="w-5 h-auto" src="../../public/img/google.png" alt="google">
                                </a>
                                <a target='_blank' href='https://partsouq.com/en/search/all?q=<?= $goods[$index]['partnumber'] ?>'>
                                    <img class="w-5 h-auto" src="../../public/img/part.png" alt="part">
                                </a>
                                <a title="بررسی تک آیتم" target='_blank' href='../inventory/singleItemReport.php?code=<?= $goods[$index]['partnumber'] ?>'>
                                    <img src="../../public/img/singleItem.svg" class="w-5 h-auto" alt="">
                                </a>
                                <a title="گزارش تقاضای بازار" target='_blank' href='../telegram/requests.php?type=hour&code=<?= $goods[$index]['partnumber'] ?>'>
                                    <img src="./assets/img/chart.svg" class="w-5 h-auto" alt="">
                                </a>
                                <a title="گزارش دلار "
                                    onclick="openDollarModal(
                                '<?= $basePrice ?>',
                                '<?= $tenPercent ?>',
                                '<?= $mobis ?>',
                                '<?= $mobisTenPercent ?>',
                                '<?= $korea ?>',
                                '<?= $koreaTenPercent ?>',
                                )">
                                    <img src="./assets/img/information.svg" class="w-5 h-auto" alt="">
                                </a>
                                <?php
                                if ($user == 'niyayesh' || $user == 'mahdi') {
                                    if ($not_registered) { ?>
                                        <a title="افزودن به لیست پیام خودکار" onclick="addSelectedGood('<?= $goods[$index]['partnumber'] ?>', this)">
                                            <img src="./assets/img/add_good.svg" class="w-5 h-auto" alt="">
                                        </a>
                                    <?php } else { ?>
                                        <a title="حذف از لیست پیام خودکار" onclick="deleteGood('<?= $goods[$index]['partnumber'] ?>', this)">
                                            <img src="./assets/img/deleteBill.svg" class="w-5 h-auto" alt="">
                                        </a>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </td>
                    <td class="px-1 pt-2 pb-10">
                        <table class="w-full text-sm font-light p-2">
                            <thead class="font-medium">
                                <tr>
                                    <?php
                                    if (abs(array_sum($exist[$index])) > 0) {
                                        foreach ($exist[$index] as $brand => $amount) {
                                            if ($amount > 0) { ?>
                                                <th onclick="appendBrand(this)" data-target="<?= $relation_id ?>" data-code="<?= $code ?>" data-price="<?= $brand ?>" data-part="<?= $partNumber ?>" scope="col" class="<?= $brand == 'GEN' || $brand == 'MOB' ? $brand : 'brand-default' ?> text-white text-sm text-center py-2 relative hover:cursor-pointer" data-key="<?= $index ?>" data-part="<?= $partNumber ?>" data-brand="<?= $brand ?>" onmouseover="seekExist(this)" onmouseleave="closeSeekExist(this)">
                                                    <?= $brand ?>
                                                    <div class="ordered-price-tooltip" id="<?= $index . '-' . $brand ?>">
                                                        <table class="w-full text-sm font-light p-2">
                                                            <thead class="font-medium bg-violet-800">
                                                                <tr>
                                                                    <th class="text-right p-2 text-xs">فروشنده</th>
                                                                    <th class="text-right p-2 text-xs"> موجودی</th>
                                                                    <th class="text-right p-2 text-xs">تاریخ</th>
                                                                    <th class="text-right p-2 text-xs">
                                                                        <img src="./assets/img/time.svg" alt="clock icon">
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                foreach ($stockInfo[$index] as $item) {
                                                                    if ($item !== 0 && $item['brandName'] === $brand && $item['remaining_qty'] > 0) { ?>
                                                                        <tr class="<?= $item['seller_name'] == 'کاربر دستوری' ? 'bg-red-500' : 'bg-gray-600' ?>">
                                                                            <td class="p-2 text-xs text-right"><?= $item['seller_name'] ?></td>
                                                                            <td class="p-2 text-xs text-right"><?= $item['remaining_qty'] ?></td>
                                                                            <td class="p-2 text-xs text-right"><?= (explode(' ', $item['invoice_date'])[0]) ?></td>
                                                                            <td class="p-2 text-xs text-right"><?= displayTimePassed($item['invoice_date']) ?></td>
                                                                        </tr>
                                                                    <?php } ?>
                                                                <?php
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </th>
                                    <?php }
                                        }
                                    } else {
                                        echo '<p class="text-rose-500 text-sm text-center font-bold">  موجود نیست </p>';
                                    } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="py-3">
                                    <?php foreach ($exist[$index] as $brand => $amount) :
                                        if ($amount > 0) : ?>
                                            <td class="<?= $brand == 'GEN' || $brand == 'MOB' ? $brand : 'brand-default' ?> whitespace-nowrap text-white px-3 py-2 text-center">
                                                <?= $amount; ?>
                                            </td>
                                    <?php endif;
                                    endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="px-1 pt-2 pb-10">
                        <table style="direction: ltr !important;" class="w-full text-left text-sm font-light">
                            <thead class="font-medium">
                                <tr>
                                    <?php foreach ($rates as $rate) : ?>
                                        <th class="text-white text-center py-2 <?= $rate['status'] !== 'N' ? $rate['status'] : 'bg-green-700' ?>">
                                            <?= $rate['amount'] ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="py-3">
                                    <?php foreach ($rates as $rate) :
                                        $price = doubleval($goods[$index]['price']);
                                        $price = str_replace(",", "", $price);
                                        $avgPrice = round(($price * 110) / 243.5);
                                        $finalPrice = round($avgPrice * $rate['amount'] * 1.2 * 1.2 * 1.3);
                                    ?>
                                        <td class="text-bold whitespace-nowrap px-3 py-2 text-center hover:cursor-pointer <?= $rate['status'] !== 'N' ? $rate['status'] : 'bg-gray-50' ?>" onclick="setPrice(this)" data-target="<?= $relation_id ?>" data-target="<?= $relation_id ?>" data-code="<?= $code ?>" data-price="<?= $finalPrice ?>" data-part="<?= $partNumber ?>">
                                            <?= $finalPrice ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php if ($goods[$index]['mobis'] > 0 && $goods[$index]['mobis'] !== '-') : ?>
                                    <tr class="bg-neutral-300">
                                        <?php foreach ($rates as $rate) :
                                            $price = doubleval($goods[$index]['mobis']);
                                            $price = str_replace(",", "", $price);
                                            $avgPrice = round(($price * 110) / 243.5);
                                            $finalPrice = round($avgPrice * $rate['amount'] * 1.25 * 1.3);
                                        ?>
                                            <td class="text-bold whitespace-nowrap px-3 text-center py-2 hover:cursor-pointer" onclick="setPrice(this)" data-target="<?= $relation_id ?>" data-code="<?= $code ?>" data-price="<?= $finalPrice ?>" data-part="<?= $partNumber ?>">
                                                <?= $finalPrice ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endif; ?>

                                <?php if ($goods[$index]['korea'] > 0 && $goods[$index]['mobis'] !== '-') : ?>
                                    <tr class="bg-amber-600" v-if="props.relation.goods[key].korea > 0">
                                        <?php foreach ($rates as $rate) :
                                            $price = doubleval($goods[$index]['korea']);
                                            $price = str_replace(",", "", $price);
                                            $avgPrice = round(($price * 110) / 243.5);
                                            $finalPrice = round($avgPrice * $rate['amount'] * 1.25 * 1.3);
                                        ?>
                                            <td class="text-bold whitespace-nowrap px-3 text-center py-2 hover:cursor-pointer" onclick="setPrice(this)" data-target="<?= $relation_id ?>" data-code="<?= $code ?>" data-price="<?= $finalPrice ?>" data-part="<?= $partNumber ?>">
                                                <?= $finalPrice ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($limit_id && $_SESSION['username'] === 'niyayesh' || $limit_id && $_SESSION['username'] === 'mahdi') :
        $fraction = explode('-', $limit_id);
        $id = $fraction[0];
        $type = $fraction[1];

        $overall = overallSpecification($id, $type);
        $inventory = inventorySpecification($id, $type);
        $mode = 'create';

        if ($overall) :
            $mode = 'update';
        else :
            $overall = ['original_all' => 0, 'fake_all' => 0];
            $inventory = ['original' => 0, 'fake' => 0];
        endif; ?>
        <div class="px-1 mt-4 mb-1">
            <form id="f-<?= $partNumber ?>" action="" class="bg-gray-200 rounded-md p-3" method="post">
                <input id="id" type="hidden" name="id" value="<?= $id ?>" />
                <input id="type" type="hidden" name="type" value="<?= $type ?>" />
                <input id="operation" type="hidden" name="operation" value="<?= $mode ?>" />
                <div class="flex gap-2">
                    <fieldset class="flex-grow">
                        <legend class="my-3 font-semibold"> هشدار موجودی انبار یدک شاپ:</legend>
                        <div class="col-span-12 sm:col-span-4 mb-3 flex flex-wrap gap-2 ">
                            <div class="flex-grow">
                                <label for="original" class="block font-medium text-sm text-gray-700">
                                    مقدار اصلی
                                </label>
                                <input name="original" value="<?= $inventory['original'] ? $inventory['original'] : 0 ?>" style="direction:ltr !important;" class="border border-2 text-sm outline-none border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="original" type="number" min='0' />
                            </div>
                            <div class="flex-grow">
                                <label for="fake" class="block font-medium text-sm text-gray-700">
                                    مقدار غیر اصلی
                                </label>
                                <input name="fake" value="<?= $inventory['fake'] ? $inventory['fake'] : 0 ?>" style="direction:ltr !important;" class="border border-2 text-sm outline-none border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="fake" type="number" min='0' />
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="flex-grow">
                        <legend class="my-3 font-semibold"> هشدار موجودی کلی:</legend>
                        <div class="col-span-12 sm:col-span-4 mb-3 flex flex-wrap gap-2 ">
                            <div class="flex-grow">
                                <label for="original" class="block font-medium text-sm text-gray-700">
                                    مقدار اصلی
                                </label>
                                <input name="original_all" value="<?= $overall['original_all'] ? $overall['original_all'] : 0 ?>" style="direction:ltr !important;" class="border border-2 text-sm outline-none border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="original_all" type="number" min='0' />
                            </div>
                            <div class="flex-grow">
                                <label for="fake" class="block font-medium text-sm text-gray-700">
                                    مقدار غیر اصلی
                                </label>
                                <input name="fake_all" value="<?= $overall['fake_all'] ? $overall['fake_all'] : 0 ?>" style="direction:ltr !important;" class="border border-2 text-sm outline-none border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="fake_all" type="number" min='0' />
                            </div>
                        </div>
                    </fieldset>
                </div>
                <button onclick="setLimitAlert(event)" data-form="<?= $partNumber ?>" class="text-xs bg-blue-500 hover:bg-blue-600 font-semibold px-5 py-2 rounded text-white" type="submit">ذخیره</button>
            </form>
        </div>
    <?php endif; ?>
</div>