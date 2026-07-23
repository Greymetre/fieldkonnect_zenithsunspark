<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.visitreport.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                    <div class="next-btn">
                  @if(auth()->user()->can(['visitreport_upload']))
                  <form action="{{ URL::to('visitreports-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.visitreport.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                
                  <a href="{{ URL::to('visitreports-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.visitreport.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @if(auth()->user()->can(['visitreport_template']))
                  <a href="{{ URL::to('visitreports-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.visitreport.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['visitreport_create']))
                  <a href="{{ route('visitreports.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.visitreport.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
          <table id="getvisitreport" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
             <!--  <th>{!! trans('panel.global.action') !!}</th> -->
              <th>{!! trans('panel.global.users') !!}</th>
              <th>{!! trans('panel.global.customers') !!}</th>
              <th>{!! trans('panel.visitreport.visit_type_id') !!}</th>
              <th>{!! trans('panel.visitreport.report_title') !!}</th>
              <th>{!! trans('panel.visitreport.description') !!}</th>
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
    oTable = $('#getvisitreport').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [0, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('visitreports.index') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            //{data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'users.name', name: 'users.name',"defaultContent": ''},
            {data: 'customers.name', name: 'customers.name',"defaultContent": ''},
            {data: 'visittypename.type_name', name: 'visittypename.type_name',"defaultContent": ''},
            {data: 'report_title', name: 'report_title',"defaultContent": ''},
            {data: 'description', name: 'description',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
