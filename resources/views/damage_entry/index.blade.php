<x-app-layout>
  <style>
   /* span.select2-dropdown.select2-dropdown--below {
      z-index: 99999 !important;
    }*/

  body  .swal2-popup .swal2-select {
    min-width: 50%;
    max-width: 100%;
    padding: .375em .625em !important;
    color: #545454;
    font-size: 1.125em;
    display: block;
    width: 100%;
    padding: .4375rem 0;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: transparent;
    background-clip: padding-box;
    border: 1px solid #d2d2d2;
    border-radius: 0;
    box-shadow: none;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    margin-top: 8px;
}
body .input_section .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #3c4858 !important;
    line-height: 36px !important;
}
body .swal2-container{
  z-index: 9;
}

.select2-container--default .select2-selection--single .select2-selection__clear {
  
    display: none;
}
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Damage Entries {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['transaction_history_download']))
                <form method="POST" action="{{ URL::to('damage_entries/download') }}" class="form-horizontal">
                  @csrf 
                  <div class="d-flex flex-wrap flex-row"><!-- 
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
                    </div> -->
                <div class="p-2 mr-5" style="width:250px;">
                  <!-- <label for="status">Status</label> -->
                  <div class="input_Section">
                  <select class="selectpicker" name="status" id="status" data-style="select-with-transition" title="Select Status">
                    <option value="">Select Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Approved</option>
                    <option value="2">Rejected</option>
                  </select>
                </div>
                </div>
                <!-- <div class="p-2" style="width:160px;">
                      <label for="scheme_type">Scheme</label>
                      <select class="select2" name="scheme_name" id="scheme_name" data-style="select-with-transition" title="Select Scheme Type">
                        <option value="">Scheme Name</option>
                        @if(@isset($scheme_names ))
                        @foreach($scheme_names as $scheme_name)
                        <option value="{!! $scheme_name->id !!}">{!! $scheme_name->scheme_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>  -->
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Damage Entries"><i class="material-icons">cloud_download</i></button></div>

                </div>
                </form>
                @endif
                <!-- </div> -->
                <!-- <button class="btn btn-just-icon btn-theme" type="button" data-toggle="collapse" data-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2"><i class="material-icons">menu</i></button> -->
                <!-- <div class="row">
                <div class="col"> -->
                <!-- <div class="collapse multi-collapse" id="multiCollapseExample2">
                    <div class="d-flex" style="font-size: 14px;align-items: center;justify-content: space-between;"> -->
                @if(auth()->user()->can(['transaction_history_upload']))
                <!-- <p>Upload Manual Transaction</p>
                      <form action="{{ URL::to('transaction_history_upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="d-flex">
                          <div class="fileinput-new text-center" data-provides="fileinput">
                            <span class="btn btn-just-icon btn-theme btn-file">
                              <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="hidden">
                              <input type="file" name="import_file" required accept=".xls,.xlsx" />
                            </span>
                          </div>
                          <div class="input-group-append">
                            <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Manual Transaction">
                              <i class="material-icons">cloud_upload</i>
                              <div class="ripple-container"></div>
                            </button>
                          </div>
                        </div>
                      </form> -->
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['transaction_history_template']))
                  <!-- <p>{!!  trans('panel.global.template') !!} Manual Transaction</p>
                      <a href="{{ URL::to('transaction_history_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Manual Transaction"><i class="material-icons">text_snippet</i></a> -->
                  @endif
                  @if(auth()->user()->can(['transaction_history_create']))
                  <!-- <p>{!!  trans('panel.global.add') !!} Transaction</p> -->
                  <a href="{{ route('damage_entries.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Damage Entry"><i class="material-icons">add_circle</i></a>
                  <!-- <p>{!!  trans('panel.global.add') !!} Manual Transaction</p>
                      <a href="{{ route('transaction_history.manualcreate') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Manual Transaction"><i class="material-icons">add_circle</i></a> -->
                  @endif
                  <!-- </div>
                  </div> -->
                 
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
            <table id="getDamageEntries" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.expenses.fields.date') !!}</th>
                <th>Firm Name</th>
                <th>CONTACT PERSON</th>
                <th>parent name</th>
                <th>Mobile Number</th>
                <th>COUPON Code(Damage)</th>
                <th>Attechement</th>
                <th>Satuts</th>
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
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getDamageEntries').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('damage_entries.index') }}",
          'data': function(d) {
            d.status = $('#status').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.scheme_name = $('#scheme_name').val(),
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
          },
          {
            data: 'attach',
            name: 'attach',
            orderable: false,
            searchable: false
          },
          {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false
          },
          {
            data: 'remark',
            name: 'remark',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#status').change(function() {
        table.draw();
      });
      $('#parent_customer').change(function() {
        table.draw();
      });
      $('#scheme_name').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
      $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('body').on('click', '.changeStatus', function() {
        setTimeout(() => {
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
        }, 1000);
        var id = $(this).attr("id");
        var active = $(this).data("status");
        var ccode = $(this).data("ccode");
        var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
        Swal.fire({
          title: 'Please Select Status',
          input: 'select',
          inputOptions: {
            '0': 'Pending',
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
          },
          html: '<div class="input_section mb-2"><input type="text" name="coupon_code" id="coupon_code" class="form-control" value="' + ccode + '" placeholder="Coupon Code"/ required></div><div class="input_section mb-2"><select class="form-control " name="product_id" id="product_id" data-style="select-with-transition" title="Select Product"></select></div><div class="input_section"><input id="remark" name="remark" class="form-control" placeholder="Remark"></div><p class="alert alert-danger d-none" id="poperror"></p>',
          preConfirm: function() {
            var status = $('.swal2-select').val();
            var remark = $('#remark').val();
            var coupon_code = $('#coupon_code').val();
            var product_id = $('#product_id').val();
            if (status != 1) {
              return true;
            }
            if (!coupon_code || !product_id) {
              $('#poperror').text('Coupon code and product are both required.');
              $('#poperror').removeClass('d-none');
              return false;
            } else {
              return true;
            }
          }
        }).then(function(result) {
          if (!result.dismiss) {
            var remark = $('#remark').val();
            var status = $('.swal2-select').val();
            var coupon_code = $('#coupon_code').val();
            var product_id = $('#product_id').val();
            $.ajax({
              url: "{{ url('damage-entries-change-status') }}",
              type: 'GET',
              data: {
                id: id,
                status: status,
                remark: remark,
                coupon_code: coupon_code,
                product_id: product_id
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
  </script>
</x-app-layout>