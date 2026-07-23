<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Service Product Category {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="GET" action="{{ URL::to('service-charge/categories/download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:200px;">
                      <select name="division" id="division" class="select2">
                        <option value="">Select Division</option>
                        @if(count($categories) > 0)
                        @foreach($categories as $category)
                        <option value="{{$category->id}}">{{ $category->division_name}}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @if(auth()->user()->can(['services_product_category_download']))
                    <div class="p-2" style="width:200px;">
                      <button href="{{ URL::to('service-charge/categories/download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.category.title') !!}"><i class="material-icons">cloud_download</i></button>
                    </div>
                    @endif
                </form>
                <div class="next-btn">
                  @if(auth()->user()->can(['services_product_category_upload']))
                  <form action="{{ URL::to('service-charge/categories/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File For Import Data" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.category.title') !!}">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif

                  @if(auth()->user()->can(['category_template']))
                  <!-- <a href="{{ URL::to('subcategories-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.category.title_singular') !!}"><i class="material-icons">text_snippet</i></a> -->
                  @endif
                  @if(auth()->user()->can(['services_product_category_create']))
                  <a data-toggle="modal" data-target="#createcategory" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.category.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session()->get('message_success') }}
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
            <table id="getcategory" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.active') !!}</th>
                <!-- <th>{!! trans('panel.category.fields.category_image') !!}</th> -->
                <th>{!! trans('panel.category.fields.category_name') !!}</th>
                <th>{!! trans('panel.division.title') !!}</th>
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
  <div class="modal fade bd-example-modal-lg" id="createcategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.category.title_singular') !!}
            <span class="pull-right">
              <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('servicecharge.categories.add') }}" enctype="multipart/form-data" id="createcategoryForm">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.category.fields.category_name') !!} <span class="text-danger"> *</span></label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="category_name" id="category_name" class="form-control" value="{!! old( 'category_name') !!}" maxlength="200" required>
                      @if ($errors->has('category_name'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('category_name') }}</p>
                      </div>
                      @endif
                    </div>
                  
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Division<span class="text-danger"> *</span></label>
                                      <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2" name="division_id" id="division_id" style="width: 100%;" required>
                        <option value="">Select Division</option>
                        @if(@isset($categories ))
                        @foreach($categories as $category)
                        <option value="{!! $category['id'] !!}" {{ old( 'category_id') == $category['id'] ? 'selected' : '' }}>{!! $category['division_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @if ($errors->has('category_id'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('category_id') }}</p>
                    </div>
                    @endif
                  </div>
              
              </div>
              <!-- <div class="col-md-3 col-sm-3">
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
                     <img src="" id="category_image" class="imagepreview1">
                   </div>
                   <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                   <label class="bmd-label-floating">{!! trans('panel.category.fields.category_image') !!}</label>
                 </div>
            </div> -->
            </div>
            <div class="clearfix"></div>
            <div class="pull-right">
              <input type="hidden" name="id" id="category_id" />
              <button class="btn btn-info save"> Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getcategory').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('servicecharge.categories.index') }}",
          'data': function(d) {
            d.division = $('#division').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'active',
            name: 'active',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          // {data: 'image', name: 'image',"defaultContent": '', orderable: false, searchable: false},
          {
            data: 'category_name',
            name: 'category_name',
            "defaultContent": ''
          },
          {
            data: 'division.division_name',
            name: 'division.division_name',
            "defaultContent": ''
          },
          {
            data: 'createdbyname.name',
            name: 'createdbyname.name',
            "defaultContent": ''
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
        ]
      });
      $('#division').change(function() {
        table.draw();
      });
      var base_url = $('.baseurl').data('baseurl');

      $(document).on('click', '.edit', function() {
        var id = $(this).attr('id');
        $.ajax({
          url: base_url + '/service-charge/categories/' + id + '/edit',
          dataType: "json",
          success: function(data) {
            $('#category_name').val(data.category_name);
            $("#division_id").val(data.division_id);
            $("#division_id").change();
            $('#category_id').val(data.id);
            var title = '{!! trans("panel.global.edit") !!}';
            $('.modal-title').text(title);
            $('#action_button').val('Edit');
            $('#createcategory').modal('show');
          }
        })
      });

      $('body').on('click', '.activeRecord', function() {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if (active == 'Y') {
          status = 'Incative ?';
        } else {
          status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want " + status)) {
          return false;
        }
        $.ajax({
          url: base_url + '/service-charge/categories/' + id + '/active',
          type: 'POST',
          data: {
            _token: token,
            id: id,
            active: active
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });

      $('.create').click(function() {
        $('#category_id').val('');
        $('#division_id').val('');
        $("#division_id").change();
        $('#createcategoryForm').trigger("reset");
        $("#category_image").attr({
          "src": "{!! asset('assets/img/placeholder.jpg') !!}"
        });
        $('.modal-title').text('{!! trans("panel.global.add") !!}');
      });

      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('service-charge/categories') }}/" + id + '/active',
          type: 'DELETE',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
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