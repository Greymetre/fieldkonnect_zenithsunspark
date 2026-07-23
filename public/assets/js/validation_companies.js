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

  /*=============== Event Validation =====================*/
  $('#storeEventsData').validate({
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
        minlength: "Please enter a valid Event Name.",
        required: "Please enter Event Name",
      },
      date:{
        required: "Please enter Event Date",
      },
      message:{
        minlength: "Please enter a valid Description.",
        required: "Please enter Event Description",
      },
    }
  });
  /*=============== Holiday Validation =====================*/
  $('#storeHolidayData').validate({
    rules:{
      occasion:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      date_from:
      {
        required:true,
        date:true,
      },
      date_to:
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
      occasion:{
        minlength: "Please enter a valid Holiday Name.",
        required: "Please enter Holiday Name",
      },
      date_from:{
        required: "Please enter Holiday Date",
      },
      date_to:{
        required: "Please enter Holiday Date",
      },
    }
  });
/*=============== Leave Validation =====================*/
  $('#storeLeaveData').validate({
    rules:{
      leave_from:
      {
        required:true,
      },
      leave_to:
      {
        required:true,
      },
      subject:
      {
        required:true,
        minlength:3,
        maxlength: 250,
      },
      body:
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
      leave_from:{
        minlength: "Please enter a valid Leave From.",
        required: "Please enter Leave From",
      },
      leave_to:{
        required: "Please enter Leave To",
      },
      subject:{
        required: "Please enter Leave Subject",
      },
      body:{
        required: "Please enter Leave Description",
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

});
    
