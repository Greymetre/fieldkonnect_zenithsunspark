
function getUserlist()
{
  var token = $("meta[name='csrf-token']").attr("content");
  var base_url =$('.baseurl').data('baseurl'); 
    $.ajax({
        url: base_url + '/getUserList',
        dataType: "json",
        type: "POST",
        data:{ "_token": token},
        success: function(res){
            var table = document.getElementById(tab_beat_users),rIndex;
            if(res){
              $('#tab_beat_users tr:last').find(".user").empty();
                $('#tab_beat_users tr:last').find(".user").append('<option value="">Select User</option>');
              $.each(res,function(key,value){ 
                $('#tab_beat_users tr:last').find('.user').append('<option value="'+value.id+'">'+value.name+' '+value.mobile+'</option>');

              });
            }
            else{
               row.find(".product").empty();
            }
        }
    });
}


function getRetailerlist()
{
    var base_url =$('.baseurl').data('baseurl'); 
    var token = $("meta[name='csrf-token']").attr("content");
    var state_id = $("select[name=state_id]").val();
    var district_id = $(".district").val();
    var city_id = $(".city").val();
    console.log(district_id,city_id);
    var users = [];    
    $(".user :selected").each(function(){
      users.push($(this).val()); 
    });
    $.ajax({
        url: base_url + '/getRetailerlist',
        dataType: "json",
        type: "POST",
        data:{ "_token": token, state_id : state_id, district_id : district_id, city_id : city_id, user_id : users },
        success: function(res){
            var table = document.getElementById(tab_beat_customer),rIndex;
            if(res){
              $('#tab_beat_customer tr:last').find(".customer").empty();
                $('#tab_beat_customer tr:last').find(".customer").append('<option value="">Select Customer</option>');
              $.each(res,function(key,value){ 
                $('#tab_beat_customer tr:last').find('.customer').append('<option value="'+value.id+'">'+value.name+' '+value.mobile+'</option>');
              });
              $('.select2').select2()
            }
            else{
               row.find(".customerlists").empty();
            }
        }
    });
}

$(document).ready(function(){
    var $tablecustomer = $('table.beat-users-rows'),
    counter = 1;
      $('a.add-users-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr class="item-row"> <td>'+counter+'</td>'+
                  '<td><select name="users[user_id]'+counter+'" class="form-control user rowchange select2"/> </select></td>' +
                  '<td class="td-actions text-right"><a class="remove-user-rows btn btn-danger" title="Remove row"><i class="material-icons">close</i></a></td> </tr>';
          $tablecustomer.append(newRow);
      });
   
      $tablecustomer.on('click', '.remove-user-rows', function() {
          $(this).closest('tr').remove();
      });
});

$(document).ready(function(){
    var $tablecustomer = $('table.beat-customer-rows'),
    counter = 1;
      $('a.add-customer-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr class="item-row"> <td>'+counter+'</td>'+
                  '<td><select name="customers[' + counter + ']" class="form-control customer rowchange select2"/> </select></td>' +
                  '<td class="td-actions text-right"><a class="remove-customer-rows btn btn-danger" title="Remove row"><i class="material-icons">close</i></a></td> </tr>';
          $tablecustomer.append(newRow);
          $('.select2').select2()
      });
      
      $tablecustomer.on('click', '.remove-customer-rows', function() {
          $(this).closest('tr').remove();
      });
});

function getScheduleUserlist()
{
  var token = $("meta[name='csrf-token']").attr("content");
  var base_url =$('.baseurl').data('baseurl'); 
  var beat_id = $('#beat_id').val();
    $.ajax({
        url: base_url + '/getUserList',
        dataType: "json",
        type: "POST",
        data:{ "_token": token, beat_id : beat_id},
        success: function(res){
            if(res){
              $('#tab_beat_schedule tr:last').find(".user").empty();
                $('#tab_beat_schedule tr:last').find(".user").append('<option value="">Select User</option>');
              $.each(res,function(key,value){ 
                $('#tab_beat_schedule tr:last').find('.user').append('<option value="'+value.id+'">'+value.name+' '+value.mobile+'</option>');

              });
            }
            else{
               row.find(".product").empty();
            }
        }
    });
}
$(document).ready(function(){
    var $table = $('table.beat-schedule-rows'),
    counter = 1;
      $('a.add-schedule-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr class="item-row"> <td>'+counter+'</td>'+
                  '<td><select name="beatdetail[' + counter + '][user_id]" class="form-control user rowchange select2"/> </select></td>' +
                   '<td><input type="date" name="beatdetail[' + counter + '][beat_date]" class="form-control datepicker"></td>' +
                  '<td class="td-actions text-right"><a class="remove-rows btn btn-danger" title="Remove row"><i class="material-icons">close</i></a></td> </tr>';
          $table.append(newRow);
      });
   
      $table.on('click', '.remove-rows', function() {
          $(this).closest('tr').remove();
      });
});

function deleteschedules(e)
{
    if (confirm('Are you sure you want to delete this?')) {
        var id = e; 
        var token = $("meta[name='csrf-token']").attr("content");
        var base_url =$('.baseurl').data('baseurl');
        $.ajax({
            url: base_url + '/schedule-delete/'+id,
            dataType: "json",
            type: "DELETE",
            cache: false,
            data:{"id": id,"_token": token},
            success: function(res){
                if(res)
                {
                    swal({
                      title: "Deleted!",
                      text: "Schedule has been deleted successfully",
                      type: "success",
                      confirmButtonText: "OK",
                    });
                    location.reload();
                }
            }
        });
    }
    else
    {
        location.reload();
    }
}
function deleteUserFromBeat(e)
{
    if (confirm('Are you sure you want to delete this?')) {
        var id = e; 
        var token = $("meta[name='csrf-token']").attr("content");
        var base_url =$('.baseurl').data('baseurl');
        $.ajax({
            url: base_url + '/beat-user-delete/'+id,
            dataType: "json",
            type: "DELETE",
            cache: false,
            data:{"id": id,"_token": token},
            success: function(res){
                if(res)
                {
                    swal({
                      title: "Deleted!",
                      text: "Schedule has been deleted successfully",
                      type: "success",
                      confirmButtonText: "OK",
                    });
                    location.reload();
                }
            }
        });
    }
    else
    {
        location.reload();
    }
}

function deletecustomers(e)
{
    if (confirm('Are you sure you want to delete this?')) {
        var id = e; 
        var token = $("meta[name='csrf-token']").attr("content");
        var base_url =$('.baseurl').data('baseurl');
        $.ajax({
            url: base_url + '/beatcustomer-delete/'+id,
            dataType: "json",
            type: "DELETE",
            cache: false,
            data:{"id": id,"_token": token},
            success: function(res){
                if(res)
                {
                    swal({
                      title: "Deleted!",
                      text: "Schedule has been deleted successfully",
                      type: "success",
                      confirmButtonText: "OK",
                    });
                    location.reload();
                }
            }
        });
    }
    else
    {
        location.reload();
    }
}

/*=============== Beat Validation =====================*/
  $('#storeBeatData').validate({
    rules:{
      beat_name:
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
        required: "Please enter Description",
      },
    }
  });
    
