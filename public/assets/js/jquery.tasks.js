
$(document).on('click', '.taskedit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('value');
      $.ajax({
        url: base_url + '/tasks/'+id,
       dataType:"json",
       success:function(data)
       {
         window.scrollTo(0, 0);
         var description = data.descriptions;
        $('#descriptions').append(description);
        $('#title').val(data.title);
        $('#datetime').val(data.datetime);
        $('#reminder').val(data.reminder);
        $('#user_id').val(data.user_id);
        $('#customer_id').val(data.customer_id);
        $('#task_id').val(data.id);
       }
      })
     });

    $('body').on('click', '.taskdelete', function () {
        var id = $(this).attr("value");
        var base_url =$('.baseurl').data('baseurl');
        var token =$('.token').data('token');
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: base_url + '/tasks/'+id,
            type: 'DELETE',
            data: {_token: token,id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
                $('#panel_'+id).hide();
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
            },
        });
    });

    $('body').on('click', '.taskcomplete', function () {
        var id = $(this).attr("value");
        var base_url =$('.baseurl').data('baseurl');
        var token =$('.token').data('token');
        if(!confirm("Are You sure want to task Complite ?")) {
           return false;
        }
        $.ajax({
            url: base_url + '/tasks-completed',
            type: 'POST',
            data: {_token: token,id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
                $('#panel_'+id).hide();
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
            },
        });
    });

    $('body').on('click', '.taskdone', function () {
        var id = $(this).attr("value");
        var base_url =$('.baseurl').data('baseurl');
        var token =$('.token').data('token');
        if(!confirm("Are You sure want to Approved Complite ?")) {
           return false;
        }
        $.ajax({
            url: base_url + '/tasks-done',
            type: 'POST',
            data: {_token: token,id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
                $('#panel_'+id).hide();
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
            },
        });
    });

    $('body').on('click', '.taskreopen', function () {
        var id = $(this).attr("value");
        var base_url =$('.baseurl').data('baseurl');
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
           url: base_url + '/tasks-done',
            type: 'POST',
            data: {_token: "{{ csrf_token() }}",id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
                $('#panel_'+id).hide();
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
            },
        });
    });

$(document).ready(function () {
     /*=============== Award Validation =====================*/
  $('#storeTaskData').validate({
    rules:{
      datetime:
      {
        required:true,
        date: true,
        dateFormat: true
      },
      // reminder:
      // {
      //   required:true,
      //   date: true,
      //   dateFormat: true
      // },
      // user_id:
      // {
      //   required:true,
      //   number:true,
      // },
      title:
      {
        // required:true,
        // minlength:3,
        // maxlength: 250,
      },
      descriptions:
      {
        // required:true,
        // maxlength: 1250,
      },
      due_datetime:
      {
        required:true
      }
    },
    highlight: function(element) {
      $(element).closest('.error').css("display", "none");
    },
    unhighlight: function(element) {
      $(element).closest('.error').css("display", "block");
    },
    messages:{
      datetime:{
        required: "Please enter Start Date",
      },
      title:{
        required: "Please enter Title",
      },
    }
  });
});



