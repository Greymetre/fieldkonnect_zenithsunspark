$(document).ready(function () {

/*=============== Leave Validation =====================*/
  $('#storeStockdData').validate({
    rules:{
      invoice_date:
      {
        required:true,
        date:true,
      },
      customer_id:
      {
        required:true,
        number:true,
      },
      stock_type:
      {
        required:true,
        maxlength: 250,
      },
      invoice_no:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      orderno:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      
      sub_total:
      {
        required:true,
        number:true,
        min: 1,
      },
      total_gst:
      {
        required:true,
        number:true,
        min: 1,
      },
      grand_total:
      {
        required:true,
        number:true,
        min: 1,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      invoice_no:{
        required: "Please enter Invoice No",
      },
      orderno:{
        required: "Please enter Order No",
      },
      invoice_date:{
        required: "Please enter Invoice Date",
      },
    }
  });

});
    
