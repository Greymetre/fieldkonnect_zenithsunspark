<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">User Reporting
              <span class="pull-right">
                <div class="btn-group">
                  <a data-toggle="modal" data-target="#createreportings" class="btn btn-just-icon btn-theme create" title="Create User Reporting"><i class="material-icons">add_circle</i></a>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
        <div class="table-responsive">
          <table id="getreportings" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>Reporting</th>
              <th>Users</th>
              <th>{!! trans('panel.global.created_by') !!}</th>
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
<div class="modal fade bd-example-modal-lg" id="createreportings" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.reportings.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('reportings.store') }}" enctype="multipart/form-data" id="storeTeamData">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <label class="col-md-3 col-form-label">Reporting To<span class="text-danger"> *</span></label>
              <div class="col-md-9">
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control" name="userid" id="userid" style="width: 100%;" required>
                    @if(@isset($users))
                    @foreach($users as $user)
                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="row">
              <label class="col-md-3 col-form-label">Users<span class="text-danger"> *</span></label>
              <div class="col-md-9">
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" name="users[]" id="users" style="width: 100%;" multiple required>
                    <option value="">Select Users</option>
                    @if(@isset($users))
                    @foreach($users as $id => $user)
                    <option value="{!! $user['id'] !!}" {{ in_array($id, old('users', [])) ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer pull-right">
          <input type="hidden" name="id" id="reportings_id" />
          <button class="btn btn-info save"> Submit</button>
        </form>
        </div>
      </div>
    </div>
  </div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.hrms.js') }}"></script>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getreportings').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('reportings.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'reportinginfo.name', name: 'reportinginfo.name',"defaultContent": ''},
            {data: 'users', name: 'users',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $.ajax({
        url: base_url + '/reportings/'+id+'/edit',
       dataType:"json",
       success:function(data)
       {
        $('#userid').val(data.userid);
        $('#reportings_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createreportings').modal('show');
       }
      })
     });

    $('body').on('click', '.activeRecord', function () {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var reportings = '';
        if(active == 'Y')
        {
          reportings = 'Incative ?';
        }
        else
        {
           reportings = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want "+reportings)) {
           return false;
        }
        $.ajax({
            url: "{{ url('reportings-active') }}",
            type: 'POST',
            data: {_token: token,id: id,active:active},
            success: function (data) {
              $('.message').empty();
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
              table.draw();
            },
        });
    });
    
    $('.create').click(function () {
        $('#reportings_id').val('');
        $('#storeTeamData').trigger("reset");
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('reportings') }}"+'/'+id,
            type: 'DELETE',
            data: {_token: token,id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
              table.draw();
            },
        });
    });
     
    });
</script>
</x-app-layout>
