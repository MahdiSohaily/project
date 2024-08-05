function convertToPersian(element) {
  // Define a mapping of English keyboard keys to Persian characters
  const persianCharMap = {
    a: "ش",
    b: "ذ",
    c: "ز",
    d: "ی",
    e: "ث",
    f: "ب",
    g: "ل",
    h: "ا",
    i: "ه",
    j: "ت",
    k: "ن",
    l: "م",
    m: "پ",
    n: "د",
    o: "خ",
    p: "ح",
    q: "ض",
    r: "ق",
    s: "س",
    t: "ف",
    u: "ع",
    v: "ر",
    w: "ص",
    x: "ط",
    y: "غ",
    z: "ظ",
    ",": "و",
    "'": "گ",
    ";": "ک",
    "]": "چ",
    1: "۱",
    2: "۲",
    3: "۳",
    4: "۴",
    5: "۵",
    6: "۶",
    7: "۷",
    8: "۸",
    9: "۹",
    0: "۰",
    "[": "ج",
  };
  const customInput = element;
  let customText = "";
  const inputText = customInput.value.toLowerCase();
  for (let i = 0; i < inputText.length; i++) {
    const char = inputText[i];
    if (char in persianCharMap) {
      customText += persianCharMap[char];
    } else {
      customText += char;
    }
  }
  customInput.value = customText;
}

function convertToEnglish(element) {
  const englishCharMap = {
    ش: "a",
    ذ: "b",
    ز: "c",
    ی: "d",
    ث: "e",
    ب: "f",
    ل: "g",
    ا: "h",
    ه: "i",
    ت: "j",
    ن: "k",
    م: "l",
    پ: "m",
    د: "n",
    خ: "o",
    ح: "p",
    ض: "q",
    ق: "r",
    س: "s",
    ف: "t",
    ع: "u",
    ر: "v",
    ص: "w",
    ط: "x",
    غ: "y",
    ظ: "z",
    و: ":",
    گ: "'",
    ک: ";",
    چ: "]",
    "۱": "1",
    "۲": "2",
    "۳": "3",
    "۴": "4",
    "۵": "5",
    "۶": "6",
    "۷": "7",
    "۸": "8",
    "۹": "9",
    "۰": "0",
  };

  const customInput = element;
  let customText = "";
  const inputText = customInput.value.toLowerCase();
  for (let i = 0; i < inputText.length; i++) {
    const char = inputText[i];
    if (char in englishCharMap) {
      customText += englishCharMap[char];
    } else {
      customText += char;
    }
  }
  customInput.value = customText;
}

function toggleSidebar() {
  const sidebar = document.getElementById("side_bar");
  sidebar.classList.toggle("open");
}

function convertToPersian(element) {
  // Define a mapping of English keyboard keys to Persian characters
  const persianCharMap = {
    a: "ش",
    b: "ذ",
    c: "ز",
    d: "ی",
    e: "ث",
    f: "ب",
    g: "ل",
    h: "ا",
    i: "ه",
    j: "ت",
    k: "ن",
    l: "م",
    m: "پ",
    n: "د",
    o: "خ",
    p: "ح",
    q: "ض",
    r: "ق",
    s: "س",
    t: "ف",
    u: "ع",
    v: "ر",
    w: "ص",
    x: "ط",
    y: "غ",
    z: "ظ",
    ",": "و",
    "'": "گ",
    ";": "ک",
    "]": "چ",
    1: "۱",
    2: "۲",
    3: "۳",
    4: "۴",
    5: "۵",
    6: "۶",
    7: "۷",
    8: "۸",
    9: "۹",
    0: "۰",
  };
  const customInput = element;
  let customText = "";
  const inputText = customInput.value.toLowerCase();
  for (let i = 0; i < inputText.length; i++) {
    const char = inputText[i];
    if (char in persianCharMap) {
      customText += persianCharMap[char];
    } else {
      customText += char;
    }
  }
  customInput.value = customText;
}

function convertToEnglish(element) {
  const englishCharMap = {
    ش: "a",
    ذ: "b",
    ز: "c",
    ی: "d",
    ث: "e",
    ب: "f",
    ل: "g",
    ا: "h",
    ه: "i",
    ت: "j",
    ن: "k",
    م: "l",
    پ: "m",
    د: "n",
    خ: "o",
    ح: "p",
    ض: "q",
    ق: "r",
    س: "s",
    ف: "t",
    ع: "u",
    ر: "v",
    ص: "w",
    ط: "x",
    غ: "y",
    ظ: "z",
    و: ":",
    گ: "'",
    ک: ";",
    چ: "]",
    "۱": "1",
    "۲": "2",
    "۳": "3",
    "۴": "4",
    "۵": "5",
    "۶": "6",
    "۷": "7",
    "۸": "8",
    "۹": "9",
    "۰": "0",
  };

  const customInput = element;
  let customText = "";
  const inputText = customInput.value.toLowerCase();
  for (let i = 0; i < inputText.length; i++) {
    const char = inputText[i];
    if (char in englishCharMap) {
      customText += englishCharMap[char];
    } else {
      customText += char;
    }
  }
  customInput.value = customText;
}

function filterCode(element) {
  const message = element.value;
  if (!message) {
    return "";
  }

  const codes = message.split("\n");

  const filteredCodes = codes
    .map(function (code) {
      code = code.replace(/\[[^\]]*\]/g, "");

      const parts = code.split(/[:,]/, 2);

      // Check if parts[1] contains a forward slash
      if (parts[1] && parts[1].includes("/")) {
        // Remove everything after the forward slash
        parts[1] = parts[1].split("/")[0];
      }

      const rightSide = (parts[1] || "").replace(/[^a-zA-Z0-9 ]/g, "").trim();

      return rightSide ? rightSide : code.replace(/[^a-zA-Z0-9 ]/g, "").trim();
    })
    .filter(Boolean);

  const finalCodes = filteredCodes.filter(function (item) {
    const data = item.split(" ");
    if (data[0].length > 4) {
      return item;
    }
  });

  const mappedFinalCodes = finalCodes.map(function (item) {
    const parts = item.split(" ");
    if (parts.length >= 2) {
      const partOne = parts[0];
      const partTwo = parts[1];
      if (!/[a-zA-Z]{4,}/i.test(partOne) && !/[a-zA-Z]{4,}/i.test(partTwo)) {
        return partOne + partTwo;
      }
    }
    return parts[0];
  });

  const nonConsecutiveCodes = mappedFinalCodes.filter(function (item) {
    const consecutiveChars = /[a-zA-Z]{4,}/i.test(item);
    return !consecutiveChars;
  });

  element.value =
    nonConsecutiveCodes
      .map(function (item) {
        return item.split(" ")[0];
      })
      .join("\n") + "\n";
}

function copyToClipboard(text) {
  if (window.clipboardData && window.clipboardData.setData) {
    // Internet Explorer-specific code path to prevent textarea being shown while dialog is visible.
    return window.clipboardData.setData("Text", text);
  } else if (
    document.queryCommandSupported &&
    document.queryCommandSupported("copy")
  ) {
    var textarea = document.createElement("textarea");
    textarea.textContent = text;
    textarea.style.position = "fixed"; // Prevent scrolling to bottom of page in Microsoft Edge.
    document.body.appendChild(textarea);
    textarea.select();
    try {
      return document.execCommand("copy"); // Security exception may be thrown by some browsers.
    } catch (ex) {
      console.warn("Copy to clipboard failed.", ex);
      return prompt("Copy to clipboard: Ctrl+C, Enter", text);
    } finally {
      document.body.removeChild(textarea);
    }
  }
}

function exportToExcel(operation) {
  var table = $("#reportTable");
  if (table && table.length) {
    var preserveColors = table.hasClass("table2excel_with_colors")
      ? true
      : false;
    $(table).table2excel({
      exclude: ".noExl",
      name: operation,
      filename: operation + " " + new Date().toISOString() + ".xls",
      fileext: ".xls",
      exclude_img: true,
      exclude_links: true,
      exclude_inputs: true,
      preserveColors: preserveColors,
    });
  }
}
