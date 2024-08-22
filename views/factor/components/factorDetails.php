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