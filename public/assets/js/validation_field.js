$(document).ready(function () {

  /*=============== Award Validation =====================*/
  $('#storeAwardData').validate({
    rules:{
      name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      description:
      {
        required:true,
        minlength:3,
        maxlength: 450,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      name:{
        minlength: "Please enter a valid Award Name.",
        required: "Please enter Award Name",
      },
      description:{
        minlength: "Please enter a valid Description.",
        required: "Please enter Award Description",
      },
    }
  });

});
    
