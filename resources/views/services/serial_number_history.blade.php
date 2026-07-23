<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.sidemenu.serial_number_history') !!} {!! trans('panel.global.list') !!}
            <span class="">
               <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['serial_number_history_download']))
                <form method="GET" action="{{ URL::to('services/serial_number_history/download') }}" class="form-horizontal">
                  <div class="d-flex flex-wrap flex-row">
                    <!-- <div class="p-2" style="width:200px;">
                      <select class="select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="product_id" id="product_id" data-style="select-with-transition" title="Select Product">
                        <option value="">Select Product</option>
                        @if(@isset($products ))
                        @foreach($products as $product)
                        <option value="{!! $product->id !!}">{!! $product->product_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>  -->
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.sidemenu.serial_number_transaction') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                {{--@if(auth()->user()->can(['serial_number_transaction_upload']))
                <form action="{{ URL::to('services/serial_number_transaction/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.sidemenu.serial_number_transaction') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif --}}
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
            <table id="getSerialTransaction" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Action</th>
                <th>Serial number</th>
                <th>Group</th>
                <th>Naration</th>
                <th>Product Model</th>
                <th>Party Name</th>
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Expiry Date</th>
                <th>Warranty Status</th>
                <th>Product Code</th>
                <!-- <th>{!! trans('panel.global.created_by') !!}</th>
              <th>{!! trans('panel.global.created_at') !!}</th> -->
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
      var table = $('#getSerialTransaction').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'type': 'POST',
          'url': "{{ url('services/serial_number_history/list') }}",
          'data': function(d) {
            d._token = token,
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
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
            data: 'serial_no',
            name: 'serial_no',
            "defaultContent": ''
          },
          {
            data: 'group',
            name: 'group',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'narration',
            name: 'narration',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'product.model_no',
            name: 'product.model_no',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'party_name',
            name: 'party_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'invoice_no',
            name: 'invoice_no',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'invoice_date',
            name: 'invoice_date',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'expiry_date',
            name: 'expiry_date',
            orderable: false,
            searchable: false
          },
          {
            data: 'warranty_status',
            name: 'warranty_status',
            orderable: false,
            searchable: false
          },
          {
            data: 'product_code',
            name: 'product_code',
            "defaultContent": '',
            orderable: false,
            searchable: false
          }
        ]
      });
      $('#branch_id').change(function() {
        table.draw();
      });
      $('#product_id').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
    });
  </script>
</x-app-layout>