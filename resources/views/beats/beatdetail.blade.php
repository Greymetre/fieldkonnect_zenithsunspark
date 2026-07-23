<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.beatdetail.title_singular') !!}{!! trans('panel.global.list') !!}
          </h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="getbeat" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.beat.beat_name') !!}</th>
              <th>{!! trans('panel.beat.beat_date') !!}</th>
              <th>{!! trans('panel.global.customer') !!}</th>
              <th>{!! trans('panel.beat.user_name') !!}</th>
              <th>{!! trans('panel.global.mobile') !!}</th>
              <th>{!! trans('panel.global.created_by') !!}</th>
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
    oTable = $('#getbeat').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [0, 'desc'] ],
        "dom": 'Bfrtip',
        "pageLength": 200,
        "ajax": "{{ route('beats.beatdetail') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'beats.beat_name', name: 'beats.beat_name',"defaultContent": ''},
            {data: 'beat_date', name: 'beat_date',"defaultContent": ''},
            {data: 'customers', name: 'customers',"defaultContent": ''},
            {data: 'users.name', name: 'users.name',"defaultContent": ''},
            {data: 'users.mobile', name: 'users.mobile',"defaultContent": ''},
            {data: 'beats.createdbyname.name', name: 'beats.createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
