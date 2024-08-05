<?php
$pageTitle = "تنظیم فاکتور پیش فروش";
$iconUrl = 'sell.svg';
require_once './components/header.php';
require_once '../../app/controller/factor/CompleteFactorController.php';
require_once '../../app/controller/factor/preSellFactorController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php'; ?>
<div class="flex justify-between items-center bg-gray-200 p-3 m-2">
    <table>
        <tr>
            <td class="text-sm px-2 font-semibold">مشتری</td>
            <td class="text-sm px-2"><?= $customerInfo['name'] . ' ' . $customerInfo['family'] ?></td>
        </tr>
        <tr>
            <td class="text-sm px-2 font-semibold">آدرس</td>
            <td class="text-sm px-2"><?= $customerInfo['address'] ?></td>
        </tr>
        <tr>
            <td class="text-sm px-2 font-semibold">شماره تماس</td>
            <td class="text-sm px-2"><?= $customerInfo['phone'] ?></td>
        </tr>
        <tr>
            <td class="text-sm px-2 font-semibold">ماشین</td>
            <td class="text-sm px-2"><?= $customerInfo['car'] ?></td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="text-sm px-2 font-semibold">شماره فاکتور</td>
            <td class="text-sm px-2"><?= $factorInfo['billNO'] ?></td>
        </tr>
        <tr>
            <td class="text-sm px-2 font-semibold">تاریخ</td>
            <td class="text-sm px-2"><?= $factorInfo['date'] ?></td>
        </tr>
        <tr>
            <td class="text-sm px-2 font-semibold">اقلام</td>
            <td class="text-sm px-2"><?= $factorInfo['quantity'] ?></td>
        </tr>
        <tr>
            <td class="text-sm px-2 font-semibold">مبلغ</td>
            <td class="text-sm px-2"><?= $factorInfo['total'] ?></td>
        </tr>
    </table>
</div>

<div class="pb-12">
    <table class="w-full">
        <thead>
            <tr class="bg-gray-800">
                <th class="text-white text-sm text-right px-3 py-2">#</th>
                <th class="text-white text-sm text-right px-3 py-2">نام کالا</th>
                <th class="text-white text-sm text-right px-3 py-2">تعداد</th>
                <th class="text-white text-sm text-right px-3 py-2">قیمت واحد</th>
                <th class="text-white text-sm text-right px-3 py-2">قیمت کل</th>
                <th class="text-white text-sm text-right px-3 py-2">توضیحات</th>
                <th class="text-white text-sm text-center px-3 py-2 w-80">اقلام</th>
                <th class="text-white text-sm text-right px-3 py-2 w-72">جستجو</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($billItems as $key => $item) : ?>
                <tr class="even:bg-gray-200">
                    <td class="px-3 py-2 text-sm font-semibold"><?= $key + 1 ?></td>
                    <td class="px-3 py-2 text-sm font-semibold"><?= $item['partName'] ?></td>
                    <td class="px-3 py-2 text-sm font-semibold"><?= $item['quantity'] ?></td>
                    <td class="px-3 py-2 text-sm font-semibold"><?= formatAsMoney($item['price_per']) ?></td>
                    <td class="px-3 py-2 text-sm font-semibold"><?= formatAsMoney($item['price_per'] * $item['quantity']) ?></td>
                    <td class="px-3 py-2 text-sm font-semibold flex items-center">
                        <textarea id="description_<?= $item['id'] ?>" onblur="saveDescription(this.value, <?= $item['id'] ?>)" class="border-2 border-gray-300 outline-none w-full bg-transparent p-2" name="description"></textarea>
                    </td>
                    <td class="py-2 text-sm font-semibold">
                        <div class="containers" id="container_<?= $item['id'] ?>"></div>
                    </td>
                    <td class="p-2 text-sm font-semibold relative">
                        <div class="relative">
                            <input style="direction: ltr !important;" onkeyup="searchGoods(this.value,<?= $item['id'] ?>)" class="border-2 border-gray-300 outline-none px-3 py-2 bg-transparent w-full" type="search" id="input_<?= $item['id'] ?>" placeholder="جستجو">
                            <div id="resultBox_<?= $item['id'] ?>" class="resultBoxes absolute hidden right-0 left-0 top-full bg-gray-100 z-50 shadow">
                                <div class="p-2">
                                    <img class="cursor-pointer" onclick="closeSearch(<?= $item['id'] ?>)" src="../../public/icons/close_red.svg" alt="close icon">
                                </div>
                                <div class="px-2 pb-2 mb-14" id="result_<?= $item['id'] ?>">
                                    <!-- search results will be appended here -->
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="fixed bottom-0 p-3 w-full bg-gray-800 z-50 flex justify-between items-center">
    <div>
        <button onclick="saveBill()" class="bg-sky-600 hover:bg-sky-700 text-sm text-white rounded px-4 py-2">ثبت فاکتور</button>
        <button onclick="saveBill(); printFactor()" id="complete_save_button" class="bg-white rounded text-gray-800 px-3 py-1 cursor-pointer">
            پرینت
        </button>
    </div>
    <p id="operation" class="hidden text-green-600"></p>
</div>
<script>
    const endpointAddress = "../../app/api/inventory/SellApi.php";
    const FactorEndpointAddress = "../../app/api/factor/PreSellFactorApi.php";
    const factorItems = <?= json_encode($billItems) ?>;
    const billId = <?= $factorInfo['id'] ?>;

    <?php if ($preSellFactor) { ?>
        let billItems = <?= json_decode($preSellFactorItems, true) ?>;
        let billItemsDescription = <?= json_decode($preSellFactorItemsDescription, true) ?>;
    <?php } else { ?>
        billItems = {};
        billItemsDescription = {};
    <?php } ?>

    // Get the keys of billItems
    let billItemOrder = Object.keys(billItems);

    previewFactor();

    for (const item of Object.keys(billItemsDescription)) {
        document.getElementById('description_' + item).value = billItemsDescription[item];
    }

    for (const item of factorItems) {
        billItemsDescription[item.id] = [];
    }

    function searchGoods(pattern, index) {
        pattern = pattern.trim().replace(/\s+/g, '');

        if (pattern.length < 7) {
            return;
        }

        const resultBoxes = document.querySelectorAll('.resultBoxes');
        resultBoxes.forEach(box => box.classList.add('hidden'));

        const resultContainer = document.getElementById('resultBox_' + index);
        resultContainer.classList.remove('hidden');

        const resultBox = document.getElementById('result_' + index);

        resultBox.innerHTML = '<img class="w-8 h-8 mx-auto" src="../../public/img/loading.png" />';
        var params = new URLSearchParams();
        params.append('searchGoods', 'searchGoods');
        params.append('pattern', pattern);
        axios.post(endpointAddress, params)
            .then(function(response) {
                let goods = Object.values(response.data);
                resultBox.innerHTML = '';
                goods = sanitizeData(goods, index);
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    function closeSearch(key) {
        document.getElementById('resultBox_' + key).classList.add('hidden');
        document.getElementById('input_' + key).value = null;
    }

    function sanitizeData(goods, index) {
        goods = goods.map(good => {
            if (billItems.hasOwnProperty(good.quantityId)) {
                good.remaining_qty -= billItems[good.quantityId].quantity;
            }
            return good;
        });

        goods = goods.filter(good => good.remaining_qty > 0);

        displayGoods(goods, index);
    }

    function displayGoods(goods, index) {
        const resultBox = document.getElementById('result_' + index);
        const bgColors = ['bg-red-600', 'bg-orange-600', 'bg-lime-700', 'bg-emerald-600', 'bg-teal-600', 'bg-cyan-600', 'bg-sky-600', 'bg-indigo-700', 'bg-pink-600'];

        if (goods.length > 0) {
            for (good of goods) {
                resultBox.innerHTML += `
            <div id="${good.quantityId}" class="bg-gray-100 shadow rounded-lg overflow-hidden mb-2 selected_card">
                <div class="bg-gray-800 rounded-t-md p-2">
                    <p class="text-left font-semibold text-white text-sm">
                        ${good.partNumber}
                    </p>
                </div>
                <div>
                <div class="cardBody">
                    <table style="direction: ltr !important;" class="w-full border border-x-2 border-gray-800">
                        <tbody>
                            <tr>
                                <td class="p-2 text-center text-gray-800 font-semibold text-sm quantity">${good.remaining_qty}</td>
                                <td class="p-2 text-center text-gray-800 font-semibold text-sm brandName">${good.brandName}</td>
                                <td class="p-2 text-center text-gray-800 font-semibold text-sm sellerName">${good.sellerName}</td>
                                <td class="px-1 text-center text-gray-800 font-semibold text-xs stockName">
                                    <p class="${bgColors[good.stockId - 1]} text-white p-2 w-20 rounded text-center">
                                        ${good.stockName}
                                    </p>
                                    <p class="text-xs text-red-500 text-center p-1">${good.quantityDescription}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                    <div class="px-2 py-1 bg-gray-800">
                        <input class="px-2 py-1 w-20 rounded text-sm text-center font-semibold" placeholder="تعداد" type="number" value="1" min="1" max="${good.remaining_qty}">
                        <button onclick="addToFactor(this, ${index})"
                        data-partNumber = "${good.partNumber}"
                        data-quantityId = "${good.quantityId}"
                        data-goodId = "${good.goodId}"
                        data-stockId = "${good.stockId}"
                        data-stockName = "${good.stockName}"
                        data-brandName = "${good.brandName}"
                        data-sellerName = "${good.sellerName}"
                        data-brandId = "${good.brandId}"
                        data-sellerId = "${good.sellerId}"
                        data-quantity = "${good.remaining_qty}"
                        data-pos1 = "${good.pos1}"
                        data-pos2 = "${good.pos2}"
                        class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1 rounded text-sm">افزودن</button>
                    </div>
                </div>
            </div>
            `;
            }

            const selectedCards = document.querySelectorAll('.selected_card');
            selectedCards.forEach(card => {
                card.addEventListener('click', function() {
                    selectedCards.forEach(c => c.querySelector(".cardBody").classList.remove('bg-green-300'));
                    card.querySelector(".cardBody").classList.add('bg-green-300');
                });
            });
        } else {
            resultBox.innerHTML = '<p class="text-center bg-gray-200 text-sm font-semibold p-3 rounded text-red-500">هیچ کالایی یافت نشد</p>';
        }
    }

    function addToFactor(element, index) {
        const sellQuantity = Number(element.previousElementSibling.value);
        const partNumber = element.getAttribute('data-partNumber');
        const quantityId = element.getAttribute('data-quantityId');
        const goodId = element.getAttribute('data-goodId');
        const stockId = element.getAttribute('data-stockId');
        const stockName = element.getAttribute('data-stockName');
        const brandName = element.getAttribute('data-brandName');
        const sellerName = element.getAttribute('data-sellerName');
        const brandId = element.getAttribute('data-brandId');
        const sellerId = element.getAttribute('data-sellerId');
        const pos1 = element.getAttribute('data-pos1');
        const pos2 = element.getAttribute('data-pos2');
        const quantity = Number(element.getAttribute('data-quantity'));

        // Validate the sell quantity
        if (sellQuantity < 1 || sellQuantity > quantity) {
            alert('تعداد وارد شده باید بین 1 تا ' + quantity + ' باشد');
            return;
        }

        const difference = quantity - sellQuantity;
        const parentElement = document.getElementById(quantityId);

        // Update the quantity display
        parentElement.querySelector('.quantity').innerHTML = difference;
        element.setAttribute('data-quantity', difference);
        element.previousElementSibling.value = 1;
        element.previousElementSibling.setAttribute('max', difference);

        // Remove the element if the quantity is zero
        if (difference === 0) {
            parentElement.remove();
        }

        // Update or add the item in billItems
        if (billItems.hasOwnProperty(quantityId)) {
            billItems[quantityId].quantity += sellQuantity;
        } else {
            billItems[quantityId] = {
                quantityId: quantityId,
                id: index,
                goodId: goodId,
                partNumber: partNumber,
                stockId: stockId,
                stockName: stockName,
                brandName: brandName,
                sellerName: sellerName,
                quantity: sellQuantity,
                pos1: pos1,
                pos2: pos2
            };
            if (!billItemOrder.includes(quantityId)) {
                billItemOrder.push(quantityId);
            }
        }

        // Update the preview
        previewFactor(index);
    }

    function previewFactor(index) {

        const containers = document.querySelectorAll('.containers');
        containers.forEach(container => container.innerHTML = '');

        // Check if there are any items in billItems
        if (Object.keys(billItems).length === 0) {
            previewFactor.innerHTML = '<p class="p-2 text-rose-700 text-center text-sm font-semibold shadow">موردی برای نمایش وجود ندارد</p>';
            return;
        }

        billItemOrder.forEach(quantityId => {
            const item = billItems[quantityId];
            const previewFactor = document.getElementById('container_' + item.id);
            if (item) {
                previewFactor.innerHTML += `
                <div id='item-${item.quantityId}' class="rounded bg-gray-200 shadow mb-2 overflow-hidden border border-2 border-gray-800">    
                    <table style="direction: ltr !important;" class="w-full">
                            <tbody>
                                <tr>
                                    <td class="p-3 text-left text-gray-800 font-bold text-xs">${item.partNumber}</td>
                                    <td class="p-3 text-left text-gray-800 font-semibold text-xs">${item.quantity}</td>
                                    <td class="p-3 text-left text-gray-800 font-semibold text-xs">${item.brandName}</td>
                                    <td class="p-3 text-left text-gray-800 font-semibold text-xs">${item.pos1}</td>
                                    <td class="p-3 text-left text-gray-800 font-semibold text-xs">${item.pos2}</td>
                                    <td class="p-3 text-left text-gray-800 font-semibold text-sm" onclick="deleteItem(${item.quantityId})">
                                        <img class="cursor-pointer" src="../../public/icons/close_red.svg" alt="delete">
                                    </td>
                                </tr>
                            </tbody>
                    </table>
                </div>`;
            }
        });
    }

    function deleteItem(quantityId) {
        const item = billItems[quantityId];
        const parentElement = document.getElementById('item-' + quantityId);
        parentElement.remove();
        delete billItems[quantityId];
        previewFactor();
    }

    function saveDescription(value, index) {
        billItemsDescription[index] = value.trim();
    }

    function saveBill() {
        var params = new URLSearchParams();
        params.append('action', 'save_pre_bill');
        params.append('billId', billId);
        params.append('billItems', JSON.stringify(billItems));
        params.append('billItemsDescription', JSON.stringify(billItemsDescription));

        axios.post(FactorEndpointAddress, params)
            .then(function(response) {
                const status = response.data['status'];
                const operation = document.getElementById('operation');
                if (status === 'create') {
                    operation.classList.remove('hidden');
                    operation.innerHTML = 'فاکتور با موفقیت ثبت شد';
                    setTimeout(() => {
                        operation.classList.add('hidden');
                    }, 3000);
                } else {
                    operation.classList.remove('hidden');
                    operation.innerHTML = 'فاکتور با موفقیت ویرایش شد';
                    setTimeout(() => {
                        operation.classList.add('hidden');
                    }, 3000);
                }
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    function printFactor() {
        window.location.href = './yadakFactor.php?factorNumber=' + billId;
    }
</script>
<?php
require_once './components/footer.php';
