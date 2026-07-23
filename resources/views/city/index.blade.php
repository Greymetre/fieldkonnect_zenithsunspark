<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.city.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                   <div class="next-btn">
                  @if(auth()->user()->can(['city_upload']))
                  <form action="{{ URL::to('city-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.city.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                 
                  @if(auth()->user()->can(['city_download']))
                  <a href="{{ URL::to('city-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.city.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['city_template']))
                  <a href="{{ URL::to('city-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.city.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['city_create']))
                   <a data-toggle="modal" data-target="#createcity" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.city.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
          <table id="getcity" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.global.active') !!}</th>
              <th>{!! trans('panel.city.city_name') !!}</th>
              <th>{!! trans('panel.city.district') !!}</th>
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
<div class="modal fade bd-example-modal-lg" id="createcity" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.city.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('city.store') }}" enctype="multipart/form-data" id="createcityForm">
        @csrf
        <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.city.city_name') !!} <span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="city_name" id="city_name" class="form-control" value="{!! old( 'city_name') !!}" maxlength="200" required>
                    @if ($errors->has('city_name'))
                      <div class="error"><p class="text-danger">{{ $errors->first('city_name') }}</p></div>
                    @endif
                  </div>
               
              </div>
            </div>
          <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.city.district') !!}<span class="text-danger"> *</span></label>
               
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="district_id" id="district_id" style="width: 100%;" required>
                        <option value="">Select {!! trans('panel.city.district') !!}</option>
                        @if(@isset($districts ))
                        @foreach($districts as $district)
                        <option value="{!! $district['id'] !!}" {{ old( 'district_id') == $district['id'] ? 'selected' : '' }}>{!! $district['district_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  @if ($errors->has('country_id'))
                   <div class="error">
                      <p class="text-danger">{{ $errors->first('country_id') }}</p>
                   </div>
                  @endif
                </div>
           
            </div>
             </div>
        <div class="clearfix"></div>
        <div class="pull-right">
          <input type="hidden" name="id" id="city_id" />
          <button class="btn btn-info save"> Submit</button>
        </form>
        </div>
     
    </div>
  </div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/validation_address.js') }}"></script>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getcity').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('city.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false},
            {data: 'active', name: 'active',"defaultContent": '', orderable: false, searchable: false},
            {data: 'city_name', name: 'city_name',"defaultContent": ''},
            {data: 'districtname.district_name', name: 'districtname.district_name',"defaultContent": ''},
             {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $.ajax({
        url: base_url + '/city/'+id+'/edit',
       dataType:"json",
       success:function(data)
       {
        $('#city_name').val(data.city_name);
        $("#district_id").append('<option value="'+data.district_id+'" selected="selected">'+data.district_name+'</option>');
        $('#city_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createcity').modal('show');
       }
      })
     });

    $('body').on('click', '.cityActive', function () {
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
            url: "{{ url('city-active') }}",
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
        $('#city_id').val('');
        $('#createcityForm').trigger("reset");
        $("#city_image").attr({ "src": '{!! asset('assets/img/placeholder.jpg') !!}' });
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('city') }}"+'/'+id,
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
