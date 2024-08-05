function updateUserAuthority(element) {
  // the target URL to send the ajax request
  const address = "../../app/api/callcenter/UsersApi.php";

  const user = element.getAttribute("data-user");

  const authorityList = document.querySelectorAll(".user-" + user);

  const data = {};

  for (const node of authorityList) {
    const authority = node.getAttribute("data-authority");
    const isChecked = node.checked;
    data[authority] = isChecked;
  }

  const params = new URLSearchParams();
  params.append("operation", "update");
  params.append("user", user);
  params.append("data", JSON.stringify(data));

  sendAjaxRequest(address, params);
}

function deleteUser(element) {
  const user = element.getAttribute("data-user");
  const confirmedDelete = confirm("Are you sure you want to delete");

  if (!confirmedDelete) {
    return false;
  }

  element.closest("tr").remove();
}

function sendAjaxRequest(address, params = null) {
  axios
    .post(address, params)
    .then(function (response) {
    })
    .catch(function (error) {
      console.log(error.message);
    });
}
