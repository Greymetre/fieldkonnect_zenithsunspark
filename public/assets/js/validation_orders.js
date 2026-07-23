$(document).ready(function () {

  /*=============== Order Validation =====================*/
  $('#storeOrderData').validate({
    rules:{
      buyer_id:
      {
        required:true,
        number:true,
      },
      seller_id:
      {
        required:true,
        number:true,
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
        min: 0,
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
      buyer_id:{
        required: "Please enter Retailer",
      },
      seller_id:{
        required: "Please enter Distributor",
      },
      sub_total:{
        required: "Please Sub Total",
      },
      total_gst:{
        required: "Please Total GST",
      },
      grand_total:{
        required: "Please Grand Total",
      },
    }
  });

    /*=============== Shipment Validation =====================*/
  $('#storeShipmentData').validate({
    rules:{
      order_id:
      {
        number:true,
      },
      buyer_id:
      {
        required:true,
        number:true,
      },
      buyer_name:
      {
        minlength:3,
        maxlength: 250,
      },
      seller_id:
      {
        required:true,
        number:true,
      },
      seller_name:
      {
        minlength:3,
        maxlength: 250,
      },
      courier_id:
      {
        number:true,
      },
      address_id:
      {
        number:true,
      },
      phone:
      {
        required:true,
        number:true,
      },
      address_text:
      {
        minlength:3,
        maxlength: 250,
      },
      shipment_value:
      {
        required:true,
        number:true,
        min: 1,
      },
      package_weight:
      {
        required:true,
        number:true,
        min: 1,
      },
      unit_price:
      {
        required:true,
        number:true,
        min: 1,
      },
      extra_fees:
      {
        number:true,
        min: 1,
      },
      total_price:
      {
        required:true,
        number:true,
        min: 1,
      },
      pieces:
      {
        required:true,
        number:true,
        min: 1,
      },
      actual_paid:
      {
        number:true,
        min: 1,
      },
      shipment_name:
      {
        minlength:3,
        maxlength: 250,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      buyer_id:{
        required: "Please enter Retailer",
      },
      seller_id:{
        required: "Please enter Distributor",
      },
      sub_total:{
        required: "Please Sub Total",
      },
      total_gst:{
        required: "Please Total GST",
      },
      grand_total:{
        required: "Please Grand Total",
      },
    }
  });
});
    
