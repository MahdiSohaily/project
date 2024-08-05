<?php
$pageTitle = "مدیریت برند و فروشندگان اجناس";
$iconUrl = 'purchase.svg';
require_once './components/header.php';
require_once '../../app/controller/inventory/GoodsManagementController.php';
require_once '../../layouts/inventory/nav.php';
require_once '../../layouts/inventory/sidebar.php';
?>
<div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
    <section class="p-3 col-span-3">
        <div class="py-2 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold">لیست فروشندگان</h2>
                <p class="text-xs">برای ویرایش بروی ستون مورد نظر دبل کلیک نمایید.</p>
            </div>
            <button onclick="toggleForm('sellerModal')" class="bg-sky-600 text-white rounded-sm py-1 px-3">ایجاد</button>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-800 text-white text-sm">
                    <th class="p-3 text-center">#</th>
                    <th class="p-3 text-right">نام فروشنده</th>
                    <th class="p-3 text-right">نام لاتین</th>
                    <th class="p-3 text-right">شماره تماس</th>
                    <th class="p-3 text-right">آدرس</th>
                    <th class="p-3 text-right">دسته بندی</th>
                    <th class="p-3 text-right">نمایش</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $key => $seller) : ?>
                    <tr class="text-sm border-b border-gray-200 even:bg-gray-100">
                        <td class="p-3 text-center"><?= $key + 1 ?></td>
                        <td class="p-3" ondblclick="makeEditable('seller',this, 'name', <?= $seller['id'] ?>)"><?= $seller['name'] ?></td>
                        <td class="p-3" ondblclick="makeEditable('seller',this, 'latinName', <?= $seller['id'] ?>)"><?= $seller['latinName'] ?></td>
                        <td style="direction: ltr !important;" class="p-3" ondblclick="makeEditable('seller',this, 'phone', <?= $seller['id'] ?>)"><?= htmlspecialchars($seller['phone']) ?></td>
                        <td class="p-3" ondblclick="makeEditable('seller',this, 'address', <?= $seller['id'] ?>)"><?= $seller['address'] ?></td>
                        <td class="p-3" ondblclick="makeEditable('seller',this, 'kind', <?= $seller['id'] ?>)"><?= $seller['kind'] ?></td>
                        <td class="p-3">
                            <input type="checkbox" name="view" onclick="updateView('seller',<?= $seller['id'] ?>)" <?= $seller['views'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </section>
    <section class="p-3">
        <div class="py-2 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold"> لیست برندها</h2>
                <p class="text-xs">برای ویرایش بروی ستون مورد نظر دبل کلیک نمایید.</p>
            </div>
            <button onclick="toggleForm('brandModal')" class="bg-sky-600 text-white rounded-sm py-1 px-3">ایجاد</button>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-800 text-white text-sm">
                    <th class="py-2">#</th>
                    <th class="py-2">برند</th>
                    <th class="py-2">نمایش</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brands as $key => $brand) : ?>
                    <tr class="text-sm border-b border-gray-200 even:bg-gray-100">
                        <td class="p-3"><?= $key + 1 ?></td>
                        <td class="p-3" ondblclick="makeEditable('brand',this, 'name', <?= $brand['id'] ?>)"><?= $brand['name'] ?></td>
                        <td class="p-3">
                            <input type="checkbox" name="view" onclick="updateView('brand',<?= $brand['id'] ?>)" <?= $brand['views'] ? 'checked' : '' ?>>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </section>
</div>

<div id="sellerModal" class="fixed inset-0 bg-gray-800 flex justify-center items-center hidden">
    <div class="bg-white p-5 rounded shadow">
        <div class="modal-box">
            <div class="flex justify-between items-center py-5">
                <h2 class="text-xl font-semibold">ایجاد فروشنده</h2>
                <img onclick="toggleForm('sellerModal')" src="../../public/icons/close_red.svg" class="w-5 h-5 cursor-pointer" alt="delete items icon">
            </div>
            <div class="modal-body">
                <form id="sellerForm" action="#" method="post" class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    <input type="hidden" name="mode" value="create">
                    <div class="form-group">
                        <label for="name">نام فروشنده</label>
                        <input type="text" name="name" id="name" class="border-2 border-gray-500 p-2 w-full outline-none">
                    </div>
                    <div class="form-group">
                        <label for="latinName">نام لاتین</label>
                        <input type="text" name="latinName" id="latinName" class="border-2 border-gray-500 p-2 w-full outline-none">
                    </div>
                    <div class="form-group
                    ">
                        <label for="phone">شماره تماس</label>
                        <input type="text" name="phone" id="phone" class="border-2 border-gray-500 p-2 w-full outline-none">
                    </div>
                    <div class="form-group">
                        <label for="address">آدرس</label>
                        <input type="text" name="address" id="address" class="border-2 border-gray-500 p-2 w-full outline-none">
                    </div>
                    <div class="form-group">
                        <label for="kind">دسته بندی</label>
                        <input type="text" name="kind" id="kind" class="border-2 border-gray-500 p-2 w-full outline-none">
                    </div>
                    <div class="form-group">
                        <label for="view">نمایش</label>
                        <input type="checkbox" name="view" id="view" checked>
                    </div>
                    <div class="form-group flex justify-between items-center">
                        <button type="button" onclick="submitSellerForm()" class="bg-sky-600 text-white rounded-sm py-1 px-3">ذخیره</button>
                        <p id="operationMessage" class="text-green-600 hidden"></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="brandModal" class="fixed inset-0 bg-gray-800 flex justify-center items-center hidden">
    <div class="bg-white p-5 rounded shadow">
        <div class="modal-box">
            <div class="flex justify-between items-center py-5">
                <h2 class="text-xl font-semibold">ایجاد برند</h2>
                <img onclick="toggleForm('brandModal')" src="../../public/icons/close_red.svg" class="w-5 h-5 cursor-pointer" alt="delete items icon">
            </div>
            <div class="modal-body">
                <form id="brandForm" action="#" method="post" class="flex items-center gap-5">
                    <input type="hidden" name="mode" value="create">
                    <div class="form-group">
                        <input type="text" name="name" id="name" class="border-2 border-gray-500 p-2 w-full outline-none">
                    </div>
                    <div class="form-group">
                        <label for="view">نمایش</label>
                        <input type="checkbox" name="view" id="view" checked>
                    </div>
                    <div class="form-group">
                        <button type="button" onclick="submitBrandForm()" class="bg-sky-600 text-white rounded-sm py-1 px-3">ذخیره</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="updateViewMessage" class="fixed left-1/2 -translate-x-1/2 top-full transition">
    <p class="bg-green-600 text-white p-2 rounded-sm">عملیات موفقانه انجام شد.</p>
</div>
<script>
    const endpoint = "../../app/api/inventory/SellersAndBrandManageApi.php";

    function submitSellerForm() {
        const form = document.getElementById('sellerForm');
        const formData = new FormData(form);

        axios.post(endpoint, formData)
            .then(response => {
                if (response.data) {
                    const operationMessage = document.getElementById('operationMessage');
                    operationMessage.classList.remove('hidden');
                    document.getElementById('operationMessage').innerText = 'فروشنده جدید با موفقیت ایجاد شد';
                    setTimeout(() => {
                        operationMessage.classList.add('hidden');
                        window.location.reload();
                    }, 3000);
                }

            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function toggleForm(form) {
        const modal = document.getElementById(form);
        modal.classList.toggle('hidden');
    }

    function submitBrandForm() {
        const form = document.getElementById('brandForm');
        const formData = new FormData(form);

        axios.post(endpoint, formData)
            .then(response => {
                if (response.data) {
                    const operationMessage = document.getElementById('operationMessage');
                    operationMessage.classList.remove('hidden');
                    document.getElementById('operationMessage').innerText = 'برند جدید با موفقیت ایجاد شد';
                    setTimeout(() => {
                        operationMessage.classList.add('hidden');
                        window.location.reload();
                    }, 3000);
                }

            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function updateView(table, id) {
        console.log('started');
        const formData = new FormData();
        formData.append('updateView', 'updateView');
        formData.append('id', id);
        formData.append('table', table);

        axios.post(endpoint, formData)
            .then(response => {
                if (response.data) {
                    const updateViewMessage = document.getElementById('updateViewMessage');
                    console.log(updateViewMessage);
                    updateViewMessage.classList.remove('top-full');
                    updateViewMessage.classList.add('bottom-5');
                    setTimeout(() => {
                        updateViewMessage.classList.add('top-full');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

    }

    function editSellerForm() {
        const form = document.getElementById('sellerEditForm');
        const formData = new FormData(form);

        axios.post(endpoint, formData)
            .then(response => {
                if (response.data) {
                    const operationMessage = document.getElementById('operationMessage');
                    operationMessage.classList.remove('hidden');
                    document.getElementById('operationMessage').innerText = 'فروشنده با موفقیت ویرایش شد';
                    setTimeout(() => {
                        operationMessage.classList.add('hidden');
                        window.location.reload();
                    }, 3000);
                }

            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function makeEditable(table, cell, fieldName, sellerId) {
        // Check if the cell already contains an input element
        if (cell.querySelector('input')) {
            return; // Exit if the input is already active
        }

        let style = '';

        const lefToRightField = ['kind', 'phone', 'latinName']

        if (lefToRightField.includes(fieldName)) {
            style = 'style="direction: ltr !important;"';
        }

        let originalContent = cell.innerHTML.replace(/[\r\n]+/g, ' ');
        cell.innerHTML = `<input ${style} type="text" class="border-2 border-gray-300 outline-none p-2 w-full" value="${originalContent}" onblur="confirmEdit('${table}',this, '${fieldName}', ${sellerId}, '${originalContent}')">`;
        cell.firstChild.focus();
    }

    function confirmEdit(table, input, fieldName, sellerId, originalContent) {
        let newValue = input.value;
        if (newValue !== originalContent) {
            if (confirm('آیا مطمئن هستید که تغییرات را ذخیره کنید?')) {
                // Save changes
                updateSeller(table, sellerId, fieldName, newValue, input);
            } else {
                // Revert changes
                input.parentElement.innerHTML = originalContent;
            }
        } else {
            // No changes made, revert back
            input.parentElement.innerHTML = originalContent;
        }
    }

    function updateSeller(table, id, field, value, input) {

        const formData = new FormData();
        formData.append('updateSeller', 'updateSeller');
        formData.append('id', id);
        formData.append('field', field);
        formData.append('value', value);
        formData.append('table', table);

        axios.post(endpoint, formData).then(response => {
            if (response.data) {
                input.parentElement.innerHTML = value;
                const updateViewMessage = document.getElementById('updateViewMessage');
                updateViewMessage.classList.remove('top-full');
                updateViewMessage.classList.add('bottom-5');
                setTimeout(() => {
                    updateViewMessage.classList.add('top-full');
                }, 3000);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('Error updating seller');
            input.parentElement.innerHTML = originalContent;
        });

    }
</script>
<?php
require_once './components/footer.php';
?>