<?php
$pageTitle = "تعریف رابطه اجناس";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../app/controller/callcenter/relationshipController.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php'; ?>
<link rel="stylesheet" href="./assets/css/select2.css">
<script src="./assets/js/select2.js"></script>
<div class=" grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-6 px-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-900 h-28">
            <div class="flex items-center justify-between p-3 ">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="material-icons font-semibold text-orange-400">search</i>
                    جستجوی اجناس
                </h2>
            </div>
            <div class="flex justify-center px-3">
                <input onkeyup="convertToEnglish(this); search(this.value)" type="text" name="serial" id="serial" class="p-2 w-full border border-2 text-sm bg-transparent border-white outline-none text-white" min="0" max="30" placeholder="شماره فنی ..." />
            </div>
        </div>
        <div id="search_result" class="p-3">
            <!-- Search Results are going to be appended here -->
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-900 h-28">
            <div class="flex items-center justify-between p-3">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="material-icons text-green-600">beenhere</i>
                    اجناس انتخاب شده
                </h2>
                <button class="flex items-center bg-red-500 hover:bg-red-600 text-white text-sm rounded px-4 py-1 text-xs" onclick="clearAll()">
                    <i class="material-icons hover:cursor-pointer">delete</i>
                </button>
            </div>
            <p class="px-3 mb-4 text-white text-sm leading-relaxed">
                <span class="text-red-500">*</span>
                لیست اجناس انتخاب شده برای افزودن به رابطه!
            </p>
        </div>
        <p id="select_box_error" class="px-3 tiny-text text-red-500 hidden">
            لیست اجناس انتخاب شده برای افزودن به رابطه خالی بوده نمیتواند!
        </p>
        <p id="duplicate_relation" class="px-3 tiny-text text-red-500 hidden">
            شما همزمان نمی توانید ۲ رابطه را بارگذاری نمایید.(شما میتوانید با حذف همه رابطه جدید را وارد نمایید)
        </p>
        <div id="selected_box" class="p-3">
            <!-- selected items are going to be added here -->
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-900 h-28">
            <div class="p-3">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="material-icons font-semibold text-blue-500">save</i>
                    ثبت رابطه در سیستم
                </h2>
            </div>

            <p class="px-3 py-1 mb-4 text-white text-sm leading-relaxed">
                برای ثبت رابطه در سیستم فورم ذیل را با دقت پر نمایید.
            </p>
        </div>
        <div class="p-3">
            <form action="" method="post" onsubmit="event.preventDefault();createRelation()" id="myForm">
                <input id="mode" type="text" name="operation" value="create" hidden>
                <div class="col-span-12 sm:col-span-4 mb-3">
                    <label class="block font-medium text-sm text-gray-700">
                        اسم رابطه
                    </label>
                    <input name="relation_name" value="" class="border border-2 outline-none text-sm border-gray-300 mt-1 w-full border-gray-300 shadow-sm px-3 py-2" required id="relation_name" type="text" />
                    <p class="mt-2"></p>
                </div>
                <div class="col-span-12 sm:col-span-4 mb-3">
                    <label for="cars">
                        خودرو های مرتبط
                    </label>
                    <select id="cars" type="cars" multiple class="p-2 border border-2 text-sm border-gray-300 mt-1 w-full border-gray-300 shadow-sm h-12">
                        <?php
                        if (count($cars) > 0) {
                            foreach ($cars as $item) { ?>
                                <option value="<?= $item['id'] ?>" class="text-sm">
                                    <?= $item['name'] ?>
                                </option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-span-12 sm:col-span-4 mb-3">
                    <label for="cars">
                        وضعیت
                    </label>
                    <select type="status" class="border border-2 p-2 text-sm border-gray-300 mt-1 block w-full border-gray-300 shadow-sm" id="status">
                        <option value="" class="text-sm">وضعیت مورد نظر خود برای رابطه را انتخاب کنید!</option>
                        <?php
                        if (count($goodStatus) > 0) {
                            foreach ($goodStatus as $status) { ?>
                                <option value="<?= $status['id'] ?>" class="text-sm">
                                    <?= $status['name'] ?>
                                </option>

                        <?php }
                        } ?>
                    </select>
                </div>
                <fieldset class="py-5">
                    <legend class="text-md font-bold"> هشدار موجودی انبار یدک شاپ:</legend>
                    <div class="col-span-12 sm:col-span-4 mb-3 flex flex-wrap gap-2 ">
                        <div class="flex-grow">
                            <label for="original" class="block font-medium text-sm text-gray-700">
                                مقدار اصلی
                            </label>
                            <input name="price" value="0" class="border border-2 outline-none text-sm border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="original" type="number" min='0' />
                        </div>
                        <div class="flex-grow">
                            <label for="fake" class="block font-medium text-sm text-gray-700">
                                مقدار غیر اصلی
                            </label>
                            <input name="price" value="0" class="border border-2 outline-none text-sm border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="fake" type="number" min='0' />
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend class="text-md font-bold"> هشدار موجودی کلی:</legend>
                    <div class="col-span-12 sm:col-span-4 mb-3 flex flex-wrap gap-2 ">
                        <div class="flex-grow">
                            <label for="original" class="block font-medium text-sm text-gray-700">
                                مقدار اصلی
                            </label>
                            <input name="original_all" value="0" class="border border-2 outline-none text-sm border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="original_all" type="number" min='0' />
                        </div>
                        <div class="flex-grow">
                            <label for="fake" class="block font-medium text-sm text-gray-700">
                                مقدار غیر اصلی
                            </label>
                            <input name="fake_all" value="0" class="border border-2 outline-none text-sm border-gray-300 mt-1 block w-full border-gray-300 shadow-sm px-3 py-2" id="fake_all" type="number" min='0' />
                        </div>
                    </div>
                </fieldset>

                <div class="col-span-12 sm:col-span-4 mb-3">
                    <label for="description">
                        توضیحات رابطه
                    </label>
                    <textarea class="border border-2 p-2 text-sm border-gray-300 mt-1 block outline-none w-full border-gray-300 shadow-sm" id="description" rows="5"></textarea>
                </div>
        </div>

        <div class="flex items-center justify-between px-4 py-3  text-right sm:px-6">
            <button type="type" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded font-semibold text-xs text-white">
                <i class="px-2 material-icons hover:cursor-pointer">save</i>
                ذخیره سازی
            </button>
            <p id="form_success" class="px-3 py-2 text-xs bg-green-700 hidden text-white rounded ">
                رابطه جدید اجناس موفقانه در پایگاه داده ثبت شد!
            </p>
            <p id="form_error" class="px-3 py-2 text-xs bg-red-700 hidden text-white rounded">
                ذخیره سازی اطلاعات ناموفق بود!
            </p>
        </div>
        </form>
        <div id="output"></div>
    </div>
</div>
</div>
<script>
    // Container Global Variables
    let serial = null;
    let pattern_id = null;
    selected_goods = [];
    let relation_active = false;

    //Global Elements Container 
    const selected_box = document.getElementById('selected_box');
    const resultBox = document.getElementById("search_result");
    const error_message = document.getElementById('select_box_error');
    const duplicate_relation = document.getElementById('duplicate_relation');
    const form_success = document.getElementById('form_success');
    const form_error = document.getElementById('form_error');

    // search for goods to define their relationship
    function search(pattern) {
        serial = pattern;
        if (pattern.length > 6) {
            error_message.classList.add('hidden');
            duplicate_relation.classList.add('hidden');
            pattern = pattern.replace(/[^a-zA-Z0-9\s]/g, "");
            pattern = pattern.replace(/-/g, "");
            pattern = pattern.replace(/_/g, "");

            resultBox.innerHTML = `<tr class=''>
                                        <div class='w-full h-96 flex justify-center items-center'>
                                            <img class=' block w-10 mx-auto h-auto' src='../../public/img/loading.png' alt='google'>
                                        </div>
                                    </tr>`;
            var params = new URLSearchParams();
            params.append('search_goods_for_relation', 'search_goods_for_relation');
            params.append('pattern', pattern);

            if (pattern.length > 6) {
                axios.post("../../app/api/callcenter/RelationshipApi.php", params)
                    .then(function(response) {
                        resultBox.innerHTML = response.data;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            } else {
                resultBox.innerHTML = "کد فنی وارد شده فاقد اعتبار است";
            }
        } else {
            resultBox.innerHTML = "";
        }
    };

    // A function to add a good to the relation box
    function add(element) {
        duplicate_relation.classList.add('hidden');
        const id = element.getAttribute("data-id");
        const partNumber = element.getAttribute("data-partnumber");
        selected_goods = selected_goods.filter((good) => {
            return good.id !== id;
        });

        selected_goods.push({
            id: id,
            partNumber: partNumber
        });
        error_message.classList.add('hidden');
        remove(id);
        displaySelectedGoods();
    };

    // A function to remove added goods from relation box
    function remove(id) {
        const item = document.getElementById("search-" + id);
        if (item) {
            item.remove();
        }
    }

    // A function to remove an specific item from selected items list
    function remove_selected(id) {
        selected_goods = selected_goods.filter((item) => {
            return item.id != id;
        });
        displaySelectedGoods();
    };

    //A function to clear all selected items
    function clearAll() {
        selected_goods = [];
        relation_active = false;
        displaySelectedGoods();
        duplicate_relation.classList.add('hidden');


        var form = document.getElementById('myForm');

        for (var i = 0; i < form.elements.length; i++) {
            var element = form.elements[i];

            // Check if the element is an input element (text, password, email, etc.)
            if (element.tagName === 'INPUT' && element.type !== 'button' && element.type !== 'submit') {
                element.value = ''; // Set the value to an empty string
            } else if (element.tagName === 'TEXTAREA') {
                element.value = ''; // Clear textarea values
            } else if (element.tagName === 'SELECT') {
                element.selectedIndex = -1; // Clear selected option in a dropdown
            }
        }

        $('#cars').select2(null);
    }

    // A function to display the selected goods in the relation box
    function displaySelectedGoods() {
        let template = '';
        for (const good of selected_goods) {
            template += `
            <div class="w-full flex justify-between items-center shadow-md hover:shadow-lg rounded-md px-4 py-3 mb-2 border border-gray-300">
                <p class="text-sm font-semibold text-gray-600">
                    ` + good.partNumber + `
                </p>
                    <i data-id="` + good.id + `" data-partNumber="` + good.partNumber + `" onclick="remove_selected(` +
                good.id + `)"
                            class="material-icons add text-red-600 cursor-pointer rounded-circle hover:bg-gray-200">do_not_disturb_on
                    </i>
                </div>
            `;
        }
        selected_box.innerHTML = template;
    }

    // this function is used to get all the selected items using Javascript and push them into an array
    // and prepare them for sending to the server
    function getSelectedItems(id) {
        let selected = [];
        for (var option of document.getElementById(id).options) {
            if (option.selected) {
                selected.push(option.value);
            }
        }

        return selected;
    }

    // A function to create the relationship
    function createRelation() {
        duplicate_relation.classList.add('hidden');
        // Accessing the form fields to get thier value for an ajax store operation
        const relation_name = document.getElementById('relation_name').value;
        const mode = document.getElementById('mode').value;
        const price = null;
        const cars = getSelectedItems('cars');
        const status = document.getElementById('status').value;
        const description = document.getElementById('description').value;
        const original = document.getElementById('original').value;
        const fake = document.getElementById('fake').value;

        const original_all = document.getElementById('original_all').value;
        const fake_all = document.getElementById('fake_all').value;

        // Defining a params instance to be attached to the axios request
        const params = new URLSearchParams();
        params.append('store_relation', 'store_relation');
        params.append('relation_name', relation_name);
        params.append('price', price);
        params.append('cars', JSON.stringify(cars));
        params.append('status', status);
        params.append('description', description);
        params.append('original', original);
        params.append('fake', fake);

        params.append('original_all', original_all);
        params.append('fake_all', fake_all);

        // Side effects data
        params.append('mode', mode);
        params.append('pattern_id', pattern_id);
        params.append('selected_goods', JSON.stringify(selected_goods));
        params.append('serial', serial);


        if (selected_goods.length > 0) {
            axios.post("../../app/api/callcenter/RelationshipApi.php", params)
                .then(function(response) {
                    if (response.data == true) {
                        form_success.classList.remove('hidden');
                        setTimeout(() => {
                            form_success.classList.add('hidden');
                            location.reload();
                        }, 2000)
                    } else {
                        form_error.classList.remove('hidden');
                        setTimeout(() => {
                            form_error.classList.add('hidden');
                            location.reload();
                        }, 2000)
                    }
                })
                .catch(function(error) {

                });
        } else {
            error_message.classList.remove('hidden');
        }
    }

    // A function to load all the relationships for the selected relationship
    function load(element) {
        const pattern = element.getAttribute("data-pattern");
        if (!relation_active) {
            duplicate_relation.classList.add('hidden');
            relation_active = true;
            pattern_id = pattern;

            const params = new URLSearchParams();
            params.append('load_relation', 'load_relation');
            params.append('pattern', pattern_id);

            axios.post("../../app/api/callcenter/RelationshipApi.php", params)
                .then(function(response) {
                    // VALUES OF THE ORIGINAL GOODS FOR SPECIFIC INVENTORY
                    let original = 0;
                    let fake = 0;

                    // VALUES OF THE GOODS FOR THE OVER ALL INVENTORIES
                    let original_all = 0;
                    let fake_all = 0;

                    if (response.data[0] !== null) {
                        original = response.data[0]['original'];
                        fake = response.data[0]['fake'];

                        original_all = response.data[0]['original_all'];
                        fake_all = response.data[0]['fake_all'];
                    }
                    document.getElementById('original').value = original;
                    document.getElementById('fake').value = fake;

                    document.getElementById('original_all').value = original_all;
                    document.getElementById('fake_all').value = fake_all;

                    push_data(response.data[1]);
                    displaySelectedGoods();
                    load_pattern_ifo(pattern_id);
                })
                .catch(function(error) {

                });
        } else {
            duplicate_relation.classList.remove('hidden');
        }
    }

    //This function helps to add all relations of a relationship into the selected items list
    const push_data = (data) => {
        for (const item of data) {
            remove(item.id);
            selected_goods.push({
                id: item.id,
                partNumber: item.partNumber
            });
        }
    };

    // This function is used to load an existing relationship information and fill out form fields with information
    const load_pattern_ifo = (id) => {

        // Accessing the form fields to get thier value for an ajax store operation
        const relation_name = document.getElementById('relation_name');
        const mode = document.getElementById('mode');
        const price = document.getElementById('price');
        const cars = getSelectedItems('cars');
        const status = document.getElementById('status');
        const description = document.getElementById('description');

        const params = new URLSearchParams();
        params.append('load_pattern_ifo', 'load_pattern_ifo');
        params.append('pattern', pattern_id);

        axios.post("../../app/api/callcenter/RelationshipApi.php", params)
            .then(function(response) {
                const pattern_info = (response.data.pattern);
                const pattern_info_cars = (response.data.cars);

                relation_name.value = pattern_info.name;
                mode.value = 'update';
                // price.value = pattern_info.price;
                description.value = pattern_info.description;

                setSelectedItems('cars', pattern_info_cars);
                const options = document.getElementById('status').options;

                for (option of options) {
                    if (option.value == pattern_info.status_id) {
                        option.selected = 'true';
                    }
                }

                $('#cars').select2(Object.values(pattern_info_cars));

            })
            .catch(function(error) {
                console.log(error);
            });
    };

    // This function helps to set the selected items from select elements when we load a predefined relationship
    function setSelectedItems(id, cars) {
        const options = document.getElementById(id).options;
        cars = Object.values(cars);
        cars = cars.map(String);
        for (option of options) {
            if (cars.includes(option.value)) {
                option.selected = 'true';
                option.style.color = 'red';
            }
        }
    }
    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
        $('#cars').select2({
            matcher: matchCustom
        });

    });

    // This function helps to display only the matching results when user types a keyword (Slecte 2 plugin)
    function matchCustom(params, data) {
        // If there are no search terms, return all of the data
        if ($.trim(params.term) === '') {
            return data;
        }

        // Do not display the item if there is no 'text' property
        if (typeof data.text === 'undefined') {
            return null;
        }

        // `params.term` should be the term that is used for searching
        // `data.text` is the text that is displayed for the data object
        if (data.text.indexOf(params.term) > -1) {
            var modifiedData = $.extend({}, data, true);
            modifiedData.text += '';

            // You can return modified objects from here
            // This includes matching the `children` how you want in nested data sets
            return modifiedData;
        }

        // Return `null` if the term should not be displayed
        return null;
    }
</script>
<?php
require_once './components/footer.php';
