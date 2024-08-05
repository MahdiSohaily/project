let bill_number = null;

function getBillData() {
  document.getElementById("billNO_bill").innerHTML = factorInfo.billNO;
  previewBill();
  displayCustomer();
  displayBillDetails();
}

function previewBill() {
  let counter = 1;
  let template = ``;
  let totalPrice = 0;

  for (const item of factorItems) {
    const payPrice = Number(item.quantity) * Number(item.price_per);
    totalPrice += payPrice;

    template += `
            <tr style="padding: 10px !important;" class="even:bg-gray-100">
                <td class="text-sm">
                    <span>${counter}</span>
                </td>
                <td class="text-sm">
                    <span>${item.partName}</span>
                </td>
                <td class="text-sm">
                    <span>${item.quantity}</span>
                </td>
                <td class="text-sm">
                    <span>${formatAsMoney(Number(item.price_per))}</span>
                </td>
                <td class="text-sm">
                    <span>${formatAsMoney(payPrice)}</span>
                </td>
            </tr> `;
    counter++;
  }
  bill_body_result.innerHTML = template;
}

function displayCustomer() {
  document.getElementById("name_bill").innerHTML =
    customerInfo.displayName + " " + customerInfo.family ?? "";
  document.getElementById("phone_bill").innerHTML = customerInfo.phone;
  if (customerInfo.address) {
    document.getElementById("userAddress").innerHTML =
      "نشانی : " + customerInfo.address;
  }
}

function displayBillDetails() {
  document.getElementById("date_bill").innerHTML = factorInfo.date.replace(
    /-/g,
    "/"
  );

  document.getElementById("quantity_bill").innerHTML = factorInfo.quantity;
  document.getElementById("totalPrice_bill").innerHTML = formatAsMoney(
    factorInfo.totalPrice
  );

  document.getElementById("totalPrice2").innerHTML = formatAsMoney(
    Number(factorInfo.totalPrice) - Number(factorInfo.discount)
  );

  document.getElementById("discount_bill").innerHTML = factorInfo.discount;
  document.getElementById("total_in_word_bill").innerHTML =
    factorInfo.totalInWords;
}

function displayAsMoney(inputInstance) {
  // Get the original value from the input and remove non-digit characters
  let originalValue = inputInstance.value.trim().replace(/\D/g, "");
  originalValue = originalValue.replace(/^0+/, "");

  // Use regex to insert commas every three digits
  let formattedValue = originalValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

  // Update the input value with the formatted value
  inputInstance.value = formattedValue;
}

function copyInfo(element) {
  const info = document.getElementById("name_bill").innerHTML.trim();
  const billNo = document.getElementById("billNO_bill").innerHTML.trim();
  const total_in_word = document.getElementById("totalPrice2").innerHTML.trim();

  const combinedText = `مشتری : ${info} \nشماره فاکتور : ${billNo} \n مبلغ قابل پرداخت: ${total_in_word} ریال`;

  const textarea = document.createElement("textarea");
  textarea.value = combinedText;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand("copy");
  document.body.removeChild(textarea);

  element.src = "./assets/img/complete.svg";

  setTimeout(() => {
    element.src = "./assets/img/copy.svg";
  }, 2000);
}

