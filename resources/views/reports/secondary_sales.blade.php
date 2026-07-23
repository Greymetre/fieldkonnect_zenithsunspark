<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Secondary Sales
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['checkin_download']))
                <form method="GET" action="{{ URL::to('customers-download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <!-- division filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="division" id="ss_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.division') !!}</option>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division->id !!}">{!! $division->division_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- branch filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="branch_id" id="ss_branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- financial year filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="financial_year" id="ss_financial_year" required data-style="select-with-transition" title="Year">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.year') !!}</option>
                        @foreach($years as $year)
                        @php
                        $startYear = $year - 1;
                        $endYear = $year;
                        @endphp
                        <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
                        @endforeach
                      </select>
                    </div>
                    <!-- month filter-->
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="ss_month" disabled id="ss_month" data-style="select-with-transition" title="Month">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.month') !!}</option>
                        @for ($month = 4; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                          @endfor
                          @for ($month = 1; $month <= 3; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                            @endfor
                      </select>
                    </div>
                    <!-- retailer filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="user" id="ss_retailer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.retailer_name') !!}</option>
                        @if(@isset($retailers ))
                        @foreach($retailers as $retailer)
                        <option value="{!! $retailer->id !!}">{!! $retailer->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- dealer/distributors filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="dealer" id="ss_dealer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.dealers_and_distibutors') !!}</option>
                        @if(@isset($dealers_and_distibutors ))
                        @foreach($dealers_and_distibutors as $dealer)
                        <option value="{!! $dealer->id !!}">{!! $dealer->first_name !!} {!! $dealer->last_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- sales persons filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="sales_person" id="ss_executive_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.sales_person') !!}</option>
                        @if(@isset($sales_persons ))
                        @foreach($sales_persons as $sales_person)
                        <option value="{!! $sales_person->id !!}">{!! $sales_person->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- product models filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="product_model" id="ss_product_model" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.product_model') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.product_model') !!}</option>
                        @if(@isset($products ))
                        @foreach($products as $product)
                        <option value="{!! $product->id !!}">{!! $product->model_no !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- new group name filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="new_group" id="ss_new_group" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.new_group_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.new_group_name') !!}</option>
                        @if(@isset($uniqueProductsNewGroup ))
                        @foreach($uniqueProductsNewGroup as $product)
                        <option value="{!! $product->id !!}">{!! $product->new_group !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </form>
                @endif
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
          <div class="col-md-12">
            <div class="row">
              <div class="col-sm">
                <div class="card  color_bg text-center">
                  <div class="card-body">
                    <h4 class="card-title" id="total_order_value">0</h4>
                    <p class="card-text">Total Order Value</p>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card  color_bg text-center">
                  <div class="card-body">
                    <h4 class="card-title" id="total_order_qty">0</h4>
                    <p class="card-text text-center">Total Order Quantity</p>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card color_bg text-center">
                  <div class="card-body">
                    <h4 class="card-title" id="total_order">0</h4>
                    <p class="card-text text-center">Total Order</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table id="getsecondarysales" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>S. No</th>
                <th>{!! trans('panel.secondary_dashboard.invoice_no') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.invoice_date') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.month') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.division') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.party_name') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.city') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.state') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.distributor_dealer_name') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.distributor_dealer_city') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.final_branch') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.sales_person') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.product_name') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.quantity') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.rate') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.net_amount') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.tax_percentage') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.gst_amount') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.total') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.store_name') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.branch') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.new_group_name') !!}</th>
                <th>{!! trans('panel.secondary_dashboard.product_id') !!}</th>
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
      getTotalOrderValueQty();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getsecondarysales').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [{
          className: 'control',
          orderable: false,
          targets: -1
        }],
        "order": [
          [0, 'desc']
        ],
        "retrieve": true,
        ajax: {
          url: "{{ route('secondary_dashboard.sales.list') }}",
          data: function(d) {
            d.executive_id = $('#ss_executive_id').val(),
              d.division_id = $('#ss_division_id').val(),
              d.branch_id = $('#ss_branch_id').val(),
              d.financial_year = $('#ss_financial_year').val(),
              d.month = $('#ss_month').val(),
              d.retailer_id = $('#ss_retailer_id').val(),
              d.dealer_id = $('#ss_dealer_id').val(),
              d.product_model = $('#ss_product_model').val(),
              d.new_group = $('#ss_new_group').val(),
              d.search = $('input[type="search"]').val()
          }
        },
        columns: [
          // {
          //   data: 'action',
          //   name: 'action',
          //   "defaultContent": ''
          // },
          {
            data: 'id',
            name: 'id',
            orderable: true,
            searchable: true,
            "defaultContent": 'orderno'
          },
          {
            data: 'orders.orderno',
            name: 'orders.orderno',
            orderable: false,
            searchable: false,
            "defaultContent": 'orderno'
          },
          {
            data: 'order_date',
            name: 'order_date',
            orderable: false,
            searchable: false,
            "defaultContent": 'test'
          },
          {
            data: 'month',
            name: 'month',
            orderable: false,
            searchable: false,
            "defaultContent": 'month'
          },
          {
            data: 'orders.getuserdetails.getdivision.division_name',
            name: 'orders.getuserdetails.getdivision.division_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'orders.buyers.name',
            name: 'orders.buyers.name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'orders.buyers.customeraddress.cityname.city_name',
            name: 'orders.buyers.customeraddress.cityname.city_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'orders.buyers.customeraddress.statename.state_name',
            name: 'orders.buyers.customeraddress.statename.state_name',
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'orders.sellers.name',
            name: 'orders.sellers.name',
            "defaultContent": ''
          },
          {
            data: 'orders.sellers.customeraddress.cityname.city_name',
            name: 'orders.sellers.customeraddress.cityname.city_name',
            "defaultContent": ''
          },
          {
            data: 'orders.getuserdetails.getbranch.branch_name',
            name: 'orders.getuserdetails.getbranch.branch_name',
            "defaultContent": ''
          },
          {
            data: 'orders.createdbyname.name',
            name: 'orders.createdbyname.name',
            "defaultContent": ''
          },
          {
            data: 'products.model_no',
            name: 'products.model_no',
            "defaultContent": 'product name'
          },
          {
            data: 'quantity',
            name: 'quantity',
            "defaultContent": 'total_qty'
          },
          {
            data: 'products.productpriceinfo.mrp',
            name: 'products.productpriceinfo.mrp',
            "defaultContent": 'mrpsss'
          },
          {
            data: 'line_total',
            name: 'line_total',
            "defaultContent": ''
          },
          {
            data: 'products.productpriceinfo.gst',
            name: 'products.productpriceinfo.gst',
            "defaultContent": 'tax amount'
          },
          {
            data: 'gst_amount',
            name: 'gst_amount',
            "defaultContent": 'gst_amount'
          },
          {
            data: 'total_amount',
            name: 'total_amount',
            "defaultContent": 'total_amount'
          },
          {
            data: '',
            name: '',
            "defaultContent": ''
          },
          {
            data: 'orders.getuserdetails.getbranch.branch_code',
            name: 'orders.getuserdetails.getbranch.branch_code',
            "defaultContent": ''
          },
          {
            data: 'products.new_group',
            name: 'products.new_group',
            "defaultContent": ''
          },
          {
            data: 'products.id',
            name: 'products.id',
            "defaultContent": ''
          },
        ]
      });

      $('#ss_executive_id').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_division_id').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_branch_id').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_financial_year').change(function() {
        $('#ss_month').prop('disabled', false);
        $('#ss_month').selectpicker('refresh');
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_retailer_id').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_dealer_id').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_product_model').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_new_group').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });
      $('#ss_month').change(function() {
        table.draw();
        getTotalOrderValueQty();
      });


      $('body').on('click', '.customerActive', function() {
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
          url: "{{ url('customers-active') }}",
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
          url: "{{ url('customers') }}" + '/' + id,
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
      setTimeout(() => {
        var $customerSelect = $('#dealer_id').select2({
          placeholder: 'Select Parent',
          allowClear: true,
          ajax: {
            url: "{{ route('getDealerDisDataSelect') }}",
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
        });
      }, 1500);

    });

    function getTotalOrderValueQty() {

      var executive_id = $('#executive_id').val();
      var division_id = $('#division_id').val();
      var branch_id = $('#branch_id').val();
      var financial_year = $('#financial_year').val();
      var month = $('#month').val();
      var retailer_id = $('#retailer_id').val();
      var dealer_id = $('#dealer_id').val();
      var product_model = $('#product_model').val();
      var new_group = $('#new_group').val();

      var token = $("meta[name='csrf-token']").attr("content");
      $.ajax({
        url: "{{ url('secondary_dashboard/total_order_value') }}",
        type: 'POST',
        data: {
          _token: token,
          executive_id: executive_id,
          division_id: division_id,
          branch_id: branch_id,
          financial_year: financial_year,
          month: month,
          retailer_id: retailer_id,
          dealer_id: dealer_id,
          product_model: product_model,
          new_group: new_group,
        },
        success: function(response) {
          var totalOrderValue = parseFloat(response.total_order_value);
          var totalOrderValueInLacs = (totalOrderValue / 100000).toFixed(2);

          $('#total_order_value').text(totalOrderValueInLacs + ' Lacs');
          $('#total_order_qty').text(response.total_order_qty);
          $('#total_order').text(response.total_order);
        },
      });
    }
  </script>
</x-app-layout>