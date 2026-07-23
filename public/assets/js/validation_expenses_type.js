$(document).ready(function () {

  /*=============== Users Validation =====================*/
  var base_url =$('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");

  $('#storeExpensesTypeData').validate({
    rules:{
      name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      rate: {
        number: true,
        pattern: /^\d+(\.\d{1,2})?$/,
      }
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      name:{
        required: "Please enter Expenses Type Name",
      },
      rate:{
        pattern: "Please enter a valid rate with up to two decimal places."
      },
    }
  });
});
    
