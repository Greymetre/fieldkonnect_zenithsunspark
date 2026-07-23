<x-app-layout>
  <style>
    .table.new-table th,
    .table.new-table td {
      border-top: 0px !important;
    }

    b {
      font-weight: 600;
    }

    .all-attach {
      align-items: center;
      border: 1px solid lightgrey;
      border-radius: 5px;
      padding: 5px 10px;
      width: 90%;
    }

    h5 {
      margin-bottom: 0 !important;
    }

    th,
    .secondTD {
      border-right: 1px solid grey !important;
    }
  </style>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">

        @if(Session::has('success'))
        <div class="alert alert-success" id="hide_div">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('success') !!}</strong>
        </div>
        @endif

        @if(Session::has('danger'))
        <div class="alert alert-danger" id="hide_danger">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('danger') !!}</strong>
        </div>
        @endif


        <div class="alert" style="display: none;" id="hide_check">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <strong class="message"></strong>
        </div>



        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-4">
                <h3 class="card-title pb-3">Resignation View</h3>
              </div>
              <div class="col-8 text-right">


                @if(auth()->user()->can(['resignationt_change_status']))

                @if($resignation->status=='0')
                <a href="#" onclick="chnageStatus(1)" type="button" class="btn btn-sm btn-success accept_status"><b>Accept</b></a>
                <a href="#" onclick="chnageStatus(2)" type="button" class="btn btn-sm btn-danger reject_status"><b>Reject</b></a>
                <a href="#" onclick="chnageStatus(3)" type="button" class="btn btn-sm btn-info revoke_status"><b>Revoke</b></a>
                @elseif($resignation->status=='1')
                <a href="#" onclick="chnageStatus(4)" type="button" class="btn btn-sm btn-success revoke_status"><b>Approve</b></a>
                <a href="#" onclick="chnageStatus(5)" type="button" class="btn btn-sm btn-warning revoke_status"><b>Hold</b></a>
                @elseif($resignation->status=='5')
                <a href="#" onclick="chnageStatus(4)" type="button" class="btn btn-sm btn-success revoke_status"><b>Approve</b></a>
                @endif

                @endif

                <a class="btn btn-primary btn-sm" href="{{route('resignations.index')}}"><b>Back</b></a>


              </div>
              <!-- /.col -->
            </div>
            <input type="hidden" id="resignation_id" name="resignation_id" value="{{$resignation['id']}}">

            <hr>

            <div class="invoice p-3 mb-1">
              <div class="row">
                <div class="col-2" style="border-right: 1px solid lightgrey;">
                  @if($resignation->status == '0')
                  <span class="badge badge-warning">Pending</span>
                  @elseif($resignation->status == '1')
                  <span class="badge badge-info">Accepted</span>
                  @elseif($resignation->status == '2')
                  <span class="badge badge-danger">Rejected</span>
                  @elseif($resignation->status == '3')
                  <span class="badge badge-success">Revoked</span>
                  @elseif($resignation->status == '4')
                  <span class="badge badge-success">Approved</span>
                  @elseif($resignation->status == '5')
                  <span class="badge badge-info">Hold</span>
                  @endif
                  <p class="text-dark">{{$resignation->remark}}</p>
                  <h4>
                    <small class="float-left">Resignation Date <p style="font-size: 22px; color:#5252b7">{!! date('d M Y',strtotime($resignation['submit_date'])) !!}</p></small>
                  </h4>
                  <h4>
                    <small class="float-left">Last Working Date <p style="font-size: 22px; color:#5252b7">{!! date('d M Y',strtotime($resignation['last_working_date'])) !!}
                        @if(auth()->user()->can('resignation_last_working_date_change'))
                        <i class="material-icons" style="font-size: 16px !important;cursor:pointer;" id="edit-last-date">edit</i>
                        @endif
                      </p></small>
                  </h4>
                </div>
                <div class="col-10">
                  <div class="row">
                    <div class="col-3">
                      <h5>Name</h5>
                      <h6 class="text-info">{{$resignation->user->name}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Employee Code</h5>
                      <h6 class="text-info">{{$resignation->employee_code}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Branch</h5>
                      <h6 class="text-info">{{$resignation->branch->branch_name}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Division</h5>
                      <h6 class="text-info">{{$resignation->division->division_name}}</h6>
                    </div>

                  </div>

                  <div class="row mt-3">
                    <div class="col-3">
                      <h5>Designation</h5>
                      <h6 class="text-info">{{$resignation->user->getdesignation->designation_name}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Mobile Number</h5>
                      <h6 class="text-info">{{$resignation->user->mobile}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Reporting Manager</h5>
                      <h6 class="text-info">{{$resignation->user->reportinginfo->name}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Date Of Joining</h5>
                      <h6 class="text-info">{{$resignation->date_of_joining}}</h6>
                    </div>
                  </div>

                  <div class="row mt-3">
                    <div class="col-3">
                      <h5>Notice Period</h5>
                      <h6 class="text-info">{{$resignation->notice}} Month</h6>
                    </div>
                    <div class="col-3">
                      <h5>Reason</h5>
                      <h6 class="text-info">{{$resignation->reason}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Personal Email ID</h5>
                      <h6 class="text-info">{{$resignation->persoanla_email}}</h6>
                    </div>
                    <div class="col-3">
                      <h5>Personal Mobile Number</h5>
                      <h6 class="text-info">{{$resignation->persoanla_mobile}}</h6>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            @if(auth()->user()->can('resignation_check_list_show'))
            <div class="invoice p-3 mb-1">
              <h3 class="text-dark">Check List</h3>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th colspan="2">HOD</th>
                    <th colspan="2">HR.DEP</th>
                    <th colspan="2">ACCOUNTS</th>
                    <th colspan="2">IT</th>
                    <th colspan="2">OEM</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="width: 7%;">Document & Files</td>
                    <td class="secondTD"><select name="document_file" class="form-control" id="document_file">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->document_file == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->document_file == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td style="width: 7%;">Exit Interview</td>
                    <td class="secondTD"><select name="exit_interview" class="form-control" id="exit_interview">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->exit_interview == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->exit_interview == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td style="width: 7%;">Loan /Advance</td>
                    <td class="secondTD"><select name="advance" class="form-control" id="advance">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->advance == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->advance == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td style="width: 7%;">Laptop</td>
                    <td class="secondTD"><select name="laptop" class="form-control" id="laptop">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->laptop == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->laptop == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td style="width: 7%;">SIM Card</td>
                    <td class="secondTD"><select name="sim_card" class="form-control" id="sim_card">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->sim_card == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->sim_card == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                  </tr>
                  <tr>
                    <td>Office/Drawer Keys</td>
                    <td class="secondTD"><select name="keys" class="form-control" id="keys">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->keys == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->keys == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>VISITING CARD</td>
                    <td class="secondTD"><select name="visiting_card" class="form-control" id="visiting_card">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->visiting_card == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->visiting_card == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>Income Tax</td>
                    <td class="secondTD"><select name="income_tax" class="form-control" id="income_tax">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->income_tax == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->income_tax == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>LAPTOP BAG</td>
                    <td class="secondTD"><select name="laptop_bag" class="form-control" id="laptop_bag">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->laptop_bag == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->laptop_bag == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td></td>
                    <td class="secondTD"></td>
                  </tr>
                  <tr>
                    <td>Expense Voucher pending if any</td>
                    <td class="secondTD"><select name="expense_voucher" class="form-control" id="expense_voucher">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->expense_voucher == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->expense_voucher == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>Matrix Id & CRM Id</td>
                    <td class="secondTD"><select name="crm_id" class="form-control" id="crm_id">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->crm_id == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->crm_id == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>Unpaid Salary</td>
                    <td class="secondTD"><select name="unpaid_salary" class="form-control" id="unpaid_salary">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->unpaid_salary == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->unpaid_salary == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>Company Data/ Email Deleted or not</td>
                    <td class="secondTD"><select name="data_email" class="form-control" id="data_email">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->data_email == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->data_email == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td></td>
                    <td class="secondTD"></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td class="secondTD"></td>
                    <td>ID CARD</td>
                    <td class="secondTD"><select name="id_card" class="form-control" id="id_card">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->id_card == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->id_card == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>Payable Expense</td>
                    <td class="secondTD"><select name="payable_expense" class="form-control" id="payable_expense">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->payable_expense == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->payable_expense == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td>DATA CARD/PEN DRIVE</td>
                    <td class="secondTD"><select name="pen_drive" class="form-control" id="pen_drive">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->pen_drive == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->pen_drive == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td></td>
                    <td class="secondTD"></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td class="secondTD"></td>
                    <td></td>
                    <td class="secondTD"></td>
                    <td>Bonus</td>
                    <td class="secondTD"><select name="bouns" class="form-control" id="bouns">
                        <option value="">Select</option>
                        <option value="Yes" {{ $resignation->check_list?->bouns == 'Yes' ? 'selected' : '' }}>Yes</option>
                        <option value="No" {{ $resignation->check_list?->bouns == 'No' ? 'selected' : '' }}>No</option>
                      </select></td>
                    <td></td>
                    <td class="secondTD"></td>
                    <td></td>
                    <td class="secondTD"></td>
                  </tr>
                </tbody>
              </table>
              @if(auth()->user()->can('resignation_check_list_update'))
              <button class="btn btn-primary mt-3 float-right" id="update_checklist" type="button">Update Check List</button>
              @endif
            </div>
            @endif
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>


      <!-- for checked -->
      <script type="text/javascript">
        var token = $("meta[name='csrf-token']").attr("content");

        function chnageStatus(status) {
          var id = $('#resignation_id').val();
          Swal.fire({
            title: 'Enter your remark',
            input: 'text',
            inputPlaceholder: 'Remark',
            showCancelButton: true,
            inputValidator: (value) => {
              if (!value) {
                return 'You need to write something!';
              }
            }
          }).then((result) => {
            console.log(result);
            if (result.value) {
              $.ajax({
                url: "{{ url('resignation_status_change') }}",
                type: 'POST',
                data: {
                  _token: token,
                  id: id,
                  status: status,
                  remark: result.value
                },
                success: function(data) {
                  if (data.status == 'success') {
                    $('.message').empty();
                    $('.alert').show();
                    $('.alert').addClass("alert-success");
                    $('.message').append(data.message);
                    setTimeout(function() {
                      location.reload();
                    }, 700);
                  } else {
                    $('.message').empty();
                    $('.alert').show();
                    $('.alert').addClass("alert-danger");
                    $('.message').append(data.message);
                  }
                }
              })
            }
          });
        }

        $('#update_checklist').on('click', function() {
          Swal.fire({
            title: "Are you sure?",
            text: "You want to update the checklist!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, I am sure!",
            cancelButtonText: "No, cancel it!",
            dangerMode: true,
          }).then((result) => {
            if (result.value) {
              const pageData = {};
              $("input, select").each(function() {
                const name = $(this).attr("name");
                if (name) {
                  pageData[name] = $(this).val();
                }
              });

              $.ajax({
                url: "{{ url('update_checklist') }}",
                type: 'POST',
                data: {
                  _token: token,
                  data: pageData
                },
                success: function(data) {
                  if (data.status) {
                    Swal.fire('success', data.message);
                  }
                }
              });
            }
          });
        })

        $('#edit-last-date').on('click', function() {
          var id = $('#resignation_id').val();
          Swal.fire({
            title: 'Last Working Date',
            html: '<input type="date" id="working_date" class="swal2-input datepicker">',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            preConfirm: () => {
              const date = document.getElementById('working_date').value;
              if (!date) {
                return Promise.reject(new Error('You need to select a date!'));
              }
              return date;
            }
          }).then((result) => {
            if (result.value) {
              const selectedDate = result.value;
              $.ajax({
                url: "{{ url('resignation_last_working_date_change') }}",
                type: 'POST',
                data: {
                  _token: token,
                  id: id,
                  date: selectedDate
                },
                success: function(data) {
                  if (data.status == 'success') {
                    $('.message').empty();
                    $('.alert').show();
                    $('.alert').addClass("alert-success");
                    $('.message').append(data.message);
                    setTimeout(function() {
                      location.reload();
                    }, 700);
                  } else {
                    $('.message').empty();
                    $('.alert').show();
                    $('.alert').addClass("alert-danger");
                    $('.message').append(data.message);
                  }
                }
              })
            }
          }).catch((error) => {
            Swal.fire('Error', error.message, 'error');
          });
        });
      </script>
  </section>
  <!-- /.content -->
</x-app-layout>