<?php
$pageTitle = "قیمت های تلگرام";
$iconUrl = 'b-t.png';
require_once './components/header.php';
require_once '../../utilities/callcenter/GivenPriceHelper.php';
require_once '../../utilities/callcenter/DollarRateHelper.php';
require_once '../../utilities/inventory/ExistingHelper.php';
require_once '../../app/controller/callcenter/TelegramAskedPricesController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
if ($isValidCustomer) : ?>
    <link href="./assets/css/report.css" rel="stylesheet" />
    <div style="direction: ltr !important;" class="">
        <div style="direction: ltr !important;" class="accordion">
            <?php
            foreach ($finalResult as $reportResult) :
                if ($reportResult) {
                    $explodedCodes = $reportResult['explodedCodes'];
                    $not_exist = $reportResult['not_exist'];
                    $existing = $reportResult['existing'];
                    $customer = $reportResult['customer'];
                    $completeCode = $reportResult['completeCode'];
                    $notification = $reportResult['notification'];
                    $rates = $reportResult['rates'];
                    $messages = $reportResult['messages'];
                    $message_date = $reportResult['message_date'];
                    $fullName = $reportResult['fullName'];
                    $relation_ids = $reportResult['relation_id'];

                    foreach ($explodedCodes as $code_index => $code) {
                        $relation_id = array_key_exists($code, $relation_ids) ? $relation_ids[$code] : 'xxx';
                        $max = 0;
                        if (array_key_exists($code, $existing)) {
                            foreach ($existing[$code] as $item) {
                                $max  += max($item['relation']['sorted']);
                            }
                        }
                        if ($max > 0) : ?>
                            <div style="direction: ltr !important;" class="accordion-header bg-cyan-800 flex justify-between items-center">
                                <p class="flex items-center gap-2">
                                    <span class='text-white font-bold'><?= strtoupper($code) ?></span>
                                    <?php
                                    if ($max > 0) {
                                        echo '<i class="material-icons text-green-500 rounded-circle">check_circle</i>';
                                    } else {
                                        echo '<i class="material-icons text-red-600 rounded-circle">do_not_disturb_on</i>';
                                    } ?>
                                </p>
                                <div class="px-7">
                                    <img class='w-8 h-8 rounded-full inline' src="../../public/userimg/default.png" alt="User profile" srcset="">
                                    <span class="text-white text-xs"><?= $fullName ?></span>
                                </div>
                            </div>
                            <div class="accordion-content overflow-hidden bg-grey-lighter" style="<?= $max > 0 ? 'max-height: 1000vh;' : 'max-height: 0vh;' ?>">
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
                                        $customer = $customer;
                                        $completeCode = $completeCode;
                                        $limit_id = $relation['limit_alert']; ?>
                                        <div style="direction: ltr !important;" class="grid grid-cols-1 lg:grid-cols-11 gap-6 lg:gap-2 overflow-auto ">
                                            <?php
                                            $infoSize = 'lg:col-span-1';
                                            $existingSize = 'lg:col-span-6';
                                            $priceSize = 'lg:col-span-3';
                                            $messagesSize = 'lg:col-span-1';
                                            require './components/givenPrice/info.php';
                                            require './components/givenPrice/existing.php';
                                            require './components/givenPrice/price.php';
                                            require './components/givenPrice/messages.php';
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
                        endif;
                    }
                    ?>
            <?php
                }
            endforeach;
            ?>
            <p id="form_success" class="custom-alert success px-3 tiny-text">
                ! موفقانه در پایگاه داده ثبت شد
            </p>
            <p id="form_error" class=" custom-alert error px-3 tiny-text">
                ! ذخیره سازی اطلاعات ناموفق بود
            </p>
        </div>
        <a class="toTop" href="#">
            <i class="material-icons">arrow_drop_up</i>
        </a>
        <script src="./assets/js/givePrice.js"></script>
    <?php
else : ?>
        <p class='col-6 mx-auto flex items-center justify-center h-full'>کاربر درخواست دهنده و یا مشتری مشخص شده معتبر نمی باشد</p>
    <?php
endif;
require_once './components/footer.php';
