$(document).ready(function () {

  /*=============== Customers Validation =====================*/
  var base_url =$('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");

  $('#storeCustomerData').validate({
    rules:{
      name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      customer_code:
      {
        minlength:3,
        maxlength: 250,
        //nowhitespace:true,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#customer_code").val();},
            table :'customers',
            column : 'customer_code',
            id: function() {return $("#customer_id").val();},
          }
        },
      },
      first_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      last_name:
      {
        required:false,
        maxlength: 250,
      },
      email:
      {
        required:false,
        maxlength: 250,
        email: true,
        // remote:{
        //   url:base_url+"/uniqueValidation",
        //   type:"post",
        //   data: {
        //     "_token": token,
        //     value: function() {return $("#email").val();},
        //     table :'customers',
        //     column : 'email',
        //     // id: function() {return $("#email").val();},
        //   }
        // }
      },
      mobile:
      {
        required:true,
        minlength:9,
        maxlength: 15,
        number: true,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#mobile").val();},
            table :'customers',
            column : 'mobile',
            id: function() {return $("#customer_id").val();},
          }
        }
      },
      customertype:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      firmtype:
      {
        number: true,
        maxlength: 50,
      },
      gstin_no:
      {
        minlength:15,
        maxlength: 15,
        // remote:{
        //   url:base_url+"/uniqueValidation",
        //   type:"post",
        //   data: {
        //     "_token": token,
        //     value: function() {return $("#gstin_no").val();},
        //     table :'customer_details',
        //     column : 'gstin_no',
        //     id: function() {return $("#customer_id").val();},
        //   }
        // }
      },
      pan_no:
      {
        minlength:10,
        maxlength: 10,
        pattern: /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/,
        // remote:{
        //   url:base_url+"/uniqueValidation",
        //   type:"post",
        //   data: {
        //     "_token": token,
        //     value: function() {return $("#pan_no").val();},
        //     table :'customer_details',
        //     column : 'pan_no',
        //     id: function() {return $("#customer_id").val();},
        //   }
        // }
      },
      aadhar_no:
      {
        minlength:12,
        maxlength: 12,
        number: true,
        // remote:{
        //   url:base_url+"/uniqueValidation",
        //   type:"post",
        //   data: {
        //     "_token": token,
        //     value: function() {return $("#aadhar_no").val();},
        //     table :'customer_details',
        //     column : 'aadhar_no',
        //     id: function() {return $("#customer_id").val();},
        //   }
        // }
      },
      otherid_no:
      {
        minlength:3,
        maxlength: 250,
        // remote:{
        //   url:base_url+"/uniqueValidation",
        //   type:"post",
        //   data: {
        //     "_token": token,
        //     value: function() {return $("#otherid_no").val();},
        //     table :'customer_details',
        //     column : 'otherid_no',
        //     id: function() {return $("#customer_id").val();},
        //   }
        // }
      },
      address1:
      {
        required:true,
        maxlength: 250,
      },
      address2:
      {
        maxlength: 250,
      },
      landmark:
      {
        maxlength: 250,
      },
      locality:
      {
        maxlength: 250,
      },
      country_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      state_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      district_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      city_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      pincode_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      enrollment_date:
      {
        anyDate: true
      },
      approval_date:
      {
        anyDate: true
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
        required: "Please enter Firm Name",
      },
      customer_code:{
        remote: "This Customer Code already exits.",
      },
      first_name:{
        required: "Please enter First Name",
      },
      last_name:{
        required: "Please enter Last Name",
      },
      email:{
        required: "Please enter Email",
        remote: "This Email already exits.",
      },
      mobile:{
        required: "Please enter Mobile No",
        remote: "This Mobile already exits.",
      },
      customertype:{
        required: "Please select Customer Type",
      },
      address1:{
        required: "Please enter Address",
      },
    }
  });

  /*=============== Customer Type Validation =====================*/
  $('#storeCustomerTypeData').validate({
    rules:{
      customertype_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#customertype_name").val();},
            table :'customer_types',
            column : 'customertype_name',
            id: function() {return $("#customertype_id").val();},
          }
        }
      },
      type_name:
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
      customertype:{
        remote: "This Customer Type already exits.",
        required: "Please enter Customer Type",
      },
      type_name:{
        required: "Please select Type Name",
      },
    }
  });
  /*=============== Firm Type Validation =====================*/
  $('#storeFirmTypeData').validate({
    rules:{
      firmtype_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#firmtype_name").val();},
            table :'firm_types',
            column : 'firmtype_name',
            id: function() {return $("#firmtype_id").val();},
          }
        }
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      firmtype_name:{
        remote: "This Firm Type already exits.",
        required: "Please enter Firm Type",
      },
    }
  });

    /*=============== Customers Validation =====================*/
  $('#createcourierForm').validate({
    rules:{
      name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      first_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      last_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      email:
      {
        required:true,
        minlength:3,
        maxlength: 250,
        email: true,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#email").val();},
            table :'couriers',
            column : 'email',
            id: function() {return $("#courier_id").val();},
          }
        }
      },
      mobile:
      {
        required:true,
        minlength:9,
        maxlength: 15,
        number: true,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#mobile").val();},
            table :'couriers',
            column : 'mobile',
            id: function() {return $("#courier_id").val();},
          }
        }
      },
      phone:
      {
        number: true,
        minlength:9,
        maxlength: 15,
      },
      address:
      {
        required:true,
        maxlength: 250,
      },
      
      country_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      state_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      district_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      city_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      pincode_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      status_id:
      {
        number: true,
        maxlength: 50,
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
        required: "Please enter Courier Name",
      },
      first_name:{
        required: "Please enter First Name",
      },
      last_name:{
        required: "Please enter Last Name",
      },
      email:{
        required: "Please enter Email",
        remote: "This Email already exits.",
      },
      mobile:{
        required: "Please enter Mobile No",
        remote: "This Mobile already exits.",
      },
      address:{
        required: "Please enter Address",
      },
    }
  });
});
    
