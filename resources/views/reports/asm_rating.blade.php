<x-app-layout>
  <style>
    #pmsForm table th {
      font-weight: bold !important;
      white-space: break-spaces;
      border: 2px solid #aba7a7 !important;
      background-color: #cdcbcba3;
    }

    #pmsForm table td {
      border: 2px solid #aba7a7 !important;
      white-space: break-spaces;
      padding: 5px !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Rating Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['asm_rating_download']))
                <form method="GET" action="{{ URL::to('asm_rating_report_download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width: 200px;">
                      <select name="role_id[]" multiple id="role_id" class="form-control select2" required>
                        <option value="" disabled>Role</option>
                        @foreach($roles as $role)
                        <option value="{{$role->id}}">{{$role->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="user_id" id="user_id" class="form-control select2">
                        <option value="" disabled selected>User</option>
                        <!-- @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach -->
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="designation_id" id="designation_id" class="form-control select2">
                        <option value="" disabled selected>Designations</option>
                        @foreach($designations as $designation)
                        <option value="{{$designation->id}}">{{$designation->designation_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="division_id" id="division_id" class="form-control select2" required>
                        <option value="" disabled selected>Divisions</option>
                        @foreach($divisions as $division)
                        <option value="{{$division->id}}">{{$division->division_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="branch_id" id="branch_id" class="form-control select2">
                        <option value="" disabled selected>Branch</option>
                        @foreach($branchs as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="financial_year" id="financial_year" class="form-control select2" required>
                        <option value="" disabled selected>Select Financial Year</option>
                        @foreach($FinancialYears as $FinancialYear)
                        <option value="{{$FinancialYear}}">{{$FinancialYear}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="month[]" multiple id="month" class="selectpicker" title="Select Month" placeholder="Select Month">
                        <option value="" disabled hidden>Select Month</option>
                        <option value="Apr">April</option>
                        <option value="May">May</option>
                        <option value="Jun">June</option>
                        <option value="Jul">July</option>
                        <option value="Aug">August</option>
                        <option value="Sep">September</option>
                        <option value="Oct">October</option>
                        <option value="Nov">November</option>
                        <option value="Dec">December</option>
                        <option value="Jan">January</option>
                        <option value="Feb">February</option>
                        <option value="Mar">March</option>
                      </select>
                    </div>
                    {{--<div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                    </div>--}}
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" value="simple" name="download" type="submit" title="Rating Download">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
                    @if(auth()->user()->can(['asm_rating_detailed_download']))
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" value="detailed" name="download" type="submit" title="Rating Details Download(PMS)">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
                    @endif
                    @if(auth()->user()->can(['pms_remark']))
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme edit_remark" type="buutton" title="Added Remark(PMS)">
                        <i class="material-icons">add_task</i>
                      </button>
                    </div>
                    @endif
                  </div>
                </form>
                @endif
              </div>
            </span>
          </h4>
        </div>
        <div class="card-body">
          @if(count($errors) > 0)
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              @foreach($errors->all() as $error)
              <li>{{$error}}</li>
              @endforeach
            </span>
          </div>
          @endif
          @if (session('info'))
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          @endif

          <div class="table-responsive">
            <table id="getfosrating" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>No</th>
                <th>Employees Code</th>
                <th>FOS Name</th>
                <th>Recommended CY Increment%</th>
                <th>Remark By Reporting Manager</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="pmsModal" tabindex="-1" aria-labelledby="pmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pmsModalLabel">PMS Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{url('/pms-form')}}" method="POST" id="pmsForm">
            @csrf
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>Name</th>
                  <td id="psname"></td>
                  <th>Branch</th>
                  <td id="psbranch"></td>
                  <th>Designation</th>
                  <td id="psdesignation"></td>
                </tr>
                <tr>
                  <th>Final Rating</th>
                  <td id="psrating"></td>
                  <th>Company Tenure (in Months)</th>
                  <td id="pscompany_tenure"></td>
                  <th>Gross Salary</th>
                  <td id="psgross_salary"></td>
                </tr>
                <tr>
                  <th>Last Year Gross Increment Value</th>
                  <td id="pslast_year_inc_value"></td>
                  <th>Last Year Increment %</th>
                  <td id="pslast_year_inc_per"></td>
                  <th>Total Target</th>
                  <td id="pstarget"></td>
                </tr>
                <tr>
                  <th>Total Sales</th>
                  <td id="pssale"></td>
                  <th>Percentage Achievement</th>
                  <td id="pssale_per"></td>
                  <th>Recommended CY Increment %</th>
                  <td><input type="number" class="form-control" name="recommended_increment" value="" required></td>
                </tr>
                <tr>
                  <th>Recommended Designation</th>
                  <td>
                    <select name="designation_id" id="designation_id" class="form-control select2" required>
                      <option value="" disabled selected>Designations</option>
                      @foreach($designations as $designation)
                      <option value="{{$designation->id}}">{{$designation->designation_name}}</option>
                      @endforeach
                    </select>
                  </td>
                  <th>Remark</th>
                  <td colspan="3">
                    <textarea class="form-control" rows="10" name="remarks" placeholder="Enter remarks" required></textarea>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="text-center">
              <input type="hidden" name="user_id" id="pms_user_id">
              <input type="hidden" name="fyear" id="pms_fyear">
              <button type="submit" class="btn btn-success btn-lg">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      var token = $("meta[name='csrf-token']").attr("content");
      oTable = $('#getfosrating').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'type': 'POST',
          'url': "{{ url('reports/asm_rating') }}",
          'data': function(d) {
            d._token = token,
              d.user_id = $('#user_id').val(),
              d.designation_id = $('#designation_id').val(),
              d.division_id = $('#division_id').val(),
              d.branch_id = $('#branch_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
            d.month = $('#month').val()
          }
        },
        "columns": [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'employee_codes',
            name: 'employee_codes',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'name',
            name: 'name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'increment',
            name: 'increment',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'remark',
            name: 'remark',
            "defaultContent": '',
            orderable: false,
            searchable: false
          }
        ]
      });
      $('#start_date').change(function() {
        oTable.draw();
      }).trigger('change');
      $('#end_date').change(function() {
        oTable.draw();
      }).trigger('change');
      $('#user_id').change(function() {
        oTable.draw();
      });
      $('#designation_id').change(function() {
        oTable.draw();
      });
      $('#division_id').change(function() {
        oTable.draw();
      });
      $('#branch_id').change(function() {
        oTable.draw();
      });
      $('#month').change(function() {
        oTable.draw();
      });
    });

    $("#role_id").on("change", function() {
      var roles = $(this).val();
      $.ajax({
        url: "{{ url('getUserList') }}",
        dataType: "json",
        type: "POST",
        data: {
          _token: "{{csrf_token()}}",
          roles: roles
        },
        success: function(res) {
          var html = '<option value="">Select User</option>';
          $.each(res, function(k, v) {
            html += '<option value="' + v.id + '"> (' + v.employee_codes + ') ' + v.name + '</option>';
          });
          $("#user_id").html(html);
        }
      });
    }).trigger("chnage");

    $(document).on("click", ".edit_remark", function() {
      event.preventDefault();
      var role_id = $("#role_id").val();
      var user_id = $("#user_id").val();
      var division_id = $("#division_id").val();
      var financial_year = $("#financial_year").val();
      if (!role_id || role_id.length === 0 || !user_id || !division_id || !financial_year) {
        alert("Role, User, Division and Financial Year fields are required!");
      } else {
        $("#loader").show();
        $.ajax({
          url: "{{ url('getPMS') }}/",
          type: 'GET',
          dataType: 'json',
          data: {
            _token: "{{csrf_token()}}",
            role_id: role_id,
            user_id: user_id,
            division_id: division_id,
            financial_year: financial_year
          },
          success: function(res) {
            if (res.status == 'success') {
              $("#loader").hide();
              $("#psname").html(res.data.name);
              $("#psbranch").html(res.data.branch);
              $("#psdesignation").html(res.data.designation);
              $("#psrating").html(res.data.rating);
              $("#pscompany_tenure").html(res.data.company_tenure);
              $("#psgross_salary").html(res.data.gross_salary);
              $("#pslast_year_inc_value").html(res.data.last_year_inc_value);
              $("#pslast_year_inc_per").html(res.data.last_year_inc_per);
              $("#pstarget").html(res.data.target);
              $("#pssale").html(res.data.sale);
              $("#pms_user_id").val(user_id);
              $("#pms_fyear").val(financial_year);
              $("#pssale_per").html(res.data.sale_per + '%');

              $("#pmsModal").modal("show");
            } else {
              Swal.fire({
                icon: 'info',
                title: 'Heads up!',
                text: res.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
              });

            }
          },
          error: function(xhr, status, error) {
            $("#loader").hide();
            alert("An error occurred: " + error);
          },
          complete: function() {
            $("#loader").hide();
          }
        })
      }
    });
  </script>
</x-app-layout>