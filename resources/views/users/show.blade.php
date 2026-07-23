<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card card-profile">
      <div class="card-header">
        <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" role="tablist">
            <li class="nav-item">
              <a class="nav-link active show" data-toggle="tab" href="#link7" role="tablist">
                <i class="material-icons">preview</i> Details
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link orderinfo" data-toggle="tab" href="#link8" role="tablist">
                <i class="material-icons">add_shopping_cart</i> Orders
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link salesinfo" data-toggle="tab" href="#link9" role="tablist">
                <i class="material-icons">shopping_bag</i> Sales
              </a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link tasksinfo" data-toggle="tab" href="#link10" role="tablist">
                <i class="material-icons">add_task</i> Tasks
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link workinginfo" data-toggle="tab" href="#link11" role="tablist">
                <i class="fa fa-clock-o" aria-hidden="true"></i> Working
              </a>
            </li> -->
            <!-- <li class="nav-item">
              <a class="nav-link attendanceinfo" data-toggle="tab" href="#link12" role="tablist">
                <i class="fa fa-calendar" aria-hidden="true"></i> Attendance
              </a>
            </li> -->
          </ul>
      </div>
      <div class="card-body">
        <div class="tab-content tab-subcategories">
          <div class="tab-pane active show" id="link7">
            <br>
            <div class="card-avatar">
              <a href="#pablo">
                <img class="img imageDisplayModel" src="{!! !empty($user['profile_image']) ? asset($user['profile_image']) : asset('assets/img/placeholder.jpg') !!}">
              </a>
            </div>
            <h6 class="card-category text-gray">{!! $user->roles->pluck('name')->first() !!}</h6>
            <h4 class="card-title">{!! $user['name'] !!}</h4>
            <div class="table-responsive">
              <table class="table table-striped text-left">
                <tbody>
                  <tr>
                  <td>{!! trans('panel.global.name') !!}</td>
                  <td>
                    {!! $user['name'] !!}
                  </td>
                </tr>
                <tr>
                  <td>{!! trans('panel.global.first_name') !!}</td>
                  <td>
                    {!! $user['first_name'] !!}
                  </td>
                </tr>
                <tr>
                  <td>{!! trans('panel.global.last_name') !!}</td>
                  <td>
                    {!! $user['last_name'] !!}
                  </td>
                </tr>
                <tr>
                  <td>{!! trans('panel.global.email') !!}</td>
                  <td>
                    {!! $user['email'] !!}
                  </td>
                </tr>
                <tr>
                  <td>{!! trans('panel.global.mobile') !!}</td>
                  <td>
                    {!! $user['mobile'] !!}
                  </td>
                </tr>
                  <tr>
                    <td>{!! trans('panel.global.gender') !!}</td>
                    <td>
                      {!! isset($user['gender']) ? $user['gender'] :'' !!}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="link8">
              <div class="table-responsive">
                <table id="getorder" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                <thead class=" text-primary">
                  <th>{!! trans('panel.global.action') !!}</th>
                  <th>{!! trans('panel.global.buyer') !!}</th>
                  <th>{!! trans('panel.global.seller') !!}</th>
                  <th>{!! trans('panel.order.orderno') !!}</th>
                  <th>{!! trans('panel.order.order_date') !!}</th>
                  <th>{!! trans('panel.order.grand_total') !!}</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="link9">
            <div class="table-responsive">
                <table id="getsales" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                <thead class=" text-primary">
                  <th>{!! trans('panel.global.action') !!}</th>
                  <th>{!! trans('panel.global.buyer') !!}</th>
                  <th>{!! trans('panel.global.seller') !!}</th>
                  <th>{!! trans('panel.sale.fields.invoice_no') !!}</th>
                  <th>{!! trans('panel.sale.fields.invoice_date') !!}</th>
                  <th>{!! trans('panel.sale.fields.grand_total') !!}</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="link10">
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">
          Today Due Tasks</h4> 
            <div class="row">
              <div class="table-responsive">
                <table id="todayTasks" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                  <thead class=" text-primary">
                    <th>{!! trans('panel.global.no') !!}</th>
                    <th>{!! trans('panel.task.task_title') !!}</th>
                    <th>{!! trans('panel.task.start_date') !!}</th>
                    <th>{!! trans('panel.task.due_date') !!}</th>
                    <th>{!! trans('panel.task.status_id') !!}</th>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">
          This Week Due Tasks</h4> 
            <div class="row">
              <div class="table-responsive">
                <table id="weekTasks" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                  <thead class=" text-primary">
                    <th>{!! trans('panel.global.no') !!}</th>
                    <th>{!! trans('panel.task.task_title') !!}</th>
                    <th>{!! trans('panel.task.start_date') !!}</th>
                    <th>{!! trans('panel.task.due_date') !!}</th>
                    <th>{!! trans('panel.task.status_id') !!}</th>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">
          Over Due Tasks</h4> 
            <div class="row">
              <div class="table-responsive">
                <table id="overdueTasks" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                  <thead class=" text-primary">
                    <th>{!! trans('panel.global.no') !!}</th>
                    <th>{!! trans('panel.task.task_title') !!}</th>
                    <th>{!! trans('panel.task.start_date') !!}</th>
                    <th>{!! trans('panel.task.due_date') !!}</th>
                    <th>{!! trans('panel.task.status_id') !!}</th>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">
          Completed Tasks</h4> 
            <div class="row">
              <div class="table-responsive">
                <table id="pastTasks" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                  <thead class=" text-primary">
                    <th>{!! trans('panel.global.no') !!}</th>
                    <th>{!! trans('panel.task.task_title') !!}</th>
                    <th>{!! trans('panel.task.start_date') !!}</th>
                    <th>{!! trans('panel.task.due_date') !!}</th>
                    <th>{!! trans('panel.task.status_id') !!}</th>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="link11">
            <div class="table-responsive">
              <table id="getworkingInfo" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                <thead class=" text-primary">
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th>{!! trans('panel.attendance.punchin_date') !!}</th>
                  <th>{!! trans('panel.attendance.start_time') !!}</th>
                  <th>{!! trans('panel.attendance.end_time') !!}</th>
                  <th>{!! trans('panel.attendance.worked_time') !!}</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="tab-pane" id="link12">
              <div class="table-responsive">
              <table id="getAttendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                <thead class=" text-primary">
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th>{!! trans('panel.attendance.punchin_date') !!}</th>
                  <th>{!! trans('panel.attendance.punchin_time') !!}</th>
                  <th>{!! trans('panel.attendance.punchout_time') !!}</th>
                  <th>{!! trans('panel.attendance.worked_time') !!}</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
          <div class="table-responsive">
            <table class="table table-striped text-left">
              <tbody>
                
              </tbody>
            </table>
          </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var orderTable = $('#getorder').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('orders.info') }}",
          data: function (d) {
                d.created_by = "{!! $user['id'] !!}"
            }
        },
        columns: [
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'buyers.name', name: 'buyers.name',"defaultContent": ''},
            {data: 'sellers.name', name: 'sellers.name',"defaultContent": ''},
            {data: 'orderno', name: 'orderno',"defaultContent": ''},
            {data: 'order_date', name: 'order_date',"defaultContent": ''},
            {data: 'grand_total', name: 'grand_total',"defaultContent": ''},
        ]
    });
    var salesTable = $('#getsales').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('sales.info') }}",
          data: function (d) {
                d.created_by = "{!! $user['id'] !!}"
            }
        },
        columns: [
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'buyers.name', name: 'buyers.name',"defaultContent": ''},
            {data: 'sellers.name', name: 'sellers.name',"defaultContent": ''},
            {data: 'invoice_no', name: 'invoice_no',"defaultContent": ''},
            {data: 'invoice_date', name: 'invoice_date',"defaultContent": ''},
            {data: 'grand_total', name: 'grand_total',"defaultContent": ''},
        ]
    });
    var todayTasks = $('#todayTasks').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('tasks.info') }}",
          data: function (d) {
                d.user_id = "{!! $user['id'] !!}",
                d.due_at = "Today"
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'task_title', name: 'task_title',"defaultContent": ''},
            {data: 'start_date', name: 'start_date',"defaultContent": ''},
            {data: 'due_date', name: 'due_date',"defaultContent": ''},
            {data: 'status_id', name: 'status_id',"defaultContent": ''},
        ]
    });
    var weekTasks = $('#weekTasks').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('tasks.info') }}",
          data: function (d) {
                d.user_id = "{!! $user['id'] !!}",
                d.due_at = "Week"
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'task_title', name: 'task_title',"defaultContent": ''},
            {data: 'start_date', name: 'start_date',"defaultContent": ''},
            {data: 'due_date', name: 'due_date',"defaultContent": ''},
            {data: 'status_id', name: 'status_id',"defaultContent": ''},
        ]
    });
    var overdueTasks = $('#overdueTasks').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('tasks.info') }}",
          data: function (d) {
                d.user_id = "{!! $user['id'] !!}",
                d.due_at = "overdue"
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'task_title', name: 'task_title',"defaultContent": ''},
            {data: 'start_date', name: 'start_date',"defaultContent": ''},
            {data: 'due_date', name: 'due_date',"defaultContent": ''},
            {data: 'status_id', name: 'status_id',"defaultContent": ''},
        ]
    });
    var pastTasks = $('#oberTasks').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('tasks.info') }}",
          data: function (d) {
                d.user_id = "{!! $user['id'] !!}",
                d.due_at = "Completed"
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'task_title', name: 'task_title',"defaultContent": ''},
            {data: 'start_date', name: 'start_date',"defaultContent": ''},
            {data: 'due_date', name: 'due_date',"defaultContent": ''},
            {data: 'status_id', name: 'status_id',"defaultContent": ''},
        ]
    });

    var workingTable = $('#getworkingInfo').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('attendances.working') }}",
          data: function (d) {
                d.user_id = "{!! $user['id'] !!}"
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'date', name: 'date',"defaultContent": ''},
            {data: 'start_time', name: 'start_time',"defaultContent": ''},
            {data: 'end_time', name: 'end_time',"defaultContent": ''},
            {data: 'worked_time', name: 'worked_time',"defaultContent": ''},
        ]
    });

    var attendanceTable = $('#getAttendance').DataTable({
        "processing": true,
        "serverSide": true,
        "pageLength": 5,
        "searching": false,
        "ordering": false,
        "bLengthChange": false,
        "retrieve": true,
        ajax: {
          url: "{{ route('attendances.info') }}",
          data: function (d) {
                d.user_id = "{!! $user['id'] !!}"
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'punchin_date', name: 'punchin_date',"defaultContent": ''},
            {data: 'punchin_time', name: 'punchin_time',"defaultContent": ''},
            {data: 'punchout_time', name: 'punchout_time',"defaultContent": ''},
            {data: 'worked_time', name: 'worked_time',"defaultContent": ''},
        ]
    });
    $('.orderinfo').change(function(){
        orderTable.draw();
    });
    $('.salesinfo').change(function(){
        salesTable.draw();
    });
    $('.attendanceinfo').change(function(){
        attendanceTable.draw();
    });
    $('.workinginfo').change(function(){
        workingTable.draw();
    });
  });
</script>
</x-app-layout>
