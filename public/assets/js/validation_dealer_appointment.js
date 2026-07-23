$(document).ready(function () {

  $('#new_dealer_appoint_form').validate({
    rules: {
      branch:
      {
        required: true
      },
    },
    highlight: function (element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function (element) {
      $(element).closest('.error').css("display", "block");
    },
    messages: {
      branch: {
        required: "Please select branch.",
      },
    }
  });

});

