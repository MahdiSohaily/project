<?php
$pageTitle = "ثبت قیمت بازار";
$iconUrl = 'favicon.ico';
require_once './components/header.php';
require_once '../../app/controller/callcenter/CallToBazarController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php'; ?>

<!-- Partners information section -->
<section class="px-5">
    <input class="p-2 my-2 outline-none text-gray-700 border-2 border-gray-700 w-56" type="search"
        onkeyup="convertToPersian(this);displaySellers(this.value)" ;
        onfocus="this.select()"
        placeholder="جستجو فروشنده ....">
    <table class="w-full">
        <thead id="tableHeading" class="font-medium sticky top-12" style="z-index: 99;">
        </thead>
        <tbody id="sellersContainer">
            <!-- Defined Sellers in the system will be displayed here -->
        </tbody>
    </table>
</section>
<section>
    <article class="fixed p-5 bg-gray-900/50 bottom-5 left-5 rounded-md">
        <h2 class="text-sm font-semibold">راهنمای استفاده از کلید های کمکی:</h2>
        <ul class="list-inside mt-3 list-disc">
            <li class="text-xs font-semibold mb-5">برای اضافه نمودن کد فنی جدید از کلید های ترکیبی
                <span class="text-xs bg-black text-white rounded px-2 py-1"> CTRL + SHIFT</span>
                استفاده نمایید.
            </li>
            <li class="text-xs font-semibold mb-5"> برای ذخیره سازی قیمت های گرفته شده از کلید های ترکیبی
                <span class="text-xs bg-black text-white rounded px-2 py-1"> CTRL + M</span>
                استفاده نمایید.
            </li>
        </ul>
        <button onclick="saveInquiredPrices()" class="text-sm p-2 bg-sky-600 hover:bg-sky-700 text-white rounded">ثبت قیمت ها</button>
    </article>
</section>
<script>
    const ALL_SELLERS = <?= json_encode($allSellers); ?>;
    const INQUIRED_CODS = [];
    const INQUIRED_PRICES = {};

    function displaySellers(filter = null) {
        if (filter != null && filter.length < 2 && filter.length != 0) {
            return false;
        }
        const container = document.getElementById('sellersContainer');
        const tableHeading = document.getElementById('tableHeading');

        // Clear the heading content
        tableHeading.innerHTML = '';

        // Create a new heading row
        let headingRow = `
        <tr class="bg-gray-800">
                <td class="text-white text-sm font-semibold p-3 w-8">ردیف</td>
                <td class="text-white text-sm font-semibold p-3 w-64">
                    اسم فروشنده
                </td>`;

        // Loop through INQUIRED_CODS to add editable columns dynamically
        for (let i = 0; i < INQUIRED_CODS.length; i++) {
            headingRow += `
                <td class="text-white text-sm font-semibold p-3 max-w-20">
                    <input class="p-2 w-full outline-none border-2 border-white text-white bg-gray-800 uppercase" 
                        type="text" 
                        onClick="this.select()"
                        value="${INQUIRED_CODS[i]}" 
                        style = "direction:ltr !important"
                        onKeyup="convertToEnglish(this);updateCode(${i}, this.value)">
                </td>`;
        }

        // Add the "Add New Code" button column
        headingRow += `
            <td>
                <span class="cursor-pointer" title="افزودن ستون جدید" onclick="addNewCode()">
                    <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.5" d="M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" fill="#ffffff"></path>
                        <path d="M12.75 9C12.75 8.58579 12.4142 8.25 12 8.25C11.5858 8.25 11.25 8.58579 11.25 9L11.25 11.25H9C8.58579 11.25 8.25 11.5858 8.25 12C8.25 12.4142 8.58579 12.75 9 12.75H11.25V15C11.25 15.4142 11.5858 15.75 12 15.75C12.4142 15.75 12.75 15.4142 12.75 15L12.75 12.75H15C15.4142 12.75 15.75 12.4142 15.75 12C15.75 11.5858 15.4142 11.25 15 11.25H12.75V9Z" fill="#ffffff"></path>
                    </svg>
                </span>
            </td>
        </tr>`;

        // Insert the constructed row into the table heading
        tableHeading.innerHTML = headingRow;

        // Clear the container content for the sellers data
        container.innerHTML = '';
        contentRow = '';

        // Loop through ALL_SELLERS and add each seller's data as a new row
        let counter = 1;
        for (const seller of ALL_SELLERS) {
            if (isSellerNameMatching(filter, seller.name)) {
                contentRow += `
            <tr class="even:bg-sky-100">
                <td class="p-2 font-semibold">${counter}</td>
                <td class="p-2 font-semibold">${seller.name}</td>
            `;
                for (let i = 0; i < INQUIRED_CODS.length; i++) {
                    contentRow += `
                    <td class="text-white text-sm font-semibold p-3 w-40">
                        <input class="p-2 w-full outline-none text-gray-900 bg-transparent border-2 " 
                            type="text" 
                            value="${INQUIRED_PRICES['seller_'+seller.id + '_' + i]?.['price'] ?? 0}" 
                            style = "direction:ltr !important"
                            onFocus="this.select()";
                            onKeyup="updatePrice(${seller.id},${i}, this.value)">
                    </td>`;
                }
                contentRow += `
                <td class="p-2"></td>
            </tr>`;
            }
            counter++;
        }

        container.innerHTML = contentRow;
    }

    // Function to update the INQUIRED_CODS array when a code is edited
    function updateCode(index, newValue) {
        INQUIRED_CODS[index] = newValue.toUpperCase();
    }

    function isSellerNameMatching(pattern, sellerName) {
        // If the pattern is null, return true
        if (pattern === null) {
            return true;
        }

        try {
            // Create a regular expression from the pattern with Unicode support
            const regex = new RegExp(pattern, 'u'); // 'u' flag ensures Unicode handling

            // Test if the sellerName matches the regular expression
            return regex.test(sellerName);
        } catch (e) {
            // If the pattern is invalid, handle the error (optional)
            console.error('Invalid regular expression pattern:', e);
            return false;
        }
    }

    function updatePrice(seller, code, price) {
        const property = 'seller_' + seller + '_' + code;
        if (price == 0) {
            delete INQUIRED_PRICES[property];
            return;
        }

        INQUIRED_PRICES[property] = {
            seller,
            code,
            price
        }
    }

    function addNewCode() {
        INQUIRED_CODS.push('کد را وارد کنید');
        displaySellers();
    }

    function saveInquiredPrices() {
        var params = new URLSearchParams();
        params.append('saveInquiredPrices', 'saveInquiredPrices');
        params.append('inquiredCodes', JSON.stringify(INQUIRED_CODS));
        params.append('inquiredPrices', JSON.stringify(INQUIRED_PRICES));

        axios.post("../../app/api/callcenter/CallToBazarApi.php", params)
            .then(function(response) {
                if (response.data) {
                    alert('قیمت ها موفقانه ثبت گردید.')
                    window.location.reload();
                } else {
                    alert('لطفا از صحت اطلاعات اطمینان حاصل نمایید.')
                }
            }).catch(function(error) {
                console.log(error);
            });

    }

    document.addEventListener('keydown', function(event) {
        if (event.ctrlKey && event.shiftKey) {
            addNewCode();
        }

        if (event.ctrlKey && event.key === 'm') {

            saveInquiredPrices();
        }
    });

    displaySellers();
</script>
<?php
require_once './components/footer.php';
