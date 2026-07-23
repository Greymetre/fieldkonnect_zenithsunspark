<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.order.title_singular') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">

                @if(auth()->user()->can(['order_download']))
                <form method="GET" action="{{ URL::to('orders-download') }}">
                  <div class="d-flex flex-wrap flex-row">
                  @if(!isCustomerUser())
                    <div class="p-2" style="width:190px;">
                      <select class="select2" name="user_id" id="user_id">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  @endif
                  <div class="p-2" style="width:190px;">
                      <select class="select2" name="dividion_id" id="dividion_id" required>
                        <option value="">Select Division</option>
                        @foreach($divisions as $division)
                        <option value="{{$division->id}}">{{$division->category_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width:190px;">
                      <select class="select2" name="retailers_id" id="retailers_id" title="Select Retailers">
                        <option value="">Select Retailers</option>
                        @foreach($retailers as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'retailers_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width:190px;">
                      <select class="select2" name="customer_type_id" id="customer_type_id" title="Select Retailers">
                        <option value="">Customer Type</option>
                        @foreach($customer_types as $customer_type)
                        <option value="{!! $customer_type['id'] !!}" {{ old( 'customer_type_id') == $customer_type->id ? 'selected' : '' }}>{!! $customer_type['customertype_name'] !!}</option>
                        @endforeach
                      </select>
                    </div>
                    @if(!isCustomerUser())
                    <div class="p-2" style="width:190px;">
                      <select class="select2" name="distributor_id" id="distributor_id" title="Select Distributor">
                        <option value="">Select Distributor</option>
                        @foreach($distributors as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'distributor_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                      </select>
                    </div>
                    @endif
                    <div class="p-2" style="width:190px;">
                      <select class="selectpicker" name="pending_status" id="pending_status" data-style="select-with-transition">
                        <option value="">Select Status</option>
                        <option value="1">Dispatch</option>
                        <option value="2">Partial Dispatch</option>
                        <option value="0">Pending</option>
                      </select>
                    </div>

                    <div class="p-2"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.order.title') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif

                <div class="next-btn">

                  @if(auth()->user()->can(['order_upload']))
                  <form action="{{ URL::to('orders-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.order.title') !!}">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif

                  <!-- @if(auth()->user()->can(['order_download']))
                  <a href="{{ URL::to('orders-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.order.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif -->

                  @if(auth()->user()->can(['order_template']))
                  <a href="{{ URL::to('orders-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.order.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['order_create']))
                  <a href="{{ route('orders.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.order.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
            <table id="getorder" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.buyer_name') !!}</th>
                <th>{!! trans('panel.global.seller_name') !!}</th>
                <th>{!! trans('panel.order.orderno') !!}</th>
                <th>{!! trans('panel.order.order_date') !!}</th>
                <th>{!! trans('panel.order.completed_date') !!}</th>
                <th>{!! trans('panel.order.total_gst') !!}</th>
                <!-- <th>{!! trans('panel.product.fields.suc-del') !!}</th> -->
                <th>{!! trans('panel.order.sub_total') !!}</th>
                <th>{!! trans('panel.order.grand_total') !!}</th>
                <th>{!! trans('panel.order.status_id') !!}</th>
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
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getorder').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          url: "{{ route('orders.index') }}",
          data: function(d) {
            d.retailers_id = $('#retailers_id').val();
            d.distributor_id = $('#distributor_id').val();
            d.customer_type_id = $('#customer_type_id').val();
            d.pending_status = $('#pending_status').val();
            d.startdate = $('#start_date').val();
            d.enddate = $('#end_date').val();
            d.user_id = $('#user_id').val();
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
            data: 'buyers.name',
            name: 'buyers.name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'sellers.name',
            name: 'sellers.name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'orderno',
            name: 'orderno',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'order_date',
            name: 'order_date',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'completed_date',
            name: 'completed_date',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'total_gst',
            name: 'total_gst',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          // {data: 'suc_del', name: 'suc_del',"defaultContent": ''},
          {
            data: 'sub_total',
            name: 'sub_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'grand_total',
            name: 'grand_total',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'statusname.status_name',
            name: 'statusname.status_name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'createdbyname.name',
            name: 'createdbyname.name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
        ]
      });

      $('#retailers_id').change(function() {
        table.draw();
      });
       $('#user_id').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
      $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });

      $('#distributor_id').change(function() {
        table.draw();
      });
      $('#customer_type_id').change(function() {
        table.draw();
      });
      $('#pending_status').change(function() {
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
          url: "{{ url('orders-active') }}",
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
          url: "{{ url('orders') }}" + '/' + id,
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