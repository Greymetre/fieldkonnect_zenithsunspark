<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">SAP Stock {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  @if(auth()->user()->can(['category_upload']))
                  {{-- <form action="{{ URL::to('categories-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.category.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form> --}}
                  @endif

                  @if(auth()->user()->can(['category_download']))
                  {{-- <a href="{{ URL::to('categories-download') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.global.download') !!} {!! trans('panel.category.title') !!}"><i class="material-icons">cloud_download</i></a> --}}
                  @endif
                  @if(auth()->user()->can(['category_template']))
                  {{-- <a href="{{ URL::to('categories-template') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.global.template') !!} {!! trans('panel.category.title_singular') !!}"><i class="material-icons">text_snippet</i></a> --}}
                  @endif
                  @if(auth()->user()->can(['ware_house_create']))
                  {{-- <a data-toggle="modal" data-target="#createWarehouse" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.category.title_singular') !!}"><i class="material-icons">add_circle</i></a> --}}
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
            <table id="getcategory" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Product Group</th>
                <th>Ware House Name</th>
                <th>InStock Quantity</th>
                <th>Value</th>
                <th>Remark</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
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
      var table = $('#getcategory').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: "{{ route('sap_stock.index') }}",
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'product_sap_code',
            name: 'product_sap_code',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'product_description',
            name: 'product_description',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'product_category_name',
            name: 'product_category_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'warehouse_name',
            name: 'warehouse_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'instock_qty',
            name: 'instock_qty',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'value',
            name: 'value',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'itm_remarks',
            name: 'itm_remarks',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
        ]
      });


    });
  </script>

</x-app-layout>