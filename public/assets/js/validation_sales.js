$(document).ready(function () {

  /*=============== Award Validation =====================*/
  $('#storeProposaldData').validate({
    rules:{
      proposal_date:
      {
        required:true,
      },
      proposal_no:
      {
        minlength:3,
        maxlength: 250,
      },
      expiry_date:
      {
        required:true,
      },
      firm_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      email:
      {
        required:true,
        email:true,
        maxlength: 250,
      },
      mobile:
      {
        required:true,
        number:true,
        minlength:9,
        maxlength: 15,
      },
      address:
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

  /*=============== Event Validation =====================*/
  $('#storeEstimatesData').validate({
    rules:{
      estimate_date:
      {
        required:true,
      },
      estimate_no:
      {
        minlength:3,
        maxlength: 250,
      },
      expiry_date:
      {
        required:true,
      },
      firm_name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      email:
      {
        required:true,
        email:true,
        maxlength: 250,
      },
      mobile:
      {
        required:true,
        number:true,
        minlength:9,
        maxlength: 15,
      },
      address:
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
      firm_name:{
        required: "Please enter Firm Name",
      },
      email:{
        required: "Please enter Email",
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
  /*=============== Purchase Validation =====================*/
  $('#storePurchaseData').validate({
    rules:{
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
      invoice_date:
      {
        required:true,
        date:true,
      },
      buyer_id:
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
/*=============== Leave Validation =====================*/
  $('#storeSalesdData').validate({
    rules:{
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
      invoice_date:
      {
        required:true,
        date:true,
      },
      seller_id:
      {
        required:true,
        number:true,
      },
      buyer_id:
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
  /*=============== Leave Type Validation =====================*/
  $('#storeLeaveTypeData').validate({
    rules:{
      leave_type:
      {
        required:true,
        minlength:3,
        maxlength: 200,
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
      leave_type:{
        minlength: "Please enter a valid LeaveType Name.",
        required: "Please enter LeaveType Name",
      },
      description:{
        required: "Please enter LeaveType Date",
      },
    }
  });

  /*=============== Meeting Validation =====================*/
  $('#storeMeetingData').validate({
    rules:{
      name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      date:
      {
        required:true,
        date:true,
      },
      message:
      {
        required:true,
        minlength:3,
        maxlength: 2000,
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
        minlength: "Please enter a valid Meeting Name.",
        required: "Please enter Meeting Name",
      },
      date:{
        required: "Please enter Meeting Date",
      },
      message:{
        required: "Please enter Meeting Description",
      },
    }
  });
  /*=============== Project Validation =====================*/
  $('#storeProjectData').validate({
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
        minlength: "Please enter a valid Project Name.",
        required: "Please enter Project Name",
      },
      description:{
        required: "Please enter Project Description",
      },
    }
  });

    /*=============== Promotion Validation =====================*/
  $('#storePromotionData').validate({
    rules:{
      old_designation:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      new_designation:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      old_salary:
      {
        required:true,
      },
      new_designation:
      {
        required:true,
      },
      promotion_date:
      {
        required:true,
        date:true,
      },
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      old_designation:{
        minlength: "Please enter a valid Old Designation.",
        required: "Please enter Promotion Name",
      },
      new_designation:{
        required: "Please enter New Designation",
      },
      old_salary:{
        required: "Please enter Old Salary",
      },
      new_salary:{
        required: "Please enter New Salary",
      },
      promotion_date:{
        required: "Please enter Promotion Date",
      },
    }
  });

    /*=============== Team Validation =====================*/
  $('#storeTeamData').validate({
    rules:{
      name:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      team_code:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      manager_id:
      {
        required:true,
      },
      leader_id:
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
      name:{
        minlength: "Please enter a valid Team Name.",
        required: "Please enter Team Name",
      },
      team_code:{
        required: "Please enter Team Code",
      },
      manager_id:{
        required: "Please Select Manager",
      },
      leader_id:{
        required: "Please Select Leader",
      },
    }
  });

    /*=============== Training Validation =====================*/
  $('#storeTrainingData').validate({
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
        minlength: "Please enter a valid Training Name.",
        required: "Please enter Training Name",
      },
      description:{
        required: "Please enter Training Description",
      },
    }
  });

   /*=============== Sales Target User Update Validation =====================*/
  $('#updateSalesTargetForm').validate({
    rules:{
      user_id:
      {
        required:true,
      },
      month:
      {
        required:true,
      },
      year:
      {
        required:true,
      },
      target:
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
      user_id:{
        required: "User field is required.",
      },
      month:{
        required: "Month field is required.",
      },
      year:{
        required: "Year field is required.",
      },
      target:{
        required: "Taget amount field is required.",
      },
    }
  });
});
    
