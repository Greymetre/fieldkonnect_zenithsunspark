$(document).ready(function () {

  /*=============== Users Validation =====================*/
  var base_url =$('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");

  $('#storeUserData').validate({
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
            id: function() {return $("#user_id").val();},
            table :'users',
            column : 'email',
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
            table :'users',
            column : 'mobile',
            id: function() {return $("#user_id").val();},
          }
        }
      },
      password:
      {
        required:{
                depends: function(element) {
                    return $("#user_id").val() == null;
                }
            },
        minlength:8,
        maxlength: 50,
      },
      father_name:
      {
        maxlength: 250,
      },
      gender:
      {
        maxlength: 250,
      },
      date_of_birth:
      {
        //anyDate: true
      },
      pan_number:
      {
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#pan_number").val();},
            table :'user_details',
            column : 'pan_number',
            id: function() {return $("#user_id").val();},
          }
        }
      },
      emergency_number:
      {
        maxlength: 250,
      },
      employee_code:
      {
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#employee_code").val();},
            table :'user_details',
            column : 'employee_code',
            id: function() {return $("#user_id").val();},
          }
        }
      },
      account_number:
      {
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#account_number").val();},
            table :'user_details',
            column : 'account_number',
            id: function() {return $("#user_id").val();},
          }
        }
      },
      current_address:
      {
        maxlength: 250,
      },
      permanent_address:
      {
        maxlength: 250,
      },
      bank_name:
      {
        maxlength: 250,
      },
      ifsc_code:
      {
        maxlength: 250,
      },
      pf_number:
      {
        maxlength: 50,
      },
      un_number:
      {
        maxlength: 50,
      },
      date_of_joining:
      {
        required:true,
        //anyDate: true
      },
      salary:
      {
        number: true,
        maxlength: 50,
      },
      designation_id:
      {
        number: true,
        maxlength: 50,
      },
      department_id:
      {
        number: true,
        maxlength: 50,
      },
      date_of_confirmation:
      {
        //anyDate: true
      },
      probation_period:
      {
        maxlength: 250,
      },
      notice_period:
      {
        maxlength: 250,
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
      employee_code:{
        remote: "This User Code already exits.",
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
      current_address:{
        required: "Please enter Address",
      },
    }
  });

  $('#updateUserData').validate({
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
            table :'users',
            column : 'email',
            id: function() {return $("#user_id").val();},
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
            table :'users',
            column : 'mobile',
            id: function() {return $("#user_id").val();},
          }
        }
      },
      password:
      {
        required:{
                depends: function(element) {
                    return $("#user_id").val() == null;
                }
            },
        minlength:8,
        maxlength: 50,
      },
      father_name:
      {
        maxlength: 250,
      },
      gender:
      {
        maxlength: 250,
      },
      date_of_birth:
      {
        //anyDate: true
      },
      pan_number:
      {
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#pan_number").val();},
            table :'user_details',
            column : 'pan_number',
            id: function() {return $("#user_id").val();},
          }
        }
      },
      emergency_number:
      {
        maxlength: 250,
      },
      employee_code:
      {
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#employee_code").val();},
            table :'user_details',
            column : 'employee_code',
            id: function() {return $("#user_id").val();},
          }
        }
      },
      current_address:
      {
        maxlength: 250,
      },
      permanent_address:
      {
        maxlength: 250,
      },
      bank_name:
      {
        maxlength: 250,
      },
      ifsc_code:
      {
        maxlength: 250,
      },
      pf_number:
      {
        maxlength: 50,
      },
      un_number:
      {
        maxlength: 50,
      },
      date_of_joining:
      {
        required:true,
        //anyDate: true
      },
      salary:
      {
        number: true,
        maxlength: 50,
      },
      designation_id:
      {
        number: true,
        maxlength: 50,
      },
      department_id:
      {
        number: true,
        maxlength: 50,
      },
      date_of_confirmation:
      {
        //anyDate: true
      },
      probation_period:
      {
        maxlength: 250,
      },
      notice_period:
      {
        maxlength: 250,
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
      employee_code:{
        remote: "This User Code already exits.",
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
      current_address:{
        required: "Please enter Address",
      },
    }
  });

  /*=============== User Type Validation =====================*/
  $('#storeRoleData').validate({
    rules:{
      name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#title").val();},
            table :'roles',
            column : 'name',
            id: function() {return $("#role_id").val();},
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
      name:{
        remote: "This Role already exits.",
        required: "Please enter Role Name",
      },
    }
  });
  /*=============== Firm Type Validation =====================*/
  $('#storePermissionData').validate({
    rules:{
      name:
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
      name:{
        required: "Please enter Permission Name",
      },
    }
  });
});
    
