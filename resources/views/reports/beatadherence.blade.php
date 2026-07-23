<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Beat Adherence Details Report
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="GET" action="{{ URL::to('beatAdherenceDetailDownload') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2">
                      <div class="form-group has-default bmd-form-group">
                        <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                      </div>
                    </div>
                    <div class="p-2">
                      <div class="form-group has-default bmd-form-group">
                        <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                      </div>
                    </div>
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" title="Checkin Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </div>
                  <form>
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
                <th>Beat Date</th>
                <th>Beat Name</th>
                <th>Total Counter Beat</th>
                <th>Total Visited Counter </th>
                <th>Beat Adherance %</th>
                <th>Total Order Counter</th>
                <th>Beat Productivity %</th>
                <th>New Counter Add</th>
                <th>Order Qty</th>
                <th>Unique SKU Count</th>
                <th>Order Value</th>
                <th>Daily Avarage Sales</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
  <script type="text/javascript">
    var table = $('#getattendance').DataTable({
      'destroy': true,
      processing: true,
      serverSide: true,
      lengthChange: true,
      responsive: true,
      "retrieve": true,
      ajax: {
        url: "{{ url('reports/beatadherence') }}",
        data: function(d) {
          d.start_date = $('#start_date').val(),
            d.end_date = $('#end_date').val()
        }
      },
      columns: [{
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
          "defaultContent": ''
        },
        {
          data: 'beat_date',
          name: 'beat_date',
          "defaultContent": ''
        },
        {
          data: 'beats.beat_name',
          name: 'beats.beat_name',
          "defaultContent": ''
        },
        {
          data: 'beatCounters',
          name: 'beatCounters',
          "defaultContent": ''
        },
        {
          data: 'visitedcounter',
          name: 'visitedcounter',
          "defaultContent": ''
        },
        {
          data: 'beatadherence',
          name: 'beatadherence',
          "defaultContent": ''
        },
        {
          data: 'totalorder',
          name: 'totalorder',
          "defaultContent": ''
        },
        {
          data: 'beatproductivity',
          name: 'beatproductivity',
          "defaultContent": ''
        },
        {
          data: 'newcounter',
          name: 'newcounter',
          "defaultContent": ''
        },
        {
          data: 'orderqty',
          name: 'orderqty',
          "defaultContent": ''
        },
        {
          data: 'uniqueskucount',
          name: 'uniqueskucount',
          "defaultContent": ''
        },
        {
          data: 'ordervalue',
          name: 'ordervalue',
          "defaultContent": ''
        },
        {
          data: 'dailyAvarageSales',
          name: 'dailyAvarageSales',
          "defaultContent": ''
        },
      ]
    });
    $(document).ready(function() {
      table.draw();
    });
    $('#start_date').change(function() {
      table.draw();
    });
    $('#end_date').change(function() {
      table.draw();
    });
  </script>
</x-app-layout>