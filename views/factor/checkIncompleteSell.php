<?php
$pageTitle = "ویرایش فاکتور";
$iconUrl = 'factor.svg';
require_once './components/header.php';
require_once '../../utilities/callcenter/GivenPriceHelper.php';
require_once '../../app/controller/factor/LoadFactorItemBrands.php';
require_once '../../utilities/callcenter/DollarRateHelper.php';
require_once '../../app/controller/factor/IncompleteFactorController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php';
?>
<link rel="stylesheet" href="./assets/css/bill.css" />
<link rel="stylesheet" href="./assets/css/incomplete.css" />
<div id="wholePage" class="bg-rose-300 mb-20">
    <?php
    /*require_once './components/factorSearch.php';*/ ?>
    <!-- Bill editing and information section -->
    <section class="rtl mb-4 mt-2">
        <!-- bill and customer information table -->
        <div class="bg-white shadow-md w-full">
            <div class="bg-gray-800 text-white p-3 ">
                مشخصات خریدار
            </div>
            <div class="min-w-full border border-gray-800 text-gray-400 mb-5 grid md:grid-cols-3 lg:grid-cols-5 gap-3 p-3">
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">تلفون</td>
                    <td class="py-2 px-4">
                        <input onblur="ifCustomerExist(this)" onkeyup="sanitizeCustomerPhone(this);updateCustomerInfo(this)" class="w-full p-2 border text-gray-500" placeholder="093000000000" type="text" name="phone" id="phone">
                        <p id="phone_error" class="hidden text-xs text-red-500 py-1">لطفا شماره تماس مشتری را وارد نمایید.</p>
                    </td>
                </div>
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">نام</td>
                    <td class="py-2 px-4">
                        <input class="w-full p-2 border" type="hidden" name="id" id="id">
                        <input class="w-full p-2 border" type="hidden" name="type" id="mode" value='create'>
                        <input onkeyup="updateCustomerInfo(this)" class="w-full p-2 border text-gray-500" placeholder="نام مشتری را وارد کنید..." type="text" name="name" id="name">
                        <p id="name_error" class="hidden text-xs text-red-500 py-1">لطفا اسم مشتری را وارد نمایید.</p>
                        <label class="text-xs ml-2 cursor-pointer" for="mr">
                            <input type="radio" class="ml-1" name="suffix" id="mr" onclick="appendPrefix('جناب آقای'); event.stopPropagation();">جناب آقای
                        </label>

                        <label class="text-xs ml-2 cursor-pointer" for="miss">
                            <input type="radio" class="ml-1" name="suffix" id="miss" onclick="appendPrefix('سرکار خانم'); event.stopPropagation();">سرکار خانم
                        </label>

                        <label class="text-xs ml-2 cursor-pointer" for="compony">
                            <input type="radio" class="ml-1" name="suffix" id="compony" onclick="appendPrefix('شرکت'); event.stopPropagation();">شرکت
                        </label>

                        <label class="text-xs ml-2 cursor-pointer" for="store">
                            <input type="radio" class="ml-1" name="suffix" id="store" onclick="appendPrefix('فروشگاه'); event.stopPropagation();">فروشگاه
                        </label>
                    </td>
                </div>
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">نام خانوادگی</td>
                    <td class="py-2 px-4">
                        <input onkeyup="updateCustomerInfo(this)" class="w-full p-2 border text-gray-500" placeholder="نام خانوادگی مشتری را وارد کنید..." type="text" name="family" id="family">
                    </td>
                </div>

                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">آدرس</td>
                    <td class="py-2 px-4">
                        <textarea onkeyup="updateCustomerInfo(this)" name="address" id="address" cols="30" rows="1" class="border p-2 w-full text-gray-500" placeholder="آدرس مشتری"></textarea>
                    </td>
                </div>
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">ماشین</td>
                    <td class="py-2 px-4">
                        <input onkeyup="updateCustomerInfo(this)" class="w-full p-2 border text-gray-500" placeholder="نوعیت ماشین مشتری را مشخص کنید" type="text" name="car" id="car">
                    </td>
                </div>
            </div>
        </div>
        <!-- bill body table -->
        <div class="bg-white shadow-md p-2 w-full col-span-3 mb-3">
            <div class=" mx-auto">
                <table class="min-w-full border border-gray-800 text-gray-400">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="py-2 px-4 border-b text-white w-10">#</th>
                            <th class="py-2 px-4 border-b text-white text-right w-2/4">نام قطعه</th>
                            <th class="py-2 px-4 border-b text-white w-18"> تعداد</th>
                            <th class="py-2 px-4 border-b text-white  w-18"> قیمت</th>
                            <th class="py-2 px-4 border-b text-white  w-18"> قیمت کل</th>
                            <th class="py-2 px-4 border-b w-12 h-12 font-medium  w-18">
                                <img class="bill_icon" src="./assets/img/setting.svg" alt="settings icon">
                            </th>
                        </tr>
                    </thead>
                    <tbody id="bill_body" class="text-gray-800">
                    </tbody>
                </table>
                <div class="flex flex-row justify-between py-5 gap-5">
                    <textarea onkeyup="updateFactorInfo(this)" class="border-2 border-gray-400 focus:border-gray-800 w-1/3 p-5 outline-none" name="description" id="description" placeholder="توضیحات فاکتور را وارد نمایید ..." cols="20" rows="3"></textarea>
                    <section class="rtl p-5 backdrop-blur-xl bg-black/20 m-5 rounded-md">
                        <ul class="list-disc list-inside">
                            <li class="text-sm">برای ایجاد آیتم جدید در فاکتور از کلیدهای ترکیبی <code class="text-white bg-black px-1 rounded-md text-xs">Ctrl + Shift</code> استفاده نمایید. </li>
                            <li class="text-sm">با استفاده از کلید <span class="text-white bg-black px-1 rounded-md text-xs">F9</span> میتوانید پیش فاکتور مشتری را مشاهده کنید.</li>
                            <li class="text-sm">برای جابجای راحت میان ستون ها از کلید <span class="text-white bg-black px-1 rounded-md text-xs">Tab</span> میتوانید استفاده کنید.</li>
                            <li class="text-sm">برای جابجای میان سطرها از کلید <span class="text-white bg-black px-1 rounded-md text-xs">Enter</span> میتوانید استفاده کنید.</li>
                            <li class="text-sm">برای ذخیره فاکتور میتوانید از کلیدهای ترکیبی <span class="text-white bg-black px-1 rounded-md text-xs">Alt + S</span> استفاده نمایید.</li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
        <div id="bill_description_details" class="bg-white mb-5">
            <div class="bg-gray-800 text-white text-center">
                <p class="p-3">
                    اطلاعات فاکتور
                </p>
            </div>
            <div class="min-w-full border border-gray-800 text-gray-400 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 p-2 gap-3">
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">تعداد اقلام</td>
                    <td class="py-2 px-4">
                        <input readonly class="w-full p-2 border text-gray-500" placeholder="تعداد اقلام فاکتور" type="text" name="quantity" id="quantity">
                    </td>
                </div>
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">جمع کل</td>
                    <td class="py-2 px-4">
                        <input readonly class="w-full p-2 border text-gray-500" placeholder="جمع کل اقلام فاکتور" type="text" name="totalPrice" id="totalPrice">
                    </td>
                </div>
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">تخفیف</td>
                    <td class="py-2 px-4">
                        <input onkeyup="updateFactorInfo(this)" class="w-full p-2 border text-gray-500" placeholder="0" type="number" name="discount" id="discount">
                    </td>
                </div>
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">مالیات (۰٪)</td>
                    <td class="py-2 px-4">
                        <input onkeyup="updateFactorInfo(this)" class="w-full p-2 border text-gray-500" placeholder="0" type="number" name="tax" id="tax">
                    </td>
                </div>
                <div>
                    <td class="py-2 px-3 text-white bg-gray-800 text-md">عوارض</td>
                    <td class="py-2 px-4">
                        <input onkeyup="updateFactorInfo(this)" class="w-full p-2 border text-gray-500" placeholder="0" type="number" name="withdraw" id="withdraw">
                    </td>
                </div>
            </div>
            <div>
                <p colspan="2" class="bg-gray-800 text-white px-3 py-2">
                    <span class="text-sm mr-x">مبلغ قابل پرداخت: </span>
                    <span id="total_in_word" class="px-3 text-sm"></span>
                </p>
            </div>
        </div>
    </section>
    <?php if ($_SESSION["financialYear"] == '1403') : ?>
        <!-- Bill Operation Section -->
        <section class="rtl fixed flex justify-between items-center min-w-full h-12 bottom-0 bg-gray-800 px-3">
            <ul class="flex gap-3">
                <li>
                    <button onclick="updateIncompleteFactor()" id="incomplete_save_button" class="bg-blue-400 text-white rounded px-3 py-1 cursor-pointer">
                        ذخیره تغییرات پیش فاکتور
                    </button>
                </li>
                <li>
                    <button onclick="generateBill(this)" id="complete_save_button" class="bg-white rounded text-gray-800 px-3 py-1 cursor-pointer disabled:opacity-50 disabled:bg-gray-300 disabled:cursor-not-allowed">
                        صدور فاکتور
                    </button>
                </li>
            </ul>
            <p id="save_message" class="hidden bg-white text-green-400 px-3 py-1">ویرایش موفقانه صورت گرفت</p>
            <p id="save_error_message" class="hidden bg-red-500 text-white px-3 py-1">خطا در ویرایش اطلاعات</p>
        </section>
    <?php endif; ?>
</div>
<?php
// Required Components
require_once './components/modal.php';
require_once './components/factor.php';
?>
<script>
    const BRANDS_ENDPOINT = '../../app/api/factor/LoadFactorItemBrandsAPI.php';
    // Accessing the conatainers to have global access for easy binding data
    const customer_results = document.getElementById('customer_results');
    const resultBox = document.getElementById("selected_box");
    const stock_result = document.getElementById("stock_result");
    const bill_body = document.getElementById("bill_body");
    let title = 'ویرایش پیش فاکتور';

    // Assign the customer info received from the server to the JS Object to work with and display after ward
    const customerInfo = <?= json_encode($customerInfo); ?>;
    factorInfo.totalInWords = numberToPersianWords(<?= (float)$factorInfo['total'] ?>)
    const factorItems = <?= $billItems ?>;
    const ItemsBrands = <?= $billItemsBrandAndPrice ?>;

    function bootstrap() {
        displayCustomer(customerInfo);
        displayBill();
    }

    // A functionn to display Bill customer information in the table
    function displayCustomer(customer) {
        if (customerInfo.displayName != "" && customerInfo.family != "") {
            title = customerInfo.displayName + " " + customerInfo.family;
        }

        document.getElementById("customer_factor").innerHTML = title;
        document.getElementById('id').value = customerInfo.id;
        document.getElementById('mode').value = customerInfo.mode;
        document.getElementById('name').value = customerInfo.displayName;
        document.getElementById('family').value = customerInfo.family;
        document.getElementById('phone').value = customerInfo.phone;
        document.getElementById('car').value = customerInfo.car;
        document.getElementById('address').value = customerInfo.address;
    }

    // A function to display bill items and calculate the amount and goods count and display bill details afterword
    function displayBill() {
        let counter = 0;
        let template = ``;
        let totalPrice = 0;
        factorInfo.quantity = 0;

        for (const item of factorItems) {
            const payPrice = Number(item.quantity) * Number(item.price_per);
            totalPrice += payPrice;
            factorInfo.quantity += Number(item.quantity);

            template += `
            <tr id="${item.id}" class="even:bg-gray-100 border-gray-800 add-column" >
                <td class="py-3 px-4 w-10 relative text-left">
                    <span>${counter + 1}</span>
                    <div class="absolute inset-0 flex flex-col items-start justify-center hidden-action">
                        <img onclick="addNewRowAt('before','${counter}')" title="افزودن ردیف قبل از این ردیف" class="cursor-pointer w-6" src="./assets/img/top_arrow.svg" />
                        <img onclick="addNewRowAt('after','${counter + 1}')" title="افزودن ردیف بعد از این ردیف" class="cursor-pointer w-6" src="./assets/img/bottom_arrow.svg" />
                    </div>
                </td>
                <td class="relative py-3 px-4 w-3/5" >
                    <input type="text" class="tab-op w-2/4 p-2 border-dotted border-1 text-gray-500 w-42" onchange="editCell(this, 'partName', '${item.id}', '${item.partName}'); searchForBrands(${this.value})" value="${item.partName}" />`;

            if (ItemsBrands[item['partNumber']]) {
                template += `<div class="absolute left-1/2 top-5 transform -translate-x-1/2 flex flex-wrap gap-1">`;
                for (const brand of Object.keys(ItemsBrands[item['partNumber']])) {
                    template += `<span style="font-size:12px" onclick="appendSufix('${item.id}','${brand}'); adjustPrice('${item.id}',${ItemsBrands[item['partNumber']][brand]})" class="cursor-pointer text-md text-white bg-sky-600 rounded p-1" title="">${brand}</span>`;
                }
                template += `</div>`;
            }
            template += `<div class="absolute left-5 top-5 flex flex-wrap gap-1 w-42">
                        <span style="font-size:12px" onclick="appendSufix('${item.id}','اصلی')" class="cursor-pointer text-md text-white bg-gray-600 rounded p-1" title="">اصلی</span>
                        <span style="font-size:12px" onclick="appendSufix('${item.id}','چین')" class="cursor-pointer text-md text-white bg-gray-600 rounded p-1" title="">چین</span>
                        <span style="font-size:12px" onclick="appendSufix('${item.id}','کره')" class="cursor-pointer text-md text-white bg-gray-600 rounded p-1" title="">کره</span>
                        <span style="font-size:12px" onclick="appendSufix('${item.id}','متفرقه')" class="cursor-pointer text-md text-white bg-gray-600 rounded p-1" title="">متفرقه</span>
                        <span style="font-size:12px" onclick="appendSufix('${item.id}','تایوان')" class="cursor-pointer text-md text-white bg-gray-600 rounded p-1" title="">تایوان</span>
                        <span style="font-size:12px" onclick="appendSufix('${item.id}','شرکتی')" class="cursor-pointer text-md text-white bg-gray-600 rounded p-1" title="">شرکتی</span>`;
            if (customerInfo.car != '' && customerInfo.car != null) {
                template += `<span style="font-size:12px" onclick="appendCarSufix('${item.id}','${customerInfo.car}')" class="cursor-pointer text-md text-white bg-gray-600 rounded p-1" title="">${customerInfo.car}</span>`;
            }
            template += `</div>
                </td>
                <td class="text-center w-18 py-3 px-4">
                    <input  onchange="editCell(this, 'quantity', '${item.id}', '${item.quantity}')" type="number" style="direction:ltr !important;" class="tab-op tab-op-number  p-2 border border-1 w-16" value="${item.quantity}" />
                </td>
                <td class="text-center py-3 px-4 w-18" >
                    <input onchange="editCell(this, 'price_per', '${item.id}', '${item.price_per}')" type="text" style="direction:ltr !important;" class="tab-op tab-op-number w-18 p-2 border " onkeyup="displayAsMoney(this);convertToEnglish(this)" value="${formatAsMoney(item.price_per)}" />
                </td>
                <td class="text-center py-3 px-4 ltr">${formatAsMoney(payPrice)}</td>
                <td class="text-center py-3 px-4 w-18 h-12 font-medium">
                    <img onclick="deleteItem(${item.id})" class="bill_icon" src="./assets/img/subtract.svg" alt="subtract icon">
                </td>
            </tr> `;
            counter++;
        }

        bill_body.innerHTML = template;
        factorInfo.totalPrice = (totalPrice);
        factorInfo.totalInWords = numberToPersianWords(totalPrice - factorInfo.discount);
        // Display the Bill Information
        document.getElementById('quantity').value = factorInfo.quantity;
        document.getElementById('discount').value = factorInfo.discount;
        document.getElementById('totalPrice').value = formatAsMoney(factorInfo.totalPrice);
        document.getElementById('total_in_word').innerHTML = factorInfo.totalInWords;
        document.getElementById('description').innerHTML = factorInfo.description;
    }

    // A function to display bill items and calculate the amount and goods count and display bill details afterword
    function updateBillDisplay() {
        let counter = 1;
        let totalPrice = 0;
        factorInfo.quantity = 0;

        for (const item of factorItems) {
            const payPrice = Number(item.quantity) * Number(item.price_per);
            totalPrice += payPrice;
            factorInfo.quantity += Number(item.quantity);
        }

        factorInfo.totalPrice = (totalPrice);
        factorInfo.totalInWords = numberToPersianWords(totalPrice);
        // Display the Bill Information
        document.getElementById('quantity').value = factorInfo.quantity;
        document.getElementById('quantity').value = factorInfo.quantity;
        document.getElementById('totalPrice').value = formatAsMoney(factorInfo.totalPrice);
        document.getElementById('total_in_word').innerHTML = factorInfo.totalInWords;
    }

    // Add new bill item manually using the icon on the browser or shift + ctrl key press
    function addNewBillItemManually() {
        factorItems.push({
            id: Math.floor(Math.random() * (9000000 - 1000000 + 1)) + 1000000,
            partName: "اسم قطعه",
            price_per: 0,
            quantity: 1,
            max: 'undefined',
            partNumber: 'NOTPART'
        });
        displayBill();
    }

    // This function adds a new bill item manually at a specific position
    function addNewRowAt(position, targetIndex) {
        // Insert the new object either before or after the target index
        const newItem = {
            id: Math.floor(Math.random() * (9000000 - 1000000 + 1)) + 1000000,
            partName: "اسم قطعه را وارد کنید.",
            price_per: 0,
            quantity: 1,
            max: 'undefined',
            partNumber: 'NOTPART'
        };

        // Ensure the targetIndex is within the valid range
        if (targetIndex >= 0 && targetIndex < factorItems.length) {
            console.log(targetIndex);
            if (position === 'before') {
                factorItems.splice(targetIndex, 0, newItem);
            } else if (position === 'after') {
                factorItems.splice(targetIndex, 0, newItem);
            } else {
                console.error("Invalid position. Use 'before' or 'after'.");
            }

            displayBill();
        } else if (targetIndex == factorItems.length && position === 'after') {
            // If 'after' is selected and the target index is at the end, add to the end
            factorItems.push(newItem);
            displayBill();
        } else {
            console.error("Invalid target index.");
        }
    }

    // Updating the bill inforation section (EX: setting the discount or tax)
    function updateFactorInfo(element) {
        const proprty = element.getAttribute("name");
        factorInfo[proprty] = element.value;
    }

    // updating the customer information by modifying the customer information table section 
    function updateCustomerInfo(element) {
        const proprty = element.getAttribute("name");
        customerInfo[proprty] = element.value;
        if (proprty == 'name') {
            customerInfo.displayName = element.value;
        }
        if (proprty == 'name' || proprty == 'family') {
            const name = customerInfo.displayName != null ? customerInfo.displayName : '';
            const family = customerInfo.family != null ? customerInfo.family : '';
            title = name + ' ' + family;
            document.getElementById("customer_factor").innerHTML = title;
        }
        displayBill();
    }

    // Edit the item property by clicking on it and giving a new value
    function editCell(cell, property, itemId, originalValue) {
        const newValue = cell.value;

        // Update the corresponding item in your data structure (factorItems)
        updateItemProperty(itemId, property, newValue, cell);

        if (property == 'quantity' || property == 'price_per') {
            const parentRow = cell.closest('tr');
            const secondToLastTd = parentRow.querySelector('td:nth-last-child(2)');

            const totalpriceParent = parentRow.querySelector('td:nth-last-child(3)');
            const totalpriceValue = Number(totalpriceParent.querySelector('input').value.replace(/\D/g, ""));

            const thirdToLastTd = parentRow.querySelector('td:nth-last-child(4)');
            const value = (thirdToLastTd.querySelector('input').value);
            // Find the second-to-last td element in the same row


            // Modify the innerHTML of the second-to-last td element
            if (secondToLastTd) {
                secondToLastTd.innerHTML = formatAsMoney(Number(totalpriceValue) * value); // Replace 'New Value' with the desired content
            }
        }

    }

    // Update the edited item property in the data source
    function updateItemProperty(itemId, property, newValue, cell) {
        newValue = newValue.replace(/,/g, '');
        for (let i = 0; i < factorItems.length; i++) {
            if (factorItems[i].id == itemId) {
                if (property !== 'quantity') {
                    factorItems[i][property] = newValue;
                    break;
                } else {
                    if (factorItems[i]['max'] === 'undefined') {
                        factorItems[i][property] = newValue;
                        break;
                    } else {
                        if (factorItems[i]['max'] >= newValue) {
                            factorItems[i][property] = newValue;
                            break;
                        } else {
                            displayModal("مقدار انتخاب شده بیشتر از مقداری موجودی در انبار بوده نمیتواند.");
                            break;
                        }
                    }
                }
            }
        }
        updateBillDisplay();
    }

    // Adding item snameElement
    function appendSufix(itemId, suffix) {
        for (let i = 0; i < factorItems.length; i++) {
            if (factorItems[i].id == itemId) {

                const partName = factorItems[i].partName;
                let lastIndex = partName.lastIndexOf('-');

                let result = lastIndex !== -1 ? partName.substring(0, lastIndex) : partName;
                factorItems[i].partName = result.trim() + ' - ' + suffix;
            }
        }
        displayBill();
    }

    // Adding item snameElement
    function adjustPrice(itemId, price) {
        for (let i = 0; i < factorItems.length; i++) {
            if (factorItems[i].id == itemId) {
                factorItems[i].price_per = price;
            }
        }
        displayBill();
    }

    // This function append a related prefix to the customer name
    function appendPrefix(prefix) {
        const nameElement = document.getElementById('name');
        if (customerInfo.name) {
            nameElement.value = prefix + ' ' + customerInfo.name.trim();
            customerInfo.displayName = prefix + ' ' + customerInfo.name.trim();
        }
    }

    // Append the customer car brand to the items
    function appendCarSufix(itemId, suffix) {
        for (let i = 0; i < factorItems.length; i++) {
            if (factorItems[i].id == itemId) {

                const partName = factorItems[i].partName;
                let lastIndex = partName;
                factorItems[i].partName = partName + ' ' + suffix;
            }
        }
        displayBill();
    }

    // deleiting the specific bill item
    function deleteItem(id) {
        for (let i = 0; i < factorItems.length; i++) {
            if (factorItems[i].id == id) {
                factorItems.splice(i, 1);
                break;
            }
        }
        displayBill();
    }

    function generateBill(element) {
        // Disable the element to avoid multiple requests
        element.disabled = true;

        // Set the date using Moment.js with Persian (Farsi) locale
        factorInfo.date = moment().locale('fa').format('YYYY/MM/DD');

        if (!checkIfReadyToUpdate('phone')) {
            // Enable the element before returning if validation fails
            element.disabled = false;
            return false;
        }

        if (!checkIfReadyToUpdate('name')) {
            // Enable the element before returning if validation fails
            element.disabled = false;
            return false;
        }

        if (factorItems.length <= 0) {
            displayModal('فاکتور مشتری خالی بوده نمیتواند.');
            // Enable the element before returning if factor is empty
            element.disabled = false;
            return false;
        }

        // Prepare parameters for the HTTP request
        var params = new URLSearchParams();
        params.append('GenerateCompleteFactor', 'GenerateCompleteFactor');
        params.append('customerInfo', JSON.stringify(customerInfo));
        params.append('factorInfo', JSON.stringify(factorInfo));
        params.append('factorItems', JSON.stringify(factorItems));

        // Send POST request to save invoice data
        axios.post("../../app/api/factor/CompleteFactorApi.php", params)
            .then(function(response) {
                const data = response.data;
                if (data) {
                    const save_message = document.getElementById('save_message');
                    save_message.classList.remove('hidden');

                    setTimeout(() => {
                        save_message.classList.add('hidden');
                        if (factorInfo['id']) {
                            localStorage.setItem('displayName', customerInfo.displayName);
                            if (factorInfo['partner']) {
                                window.location.href = './partnerFactor.php?factorNumber=' + factorInfo['id'];
                                return false;
                            }
                            window.location.href = './yadakFactor.php?factorNumber=' + factorInfo['id'];
                        }
                    }, 1000);

                } else {
                    const save_error_message = document.getElementById('save_error_message');
                    save_error_message.classList.remove('hidden');

                    setTimeout(() => {
                        save_error_message.classList.add('hidden');
                        // Enable the element after unsuccessful request
                        element.disabled = false;
                    }, 3000);
                }

            })
            .catch(function(error) {
                // Handle errors
                console.error("Error saving invoice data:", error);
                displayModal("An error occurred while saving the invoice data. Please try again later.");
                // Enable the element after error
                element.disabled = false;
            });
    }

    function insuranceBillDisplay() {
        localStorage.setItem('displayName', customerInfo.displayName);
        window.location.href = "./individualInsurance.php?factorNumber=" + factorInfo['id'];
    }

    // Update the incomplete 
    function updateIncompleteFactor() {
        if (factorInfo.date == 'null')
            factorInfo.date = moment().locale('fa').format('YYYY/MM/DD');

        if (!checkIfReadyToUpdate('phone')) {
            return false
        }

        if (!checkIfReadyToUpdate('name')) {
            return false
        }
        if (factorItems.length <= 0) {

            displayModal('فاکتور مشتری خالی بوده نمیتواند.')
            return false;
        }

        var params = new URLSearchParams();
        params.append('updateIncompleteFactor', 'updateIncompleteFactor');
        params.append('customerInfo', JSON.stringify(customerInfo));
        params.append('factorInfo', JSON.stringify(factorInfo));
        params.append('factorItems', JSON.stringify(factorItems));

        axios.post("../../app/api/factor/IncompleteFactorApi.php", params)
            .then(function(response) {
                const data = response.data;
                if (data) {
                    const save_message = document.getElementById('save_message');
                    save_message.classList.remove('hidden');

                    setTimeout(() => {
                        save_message.classList.add('hidden');
                    }, 3000);
                } else {
                    const save_error_message = document.getElementById('save_error_message');
                    save_error_message.classList.remove('hidden');

                    setTimeout(() => {
                        save_error_message.classList.add('hidden');
                    }, 3000);
                }
            })
            .catch(function(error) {
                console.log(error);
            });
    }

    // A function to check if the necessary information is provided to update the incomplete factor
    function checkIfReadyToUpdate(property) {
        if (customerInfo[property] === '' || customerInfo[property] === null) {
            document.getElementById(property).classList.add('border-2');
            document.getElementById(property).classList.add('border-red-600');
            document.getElementById(property + "_error").classList.remove('hidden');
            setTimeout(() => {
                document.getElementById(property + "_error").classList.add('hidden');
                document.getElementById(property).classList.remove('border-2');
                document.getElementById(property).classList.remove('border-red-600');
            }, 4000);
            return false;
        }
        return true;
    }

    // This function checks wheter the phone numbers is a valid number and correct the format
    function sanitizeCustomerPhone(inputElement) {
        // Get the input value and remove white spaces
        const phone = inputElement.value.replace(/\s/g, '')
        let cleanPhoneNumber = convertToEnglishNumbers(phone);

        // Remove any character except digits and '+'
        cleanPhoneNumber = cleanPhoneNumber.replace(/[^\d+]/g, '');

        // Check if cleanPhoneNumber is defined and not null
        if (cleanPhoneNumber && cleanPhoneNumber.indexOf('+98') === 0) {
            // If it does, replace '+98' with '0'
            cleanPhoneNumber = '0' + cleanPhoneNumber.slice(3);
        }

        // Update the input value
        inputElement.value = cleanPhoneNumber;
    }

    // A function to check if the phone number is already assigned to a customer
    function ifCustomerExist(element) {

        if (element.value.length > 0) {
            var params = new URLSearchParams();
            params.append('isPhoneExist', 'isPhoneExist');
            params.append('phone', element.value);

            axios.post("../../app/api/factor/FactorCommonApi.php", params)
                .then(function(response) {
                    const customer = response.data;
                    if (customer !== 0) {
                        document.getElementById('name').value = customer.name;
                        document.getElementById('family').value = customer.family;
                        document.getElementById('address').value = customer.address;
                        document.getElementById('car').value = customer.car;
                        customerInfo['id'] = customer.id;
                        customerInfo['name'] = customer.name;
                        customerInfo['displayName'] = customer.name;
                        customerInfo['family'] = customer.family;
                        customerInfo['address'] = customer.address;
                        customerInfo['car'] = customer.car;
                        customerInfo.mode = "update";
                    } else {
                        document.getElementById('name').value = null;
                        customerInfo['displayName'] = customer.name;
                        document.getElementById('family').value = null;
                        document.getElementById('address').value = null;
                        document.getElementById('car').value = null;
                        customerInfo['id'] = null;
                        customerInfo['name'] = null;
                        customerInfo['family'] = null;
                        customerInfo['address'] = null;
                        customerInfo['car'] = null;
                        customerInfo.mode = "create";
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        }


    }

    bootstrap(); // Display the form data after retrieving every initial data
    document.addEventListener("keydown", function(event) {
        // Check if the Ctrl key is pressed and the key is 'S'
        if (event.altKey && (event.key === "s" || event.key === "س")) {
            // Prevent the default browser behavior for Ctrl + S (e.g., saving the page)
            event.preventDefault();

            // Call the saveIncompleteForm function
            updateIncompleteFactor();

            // Optionally, use return false to further prevent default behavior
            return false;
        }
    });
</script>
<script src="./assets/js/billSearchPart.js?=<?= rand() ?>"></script>
<script src="./assets/js/displayBill.js?v=<?= rand() ?>"></script>
<script src="./assets/js/modal.js?v=<?= rand() ?>"></script>
<script src="./assets/js/billShortcuts.js?v=<?= rand() ?>"></script>
<?php
require_once './components/footer.php';
