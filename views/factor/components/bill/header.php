<div class="bill_header dashed">
    <div class="bill_info">
        <div class="nisha-bill-info">
            <div class="A-main">
                <div class="A-1">شماره</div>
                <div class="A-2"><span id="billNO"></span></div>
            </div>
            <div class="B-main">
                <div class="B-1">تاریخ</div>
                <div class="B-2"><span id="date"></span></div>
            </div>
        </div>
    </div>
    <div class="headline">
        <h2 style="margin-bottom: 7px;" class="element"><?= $title; ?></h2>
        <h2 style="margin-bottom: 7px;"><?= $subTitle; ?></h2>
    </div>
    <div class="log_section">
        <img class="logo" src="<?= $logo ?>" alt="logo of yadakshop">
    </div>
</div>
<div class="customer_info relative flex">
    <ul class="w-1/2">
        <li class="text-sm">
            نام :
            <span id="name"></span>
        </li>
        <li class="text-sm">
            شماره تماس :
            <span id="phone"></span>
        </li>
    </ul>
    <p class="w-1/2" id="userAddress" style="font-size: 13px;"></p>
    <img id="copy_icon" class="cursor-pointer" src="./assets/img/copy.svg" alt="copy customer info" onclick="copyInfo(this)">
</div>