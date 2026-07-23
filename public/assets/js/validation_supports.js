$(document).ready(function () {
     /*=============== Award Validation =====================*/
  $('#storeSupportData').validate({
    rules:{
      subject:
      {
        required:true,
        maxlength: 250,
      },
      user_id:
      {
        required:true,
        number:true,
      },
      priority:
      {
        required:true,
        number:true,
      },
      'associated[]':
      {
        required:true,
      },
      'dependency[]':
      {
        required:true,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      subject:{
        required: "Please enter Subject",
      },
      user_id:{
        required: "Please select User",
      },
    }
  });
});
