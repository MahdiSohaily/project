$(document).ready(function () {
  var frm = $(".estelam-form");

  frm.submit(function (e) {
    e.preventDefault();

    $.ajax({
      type: frm.attr("method"),
      url: frm.attr("action"),
      data: frm.serialize(),
      success: function (data) {
        $(".sellername-input").val("Submission was successful.");
        $(".sellername-input").val(data);
        $(".estelam-form-price").val(null);
        $(".save-estelam-form").prop("disabled", true);
      },
      error: function (data) {
        $(".sellername-input").val("An error occurred.");
        $(".sellername-input").val(data);
        $(".bottom-bar").removeClass("msg-loading");
        $(".save-estelam-form").prop("disabled", true);
      },
    });
  });
});

$(document).ready(function () {
  var frm = $(".shomare-faktor-form");

  frm.submit(function (e) {
    e.preventDefault();

    if (!$(".kharidar").val()) {
      $(".shomare-faktor-result").html(
        '<div class="shomare-faktor-error">نام خریدار خالی می باشد</div>'
      );

      return;
    }

    $.ajax({
      type: frm.attr("method"),
      url: frm.attr("action"),
      data: frm.serialize(),
      success: function (data) {
        $(".shomare-faktor-result").text("Submission was successful.");
        $(".shomare-faktor-result").html(data);
      },
      error: function (data) {
        $(".shomare-faktor-result").text("An error occurred.");
        $(".shomare-faktor-result").html(data);
      },
    });
  });
});

$(document).ready(function () {
  var frm = $(".shomare-faktor-form-edit");

  frm.submit(function (e) {
    e.preventDefault();

    $.ajax({
      type: frm.attr("method"),
      url: frm.attr("action"),
      data: frm.serialize(),
      success: function (data) {
        $(".error-edit-shomare").text("Submission was successful.");
        $(".error-edit-shomare").html(data);
      },
      error: function (data) {
        $(".error-edit-shomare").text("An error occurred.");
        $(".error-edit-shomare").html(data);
      },
    });
  });
});
