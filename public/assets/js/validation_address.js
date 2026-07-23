$(document).ready(function () {

  /*=============== Country Validation =====================*/
  $('#createcountryForm').validate({
    rules:{
      country_name:
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
      country_name:{
        required: "Please enter Country Name",
      },
    }
  });

  /*=============== State Validation =====================*/
  $('#createstateForm').validate({
    rules:{
      state_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      country_id:
      {
        required:true,
        number: true,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      state_name:{
        required: "Please enter State Name",
      },
      country_id:{
        required: "Please select Country",
      },
    }
  });

  /*=============== District Validation =====================*/
  $('#createdistrictForm').validate({
    rules:{
      district_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      state_id:
      {
        required:true,
        number: true,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      district_name:{
        required: "Please enter District Name",
      },
      state_id:{
        required: "Please select State",
      },
    }
  });
  /*=============== City Validation =====================*/
  $('#createcityForm').validate({
    rules:{
      city_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      district_id:
      {
        required:true,
        number: true,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      city_name:{
        required: "Please enter City Name",
      },
      district_id:{
        required: "Please select District",
      },
    }
  });
/*=============== Leave Validation =====================*/
  $('#createpincodeForm').validate({
    rules:{
      pincode:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      city_id:
      {
        required:true,
        number: true,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      pincode:{
        required: "Please enter Pincode / Zipcode",
      },
      city_id:{
        required: "Please select City",
      },
    }
  });

});
    
