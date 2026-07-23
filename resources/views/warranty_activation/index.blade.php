<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Warranty Activation {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['warranty_activation_download']))
                <form method="GET" action="{{ route('warranty_activation.download') }}" class="form-horizontal">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:180px;">
                      <!-- <label for="status">Status</label> -->
                      <select class="select2" placeholder="Select Branch" name="status" id="status" data-style="select-with-transition" title="Select Branch">
                      <option value="0" {{$currunt_status=='0'?'selected':''}}>In Verification</option>
                        <option value="1" {{$currunt_status=='1'?'selected':''}}>Activated</option>
                        <option value="2" {{$currunt_status=='2'?'selected':''}}>Pending Activated</option>
                        <option value="3" {{$currunt_status=='3'?'selected':''}}>Rejected</option>
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <!-- <label for="branch_id">Branch</label> -->
                      <select class="select2" placeholder="Select Branch" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <!-- <label for="parent_customer">Seller Name</label> -->
                      <select name="parent_customer" id="parent_customer" data-style="select-with-transition" title="Select seller">
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <!-- <label for="product_id">Product Name</label> -->
                      <select name="product_id" id="product_id" data-style="select-with-transition" title="Select seller">
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <!-- <label for="product_id">Product Name</label> -->
                      <select name="state_id" id="state_id" data-style="select-with-transition" title="Select seller">
                      </select>
                    </div>

                    <!-- <div class="p-2" style="width:180px;"><label for="start_date">Start Date</label><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:180px;"><label for="end_date">End Date</label><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div> -->
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Warranty Activation"><i class="material-icons">cloud_download</i></button></div>

                  </div>
                </form>
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['warranty_activation_create']))
                  <a href="{{ route('warranty_activation.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Warranty Activation"><i class="material-icons">add_circle</i></a>
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
          @if(session('message_info'))
          <div class="alert alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_info') }}
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
            <table id="getTransactionHistory" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>Customer Name</th>
                <th>Contact Number</th>
                <th>Status</th>
                <th>Serail_ Number</th>
                <th>Seller Name</th>
                <th>Product Name</th>
                <th>Activation Status</th>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getTransactionHistory').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('warranty_activation.index') }}",
          'data': function(d) {
            d.branch_id = $('#branch_id').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.status = $('#status').val(),
              d.product_id = $('#product_id').val(),
              d.state_id = $('#state_id').val()
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
            data: 'customer.customer_name',
            name: 'customer.customer_name',
            "defaultContent": ''
          },
          {
            data: 'customer.customer_number',
            name: 'customer.customer_number',
            "defaultContent": ''
          },
          {
            data: 'cust_status',
            name: 'cust_status',
            orderable: false,
            searchable: false
          },
          {
            data: 'product_serail_number',
            name: 'product_serail_number',
            "defaultContent": '',
            render: function(data, type, row) {
              return data.toUpperCase();
            }
          },
          {
            data: 'seller_details.name',
            name: 'seller_details.name',
            "defaultContent": ''
          },
          {
            data: 'product_details.product_name',
            name: 'product_details.product_name',
            "defaultContent": ''
          },
          {
            data: 'status',
            name: 'status',
            "defaultContent": ''
          },
        ]
      });
      $('#branch_id').change(function() {
        table.draw();
      });
      $('#parent_customer').change(function() {
        table.draw();
      });
      $('#status').change(function() {
        table.draw();
      });
      $('#product_id').change(function() {
        table.draw();
      });
      $('#state_id').change(function() {
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
          url: "{{ url('schemes-active') }}",
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
          url: "{{ url('warranty_activation') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
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

    });

    setTimeout(() => {
      $('#parent_customer').select2({
        placeholder: 'Select Seller',
        allowClear: true,
        ajax: {
          url: "{{ route('getRetailerDataSelect') }}",
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              term: params.term || '',
              page: params.page || 1
            }
          },
          cache: true
        }
      }).trigger('change');

      $('#product_id').select2({
        placeholder: 'Select Product',
        allowClear: true,
        ajax: {
          url: "{{ route('getProductDataSelect') }}",
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              term: params.term || '',
              page: params.page || 1
            }
          },
          cache: true
        }
      }).trigger('change');

      $('#state_id').select2({
        placeholder: 'Select State',
        allowClear: true,
        ajax: {
          url: "{{ route('getStateDataSelect') }}",
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              term: params.term || '',
              page: params.page || 1
            }
          },
          cache: true
        }
      }).trigger('change');

    }, 1000);
  </script>
</x-app-layout>