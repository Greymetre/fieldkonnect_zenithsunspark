<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">Beat Adherence Summary Report
              <span class="pull-right">
                <div class="btn-group">
                
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
        <form method="GET" action="{{ URL::to('counterVisitReportDownload') }}">
            <div class="row">
              <div class="col-md-4">
              </div>
              <div class="col-md-4">
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                </div>
              </div>
            </div>
          <form>
        <div class="table-responsive">
          <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>No</th>
              <th>User ID</th>
              <th>User Name</th>
              <th>Total Counter  Beat</th>
              <th>Total Visited Counter </th>
              <th>Beat Adherance %</th>
              <th>Total Order Counter</th>
              <th>Beat Productivity %</th>
              <th>New Counter Add</th>
              <th>Total Qty</th>
              <th>Order Value</th>
              <!-- <th>Per Day Sale</th> -->
              <th>Total Cumulative Counter</th>
              <!-- <th>TLSD</th> -->
              <th>Unique SKU Count</th>
              <th>Active Counter</th>
              <th>Inactive Counter</th>
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
        "pageLength": 100,
        lengthMenu: [[100, 200, 500,1000], [100, 200, 500,1000]],
        dom: 'Bfrtip',             
        buttons: ['pageLength',{
                extend: 'excel',
            },],
        "retrieve": true,
        ajax: {
          url: "{{ url('reports/adherencesummary') }}",
          data: function (d) {
                d.start_date = $('#start_date').val(),
                d.end_date = $('#end_date').val()
            }
        },
        columns: [
          {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'id', name: 'id',"defaultContent": ''},
            {data: 'name', name: 'name',"defaultContent": ''},
            {data: 'total_beat_counter', name: 'total_beat_counter',"defaultContent": ''},
            {data: 'total_visited_counter', name: 'total_visited_counter',"defaultContent": ''},
            {data: 'beat_adherence', name: 'beat_adherence',"defaultContent": ''},
            {data: 'total_order_counter', name: 'total_order_counter',"defaultContent": ''},
            {data: 'beat_productivity', name: 'beat_productivity',"defaultContent": ''},
            {data: 'new_counter_added', name: 'new_counter_added',"defaultContent": ''},
            {data: 'total_order_qty', name: 'total_order_qty',"defaultContent": ''},
            {data: 'total_order_value', name: 'total_order_value',"defaultContent": ''},
            // {data: 'pincode', name: 'pincode',"defaultContent": ''},
            {data: 'total_assign_counter', name: 'total_assign_counter',"defaultContent": ''},
            {data: 'unique_sku_count', name: 'unique_sku_count',"defaultContent": ''},
            {data: 'active_counter', name: 'active_counter',"defaultContent": ''},
            {data: 'inactive_counter', name: 'inactive_counter',"defaultContent": ''},
        ]
    });
  $(document).ready(function(){
    table.draw();
  });
    $('#start_date').change(function(){
        table.draw();
    });
    $('#end_date').change(function(){
        table.draw();
    });
</script>
</x-app-layout>
