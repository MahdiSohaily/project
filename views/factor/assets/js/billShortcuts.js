document.addEventListener("keydown", handelShortcuts);

function handelShortcuts(event) {
  if (event.ctrlKey && event.shiftKey) {
    addNewBillItemManually();
  }

  if (event.keyCode === 120) {
    // Toggle the preview bill and use a callback to get data after the toggle
    togglePreviewBill(function () {
      const type = factorInfo.partner !== 0 ? 'partner': 'yadak';
      getBillData(type);
      // Get the parent div
      var parentDiv = document.getElementById("bill_body_pdf");
      var previewBill = document.getElementById("previewBill");

      // Get all child elements
      var childElements = parentDiv.children;

      // Calculate total height of child elements
      var totalHeight = 0;
      for (var i = 0; i < childElements.length; i++) {
        totalHeight += childElements[i].offsetHeight; // or clientHeight depending on your layout
      }
      totalHeight += 130;

      // Set the height of the parent div to cover all children
      parentDiv.style.height = totalHeight + "px";
    });
  }
}

function togglePreviewBill(callback) {
  var previewBill = document.getElementById("previewBill");
  var wholePage = document.getElementById("wholePage");

  wholePage.style.display =
    wholePage.style.display === "none" ? "block" : "none";

  previewBill.style.display =
    previewBill.style.display === "none" ? "flex" : "none";

  // Call the callback after the toggle animation is complete
  setTimeout(callback, 0);
}

document.addEventListener("keydown", function (event) {
  // Check if the Tab key is pressed
  if (event.key === "Tab") {
    // Get all input elements with the class "tab-op" within the table
    const tableInputFields = document.querySelectorAll("table input.tab-op");

    // Find the currently focused input element
    const focusedInput = document.activeElement;

    // Check if the focused input is within the table or outside
    const isTableInput = Array.from(tableInputFields).includes(focusedInput);

    // If the focused input is within the table and has the class "tab-op"
    if (isTableInput) {
      // Prevent the default Tab behavior
      event.preventDefault();

      // Find the index of the currently focused input element
      const currentIndex = Array.from(tableInputFields).indexOf(focusedInput);

      // Calculate the index of the next input element with the class "tab-op"
      let nextIndex = (currentIndex + 1) % tableInputFields.length;

      // Use setTimeout to delay focusing on the next input element
      setTimeout(() => {
        // Focus on the next input element with the class "tab-op"
        tableInputFields[nextIndex].focus();
        tableInputFields[nextIndex].select();
      }, 0);
    }
    // Allow default Tab behavior for inputs outside the table or without the "tab-op" class
  }
});

document.addEventListener("keydown", function (event) {
  if (event.key === "Enter") {
    // Get all input elements with the class "tab-op" within the table
    const tableInputFields = document.querySelectorAll("table input.tab-op");

    // Find the currently focused input element
    const focusedInput = document.activeElement;
    const currentIndex = Array.from(tableInputFields).indexOf(focusedInput);

    // Check if the focused input is within the table and has the class "tab-op"
    if (Array.from(tableInputFields).includes(focusedInput)) {
      // Prevent the default Enter behavior
      event.preventDefault();

      const inputsPerRow = 3;
      // Calculate the index of the first input of the next row
      const nextRowFirstIndex =
        (Math.trunc(currentIndex / 3) * inputsPerRow + inputsPerRow) %
        tableInputFields.length;

      // Focus on the first input of the next row
      tableInputFields[nextRowFirstIndex].focus();
    }
  }
});

document
  .getElementById("bill_body")
  .addEventListener("focusin", function (event) {
    if (event.target.tagName === "INPUT") {
      event.target.select();
    }
  });

document.addEventListener("DOMContentLoaded", function () {
  // Get all input elements with the class "tab-op" within the table
  const tableInputFields = document.querySelectorAll("table input.tab-op");

  // If there are "tab-op" inputs within the table
  if (tableInputFields.length > 0) {
    // Give focus to the first "tab-op" input
    tableInputFields[0].focus();
    tableInputFields[0].select();
  }
});
