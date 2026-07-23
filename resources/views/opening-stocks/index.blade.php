<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Opening Stock {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  @if(auth()->user()->can(['opening_stock_import']))
                  <form action="{{ URL::to('opening-stocks-import') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Opening Stock">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form> 
                  @endif

                  @if(auth()->user()->can(['opening_stock_download']))
                  <a href="{{ URL::to('opening-stocks-export') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.global.download') !!} Opening Stock"><i class="material-icons">cloud_download</i></a> 
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
          @if(session('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_success') }}
            </span>
          </div>
          @endif
          @if(session('message_error'))
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_error') }}
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
                <th>Item Code</th>
                <th>Item Desc</th>
                <th>Item Group Name</th>
                <th>Ware House Name</th>
                <th>Branch</th>
                <th>InStock Qty</th>
                <!-- <th>Opening qty (Prod.)</th> -->
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
        ajax: "{{ route('getOpeningStocks') }}",
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'item_code',
            name: 'item_code',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'item_description',
            name: 'item_description',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'item_group',
            name: 'item_group',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'ware_house_name',
            name: 'ware_house_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'branch_names',
            name: 'branch_names',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'opening_stocks',
            name: 'opening_stocks',
            "defaultContent": '',
            orderable: false,
            searchable: false
          }
          // {
          //  data: 'open_order_qty',
          //  name: 'open_order_qty',
          //  "defaultContent": '',
          //  orderable: false,
          //  searchable: false
          //}
        ]
      });


    });
  </script>

</x-app-layout>