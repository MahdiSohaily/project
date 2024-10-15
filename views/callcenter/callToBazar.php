<?php
$pageTitle = "تماس با بازار";
$iconUrl = 'favicon.ico';
require_once './components/header.php';
require_once '../../app/controller/callcenter/CallToBazarController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php'; ?>
<!-- search and categories section  -->
<section class="flex gap-2 items-center p-5">
    <input class="border-2 border-gray-400 p-2" id="myInput" type="text" placeholder="سرچ کنید ...">
    <a href="#" class="inline-block bg-gray-500 p-2 w-24 text-center rounded text-sm font-semibold text-white hover:bg-gray-600 hyundaimobisshow">هیوندای موبیز</a>
    <a href="#" class="inline-block bg-gray-500 p-2 w-24 text-center rounded text-sm font-semibold text-white hover:bg-gray-600 kiamobisshow">کیا موبیز</a>
    <a href="#" class="inline-block bg-gray-500 p-2 w-24 text-center rounded text-sm font-semibold text-white hover:bg-gray-600 mobisshow">موبیز</a>
    <a href="#" class="inline-block bg-gray-500 p-2 w-24 text-center rounded text-sm font-semibold text-white hover:bg-gray-600 kiashow">کیا</a>
    <a href="#" class="inline-block bg-gray-500 p-2 w-24 text-center rounded text-sm font-semibold text-white hover:bg-gray-600 hyundaishow">هیوندای</a>
</section>

<!-- Partners information section -->
<section class="px-5">
    <table class="w-full estelam-table">
        <thead>
            <tr class="bg-gray-800">
                <td class="text-white text-sm font-semibold p-3">معرفی</td>
                <td class="text-white text-sm font-semibold p-3">وضعیت</td>
                <td class="text-white text-sm font-semibold p-3">فروشگاه</td>
                <td class="text-white text-sm font-semibold p-3">شماره تماس</td>
                <td class="text-white text-sm font-semibold p-3">توضیحات</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($allSellers) :
                foreach ($allSellers as $row) :
                    $sellerId = $row['id'];
                    $sellerName = $row['name'];
                    $sellerPhone = $row['phone'];

                    $sellerPhone = "<span class='text-white bg-rose-600 rounded mx-1 p-2 cursor-pointer'>" . str_replace("\n", "</span><span class='text-white bg-rose-600 rounded mx-1 p-2 cursor-pointer'>", $sellerPhone) . "</span>";
                    $sellerWhoIs = $row['whois'];
                    $sellerKind = $row['kind'];
                    $sellerDes = $row['des']; ?>
                    <tr class="even:bg-gray-100">
                        <td class="p-3 sellerwhois"><?= $sellerWhoIs ?></td>
                        <td class="p-3 <?= $sellerKind ?> sellerkind flex gap-2 flex-center ">
                            <span class="w-5 h-5 rounded-full p-2 border-2"></span>
                            <span class="w-5 h-5 rounded-full p-2 border-2"></span>
                            <span class="w-5 h-5 rounded-full p-2 border-2"></span>
                            <span class="w-5 h-5 rounded-full p-2 border-2"></span>
                            <span class="w-5 h-5 rounded-full p-2 border-2"></span>
                            <span class="w-5 h-5 rounded-full p-2 border-2"></span>
                        </td>
                        <td class="p-3 sellername" tableid="<?= $sellerId ?>"> <?= $sellerName ?></td>
                        <td class="p-3 sellerphone"><?= $sellerPhone ?></td>
                        <td class="p-3 sellerdes"><?= $sellerDes ?></td>
                    </tr>
            <?php
                endforeach;
            endif; ?>
        </tbody>
    </table>
</section>

<!-- Received prices operation section -->
<div class="bazar-click-to-cancel fixed bottom-5 right-5 bg-rose-600 hover:bg-rose-700 text-white rounded text-xm px-3 py-2 cursor-pointer" href="">قطع تماس جاری</div>
<!-- <form id="credential" class="estelam-form fixed left-5 bottom-5 bg-sky-700 p-3 rounded" action="" method="post" autocomplete="off">
    <div class="flex justify-between items-center gap-2 mb-2">
        <input style="direction: ltr !important;" class="p-2 outline-none text-sm font-semibold" type="text" onkeyup="convertToEnglish(this)" name="code[]" placeholder="کد فنی">
        <input style="direction: ltr !important;" class="p-2 outline-none text-sm font-semibold" type="text" name="price[]" placeholder="قیمت">
        <a class="remove-from-estelam-form" href="#">
            <i class="material-icons bold text-red-600">close</i>
        </a>
    </div>
    <div class="estelam-form-box">
        <input class="sellername-input p-2 outline-none text-sm font-semibold w-full" type="text" onkeyup="convertToPersian(this)" placeholder="فروشنده">
        <input type="text" name="sellerid" class="sellerid-input" value="" hidden>
        <div class="flex justify-between py-3">
            <span class="add-item bg-yellow-600 hover:bg-yellow-700 rounded text-white px-5 py-2 text-xs cursor-pointer">افزودن</span>
            <button class="bg-green-700 hover:bg-green-800 rounded px-5 py-2 text-xs text-white " type="submit">
                ذخیره
            </button>
        </div>
    </div>
</form> -->

<div id='form_operation' class="bg-green-600 fixed text-xs -bottom-50 text-white px-5 py-2 rounded -translate-x-1/2 left-1/2">Operation successful</div>
<!-- Scripts section -->
<script>
    const form_operation = document.getElementById('form_operation');
    $(document).ready(function() {
        $(".hyundaimobisshow").click(function() {
            $("tr").hide();
            $(".h.m").parent().show();
        });

        $(".kiamobisshow").click(function() {
            $("tr").hide();
            $(".k.m").parent().show();
        });

        $(".mobisshow").click(function() {
            $("tr").hide();
            $(".m").parent().show();
        });

        $(".kiashow").click(function() {
            $("tr").hide();
            $(".k").parent().show();
        });

        $(".hyundaishow").click(function() {
            $("tr").hide();
            $(".h").parent().show();
        });

        $(".save-estelam-form").prop('disabled', true);

        $(".add-item").click(function() {
            $("#credential").prepend(` <div class="flex justify-between items-center gap-2 mb-2">
                                            <input style="direction: ltr !important;" class="p-2 outline-none text-sm font-semibold" type="text" onkeyup="convertToEnglish(this)" name="code[]" placeholder="کد فنی">
                                            <input style="direction: ltr !important;" class="p-2 outline-none text-sm font-semibold" type="text" name="price[]" placeholder="قیمت">
                                            <a class="remove-from-estelam-form" href="#">
                                                <i class="material-icons bold text-red-600">close</i>
                                            </a>
                                        </div>`);
        });

        $(".sellerphone span").click(function() {
            $(this).removeClass("bg-rose-600");
            $(this).addClass("bg-green-600");
            $(".sellername-input").val($(this).parent().prev().text());
            $(".sellerid-input").val($(this).parent().prev().attr("tableid"));
            $(".save-estelam-form").prop('disabled', false);

            if (confirm($(this).parent().prev().text() + "\n" + "شماره تماس : " + $(this).text())) {
                window.open('http://admin:1028400NRa@<?= getIp($_SESSION["id"]) ?>/servlet?key=number=' + $(this).text() + '&outgoing_uri=@192.168.9.10', 'برقراری تماس', 'width=200,height=200')
            }
        });

        $(".bazar-click-to-cancel").click(function() {
            window.open('http://admin:1028400NRa@<?= getIp($_SESSION["id"]) ?>/servlet?key=CALLEND', 'برقراری تماس', 'width=200,height=200')
        });

        $(".estelam-form").on("click", ".remove-from-estelam-form", function() {
            $(this).parent().remove();
        });
    });

    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".estelam-table tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        const askedPricesForm = $(".estelam-form");

        askedPricesForm.submit(function(e) {
            e.preventDefault();
            let params = askedPricesForm.serialize();
            params = params + "&saveAskedPrice=saveAskedPrice";
            axios.post("../../app/api/callcenter/CallToBazarApi.php", params)
                .then(function(response) {
                    if (response.data) {
                        form_operation.style.bottom = "10px";
                        form_operation.classList.remove("bg-gray-800");
                        form_operation.classList.add("bg-green-600");
                        form_operation.innerHTML = "عملیات با موفقیت انجام شد";

                        const priceInputs = document.querySelectorAll('input[name="price[]"]');
                        // Set the value of each input to null
                        priceInputs.forEach(input => {
                            input.value = null;
                        });
                        setTimeout(() => {
                            form_operation.style.bottom = "-300px";
                        }, 4000);
                    } else {
                        form_operation.style.bottom = "10px";
                        form_operation.classList.remove("bg-green-600");
                        form_operation.classList.add("bg-gray-800");
                        form_operation.innerHTML = "عملیلت با خطا مواجه شد. وارد کردن کد فنی و قیمت الزامی است";
                        setTimeout(() => {
                            form_operation.style.bottom = "-300px";
                            window.location.reload();
                        }, 4000);
                    }
                })
                .catch(function(error) {});
        });
    });
</script>

<?php
require_once './components/footer.php';