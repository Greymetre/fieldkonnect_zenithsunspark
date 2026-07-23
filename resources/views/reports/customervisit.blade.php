<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Customer Visit Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['checkin_download']))
                <form method="GET" action="{{ URL::to('checkin-download') }}">
                  <div class="d-flex flex-row">
                    <div class="p-2" style="width: 200px;">
                      <select name="branch_id" id="branch_id" class="form-control select2">
                        <option value="" disabled selected>Select Branch</option>
                        @foreach($branches as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="division_id" id="division_id" class="form-control select2">
                        <option value="" disabled selected>Select Division</option>
                        @foreach($divisions as $division)
                        <option value="{{$division->id}}">{{$division->division_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="user_id" id="user_id" class="form-control select2">
                        <option value="" disabled selected>Select User</option>
                        @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
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
            <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>No</th>
                <th>User ID</th>
                <th>User Name</th>
                <th>Chekin Date</th>
                <th>Checkin Time</th>
                <th>Checkin Out</th>
                <th>Total Visit Time</th>
                <th>Beat Name</th>
                <th>Customer Id</th>
                <th>Customer Name</th>
                <th>Customer Mobile</th>
                <th>District</th>
                <th>City</th>
                <th>Pin</th>
                <th>Address</th>
                <th>Order Qty SKU</th>
                <th>Order Value </th>
                <th>Unique SKU</th>
                <th>Unique Orders</th>
                <th>Visit Remark </th>
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
      oTable = $('#getattendance').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        //"dom": 'Bfrtip',
        "ajax": {
          'type': 'POST',
          'url': "{{ url('reports/customervisit') }}",
          'data': function(d) {
            d._token = token,
              d.user_id = $('#user_id').val(),
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
            data: 'user_id',
            name: 'user_id',
            "defaultContent": ''
          },
          {
            data: 'users.name',
            name: 'users.name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'checkin_date',
            name: 'checkin_date',
            "defaultContent": ''
          },
          {
            data: 'checkin_time',
            name: 'checkin_time',
            "defaultContent": ''
          },
          {
            data: 'checkout_time',
            name: 'checkout_time',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'visit_time',
            name: 'visit_time',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'beat_name',
            name: 'beat_name',
            "defaultContent": ''
          },
          {
            data: 'customer_id',
            name: 'customer_id',
            "defaultContent": ''
          },
          {
            data: 'customers.name',
            name: 'customers.name',
            "defaultContent": ''
          },
          {
            data: 'customers.mobile',
            name: 'customers.mobile',
            "defaultContent": ''
          },
          {
            data: 'district_name',
            name: 'district_name',
            "defaultContent": ''
          },
          {
            data: 'city_name',
            name: 'city_name',
            "defaultContent": ''
          },
          {
            data: 'pincode',
            name: 'pincode',
            "defaultContent": ''
          },
          {
            data: 'address',
            name: 'address',
            "defaultContent": ''
          },
          {
            data: 'ordersum',
            name: 'ordersum',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'uniquesku',
            name: 'uniquesku',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'uniqueorder',
            name: 'uniqueorder',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'remarks',
            name: 'remarks',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
        ]
      });
      $('#start_date').change(function() {
        oTable.draw();
      });
      $('#end_date').change(function() {
        oTable.draw();
      });
      $('#user_id').change(function() {
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