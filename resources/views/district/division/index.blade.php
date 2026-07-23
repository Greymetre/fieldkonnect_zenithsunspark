<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.brand.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="pull-right">
                <div class="btn-group">
                  @if(auth()->user()->can(['brand_upload']))
                  <form action="{{ URL::to('brands-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.brand.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['brand_download']))
                  <a href="{{ URL::to('brands-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.brand.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['brand_template']))
                  <a href="{{ URL::to('brands-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.brand.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['brand_create']))
                  <a data-toggle="modal" data-target="#createbrand" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.brand.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
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
          <span class="message">
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
          <table id="getbrand" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
               <th>{!! trans('panel.global.active') !!}</th>
              <th>{!! trans('panel.brand.fields.brand_image') !!}</th>
              <th>{!! trans('panel.brand.fields.brand_name') !!}</th>
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
<div class="modal fade bd-example-modal-lg" id="createbrand" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.brand.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route' => 'brands.store','id' => 'createBrandForm','files'=>true ]) !!}
        <div class="row">
          <div class="col-md-8">
            <div class="row">
              <label class="col-md-3 col-form-label">{!! trans('panel.brand.fields.brand_name') !!} <span class="text-danger"> *</span></label>
              <div class="col-md-9">
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="brand_name" class="form-control" id="brand_name" value="{!! old( 'brand_name') !!}" maxlength="200" required>
                  @if ($errors->has('brand_name'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('brand_name') }}</p></div>
                  @endif
                </div>
              </div>
            </div>
            </div>
            <div class="col-md-3 col-sm-3">
              <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                <div class="selectThumbnail">
                 <span class="btn btn-just-icon btn-round btn-file">
                   <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                   <span class="fileinput-exists">Change</span>
                   <input type="file" name="image" class="getimage1" accept="image/*">
                 </span>
                 <br>
                 <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
               </div>
               <div class="fileinput-new thumbnail">
                 <img src="{!! asset('assets/img/placeholder.jpg') !!}" id="brand_image" class="imagepreview1">
               </div>
               <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
               <label class="bmd-label-floating">{!! trans('panel.brand.fields.brand_image') !!}</label>
             </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
          <input type="hidden" name="id" id="brand_id" />
          {{ Form::submit('Submit', array('class' => 'btn btn-info save')) }}
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getbrand').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('brands.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'active', name: 'active',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'image', name: 'image',"defaultContent": '', orderable: false, searchable: false},
            {data: 'brand_name', name: 'brand_name',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $('#form_result').html('');
      $.ajax({
        url: base_url + '/brands/'+id,
       dataType:"json",
       success:function(data)
       {
        if(data.brand_image)
        {
          var image = data.brand_image ;
        }
        else
        {
          var image = "{!! asset('assets/img/placeholder.jpg') !!}" ;
        }
        
        $('#brand_name').val(data.brand_name);
        $("#brand_image").attr({ "src": image });
        $('#brand_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createbrand').modal('show');
       }
      })
     });
    $('body').on('click', '.brandActive', function () {
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
            url: "{{ url('brands-active') }}",
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
        $('#brand_id').val('');
        $('#createBrandForm').trigger("reset");
        $("#brand_image").attr({ "src": '{!! asset('assets/img/placeholder.jpg') !!}' });
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('brands') }}"+'/'+id,
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
  });
</script>
</x-app-layout>
