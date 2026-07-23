<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.gift.title_singular') !!}{!! trans('panel.global.list') !!}
            <span class="">
              <div class="d-flex flex-wrap flex-row">
                <div class="next-btn">
                @if(auth()->user()->can(['gift_upload']))
                <form action="{{ URL::to('gifts-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                      <span class="btn btn-just-icon btn-theme btn-file">
                        <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden">
                        <input type="file" title="Selet File" name="import_file" required accept=".xls,.xlsx" />
                      </span>
                    </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.gift.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                @if(auth()->user()->can(['gift_download']))
                <a href="{{ URL::to('gifts-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.gift.title') !!}"><i class="material-icons">cloud_download</i></a>
                @endif
                @if(auth()->user()->can(['gift_template']))
                <a href="{{ URL::to('gifts-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.gift.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                @endif
                @if(auth()->user()->can(['gift_create']))
                <a href="{{ route('gifts.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.gift.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                @endif
                @if(auth()->user()->can(['gift_create']))
                <a href="{{ route('gifts.pdf') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.pdf_module.download') !!} {!! trans('panel.pdf_module.pdf') !!}"><i class="material-icons">picture_as_pdf</i></a>
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
            <table id="getproduct" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.active') !!}</th>
                <th>{!! trans('panel.gift.fields.product_image') !!}</th>
                <th>{!! trans('panel.gift.fields.product_name') !!}</th>
                <th>{!! trans('panel.gift.fields.display_name') !!}</th>
                <th>{!! trans('panel.gift.fields.category_name') !!}</th>
                <th>{!! trans('panel.gift.fields.subcategory_name') !!}</th>
                <th>{!! trans('panel.gift.fields.brand_name') !!}</th>
                <th>{!! trans('panel.gift.fields.unit_name') !!}</th>
                <th>{!! trans('panel.gift.fields.mrp') !!}</th>
                <th>{!! trans('panel.gift.fields.price') !!}</th>
                <th>{!! trans('panel.gift.fields.points') !!}</th>
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
      oTable = $('#getproduct').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('gifts.index') }}",
        "columns": [{
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
            data: 'product_name',
            name: 'product_name',
            "defaultContent": ''
          },
          {
            data: 'display_name',
            name: 'display_name',
            "defaultContent": ''
          },
          {
            data: 'categories.category_name',
            name: 'categories.category_name',
            "defaultContent": ''
          },
          {
            data: 'subcategories.subcategory_name',
            name: 'subcategories.subcategory_name',
            "defaultContent": ''
          },
          {
            data: 'brands.brand_name',
            name: 'brands.brand_name',
            "defaultContent": ''
          },
          {
            data: 'models.model_name',
            name: 'models.model_name',
            "defaultContent": ''
          },
          {
            data: 'mrp',
            name: 'mrp',
            "defaultContent": ''
          },
          {
            data: 'price',
            name: 'price',
            "defaultContent": ''
          },
          {
            data: 'points',
            name: 'points',
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
      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('gifts') }}" + '/' + id,
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
            oTable.draw();
          },
        });
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
              url: "{{ url('gifts-active') }}",
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
                oTable.draw();
              },
          });
      });
    });


  </script>
</x-app-layout>