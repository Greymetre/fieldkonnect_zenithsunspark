<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">CH/BM Rating Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['checkin_download']))
                <form method="GET" action="{{ URL::to('ch_rating_report_download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width: 200px;">
                      <select name="user_id" id="user_id" class="form-control select2">
                        <option value="" disabled selected>User</option>
                        @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    {{--<div class="p-2" style="width: 200px;">
                      <select name="designation_id" id="designation_id" class="form-control select2">
                        <option value="" disabled selected>Designations</option>
                        @foreach($designations as $designation)
                        <option value="{{$designation->id}}">{{$designation->designation_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="division_id" id="division_id" class="form-control select2">
                        <option value="" disabled selected>Divisions</option>
                        @foreach($divisions as $division)
                        <option value="{{$division->id}}">{{$division->division_name}}</option>
                        @endforeach
                      </select>
                    </div>--}}
                    <div class="p-2" style="width: 200px;">
                      <select name="branch_id" id="branch_id" class="form-control select2">
                        <option value="" disabled selected>Branch</option>
                        @foreach($branchs as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="financial_year" id="financial_year" class="form-control select2">
                        <option value="" disabled selected>Select Financial Year</option>
                        @foreach($FinancialYears as $FinancialYear)
                        <option value="{{$FinancialYear}}">{{$FinancialYear}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="month[]" multiple id="month" class="form-control select2" title="Select Month" placeholder="Select Month">
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
                      <button class="btn btn-just-icon btn-theme" title="Checkin Download">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
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
          <div class="table-responsive">
            {{-- <table id="getfosrating" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>No</th>
                <th>Employees Code</th>
                <th>FOS Name</th>
                <th>Area Of Operation (Districts Covered)</th>
                <th>Date of Appointment</th>
                <th>Yesterday Productivity vs Visit</th>
                <th>Order Value This Month (in Lacs After 35%)</th>
                <th>Total Order Value in Lacs After 35%</th>
                <th>Sale Index</th>
                <th>Registration Index</th>
                <th>Visit Index</th>
                <th>Activation Index</th>
                <th>Performance Rating</th>
              </thead>
              <tbody>
              </tbody>
            </table> --}}
          </div>
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
          'url': "{{ url('reports/fos_rating') }}",
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
            data: 'cities',
            name: 'cities',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'userinfo.date_of_joining',
            name: 'userinfo.date_of_joining',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'yesterday_productivity_visit',
            name: 'yesterday_productivity_visit',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'order_value_current_month',
            name: 'order_value_current_month',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'total_order_value',
            name: 'total_order_value',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'sale_index',
            name: 'sale_index',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'registration_index',
            name: 'registration_index',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'visit_index',
            name: 'visit_index',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'activation_index',
            name: 'activation_index',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'performance_rating',
            name: 'performance_rating',
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
  </script>
</x-app-layout>