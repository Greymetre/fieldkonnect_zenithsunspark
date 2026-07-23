<x-app-layout>
<style type="text/css">
  .table-responsive th{
    white-space: nowrap !important;
  }
  #gettasks td {
    text-align: left;
  }
  /* Apply wider width to the description column */
  td.description-column, th.description-column {
      max-width: 400px;   /* You can increase this value as needed */
      min-width: 300px;
      white-space: normal; /* Allow text wrapping */
      word-wrap: break-word;
      text-align: left;
  }
/*  #gettasks thead{
    display: none !important;
  } */

</style>
<div class="row">
   <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.task.title_singular') !!} {!! trans('panel.global.list') !!}
              <span class="">
                <button class="btn btn-info mb-3 float-right" type="button" data-toggle="collapse" data-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
                  <i class="material-icons">tune</i> Filters
                </button>
                <!-- fiilters start -->
                <div class="collapse" id="filterSection">
                <form method="GET" action="{{ URL::to('customers-download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="user_id" id="user_id" data-style="select-with-transition" title="Select User">
                        <option value="">Select Assigned User</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'user_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    
                    
                    
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="status" id="status" data-style="select-with-transition" title="Status">
                        <option value="">Select Status</option>
                        @if(@isset($statuses ))
                          @foreach($statuses as $status)
                          <option value="{!! $status!!}" {{ old( 'status') == $status ? 'selected' : '' }}>{!! $status !!}</option>
                          @endforeach
                        @endif
                        
                      </select>
                    </div>

                    <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                  </div>
                </form>
                
              </div>
                <!-- filters end -->
                <div class="btn-group header-frm-btn">
                  <div class="next-btn">
                  @if(auth()->user()->can(['tasks_upload']))
                 <!--  <form action="{{ URL::to('tasks-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.task.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form> -->
                  @endif
                  
                  <!-- @if(auth()->user()->can(['tasks_download']))
                  <a href="{{ URL::to('tasks-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.task.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['tasks_template']))
                  <a href="{{ URL::to('tasks-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.task.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['tasks_create']))
                  <a href="{{ route('tasks.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.task.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif -->
                  @if(auth()->user()->can(['tasks_download']))
                    <a href="{{ URL::to('tasks-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.task.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  <a href="{{ route('tasks.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.task.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                </div>
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
            <table id="gettasks" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                <thead class="text-rose">
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th >{!! trans('panel.global.action') !!}</th>
                  <th>{!! trans('panel.task.assigned_date') !!}</th>
                  <th>{!! trans('panel.task.assigned_to') !!}</th>
                  <th>{!! trans('panel.task.task_department') !!}</th>
                  <th>{!! trans('panel.task.task_type') !!}</th>
                  <th>{!! trans('panel.task.task_title') !!}</th>
                  <th>{!! trans('panel.task.priority') !!}</th>
                  <th>{!! trans('panel.task.status_id') !!}</th>
                  <th>{!! trans('panel.task.assigned_by') !!}</th>
                  <th>{!! trans('panel.task.descriptions') !!}</th>
                  <th>{!! trans('panel.task.due_datetime') !!}</th>
                  <th>{!! trans('panel.task.close_datetime') !!}</th>
                  <th>{!! trans('panel.task.start_datetime') !!}</th> 
                  <th>Comment 1 </th>
                  <th>Comment 2 </th>
                  <th>Comment 3 </th>
                  <th>Comment 4 </th>
                  <th>Comment 5 </th>

                </tr>
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
<div class="modal fade bd-example-modal-lg" id="showTaskData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.show') !!}</span> {!! trans('panel.task.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body all_text">
          <h4 class="title"></h4>
          <p class="datetime"></p>
          <div class="descriptions"></div>
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.tasks.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#user_id').select2();
    oTable = $('#gettasks').DataTable({
        processing: true,
        "serverSide": true,
        "order": [ [2, 'desc'] ],
        "responsive": false,
        "scrollX": true,
        //"dom": 'Bfrtip',
        ajax: {
          url: "{{ route('tasks.index') }}",
          data: function(d) {
            d.user_id = $('#user_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val(),
              d.status = $('#status').val()
          }
        },
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
            {data: 'assigned_users', name: 'assigned_users',"defaultContent": ''},
            {data: 'task_department.name', name: 'task_department.name',"defaultContent": ''},
            {data: 'task_type', name: 'task_type',"defaultContent": ''},
            {data: 'title', name: 'title',"defaultContent": ''},
            {data: 'task_priority.name', name: 'task_priority.name',"defaultContent": ''},
            {data: 'task_status', name: 'task_status',"defaultContent": ''},
            {data: 'users.name', name: 'users.name',"defaultContent": ''},
            {data: 'descriptions', name: 'descriptions',"defaultContent": ''},
            {data: 'due_datetime', name: 'due_datetime',"defaultContent": ''},
            {data: 'completed_at', name: 'completed_at',"defaultContent": ''},
            {data: 'open_datetime', name: 'open_datetime',"defaultContent": ''},
            {data: 'comment1', name: 'comment1',"defaultContent": ''},
            {data: 'comment2', name: 'comment2',"defaultContent": ''},
            {data: 'comment3', name: 'comment3',"defaultContent": ''},
            {data: 'comment4', name: 'comment4',"defaultContent": ''},
            {data: 'comment5', name: 'comment5',"defaultContent": ''},
            
        ]
    });
    $('#end_date').change(function() {
        oTable.draw();
      });
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
        $('#end_date').datepicker("option", "minDate", selectedStartDate);
        oTable.draw();
      });
      $('#status').change(function() {
        oTable.draw();
      });
      $('#user_id').change(function() {
        oTable.draw();
      });
    // $(document).on('click', '.show', function(){
    //   var base_url =$('.baseurl').data('baseurl');
    //   var id = $(this).attr('value');
    //   $.ajax({
    //     url: base_url + '/tasks/'+id,
    //    dataType:"json",
    //    success:function(data)
    //    {
    //     $('.task_type').html(data.task_type);
    //     $('.priority').html(data.priority_name);
    //     $('.title').html(data.title);
    //     $('.descriptions').html(data.descriptions);
    //     $('.datetime').html(data.datetime);
    //     $('.due_time').html(data.due_date +' '+data.due_day+' '+data.due_time);
    //     $('#showTaskData').modal('show');
    //    }
    //   })
    //  });
});
</script>
</x-app-layout>
