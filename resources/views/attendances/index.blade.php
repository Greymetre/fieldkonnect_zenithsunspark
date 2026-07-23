<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
      <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">{!! trans('panel.attendance.title_singular') !!}{!! trans('panel.global.list') !!}
            <span class="pull-right">
              <div class="btn-group">
                <a href="{{ URL::to('attendance-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.customers.title') !!}"><i class="material-icons">cloud_download</i></a>
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
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.users') !!}</th>
                <th>{!! trans('panel.attendance.punchin_date') !!}</th>
                <th>{!! trans('panel.attendance.worked_time') !!}</th>
                <th>{!! trans('panel.attendance.punchin_time') !!}</th>
                <th>{!! trans('panel.attendance.punchin_address') !!}</th>
                <th>{!! trans('panel.attendance.punchin_image') !!}</th>
                <th>{!! trans('panel.attendance.punchout_date') !!}</th>
                <th>{!! trans('panel.attendance.punchout_time') !!}</th>
                <th>{!! trans('panel.attendance.punchin_longitude') !!}</th>
                <th>{!! trans('panel.attendance.punchin_latitude') !!}</th>
                <th>{!! trans('panel.attendance.punchout_latitude') !!}</th>
                <th>{!! trans('panel.attendance.punchout_longitude') !!}</th>
                <th>{!! trans('panel.attendance.punchout_address') !!}</th>
                <th>{!! trans('panel.attendance.punchout_image') !!}</th>
                <th>{!! trans('panel.global.created_at') !!}</th>
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
    oTable = $('#getattendance').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [0, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('attendances.index') }}",
        "columns": [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'users.name', name: 'users.name',"defaultContent": ''},
            {data: 'punchin_date', name: 'punchin_date',"defaultContent": ''},
            {data: 'worked_time', name: 'worked_time',"defaultContent": ''},
            {data: 'punchin_time', name: 'punchin_time',"defaultContent": ''},
            {data: 'punchin_address', name: 'punchin_address',"defaultContent": ''},
            {data: 'punchin', name: 'punchin',"defaultContent": '', orderable: false, searchable: false },
            {data: 'punchout_date', name: 'punchout_date',"defaultContent": ''},
            {data: 'punchout_time', name: 'punchout_time',"defaultContent": '21:00:00'},
            {data: 'punchin_longitude', name: 'punchin_longitude',"defaultContent": ''},
            {data: 'punchin_latitude', name: 'punchin_latitude',"defaultContent": ''},
            {data: 'punchout_latitude', name: 'punchout_latitude',"defaultContent": ''},
            {data: 'punchout_longitude', name: 'punchout_longitude',"defaultContent": ''},
            {data: 'punchout_address', name: 'punchout_address',"defaultContent": ''},
            {data: 'punchout', name: 'punchout',"defaultContent": '', orderable: false, searchable: false },
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
