
function checkPageLoad() {
    if (performance.navigation.type === 1) {
        if (localStorage.getItem('payroll')) {
            localStorage.removeItem('payroll');
        }
        if (localStorage.getItem('executive_id')) {
            localStorage.removeItem('executive_id');
        }
        if (localStorage.getItem('expenses_type')) {
            localStorage.removeItem('expenses_type');
        }
        if (localStorage.getItem('branch_id')) {
            localStorage.removeItem('branch_id');
        }
        if (localStorage.getItem('division_id')) {
            localStorage.removeItem('division_id');
        }
        if (localStorage.getItem('expense_id')) {
            localStorage.removeItem('expense_id');
        }
        if (localStorage.getItem('status')) {
            localStorage.removeItem('status');
        }
        if (localStorage.getItem('start_date')) {
            localStorage.removeItem('start_date');
        }
        if (localStorage.getItem('end_date')) {
            localStorage.removeItem('end_date');
        }
    } else { }
}
document.addEventListener('DOMContentLoaded', function () {
    checkPageLoad();
});



$(document).ready(function () {
    $('.selectpicker').selectpicker();
    oTable = $('#getallexpenses').DataTable({
        "processing": true,
        "serverSide": true,
        "stateSave": true,
        "bStateSave": true,
        "lengthMenu": [
            [10, 25, 50, 100, 500, 1000],
            [10, 25, 50, 100, 500, 1000]
        ],

        ajax: {
            url: expensesIndexUrl,
            data: function (d) {
                d.payroll = $('#payroll').val(),
                    d.executive_id = $('#executive_id').val(),
                    d.expenses_type = $('#expenses_type').val(),
                    d.branch_id = $('#branch_id').val(),
                    d.division_id = $('#division_id').val(),
                    d.expense_id = $('#expense_id').val(),
                    d.status = $('#status').val(),
                    d.start_date = $('#start_date').val(),
                    d.end_date = $('#end_date').val()
                d.attechments = $('#attechments').val()

            }
        },

        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
            {
                data: 'id',
                name: 'id',
                searchable: false
            },
            {
                data: 'date',
                name: 'date',
                searchable: false
            },
            {
                data: 'users.name',
                name: 'users.name',
                orderable: false,
                searchable: false
            },
            {
                data: 'users.getdesignation.designation_name',
                name: 'users.getdesignation.designation_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'expense_type.name',
                name: 'expense_type.name',
                orderable: false,
                searchable: false
            },
            {
                data: 'claim_amount',
                name: 'claim_amount',
                orderable: false,
                searchable: false
            },
            {
                data: 'approve_amount',
                name: 'approve_amount',
                orderable: false,
                searchable: false
            },
            {
                data: 'checker_status',
                name: 'checker_status',
                orderable: false,
                searchable: false
            },
            {
                data: 'note',
                name: 'note',
                orderable: false,
                searchable: false
            },
            {
                data: 'date_create',
                name: 'date_create',
                orderable: false,
                searchable: false
            },
            {
                data: 'users.getbranch.branch_name',
                name: 'users.getbranch.branch_name',
                orderable: false,
                searchable: false
            },
            {
                data: 'total_km',
                name: 'total_km',
                orderable: false,
                searchable: false
            },
            {
                data: 'action',
                name: 'action',
                "defaultContent": '',
                orderable: false,
                searchable: false
            },
            {
                data: 'attech',
                name: 'attech',
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#attechments').change(function () {
        oTable.draw();
    });
    $('#payroll').change(function () {
        $('#executive_id').val('');
        $('#executive_id').change();
        localStorage.setItem("payroll", $(this).val());
        localStorage.removeItem('executive_id');
        oTable.draw();
    });
    $('#branch_id').change(function () {
        localStorage.setItem("branch_id", $(this).val());
        oTable.draw();
    });
    $('#status').change(function () {
        localStorage.setItem("status", $(this).val());
        oTable.draw();
    });
    $('#executive_id').change(function () {
        localStorage.setItem("executive_id", $(this).val());
        fetch(removeSessionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        })
        localStorage.setItem("is_reset", '1');
        oTable.draw();
    });

    $('#expenses_type').change(function () {
        localStorage.setItem("expenses_type", $(this).val());
        oTable.draw();
    });
    $('#start_date').change(function () {
        $.ajax({
            url: "/getExpenseCount",
            data: {
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val()
            },
            method: "GET",
            success: function (data) {
                $("#pending_count").html(data.data.pending_count);
                $("#approve_count").html(data.data.approve_count);
                $("#reject_count").html(data.data.reject_count);
                $("#checked_count").html(data.data.checked_count);
            }
        });
        localStorage.setItem("start_date", $(this).val());
        oTable.draw();
    });
    $('#end_date').change(function () {
        $.ajax({
            url: "/getExpenseCount",
            data: {
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val()
            },
            method: "GET",
            success: function (data) {
                $("#pending_count").html(data.data.pending_count);
                $("#approve_count").html(data.data.approve_count);
                $("#reject_count").html(data.data.reject_count);
                $("#checked_count").html(data.data.checked_count);
            }
        });
        localStorage.setItem("end_date", $(this).val());
        oTable.draw();
    });
    $('#division_id').change(function () {
        localStorage.setItem("division_id", $(this).val());
        oTable.draw();
    });
    $('#expense_id').change(function () {
        localStorage.setItem("expense_id", $(this).val());
        oTable.draw();
    });

    $('body').on('click', '.multiChange', function () {
        const selectedValues = [];
          $('.row-checkbox:checked').each(function () {
              selectedValues.push($(this).val());
          });
          if(selectedValues.length == 0){
            alert("Please select at least one record");
            return false;
          }
          const status = $(this).data('status');

          var token = $("meta[name='csrf-token']").attr("content");
          if(status == 1){
            if(!confirm("Are You sure want to approve "+selectedValues.length+" Expenses?")) {
               return false;
            }
            $.ajax({
              url: multiApprove,
              type: 'POST',
              data: {
                _token: token,
                id: selectedValues.toString()
              },
              success: function(data) {
                oTable.draw();
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                  $('.alert').addClass("alert-success");
                } else {
                  $('.alert').addClass("alert-danger");
                }
                $('.message').append(data.message);
              },
            });
          }else if(status == 3){
            if(!confirm("Are You sure want to checked "+selectedValues.length+" Expenses?")) {
               return false;
            }
            $.ajax({
              url: multiCheck,
              type: 'POST',
              data: {
                _token: token,
                id: selectedValues.toString()
              },
              success: function(data) {
                oTable.draw();
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                  $('.alert').addClass("alert-success");
                } else {
                  $('.alert').addClass("alert-danger");
                }
                $('.message').append(data.message);
              },
            });
          }else if(status == 2){
            if(!confirm("Are You sure want to reject "+selectedValues.length+" Expenses?")) {
               return false;
            }
            $.ajax({
                url: multiReject,
                type: 'POST',
                data: {
                  _token: token,
                  id: selectedValues.toString()
                },
                success: function(data) {
                  oTable.draw();
                  $('.message').empty();
                  $('.alert').show();
                  if (data.status == 'success') {
                    $('.alert').addClass("alert-success");
                  } else {
                    $('.alert').addClass("alert-danger");
                  }
                  $('.message').append(data.message);
                },
              });
          }        
      });

});


$(document).ready(function () {
    var payroll = localStorage.getItem('payroll');
    var reset = localStorage.getItem('is_reset');

    if (session_exec && session_exec != '' && session_exec != null && reset != '1') {
        var executive_id = session_exec;
    } else {
        var executive_id = localStorage.getItem('executive_id');
    }
    var expenses_type = localStorage.getItem('expenses_type');
    var branch_id = localStorage.getItem('branch_id');
    var division_id = localStorage.getItem('division_id');
    var expense_id = localStorage.getItem('expense_id');
    var status = localStorage.getItem('status');
    var start_date = localStorage.getItem('start_date');
    var end_date = localStorage.getItem('end_date');

    if (payroll) {
        $('#payroll').val(payroll).trigger('change');
    }
    if (executive_id) {
        setTimeout(() => {

            $('#executive_id').val(executive_id).trigger('change');
        }, 1500);
    }
    if (expenses_type) {
        // $('#expenses_type').val(expenses_type).trigger('change');
        $.post(expensesTypeUrl, {
            'payroll': $('#payroll').val(),
            '_token': token
        }, function (response) {
            var select = $('#expenses_type');
            select.empty();
            select.append(response);
            select.val(expenses_type);
            setTimeout(() => {
                select.selectpicker('refresh');
            }, 1500);
            oTable.draw();
        });
    }
    if (branch_id) {
        $('#branch_id').val(branch_id).trigger('change');
    }
    if (division_id) {
        $('#division_id').val(division_id).trigger('change');
    }
    if (expense_id) {
        $('#expense_id').val(expense_id).trigger('change');
    }
    if (status) {
        $('#status').val(status);
        $('#status').selectpicker('refresh');
    }
    if (start_date) {
        $('#start_date').val(start_date).trigger('change');
    }
    if (end_date) {
        $('#end_date').val(end_date).trigger('change');
    }
});

$('body').on('click', '.activeRecord', function () {
    var id = $(this).attr("id");
    var active = $(this).attr("value");
    var status = '';
    if (active == '1') {
        status = 'Incative ?';
    } else {
        status = 'Ative ?';
    }
    var token = $("meta[name='csrf-token']").attr("content");
    if (!confirm("Are You sure want " + status)) {
        return false;
    }
    $.ajax({
        url: expensesActiveUrl,
        type: 'POST',
        data: {
            _token: token,
            id: id,
            active: active
        },
        success: function (data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
                $('.alert').addClass("alert-success");
            } else {
                $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            oTable.draw();
        },
    });
});



$('body').on('click', '.delete', function () {
    var id = $(this).attr("value");
    var token = $("meta[name='csrf-token']").attr("content");
    if (!confirm("Are You sure want to delete ?")) {
        return false;
    }
    $.ajax({
        url: expensesMainUrl + '/' + id,
        type: 'DELETE',
        data: {
            _token: token,
            id: id
        },
        success: function (data) {
            $('.alert').show();
            if (data.status == 'success') {
                $('.alert').addClass("alert-success");
            } else {
                $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            oTable.draw();
        },
    });
});

// for get expense type
$('#payroll').change(function () {
    var payroll = $(this).val();

    $.post(expensesTypeUrl, {
        'payroll': payroll,
        '_token': token
    }, function (response) {

        var select = $('#expenses_type');
        select.empty();
        select.append(response);
        setTimeout(() => {
            select.selectpicker('refresh');
        }, 1500);

    })

}).trigger('change');


setTimeout(() => {
    $('#expense_id').select2({
        placeholder: 'Select Expense Id',
        allowClear: true,
        ajax: {
            url: expensesDataUrl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            cache: true
        }
    }).trigger('change');
}, 1500);

$('body').on('click', '.approve_status', function () {
    var id = $('#expenseid').val();
    var claimamount = $('#claim_new_amount').val();
    var token = $("meta[name='csrf-token']").attr("content");
    $('#expense_new_id').val(id);
    $('#approve_amnt').val(claimamount);
    $("#approve_expense").modal();

});

function disableButton() {
    var btn = document.querySelector('.save-apr');
    btn.disabled = true;

    var form = $('#approveExpenseForms');
    var url = form.attr('action');
    var formData = form.serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function (response) {
            $('.message').empty();
            $('.alert').show();
            if (response.status == 'success') {
                $('.alert').addClass("alert-success");
                // setTimeout(function() {
                //   location.reload();
                // }, 3000);
                $('#approve_expense').modal('hide');
                $('#expenseModal').modal('hide');
                oTable.draw();

            } else {
                $('.alert').addClass("alert-danger");
                // setTimeout(function() {
                //   location.reload();
                // }, 3000);
                $('#approve_expense').modal('hide');
                oTable.draw();
            }
            $('.message').append(response.message);
        },
    });
}

$('body').on('click', '.reject_status', function () {
    var id = $('#expenseid').val();
    var token = $("meta[name='csrf-token']").attr("content");
    $('#reject_expense_id').val(id);
    $("#reject_expense").modal();
});

function disableButtonreject() {
    var btn = document.querySelector('.save-rjc');
    btn.disabled = true;

    var form = $('#rejectExpenseForm');
    var url = form.attr('action');
    var formData = form.serialize();

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        success: function (response) {
            $('.message').empty();
            $('.alert').show();
            if (response.status == 'success') {
                $('.alert').addClass("alert-success");
                // setTimeout(function() {
                //   location.reload();
                // }, 3000);
                $('#reject_expense').modal('hide');
                $('#expenseModal').modal('hide');
                oTable.draw();
            } else {
                $('.alert').addClass("alert-danger");
                // setTimeout(function() {
                //   location.reload();
                // }, 3000);
                $('#reject_expense').modal('hide');
                oTable.draw();
            }
            $('.message').append(response.message);
        },
    });
}

$('body').on('click', '.unchecked_status', function () {
    var id = $('#expenseid').val();
    console.log(id);
    var token = $("meta[name='csrf-token']").attr("content");
    if (!confirm("Are You sure do you want to unchecked expense status")) {
        return false;
    } else {

        $.ajax({
            url: expensesUncheckUrl,
            type: 'POST',
            data: {
                _token: token,
                id: id
            },
            success: function (data) {
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                    $('.alert').addClass("alert-success");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();

                } else {
                    $('.alert').addClass("alert-danger");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();
                }
                $('.message').append(data.message);

            },
        });


    }

});

$('body').on('click', '.checked_by_reporting_status', function () {
    var id = $('#expenseid').val();
    var token = $("meta[name='csrf-token']").attr("content");
    if (!confirm("Are You sure do you want to checked expense status")) {
        return false;
    } else {

        $.ajax({
            url: expensesCheckedUrl,
            type: 'POST',
            data: {
                _token: token,
                id: id,
                status: '4'
            },
            success: function (data) {
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                    $('.alert').addClass("alert-success");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();

                } else {
                    $('.alert').addClass("alert-danger");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();
                }
                $('.message').append(data.message);

            },
        });


    }

});
$('body').on('click', '.checked_status', function () {
    var id = $('#expenseid').val();
    var token = $("meta[name='csrf-token']").attr("content");
    if (!confirm("Are You sure do you want to checked expense status")) {
        return false;
    } else {

        $.ajax({
            url: expensesActiveUrl,
            type: 'POST',
            data: {
                _token: token,
                id: id,
                status: '3'
            },
            success: function (data) {
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                    $('.alert').addClass("alert-success");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();

                } else {
                    $('.alert').addClass("alert-danger");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();
                }
                $('.message').append(data.message);

            },
        });


    }

});

$('body').on('click', '.hold_status', function () {
    var id = $('#expenseid').val();
    var token = $("meta[name='csrf-token']").attr("content");
    if (!confirm("Are You sure do you want to hold expense?")) {
        return false;
    } else {

        $.ajax({
            url: expensesActiveUrl,
            type: 'POST',
            data: {
                _token: token,
                id: id,
                status: '5'
            },
            success: function (data) {
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                    $('.alert').addClass("alert-success");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();

                } else {
                    $('.alert').addClass("alert-danger");
                    // setTimeout(function() {
                    //   location.reload();
                    // }, 3000);
                    $('#expenseModal').modal('hide');
                    oTable.draw();
                }
                $('.message').append(data.message);

            },
        });


    }

});

$("#checkAll").on("click", function () {
    $('input:checkbox').not(this).prop('checked', this.checked);
    $(".multi-a-r").toggleClass('d-none');
});
