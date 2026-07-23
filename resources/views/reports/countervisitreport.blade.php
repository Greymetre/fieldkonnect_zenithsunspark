<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
      </div>
      
      <div class="card-body">
          <form method="GET" action="{{ URL::to('counterVisitReportDownload') }}">
            <div class="row">
              <div class="col-md-4">
                <h4 class="card-title ">Per day Counter Visit Report </h4>
              </div>
              <div class="col-md-4">
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                </div>
              </div>
              <div class="col-md-1">
                  <span class="pull-right">
                    <div class="btn-group">
                    <button class="btn btn-just-icon btn-theme" title="Checkin Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </span>
              </div>
            </div>
          <form>
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
              <th>Mobile</th>
              <th>Location</th>
              <th>Non Field Working Days</th>
              <th>Field Working Days</th>
              <th>New Visit Counters</th>
              <th>Revisited Counters</th>
              <th>Visits Per day</th>
              <th>Cumulative Non Field Working Days</th>
              <th>Cumulative Field Working Days</th>
              <th>Cumulative New Visit Counters</th>
              <th>Cumulative Revisited Counters</th>
              <th>Cumulative Visits Per day</th>
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
          url: "{{ url('reports/per_day_counter_visit_report') }}",
          data: function (d) {
                d.start_date = $('#start_date').val(),
                d.end_date = $('#end_date').val()
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'id', name: 'id',"defaultContent": ''},
            {data: 'name', name: 'name',"defaultContent": ''},
            {data: 'mobile', name: 'mobile',"defaultContent": ''},
            {data: 'location', name: 'location',"defaultContent": ''},
            {data: 'between_non_field_working_days', name: 'between_non_field_working_days',"defaultContent": ''},
            {data: 'between_field_working_days', name: 'between_field_working_days',"defaultContent": ''},
            {data: 'between_new_visit_counters', name: 'between_new_visit_counters',"defaultContent": ''},
            {data: 'between_revisited_counters', name: 'between_revisited_counters',"defaultContent": ''},
            {data: 'between_visits_per_day', name: 'between_visits_per_day',"defaultContent": ''},
            {data: 'non_field_working_days', name: 'non_field_working_days',"defaultContent": ''},
            {data: 'field_working_days', name: 'field_working_days',"defaultContent": ''},
            {data: 'new_visit_counters', name: 'new_visit_counters',"defaultContent": ''},
            {data: 'revisited_counters', name: 'revisited_counters',"defaultContent": ''},
            {data: 'visits_per_day', name: 'visits_per_day',"defaultContent": ''},
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
