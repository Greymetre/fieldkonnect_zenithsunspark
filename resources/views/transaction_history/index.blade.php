<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Transaction Coupon History {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['transaction_history_download']))
                <form method="GET" action="{{ route('transaction_history.download') }}">
                  <div class="d-flex flex-wrap flex-row" style="align-items: end;">
                    <div class="p-2" style="width:160px;">
                      <label for="branch_id">Branch</label>
                      <select class="select2" placeholder="Select Branch" multiple name="branch_id[]" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <label for="parent_customer">Parent Customer</label>
                      <select class="select2" multiple name="parent_customer[]" id="parent_customer" data-style="select-with-transition" title="Select Parent Customer">
                        <option value="">Select Parent Customer</option>
                        @if(@isset($parent_customers ))
                        @foreach($parent_customers as $parent_customer)
                        <option value="{!! $parent_customer->id !!}">{!! $parent_customer->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <!-- <label for="designation">Designation</label> -->
                      <select class="select2" name="designation" id="designation" data-style="select-with-transition" title="Select Parent Customer">
                        <option value="">Select Designation</option>
                        @if(@isset($designations ))
                        @foreach($designations as $designation)
                        <option value="{!! $designation->id !!}">{!! $designation->designation_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <label for="scheme_type">Scheme</label>
                      <select class="select2" name="scheme_name" id="scheme_name" data-style="select-with-transition" title="Select Scheme Type">
                        <option value="">Scheme Name</option>
                        @if(@isset($scheme_names ))
                        @foreach($scheme_names as $scheme_name)
                        <option value="{!! $scheme_name->id !!}">{!! $scheme_name->scheme_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                    <select class="select2" name="customer_id" id="customer_id" data-style="select-with-transition" title="Select">
                      <option value="">Firm Name</option>
                      @if(@isset($customers))
                      @foreach($customers as $customer)
                      <!-- <option value="{!! $customer->id !!}">{!! $customer->_name !!}</option> -->
                      <option value="{!! $customer['id'] !!}" {{ old( 'executive_id') == $customer->id ? 'selected' : '' }}>{!! $customer['name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                    <div class="p-2" style="width:160px;"><label for="start_date">Start Date</label><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:160px;"><label for="end_date">End Date</label><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2">
                      <label for="">Download</label><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Transaction"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                  <div class="btn-group multi-a-r d-none">
                    <button class="btn btn-just-icon btn-success" title="Re-Calculate Point" id="recalcute_point">
                      <i class="material-icons">check</i> 
                    </button>
                  </div>
                  <div class="p-2">
                     <input type="text" name="point_search" id="point_serch" class="from-control" placeholder="Point" style="border: 2px solid #1072ae;
                     border-radius: 100px;
                     height: 40px;
                     text-align: center;
                     width: 198px;
                     font-size: initial;">
                  </div>
                  @if(auth()->user()->can(['transaction_history_upload']))
                  <form action="{{ URL::to('transaction_history_upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="d-flex">

                      <div class="fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Manual Transaction">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['transaction_history_upload']))
                  <form action="{{ URL::to('transaction_history_main_upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="d-flex">
                      <div class="fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File" name="import_file_main" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Transaction">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['transaction_history_template']))
                  <a href="{{ URL::to('transaction_history_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Manual Transaction"><i class="material-icons">text_snippet</i></a>
                  <a href="{{ URL::to('transaction_history_main_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Transaction"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['transaction_history_create']))
                  <a href="{{ route('transaction_history.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Transaction"><i class="material-icons">add_circle</i></a>
                  <a href="{{ route('transaction_history.manualcreate') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Manual Transaction"><i class="material-icons">add_circle</i></a>
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
                <th>#</th>
                <th>{!! trans('panel.expenses.fields.date') !!}</th>
                <th>Firm Name</th>
                <th>CONTACT PERSON</th>
                <th>parent name</th>
                <th>Mobile Number</th>
                <th>COUPON Code</th>
                <th>Sub Category</th>
                <th>Prodcut Name</th>
                <th>Point</th>
                <th>Remark</th>
                <th>{!! trans('panel.global.action') !!}</th>
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
          'url': "{{ route('transaction_history.index') }}",
          'data': function(d) {
            d.branch_id = $('#branch_id').val(),
            d.parent_customer = $('#parent_customer').val(),
            d.scheme_name = $('#scheme_name').val(),
            d.start_date = $('#start_date').val(),
            d.end_date = $('#end_date').val(),
            d.customer_id = $('#customer_id').val(),
            d.designation = $('#designation').val(),
            d.point = $('#point_serch').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
          {
            data: 'customer.name',
            name: 'customer.name',
          },
          {
            data: 'contact_person',
            name: 'contact_person',
            orderable: false,
            searchable: false
          },
          {
            data: 'parent_name',
            name: 'parent_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'customer.mobile',
            name: 'customer.mobile',
            "defaultContent": ''
          },
          {
            data: 'coupon_code',
            name: 'coupon_code',
            render: function(data, type, row) {
              return data ? data : 'Manual';
            }
          },
          {
            data: 'subcategory_name',
            name: 'subcategory_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'product_name',
            name: 'product_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'point',
            name: 'point',
            orderable: false,
            searchable: false
          },
          {
            data: 'remark',
            name: 'remark'
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            className: 'td-actions text-center',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#branch_id').change(function() {
        table.draw();
      });
      $('#customer_id').change(function() {
        table.draw();
      });
      $('#designation').change(function() {
        table.draw();
      });
      $('#parent_customer').change(function() {
        table.draw();
      });
      $('#scheme_name').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });

      $('#point_serch').on('keyup' , function(){
        table.draw();
      })

      $(document).on('click', '.row-checkbox', function () {
          const selectedValues = [];
          $('.row-checkbox:checked').each(function () {
              selectedValues.push($(this).val());
          });
          if(selectedValues.length > 0){
            $(".multi-a-r").removeClass('d-none');
          }else{
            $(".multi-a-r").addClass('d-none');
          }
      });

      $(document).on('click' , '#recalcute_point' , function(){
        const selectedValues = [];
          $('.row-checkbox:checked').each(function () {
              selectedValues.push($(this).val());
          });
          var token = $("meta[name='csrf-token']").attr("content");
          $.ajax({
            url: "{{ route('transaction_history.point_recalculate') }}",
            type: 'POST',
            data: {
              _token: token,
              ids: selectedValues,
            },
            success: function(data) {
              $('.message').empty();
              if (data.status == true) {
                  if(data.update == true){
                    $('.alert').show();
                    $('.alert').addClass("alert-success");
                    $('.message').append('Point updated successfully');
                  }
                  if(data.message != ''){
                    $('.alert').show();
                    $('.alert').addClass("alert-danger");
                    $('.message').append(data.message);
                  }
              } 
              table.draw();
            },
          });
      })
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
          url: "{{ url('transaction_history') }}" + '/' + id,
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
        placeholder: 'Select...',
        multiple: true,
        allowClear: true,
        ajax: {
          url: "{{ route('getCustomerDataSelect') }}",
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
    }, 2000);
  </script>
</x-app-layout>