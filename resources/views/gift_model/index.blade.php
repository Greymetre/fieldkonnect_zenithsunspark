<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.gift_model.title_singular') !!} {!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                  <div class="next-btn">
                  @if(auth()->user()->can(['gift_model_upload']))
                  <form action="{{ URL::to('gift-model-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.gift_model.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['gift_model_download']))
                  <a href="{{ URL::to('gift-model-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.gift_model.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['gift_model_template']))
                  <a href="{{ URL::to('gift-model-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.gift_model.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['gift_model_create']))
                   <a data-toggle="modal" data-target="#createsubcategory" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.gift_model.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
          <table id="getsubcategory" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.global.active') !!}</th>
               <th>{!! trans('panel.gift_model.fields.subcategory_image') !!}</th>
              <th>{!! trans('panel.gift_model.fields.subcategory_name') !!}</th>
              <th>{!! trans('panel.gift_model.fields.category') !!}</th>
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
<div class="modal fade bd-example-modal-lg" id="createsubcategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.gift_model.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('gift-model.store') }}" enctype="multipart/form-data" id="createModelForm">
        @csrf
        <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift_model.fields.subcategory_name') !!} <span class="text-danger"> *</span></label>
              
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="model_name" id="model_name" class="form-control" value="{!! old( 'model_name') !!}" maxlength="200" required>
                    @if ($errors->has('model_name'))
                      <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('model_name') }}</p></div>
                    @endif
                  </div>
                </div>
            
            </div>
          <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift_model.fields.category') !!}<span class="text-danger"> *</span></label>
              
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="sub_category_id" id="sub_category_id" style="width: 100%;" required>
                        <option value="">Select {!! trans('panel.gift_model.fields.category') !!}</option>
                        @if(@isset($categories ))
                        @foreach($categories as $category)
                        <option value="{!! $category['id'] !!}" {{ old( 'sub_category_id') == $category['id'] ? 'selected' : '' }}>{!! $category['subcategory_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  @if ($errors->has('sub_category_id'))
                   <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('sub_category_id') }}</p>
                   </div>
                  @endif
             
              </div>
            </div>
            <div class="col-md-3 col-sm-3">
              <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                  
                   <div class="fileinput-new thumbnail">
                     <img src="" id="subcategory_image" class="imagepreview1">
                      <div class="selectThumbnail">
                     <span class="btn btn-just-icon btn-round btn-file">
                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                       <span class="fileinput-exists">Change</span>
                       <input type="file" name="image" class="getimage1" accept="image/*">
                     </span>
                     <br>
                     <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                   </div>
                   </div>
                   <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                   <label class="bmd-label-floating">{!! trans('panel.gift_model.fields.subcategory_image') !!}</label>
                 </div>
            </div>
          </div>
        <div class="clearfix"></div>
        <div class="pull-right">
          <input type="hidden" name="id" id="subcategory_id" />
          <button class="btn btn-info save"> Submit</button>
        </form>
        </div>
      </div>
    </div>
  </div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getsubcategory').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('gift-model.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false},
            {data: 'active', name: 'active',"defaultContent": '', orderable: false, searchable: false},
            {data: 'image', name: 'image',"defaultContent": '', orderable: false, searchable: false},
            {data: 'model_name', name: 'model_name',"defaultContent": ''},
            {data: 'subCategories.subcategory_name', name: 'subCategories.subcategory_name',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $.ajax({
        url: base_url + '/gift-model/'+id+'/edit',
       dataType:"json",
       success:function(data)
       {
        $('#model_name').val(data.model_name);
        // $("#sub_category_id").append('<option value="'+data.sub_category_id+'" selected="selected">'+data.sub_category_name+'</option>');
        $("#sub_category_id").val(data.sub_category_id);
        $('#sub_category_id').trigger('change.select2');
        if(data.subcategory_image)
        {
          var image = data.subcategory_image ;
        }
        else
        {
          var image = "{!! asset('assets/img/placeholder.jpg') !!}" ;
        }
        $("#subcategory_image").attr({ "src": image });
        $('#subcategory_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createsubcategory').modal('show');
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
            url: "{{ url('gift-model-active') }}",
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
        $('#sub_category_id').val('');
        $('#createModelForm').trigger("reset");
        $("#subcategory_image").attr({ "src": "{!! asset('assets/img/placeholder.jpg') !!}" });
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('gift-model') }}"+'/'+id,
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
