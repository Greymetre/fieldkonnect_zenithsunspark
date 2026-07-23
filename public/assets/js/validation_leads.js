$(document).ready(function () {
   /*=============== Customers Validation =====================*/
  var base_url =$('.baseurl').data('baseurl');
  var token = $("meta[name='csrf-token']").attr("content");

  $('#storeLeadData').validate({
    rules:{
      first_name:
      {
        // required:true,
        minlength:3,
        maxlength: 250,
      },
      last_name:
      {
        // required:true,
        minlength:3,
        maxlength: 250,
      },
      mobile:
      {
        // required:true,
        minlength:9,
        maxlength: 15,
        number: true,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#mobile").val();},
            table :'leads',
            column : 'mobile',
            id: function() {return $("#lead_id").val();},
          }
        }
      },
      email:
      {
        // required:true,
        minlength:3,
        maxlength: 250,
        email: true,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#email").val();},
            table :'leads',
            column : 'email',
            id: function() {return $("#lead_id").val();},
          }
        }
      },
      phone:
      {
        minlength:9,
        maxlength: 15,
        number: true,
      },
      leadstage_id:
      {
        required:true,
        number: true,
      },
      status_id:
      {
        required:true,
        number: true,
        maxlength: 50,
      },
      priority_id:
      {
        number: true,
        maxlength: 50,
      },
      leadtype_id:
      {
        number: true,
        maxlength: 50,
      },
      source_id:
      {
        number: true,
        maxlength: 50,
      },
      designation_id:
      {
        number: true,
        maxlength: 50,
      },
      industry_id:
      {
        number: true,
        maxlength: 50,
      },
      employeesize_id:
      {
        number: true,
        maxlength: 50,
      },
      profile_link:
      {
        minlength:3,
        maxlength: 250,
        remote:{
          url:base_url+"/uniqueValidation",
          type:"post",
          data: {
            "_token": token,
            value: function() {return $("#profile_link").val();},
            table :'leads',
            column : 'profile_link',
            id: function() {return $("#lead_id").val();},
          }
        }
      },
      description:
      {
        maxlength: 450,
      },
      name:
      {
        maxlength: 250,
      },
      customer_website:
      {
        maxlength: 250,
      },
      address:
      {
        maxlength: 250,
      },
      country_id:
      {
        number: true,
        maxlength: 50,
      },
      state_id:
      {
        number: true,
        maxlength: 50,
      },
      district_id:
      {
        number: true,
        maxlength: 50,
      },
      city_id:
      {
        number: true,
        maxlength: 50,
      },
      pincode_id:
      {
        number: true,
        maxlength: 50,
      },
      list:
      {
        // required:true,
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
        required: "Please enter Firm Name",
      },
      profile_link:{
        remote: "This Profile Link already exits.",
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
    }
  });

});
    
