<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.product.title_singular') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['product_download']))
                <form method="GET" action="{{ URL::to('products-download') }}">
                  <div class="d-flex flex-wrap flex-row">

                  <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="active" id="active" data-style="select-with-transition" title="Select Status">
                        <option value="">Select Status</option>
                        <option value="Y">Active</option>
                        <option value="N">Inactive</option>
                      </select>
                    </div>

                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="category_id" id="category_id" data-style="select-with-transition" title="Select Division">
                        <option value="">Select Division</option>
                        @if(@isset($categories ))
                        @foreach($categories as $category)
                        <option value="{!! $category['id'] !!}" {{ old( 'category_id') == $category->id ? 'selected' : '' }}>{!! $category['category_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- <div class="p-2" style="width:180px;">
                      <select class="selectpicker" multiple name="branch_id[]" id="branch_id" data-style="select-with-transition" title="Select Sub Division">
                        <option value="">Select Sub Division</option>
                        @if(@isset($subCategories ))
                        @foreach($subCategories as $subCategory)
                        <option value="{!! $subCategory['id'] !!}">{!! $subCategory['subcategory_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div> -->
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.product.title') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['product_upload']))
                  <form action="{{ URL::to('products-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.product.title') !!}">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif

                  @if(auth()->user()->can(['product_download']))
                  <!-- <a href="{{ URL::to('products-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.product.title') !!}"><i class="material-icons">cloud_download</i></a> -->
                  @endif
                  @if(auth()->user()->can(['product_template']))
                  <a href="{{ URL::to('products-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.product.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['product_create']))
                  <a href="{{ route('products.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.product.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
            <table id="getproduct" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.product.fields.product_image') !!}</th>
                <th>{!! trans('panel.product.fields.category_name') !!}</th>
                <th>{!! trans('panel.product.fields.subcategory_name') !!}</th>
                <th>{!! trans('panel.product.fields.product_name') !!}</th>
                <th>HP</th>
                <th>Product Stage</th>
                <th>{!! trans('panel.product.fields.mrp') !!}</th>
                <th>{!! trans('panel.product.fields.gst') !!}</th>
                <th>Discount %</th>
                <th>Expiry Interval Preiod</th>
                <th>{!! trans('panel.product.fields.suc-del') !!}</th>
                <th>SAP Code</th>
                <!-- <th>{!! trans('panel.product.fields.unit_name') !!}</th> -->
                <!-- <th>{!! trans('panel.product.fields.price') !!}</th> -->
                <!-- <th>{!! trans('panel.product.fields.selling_price') !!}</th> -->
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
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getproduct').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          type:'POST',
          url: "{{ url('products-list') }}",
          data: function (d) {
                d._token = token,
                d.category_id = $('#category_id').val()
                d.active = $('#active').val()
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
            className: 'td-actions text-center',
            orderable: false,
            searchable: false
          },
          {
            data: 'image',
            name: 'image',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'categories.category_name',
            name: 'categories.category_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'subcategories.subcategory_name',
            name: 'subcategories.subcategory_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'product_name',
            name: 'product_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'specification',
            name: 'specification',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'product_no',
            name: 'product_no',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'productpriceinfo.mrp',
            name: 'productpriceinfo.mrp',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'productpriceinfo.gst',
            name: 'productpriceinfo.gst',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'productpriceinfo.discount',
            name: 'productpriceinfo.discount',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'expiry_info',
            name: 'expiry_info',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'suc_del',
            name: 'suc_del',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'sap_code',
            name: 'sap_code',
            orderable: false,
            "defaultContent": ''
          },
          // {
          //   data: 'unitmeasures.unit_code',
          //   name: 'unitmeasures.unit_code',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'productpriceinfo.price',
          //   name: 'productpriceinfo.price',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'productpriceinfo.selling_price',
          //   name: 'productpriceinfo.selling_price',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          {
            data: 'createdbyname.name',
            name: 'createdbyname.name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'created_at',
            name: 'created_at',
            orderable: false,
            "defaultContent": ''
          },
        ]
      });

      $('#category_id').change(function() {
        console.log($('#category_id').val());
        table.draw();
      });
      $('#active').change(function() {
        console.log($('#active').val());
        table.draw();
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
          url: "{{ url('products-active') }}",
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

      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('products') }}" + '/' + id,
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