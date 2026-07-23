<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.beat.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                  <div class="next-btn">
                  @if(auth()->user()->can(['beat_upload']))
                  <form action="{{ URL::to('beats-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.beat.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                  
                  @if(auth()->user()->can(['beat_download']))
                  <a href="{{ URL::to('beats-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.beat.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['beat_template']))
                  <a href="{{ URL::to('beats-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.beat.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['beat_create']))
                  <a href="{{ route('beats.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.beat.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
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
          <table id="getbeat" class="table">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.beat.beat_name') !!}</th>
              <th>{!! trans('panel.beat.description') !!}</th>
              <th>{!! trans('panel.global.city') !!}</th>
              <th>{!! trans('panel.global.district') !!}</th>
              <th>{!! trans('panel.global.state') !!}</th>
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
        //"dom": 'Bfrtip',
        "ajax": "{{ route('beats.index') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false},
            {data: 'beat_name', name: 'beat_name',"defaultContent": '', orderable: false},
            {data: 'description', name: 'description',"defaultContent": '', orderable: false},
            {data: 'city_name', name: 'city_name',"defaultContent": '', orderable: false, searchable: false},
            {data: 'district_name', name: 'district_name',"defaultContent": '', orderable: false, searchable: false},
            {data: 'statename.state_name', name: 'statename.state_name',"defaultContent": '', orderable: false, searchable: false},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": '', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at',"defaultContent": '', orderable: false, searchable: false},
        ]
    });
});
</script>
</x-app-layout>
