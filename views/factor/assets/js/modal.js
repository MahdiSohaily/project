// Modal to alert user the leak of data
const modal = document.getElementById("popup-modal");
const btn_close_modal = document.getElementById("close-modal");
const error_message = document.getElementById("message");
btn_close_modal.addEventListener("click", function () {
  modal.classList.remove("flex");
  modal.classList.add("hidden");
});

function displayModal(message) {
  // Display modal with the provided message
  modal.classList.remove("hidden");
  modal.classList.add("flex");
  error_message.innerHTML = message;
  setTimeout(() => {
    modal.classList.remove("flex");
    modal.classList.add("hidden");
  }, 2500);
}
