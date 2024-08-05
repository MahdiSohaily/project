<?php
$pageTitle = "بررسی قیمت";
$iconUrl = 'report.png';
require_once './components/header.php';
require_once '../../layouts/callcenter/nav.php';
require_once '../../layouts/callcenter/sidebar.php'; ?>
<div class="rtl w-5/6 mx-auto bg-gray-50 p-5 rounded-lg shadow-sm d-flex">
    <div class="grow px-4 mb-4">
        <!-- Korea section -->
        <div class="col-span-6 sm:col-span-4">
            <label for="code" class="block font-medium text-sm text-gray-700">
                قیمت کد های ارائه شده
            </label>
            <textarea onclick="copyOperation(this.value)" id="results_box" readonly rows="10" class="border border-gray-300 ltr mt-1 shadow-sm block w-full rounded-md border-gray-300 p-3"></textarea>
        </div>
    </div>
    <div id="copied_message" class="flex justify-center text-green-500 text-sm font-semibold">
        <span>
            قیمت ها موفقانه کپی شد!
        </span>
    </div>
    <form id="partNumbers" class="grow px-4" target="_blank" action="giveOrderedPrice.php" method="post">
        <!-- Korea section -->
        <div class="col-span-6 sm:col-span-4">
            <label for="code" class="block font-medium text-sm text-gray-700">
                کدهای مدنظر
            </label>
            <textarea rows="10" id="code" name="code" required class="border border-gray-300 ltr mt-1 shadow-sm block w-full rounded-md border-gray-300 p-3" placeholder="لطفا کد های مود نظر خود را در خط های مجزا قرار دهید"></textarea>
        </div>
        <div v-if="hasActions" class="flex items-center py-3 text-right sm:rounded-bl-md sm:rounded-br-md">
            <button type="type" class="inline-flex items-center px-4 py-2 bg-gray-800 border font-semibold text-xs text-white hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="px-2 material-icons hover:cursor-pointer">search</i>
                جستجو
            </button>
        </div>
    </form>
</div>
<script>
    document.getElementById('copied_message').style.display = 'none';
    const textArea = document.getElementById('code');
    const form = document.getElementById('partNumbers');
    const results_box = document.getElementById('results_box');
    textArea.focus();

    form.addEventListener("submit", function(event) {
        event.preventDefault();
        const code = document.getElementById('code').value;
        const params = new URLSearchParams();
        params.append('codes', code);
        results_box.value = '';
        axios
            .post("../../app/api/callcenter/PriceCheckApi.php", params)
            .then(function(response) {
                data = response.data;

                for (const item of data) {
                    results_box.value += item;
                }
            })
            .catch(function(error) {
                console.log(error);
            });
    })

    function copyOperation(text) {
        const textArea = document.getElementById('results_box');
        textArea.value = text;
        textArea.select();
        document.execCommand('copy');
        document.getElementById('copied_message').style.display = 'flex';
        setTimeout(function() {
            document.getElementById('copied_message').style.display = 'none';
        }, 2000);
    }
</script>
<?php
require_once './components/footer.php';
