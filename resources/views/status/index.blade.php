<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.status.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                     <div class="next-btn">
                  @if(auth()->user()->can(['status_upload']))
                  <form action="{{ URL::to('status-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.status.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
               
                  @if(auth()->user()->can(['status_download']))
                  <a href="{{ URL::to('status-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.status.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['status_template']))
                  <a href="{{ URL::to('status-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.status.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['status_create']))
                   <a data-toggle="modal" data-target="#createstatus" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.status.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
        <div class="table-responsive">
          <table id="getstatus" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.status.status_name') !!}</th>
              <th>{!! trans('panel.status.display_name') !!}</th>
              <th>{!! trans('panel.status.status_message') !!}</th>
              <th>{!! trans('panel.status.module') !!}</th>
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
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="createstatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.status.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('status.store') }}" enctype="multipart/form-data" id="createstatusForm">
        @csrf
        <div class="row">
          <div class="col-md-6">
            <div class="input_section">
              <label class="col-form-label">{!! trans('panel.status.status_name') !!} <span class="text-danger"> *</span></label>
           
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="status_name" id="status_name" class="form-control" value="{!! old( 'status_name') !!}" maxlength="200" required>
                  @if ($errors->has('status_name'))
                    <div class="error"><p class="text-danger">{{ $errors->first('status_name') }}</p></div>
                  @endif
          
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input_section">
              <label class="col-form-label">{!! trans('panel.status.display_name') !!} <span class="text-danger"> *</span></label>
              
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="display_name" id="display_name" class="form-control" value="{!! old( 'display_name') !!}" maxlength="200" required>
                  @if ($errors->has('display_name'))
                    <div class="error"><p class="text-danger">{{ $errors->first('display_name') }}</p></div>
                  @endif
              
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="input_section">
              <label class="col-form-label">{!! trans('panel.status.status_message') !!} <span class="text-danger"> *</span></label>
           
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="status_message" id="status_message" class="form-control" value="{!! old( 'status_message') !!}" maxlength="200" required>
                  @if ($errors->has('status_message'))
                    <div class="error"><p class="text-danger">{{ $errors->first('status_message') }}</p></div>
                  @endif
                </div>
             
            </div>
          </div>
          <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.status.module') !!}<span class="text-danger"> *</span></label>
            
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control" name="module" id="module" style="width: 100%;" required >
                       <option value="">Select {!! trans('panel.status.module') !!}</option>
                       <option value="Customer" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Customer' ? 'selected' : '' }}>Customer</option>
                       <option value="Order" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Order' ? 'selected' : '' }}>Order</option>
                       <option value="LeadStatus" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'LeadStatus' ? 'selected' : '' }}>Lead Status</option>
                      <option value="Payment Status" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Payment Status' ? 'selected' : '' }}>Payment Status</option>
                      <option value="Coupons" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Coupons' ? 'selected' : '' }}>Coupons</option>
                      <option value="Campaign Status" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Campaign Status' ? 'selected' : '' }}>Campaign Status</option>
                    </select>
                  </div>
                  @if ($errors->has('module'))
                   <div class="error">
                      <p class="text-danger">{{ $errors->first('module') }}</p>
                   </div>
                  @endif
                </div>
             
            </div>
          </div>
        <div class="clearfix"></div>
        <div class="pull-right">
          <input type="hidden" name="id" id="status_id" />
          <button class="btn btn-info save"> Submit</button>
        </form>
        </div>
      </div>
    </div>
  </div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getstatus').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('status.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'status_name', name: 'status_name',"defaultContent": ''},
            {data: 'display_name', name: 'display_name',"defaultContent": ''},
            {data: 'status_message', name: 'status_message',"defaultContent": ''},
            {data: 'module', name: 'module',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $.ajax({
        url: base_url + '/status/'+id+'/edit',
       dataType:"json",
       success:function(data)
       {
        $('#status_name').val(data.status_name);
        $('#display_name').val(data.display_name);
        $('#status_message').val(data.status_message);
        $("#module").append('<option value="'+data.module+'" selected="selected">'+data.module+'</option>');
        $('#status_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createstatus').modal('show');
       }
      })
     });

    $('body').on('click', '.activeRecord', function () {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if(active == 'Y')
        {
          status = 'Incative ?';
        }
        else
        {
           status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want "+status)) {
           return false;
        }
        $.ajax({
            url: "{{ url('status-active') }}",
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
        $('#status_id').val('');
        $('#createstatusForm').trigger("reset");
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('status') }}"+'/'+id,
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
