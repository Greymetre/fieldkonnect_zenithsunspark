<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Redemption {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['redemption_download']))
                <form method="GET" action="{{ route('redemptions.download') }}" class="form-horizontal">
                  <div class="d-flex flex-wrap flex-row">
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
                      <label for="status">Status</label>
                      <select class="select2" name="status" id="status" data-style="select-with-transition" title="Select Parent Customer">
                        <option value="">Select Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                        <option value="3">Success</option>
                        <option value="4">Dispatch/Fail</option>
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <label for="redeem_mode">Redeem Mode</label>
                      <select class="select2" name="redeem_mode" id="redeem_mode" data-style="select-with-transition" title="Select Scheme Type">
                        @if(@isset($redeem_modes ))
                        @foreach($redeem_modes as $k=>$redeem_mode)
                        <option value="{!! $k !!}">{!! $redeem_mode !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;"><label for="start_date">Start Date</label><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:160px;"><label for="end_date">End Date</label><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2">
<label for="redeem_mode">Download</label>
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Redemption"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                @if(auth()->user()->can(['redemption_upload']))
                <form id="neft_status_form" action="{{ URL::to('redemptions-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="d-flex flex-row align-items-center">
                    <div class="fileinput-new text-center" data-provides="fileinput">
                      <span class="btn btn-just-icon btn-theme btn-file">
                        <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden">
                        <input type="file" name="import_file" required accept=".xls,.xlsx" />
                      </span>
                    </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Redemption">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                @if(auth()->user()->can(['redemption_template']))
                <a id="neft_status_tempalte" href="{{ URL::to('redemptions-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} NEFT Redemption" style="display: none;"><i class="material-icons">text_snippet</i></a>
                <a id="gift_status_tempalte" href="{{ URL::to('redemptions-template-gift') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Gift Redemption"><i class="material-icons">text_snippet</i></a>
                @endif
                @if(auth()->user()->can(['redemption_create']))
                <a href="{{ route('redemptions.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Redemption"><i class="material-icons">add_circle</i></a>
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive" id="neft_table" style="display: none;">
            <table id="getNeftTransactionHistory" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.expenses.fields.date') !!}</th>
                <th>Firm Name</th>
                <th>Redeem Mode</th>
                <th>Contact Person</th>
                <th>Parent Name</th>
                <th>Mobile Number</th>
                <th>Point</th>
                <th>Status</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="table-responsive" id="gift_table">
            <table id="getGiftTransactionHistory" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.expenses.fields.date') !!}</th>
                <th>Firm Name</th>
                <th>Redeem Mode</th>
                <th>Contact Person</th>
                <th>Parent Name</th>
                <th>Mobile Number</th>
                <th>Category Name</th>
                <th>Prodcut Name</th>
                <th>Point</th>
                <th>Status</th>
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
      var table = $('#getNeftTransactionHistory').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('redemptions.index') }}",
          'data': function(d) {
            d.branch_id = $('#branch_id').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.redeem_mode = $('#redeem_mode').val(),
              d.status = $('#status').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
          }
        },
        columns: [{
            data: 'action',
            name: 'action',
            "defaultContent": '',
            className: 'td-actions text-center',
            orderable: false,
            searchable: false
          },
          {
            data: 'id',
            name: 'id',
            orderable: false,
            searchable: false
          },
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
            data: 'mode',
            name: 'mode',
            orderable: false,
            searchable: false
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
            data: 'redeem_amount',
            name: 'redeem_amount',
            orderable: false,
            searchable: false
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
      $('#redeem_mode').change(function() {
        var mode = $(this).val();
        if (mode == '1') {
          $("#gift_table").show();
          $("#neft_table").hide();
          // $("#neft_status_form").hide();
          $("#neft_status_tempalte").hide();
          $("#gift_status_tempalte").show();
        } else if (mode == '2') {
          // $("#neft_status_form").show();
          $("#neft_status_tempalte").show();
          $("#gift_status_tempalte").hide();
          $("#gift_table").hide();
          $("#neft_table").show();
        }
      });
      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('body').on('click', '.ChangeStatus', function() {
        var id = $(this).attr("id");
        var active = $(this).data("status");
        var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
        Swal.fire({
          title: 'Please Select Status',
          input: 'select',
          inputOptions: {
            '0': 'Pendding',
            '1': 'Approve',
            '2': 'Reject'
          },
          inputValue: status,
          inputPlaceholder: 'Select Status',
          showCancelButton: true,
          inputValidator: function(value) {
            return new Promise(function(resolve, reject) {
              if (value !== '') {
                resolve();
              } else {
                resolve('You need to select a Status');
              }
            });
          }
        }).then(function(result) {
          if (!result.dismiss) {
            $.ajax({
              url: "{{ url('redemption-change-status') }}",
              type: 'GET',
              data: {
                id: id,
                status: result.value
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
          }
        });
      });

      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('redemptions') }}" + '/' + id,
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

      $('body').on('click', '.successStatus', function() {
        var id = $(this).attr("id");
        var active = $(this).data("status");
        var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
        Swal.fire({
          title: 'Transfer details and Status',
          html: '<select id="statusSelect" class="swal2-select">' +
            '<option value="0">Pendding</option>' +
            '<option value="2">Reject</option>' +
            '<option value="3">Success</option>' +
            '<option value="4">Fail</option>' +
            '</select>' +
            '<input type="text" id="utr_number" name="utr_number" class="swal2-input" placeholder="UTR Number">' +
            '<lable for="tds">TDS</lable> ' +
            ' <input type="number" id="tds" name="tds" value="10" class="swal2-input" placeholder="TDS %">' +
            '<input id="remark" name="remark" class="swal2-input" placeholder="Remark">',
          inputAttributes: {
            autocapitalize: 'off'
          },
          inputValue: status,
          inputPlaceholder: 'Select Status',
          showCancelButton: true,
          inputValidator: function(value) {
            return new Promise(function(resolve, reject) {
              if (value !== '') {
                resolve();
              } else {
                resolve('You need to select a Status');
              }
            });
          }
        }).then(function(result) {
          if (!result.dismiss) {
            var statusValue = $('#statusSelect').val();
            var utr_number = $('#utr_number').val();
            var tds = $('#tds').val();
            var remark = $('#remark').val();
            $.ajax({
              url: "{{ url('neft-redemption-change-status') }}",
              type: 'GET',
              data: {
                id: id,
                status: statusValue,
                utr_number: utr_number,
                tds: tds,
                remark: remark
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
          }
        });

      });

    });
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table2 = $('#getGiftTransactionHistory').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('redemptions.gifttable') }}",
          'data': function(d) {
            d.branch_id = $('#branch_id').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.redeem_mode = $('#redeem_mode').val(),
              d.status = $('#status').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
          }
        },
        columns: [{
            data: 'action',
            name: 'action',
            "defaultContent": '',
            className: 'td-actions text-center',
            orderable: false,
            searchable: false
          },
          {
            data: 'id',
            name: 'id',
            orderable: false,
            searchable: false
          },
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
            data: 'mode',
            name: 'mode',
            orderable: false,
            searchable: false
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
            data: 'product.categories.category_name',
            name: 'product.categories.category_name',
            "defaultContent": ''
          },
          {
            data: 'product.product_name',
            name: 'product.product_name',
            "defaultContent": ''
          },
          {
            data: 'redeem_amount',
            name: 'redeem_amount',
            orderable: false,
            searchable: false
          },
          {
            data: 'status',
            name: 'status',
            "defaultContent": ''
          },
        ]
      });
      $('#branch_id').change(function() {
        table2.draw();
      });
      $('#parent_customer').change(function() {
        table2.draw();
      });
      $('#status').change(function() {
        table2.draw();
      });
      $('#redeem_mode').change(function() {
        var mode = $(this).val();
        if (mode == '1') {
          $("#gift_table").show();
          $("#neft_table").hide();
          // $("#neft_status_form").hide();
          $("#neft_status_tempalte").hide();
          $("#gift_status_tempalte").show();
        } else if (mode == '2') {
          $("#gift_table").hide();
          $("#neft_table").show();
          // $("#neft_status_form").show();
          $("#neft_status_tempalte").show();
          $("#gift_status_tempalte").hide();
        }
      });
      $('#start_date').change(function() {
        table2.draw();
      });
      $('#end_date').change(function() {
        table2.draw();
      });
      $('body').on('click', '.ChangeStatusGift', function() {
        var id = $(this).attr("id");
        var active = $(this).data("status");
        var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
        Swal.fire({
          title: 'Please Select Status',
          input: 'select',
          inputOptions: {
            '0': 'Pendding',
            '1': 'Approve',
            '3': 'Dispatch',
            '2': 'Reject'
          },
          inputValue: status,
          inputPlaceholder: 'Select Status',
          showCancelButton: true,
          inputValidator: function(value) {
            return new Promise(function(resolve, reject) {
              if (value !== '') {
                resolve();
              } else {
                resolve('You need to select a Status');
              }
            });
          },
          html: '<div class="input_section"><input id="dispatch_number" name="dispatch_number" class="swal2-input" placeholder="Dispatch Number"></div>',
        }).then(function(result) {
          if (!result.dismiss) {
            var dispatch_number = $('#dispatch_number').val();
            $.ajax({
              url: "{{ url('redemption-change-status') }}",
              type: 'GET',
              data: {
                id: id,
                status: result.value,
                dispatch_number: dispatch_number
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
                table2.draw();
              },
            });
          }
        });
      });

      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('redemptions') }}" + '/' + id,
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
            table2.draw();
          },
        });
      });

      $('body').on('click', '.deliveredStatus', function() {
        var id = $(this).attr("id");
        var active = $(this).data("status");
        var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
        Swal.fire({
          title: 'Transfer details and Status',
          html: '<div class="input_section"><select id="statusSelect" class="swal2-select">' +
            '<option value="4">Delivered</option>' +
            '</select></div>' +
            '<div class="input_section"><input id="remark" name="remark" class="swal2-input" placeholder="Remark"></div>',
          inputAttributes: {
            autocapitalize: 'off'
          },
          inputPlaceholder: 'Select Status',
          showCancelButton: true,
          inputValidator: function(value) {
            return new Promise(function(resolve, reject) {
              if (value !== '') {
                resolve();
              } else {
                resolve('You need to select a Status');
              }
            });
          }
        }).then(function(result) {
          if (!result.dismiss) {
            var statusValue = $('#statusSelect').val();
            var remark = $('#remark').val();
            $.ajax({
              url: "{{ url('redemption-gift-delivered') }}",
              type: 'GET',
              data: {
                id: id,
                status: statusValue,
                remark: remark
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
                table2.draw();
              },
            });
          }
        });

      });
    });

    $(document).on('click', '.neft_details', function() {
      var details = $(this).data('details');
      Swal.fire({
        title: 'NEFT Transfer details',
        html: '<h6>UTR Number : ' + details.utr_number +
        '</h6><h6>Date : ' + moment(details.updated_at).format("DD MMMM YYYY") + '</h6>' +
        '</h6><h6>TDS : ' + details.tds + '%' +
          '</h6><h6>Remark : ' + details.remark 
      })
    });

    setTimeout(() => {
      $('#parent_customer').select2({
        placeholder: 'Please Select...',
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
       $('#branch_id').select2({
        placeholder: 'Please Select...',
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
    }, 1000);
  </script>
</x-app-layout>