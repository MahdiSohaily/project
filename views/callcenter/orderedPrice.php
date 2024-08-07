<?php
$pageTitle = "قیمت دستوری";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../utilities/callcenter/DollarRateHelper.php';
require_once '../../utilities/callcenter/GivenPriceHelper.php';
require_once '../../utilities/inventory/ExistingHelper.php';
require_once '../../app/controller/callcenter/OrderedPriceController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
if ($isValidCustomer) :
    if ($finalResult) :
        $explodedCodes = $finalResult['explodedCodes'];
        $not_exist = $finalResult['not_exist'];
        $existing = $finalResult['existing'];
        $customer = $finalResult['customer'];
        $completeCode = $finalResult['completeCode'];
        $notification = $finalResult['notification'];
        $rates = $finalResult['rates'];
        $relation_ids = $finalResult['relation_id']; ?>

        <link href="./assets/css/report.css" rel="stylesheet" />
        <section class="flex gap-8 justify-between">
            <div class="m-2 bg-gray-700 p-2 w-96">
                <table class="col-6 text-sm border border-white font-light mb-2 w-full h-full">
                    <thead class="font-medium bg-gray-700 border-b border-white">
                        <tr>
                            <th scope="col" class="p-3 text-white text-right">
                                نام
                            </th>
                            <th scope="col" class="px-3 text-white text-right">
                                نام خانوادگی
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-white">
                        <tr>
                            <td class="px-1">
                                <p class="text-right font-semibold px-2 py-3">
                                    <?= $customer_info['name'] ?>
                                </p>
                            </td>
                            <td class=" px-1">
                                <p class="text-right font-semibold px-2 py-3">
                                    <?= $customer_info['family'] ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="m-2 p-2 bg-gray-700  w-96">
                <form class="h-full border border-white flex gap-2 p-2 " target="_blank" action="./orderedPrice.php" method="post">
                    <div class="w-5/6 h-full">
                        <input type="text" name="givenPrice" value="givenPrice" id="form" hidden>
                        <input type="text" name="user" value="<?= $_SESSION["id"] ?>" hidden>
                        <input type="text" name="customer" value="1" id="target_customer" hidden>
                        <textarea style="direction: ltr !important;" onchange="filterCode(this)" id="code" name="code" required class="h-full bg-transparent w-full p-3 text-white placeholder-white" placeholder="لطفا کد های مورد نظر خود را در خط های مجزا قرار دهید"></textarea>
                    </div>
                    <button type="type" class="inline-flex self-end p-3 bg-indigo-500 border-indigo-700  rounded-md font-semibold text-xs text-white hover:bg-indigo-700">
                        جستجو
                    </button>
                </form>
            </div>
            <div class="m-2 p-2 bg-gray-700  w-4/12">
                <table style="direction: ltr !important;" class="w-full h-full text-sm p-2">
                    <thead class="font-medium">
                        <tr class="border">
                            <th class="text-left px-3 py-2 w-24">کد فنی</th>
                            <th class="text-left px-3 py-2">قیمت</th>
                            <th class="text-left px-3 py-2 w-24" onclick="closeTab()">
                                <i id="copy_all" title="کاپی کردن مقادیر دارای قیمت" onclick="copyItemsWith(this)" class="pr-1 text-sm material-icons hover:cursor-pointer text-green-500">content_copy</i>
                                <i id="copy_all" title="کاپی کردن مقادیر" onclick="copyPrice(this)" class="pr-2 text-sm material-icons hover:cursor-pointer text-rose-500">content_copy</i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="priceReport">
                        <?php
                        foreach ($explodedCodes as $code) {
                            $relation_id =  array_key_exists($code, $relation_ids) ? $relation_ids[$code] : 'xxx';
                            $max = 0;
                            if (array_key_exists($code, $existing)) {
                                foreach ($existing[$code] as $item) {
                                    $max += $item['relation']['existingQuantity'];
                                }
                            } ?>
                            <tr class="border">
                                <td class="px-3 py-2 text-left text-white hover:cursor-pointer" data-move="<?= $code ?>" onclick="onScreen(this)"><?= strtoupper($code) ?></td>
                                <td class="px-3 py-2 text-left text-white">
                                    <?php
                                    if (in_array($code, $not_exist)) {
                                        echo "<p class ='text-red-600' data-relation='" . $relation_id . "' id='" . $code . '-append' . "'>کد اشتباه</p>";
                                    } else {
                                        if ($max && current($existing[$code])['givenPrice']) {

                                            $target = current(current($existing[$code])['givenPrice']);
                                            $priceDate = $target['created_at'];

                                            $rawPrice = current(current($existing[$code])['givenPrice']);

                                            $existing_brands = getExistingBrands(current($existing[$code])['relation']['stockInfo']);
                                            $finalPrice = getFinalSanitizedPrice([$rawPrice], $existing_brands);

                                            if (!$finalPrice) {
                                                $finalPrice = $rawPrice['price'];
                                                if (checkDateIfOkay($applyDate, $priceDate) && $target['price'] !== 'موجود نیست') :
                                                    $rawGivenPrice = $target['price'];
                                                    $finalPrice = strtoupper(applyDollarRate($rawGivenPrice, $priceDate));
                                                endif;
                                            }
                                    ?>
                                            <p style='direction: ltr !important;' data-relation='<?= $relation_id ?>' id='<?= $code ?>-append' class="<?= $finalPrice !== 'موجود نیست' ? '' : 'text-yellow-400' ?>">
                                                <?= $finalPrice !== 'موجود نیست' ? $finalPrice : 'نیاز به بررسی' ?>
                                            </p>
                                        <?php
                                        } else if ($max) {
                                            echo "<p style='direction: ltr !important;' data-relation='" . $relation_id . "' id='" . $code . '-append' . "'class ='text-green-400'>نیاز به قیمت</p>";
                                        } else if ($max == 0) {
                                            echo "<p style='direction: ltr !important;' data-relation='" . $relation_id . "' id='" . $code . '-append' . "'>" . 'موجود نیست' . "</p>";
                                        }
                                        ?>
                                </td>
                                <td class="text-left py-2" onclick="closeTab()">
                                    <i title="کاپی کردن مقادیر" onclick="copyItemPrice(this)" class="px-4 text-white text-sm material-icons hover:cursor-pointer">content_copy</i>
                                </td>
                            <?php
                                    }
                            ?>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="accordion mb-10">
            <?php foreach ($explodedCodes as $code_index => $code) {

                $relation_id =  array_key_exists($code, $relation_ids) ? $relation_ids[$code] : 'xxx';
                $max = 0;
                if (array_key_exists($code, $existing)) {
                    foreach ($existing[$code] as $item) {
                        $max += $item['relation']['existingQuantity'];
                    }
                }
            ?>
                <div style="direction: ltr !important;" id="<?= $code ?>" class="accordion-header bg-cyan-800">
                    <p class="flex text-left items-center gap-2">
                        <span class='text-white font-semibold uppercase'><?= $code; ?></span>
                        <?php if (in_array($code, $not_exist)) {
                            echo '<i class="material-icons text-neutral-400">block</i>';
                        } else if ($max > 0) {
                            echo '<i class="material-icons text-green-500">check_circle</i>';
                        } else {
                            echo '<i class="material-icons text-red-600">do_not_disturb_on</i>';
                        } ?>

                    </p>
                </div>
                <div style="<?= $max > 0 ? 'max-height: 1000vh' : 'max-height: 0vh' ?>" class="accordion-content overflow-hidden bg-grey-lighter">
                    <?php
                    if (array_key_exists($code, $existing)) {
                        foreach ($existing[$code] as $index => $item) {
                            $partNumber = $index;
                            $information = $item['information'];
                            $relation = $item['relation'];
                            $goods =  $relation['goods'];
                            $exist =  $relation['existing'];
                            $sorted =  $relation['sorted'];
                            $stockInfo =  $relation['stockInfo'];
                            $givenPrice =  $item['givenPrice'];
                            $limit_id = $relation['limit_alert'];
                            $existing_brands = getExistingBrands($stockInfo);
                    ?>
                            <div style="direction: ltr !important;" class="grid grid-cols-1 gap-6 lg:grid-cols-11 lg:gap-2 mb-7">
                                <?php
                                $infoSize = 'lg:col-span-2';
                                $existingSize = 'lg:col-span-6';
                                $priceSize = 'lg:col-span-3';
                                require './components/givenPrice/info.php';
                                require './components/givenPrice/existing.php';
                                require './components/givenPrice/price.php';
                                ?>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="bg-white rounded-lg overflow-auto mb-3 py-4">
                            <p class="text-center">کد مد نظر در سیستم موجود نیست</p>
                        </div>
                    <?php } ?>
                </div>
            <?php
            }
            ?>
            <p id="form_success" class="custom-alert success px-3 tiny-text">
                موفقانه در پایگاه داده ثبت شد!
            </p>
            <p id="form_error" class=" custom-alert error px-3 tiny-text">
                ذخیره سازی اطلاعات ناموفق بود!
            </p>
        </section>
        <a class="toTop" href="#">
            <i class="material-icons">arrow_drop_up</i>
        </a>
        <p id="copied_message" style="display:none;position: fixed; top:50%; left:50%; transform: translate(-50%, -50%); font-size: 60px;font-weight: bold; color:seagreen">کد ها کاپی شدند</p>
        <script src="./assets/js/givePrice.js"></script>
<?php
    endif;
else :
    echo "<p class='col-6 mx-auto flex items-center justify-center h-full'>کاربر درخواست دهنده و یا مشتری مشخص شده معتبر نمی باشد</p>";
endif;
require_once './components/footer.php';
