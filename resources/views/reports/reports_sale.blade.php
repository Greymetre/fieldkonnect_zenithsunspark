<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">User Working Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['checkin_download']))
                <form method="GET" action="{{ URL::to('user_sales_report_download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width: 200px;">
                      <select name="user_id" id="user_id" class="form-control select2">
                        <option value="" disabled selected>User</option>
                        @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
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
                      <select name="division_id" id="division_id" class="form-control select2">
                        <option value="" disabled selected>Divisions</option>
                        @foreach($divisions as $division)
                        <option value="{{$division->id}}">{{$division->division_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="branch_id" id="branch_id" class="form-control select2">
                        <option value="" disabled selected>Branchs</option>
                        @foreach($branchs as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" value="{{ date('Y-m-01') }}" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" value="{{ date('Y-m-d') }}" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                    </div>
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
            <table id="getusereport" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>No</th>
                <th>Employees Code</th>
                <th>User Name</th>
                <th>Designation</th>
                <th>Branch Name</th>
                <th>Division</th>
                <th>Reporting Manager</th>
                <th>Field Working Days</th>
                <th>Other Days</th>
                <th>Total Days</th>
                <th>Distributor Visit Total</th>
                <th>Distributor Visit Unique</th>
                <th>Dealer Visit Total</th>
                <th>Dealer Visit Unique</th>
                <th>Retailer Visit Total</th>
                <th>Retailer Visit Unique</th>
                <th>Service Center Visit Total</th>
                <th>Service Center Visit Unique</th>
                <th>Influencer Visit Total</th>
                <th>Influencer Visit Unique</th>
                <th>Total Visit</th>
                <th>Total Visit Unique</th>
                <th>Distributor New Registration</th>
                <th>Dealer New Registration</th>
                <th>Retailer New Registration</th>
                <th>Service Center New Registration</th>
                <th>Influencer-New Registration</th>
                <th>Total New Registration</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      var token = $("meta[name='csrf-token']").attr("content");
      oTable = $('#getusereport').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        //"dom": 'Bfrtip',
        "ajax": {
          'type': 'POST',
          'url': "{{ url('reports/reports_sale') }}",
          'data': function(d) {
              d._token = token,
              d.user_id = $('#user_id').val(),
              d.designation_id = $('#designation_id').val(),
              d.division_id = $('#division_id').val(),
              d.branch_id = $('#branch_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
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
            data: 'getdesignation.designation_name',
            name: 'getdesignation.designation_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'getbranch.branch_name',
            name: 'getbranch.branch_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'getdivision.division_name',
            name: 'getdivision.division_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'reportinginfo.name',
            name: 'reportinginfo.name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'attendance_count',
            name: 'attendance_count',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'other_attendance_count',
            name: 'other_attendance_count',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'total_attendance_count',
            name: 'total_attendance_count',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'dis_visit_total',
            name: 'dis_visit_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'dis_visit_unique',
            name: 'dis_visit_unique',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'dil_visit_total',
            name: 'dil_visit_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'dil_visit_unique',
            name: 'dil_visit_unique',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'ret_visit_total',
            name: 'ret_visit_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'ret_visit_unique',
            name: 'ret_visit_unique',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'serv_visit_total',
            name: 'serv_visit_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'serv_visit_unique',
            name: 'serv_visit_unique',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'inf_visit_total',
            name: 'inf_visit_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'inf_visit_unique',
            name: 'inf_visit_unique',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'tot_visit_total',
            name: 'tot_visit_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'tot_visit_unique',
            name: 'tot_visit_unique',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'dis_registration',
            name: 'dis_registration',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'del_registration',
            name: 'del_registration',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'ret_registration',
            name: 'ret_registration',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'serv_registration',
            name: 'serv_registration',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'inf_registration',
            name: 'inf_registration',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'tot_registration',
            name: 'tot_registration',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
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
    });

  </script>
</x-app-layout>