$(document).ready(function () {
  //adding event listener to the update label form
  const updateLabelForm = $(".cartable-save-form");
  updateLabelForm.submit(function (e) {
    e.preventDefault();
    updateUserLabel(updateLabelForm.serialize());
  });

  //adding event listener to the report options to add the selected option to the call info textarea
  $(".callInfoBox-option div").click(function () {
    var txt = $.trim($(this).text());
    var box = $("#call_info_text");
    box.val(box.val() + " " + txt);
  });

  //   save the call info of customer
  var contact = $(".save-contact");
  contact.submit(function (e) {
    e.preventDefault();
    saveContact(contact.serialize());
  });
}); 

function updateUserLabel(postData = null) {
  // Axios POST request with multiple data
  postData = postData + "&updateUserLabel=updateUserLabel";
  const operation_acknowledge = document.getElementById(
    "operation_acknowledge"
  );
  axios
    .post("../../app/api/callcenter/CartableApi.php", postData)
    .then((response) => {
      if (response.data) {
        operation_acknowledge.classList.add("bg-green-600");
        operation_acknowledge.style.left = "10px";
        operation_acknowledge.innerHTML = "عملیات موفقانه صورت گرفت";
        setTimeout(() => {
          operation_acknowledge.classList.remove("bg-green-600");
          operation_acknowledge.style.left = "-100%";
        }, 3000);
      } else {
        operation_acknowledge.classList.add("bg-red-600");
        operation_acknowledge.style.left = "10px";
        operation_acknowledge.innerHTML =
          "عملیات ناموفق بود لطفا دوباره تلاش کنید.";
        setTimeout(() => {
          operation_acknowledge.classList.remove("bg-red-600");
          operation_acknowledge.style.left = "-100%";
        }, 3000);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function saveContact(postData = null) {
  // Axios POST request with multiple data
  postData = postData + "&saveContact=saveContact";
  const operation_acknowledge = document.getElementById(
    "operation_acknowledge"
  );
  axios
    .post("../../app/api/callcenter/CartableApi.php", postData)
    .then((response) => {
      if (response.data) {
        operation_acknowledge.classList.add("bg-green-600");
        operation_acknowledge.style.left = "0px";
        operation_acknowledge.innerHTML = "عملیات موفقانه صورت گرفت";
        setTimeout(() => {
          operation_acknowledge.classList.remove("bg-green-600");
          operation_acknowledge.style.left = "-100%";
        }, 3000);
      } else {
        operation_acknowledge.classList.add("bg-red-600");
        operation_acknowledge.style.left = "0px";
        operation_acknowledge.innerHTML =
          "عملیات ناموفق بود لطفا دوباره تلاش کنید.";
        setTimeout(() => {
          operation_acknowledge.classList.remove("bg-red-600");
          operation_acknowledge.style.left = "-100%";
        }, 3000);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
