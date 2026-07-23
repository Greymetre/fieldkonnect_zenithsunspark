$(document).ready(function () {

  /*=============== Wallet Validation =====================*/
  $('#storeWalletdData').validate({
    rules:{
      customer_id:
      {
        required:true,
        number:true,
      },
      coupon_code:
      {
        required:true,
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
      customer_id:{
        required: "Please enter Retailer",
      },
      coupon_code:{
        required: "Please enter Coupon Code",
      },
    }
  });

  /*=============== Schme Validation =====================*/
  $('#storeSchemeData').validate({
    rules:{
      scheme_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      customer_type:
      {
        required:true,
      },
      scheme_basedon:
      {
        required:true,
      },
      assign_to:
      {
        required:true,
      },
      start_date:
      {
        required:true,
        date:true,
      },
      end_date:
      {
        required:true,
        date:true,
      },
      scheme_type:
      {
        required:true,
        maxlength: 250,
      },
      scheme_description:
      {
        required:true,
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
      scheme_name:{
        required: "Please enter Scheme Name",
      },
      point_value:{
        required: "Please enter Point Value",
      },
      start_date:{
        required: "Please enter Start Date",
      },
      end_date:{
        required: "Please enter End Date",
      },
      scheme_type:{
        required: "Please Select Scheme Type",
      },
      scheme_description:{
        required: "Please enter Scheme Description",
      },
    }
  });

  /*=============== Coupons Validation =====================*/
  $('#storeCouponsData').validate({
    rules:{
      profile_name:
      {
        required:true,
        maxlength: 250,
      },
      coupon_length:
      {
        required:true,
        number:true,
      },
      coupon_count:
      {
        required:true,
        number:true,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      profile_name:{
        required: "Please enter Coupon Profile Name",
      },
      coupon_length:{
        required: "Please enter Coupon Length",
      },
      coupon_count:{
        required: "Please enter No of Coupons",
      },
    }
  });

  /*=============== Transaction History Validation =====================*/
  $('#storeTransactionHistoryData').validate({
    rules:{
      customer_id:
      {
        required:true,
      },
      coupen_code:
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
      customer_id:{
        required: "Please select Customer",
      },
      coupen_code:{
        required: "Please enter Coupon Code",
      },
    }
  });

});

/*=============== Damage Entries Validation =====================*/
$('#storeDamageEntryData').validate({
  rules:{
    customer_id:
    {
      required:true,
      number:true,
    },
    damageattach1:
    {
      required:true,
      image:true,
    },
  },
  highlight: function(element) {
    $(element).closest('.error').css("display", "none");
  },
  unhighlight: function(element) {
    $(element).closest('.error').css("display", "block");
  },
  messages:{
    customer_id:{
      required: "Please enter Retailer",
    },
    coupon_code:{
      required: "Please enter Coupon Code",
    },
  }
});
    
