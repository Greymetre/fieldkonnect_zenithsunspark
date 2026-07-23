<x-app-layout>
  <style>
    table tbody tr{
      font-size: 12px !important;
      font-weight: 100 !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Primary Sales
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['primary_sales_download']))
                <form method="POST" action="{{url('primary_sales/download')}}" id="prifilfrm">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- division filter -->
                    <div class="p-2" style="width:200px;">
                      <label for="division">Division</label>
                      <select class="select2" name="division[]" placeholder="Division" multiple id="ps_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                        <option value="" disabled>{!! trans('panel.secondary_dashboard.division') !!}</option>
                        @if(@isset($ps_divisions ))
                        @foreach($ps_divisions as $division)
                        <option value="{!! $division !!}">{!! $division !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @if(!isCustomerUser())
                    <!-- branch filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="branch_id" id="ps_branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                        @if(@isset($ps_branches ))
                        @foreach($ps_branches as $branch)
                        <option value="{!! $branch !!}">{!! $branch !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @endif
                    <!-- financial year filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="financial_year" id="ps_financial_year" required data-style="select-with-transition" title="Year">
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
                      <label for="month">Month </label>
                      <select class="selectpicker" name="month[]" multiple id="ps_month" disabled data-style="select-with-transition" title="Month">
                        <option value="" disabled>{!! trans('panel.secondary_dashboard.month') !!}</option>
                        @for ($month = 1; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                          @endfor
                      </select>
                    </div>
                    @if(!isCustomerUser())
                    <!-- dealer/distributors filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="dealer" id="ps_dealer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.dealers_and_distibutors') !!}</option>
                        @if(@isset($ps_dealers ))
                        @foreach($ps_dealers as $dealer)
                        <option value="{!! $dealer !!}">{!! $dealer !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- sales persons filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="sales_person" id="ps_executive_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.sales_person') !!}</option>
                        @if(@isset($ps_sales_persons ))
                        @foreach($ps_sales_persons as $sales_person)
                        <option value="{!! $sales_person !!}">{!! $sales_person !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @endif
                    <!-- product models filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="product_model" id="ps_product_model" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.product_model') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.product_model') !!}</option>
                        @if(@isset($ps_product_models ))
                        @foreach($ps_product_models as $product)
                        <option value="{!! $product !!}">{!! $product !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- new group name filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="new_group" id="ps_new_group" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.new_group_name') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.new_group_name') !!}</option>
                        @if(@isset($ps_new_group_names ))
                        @foreach($ps_new_group_names as $product)
                        <option value="{!! $product !!}">{!! $product !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Primary Sales">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" type="button" id="reset-filter" title="Reset">
                        <i class="fa fa-refresh"></i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                <div class="row next-btn">
                  @if(auth()->user()->can(['primary_sales_upload']))
                  <form action="{{ URL::to('primary_sales/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group" style="flex-wrap:nowrap;">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input title="Please select a file for upload data" type="file" title="Select file for upload data" name="import_file" style="flex-wrap: nowrap;" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Primary Sales">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  <!-- primary sales import -->
                  @if(auth()->user()->can(['primary_sales_template']))
                  <!-- primary sales template creation -->
                  <a href="{{ URL::to('primary_sales_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Primary Sales"><i class="material-icons">text_snippet</i></a>
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
          <div class="col-md-12">
            <div class="row">
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text">Total Sale Value</h4>
                    <h5 class="card-title" id="total_primary_sale_value">{{number_format($total_sale/100000,2,'.','')}} (Lac)</h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text text-center">Total Quantity</h4>
                    <h5 class="card-title" id="total_primary_qty">{{$total_qty}}</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table id="getprimarysales" class="table table-striped table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>S. No</th>
                <th>{!! trans('panel.primary_dashboard.invoice_no') !!}</th>
                <th>{!! trans('panel.primary_dashboard.invoice_date') !!}</th>
                <th>{!! trans('panel.primary_dashboard.month') !!}</th>
                <th>DIV</th>
                <th>Dealer</th>
                <th>{!! trans('panel.primary_dashboard.city') !!}</th>
                <th>{!! trans('panel.primary_dashboard.state') !!}</th>
                <th>Final Branch</th>
                <th>Sales person</th>
                <th>Emp Code</th>
                <th>Model Name</th>
                <th>Product Name</th>
                <th>Qty.</th>
                <th>Rate</th>
                <th>Net Amount</th>
                <th>CGST Amt</th>
                <th>SGST Amt</th>
                <th>IGST Amt</th>
                <th>Total</th>
                <th>Store Name</th>
                <th>Group</th>
                <th>Branch</th>
                <th>New Group Name</th>
                <th>Product ID</th>
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
      var table = $('#getprimarysales').DataTable({
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
          url: "{{ route('primary_dashboard.sales.list') }}",
          data: function(d) {
            d.executive_id = $('#ps_executive_id').val(),
              d.division_id = $('#ps_division_id').val(),
              d.branch_id = $('#ps_branch_id').val(),
              d.financial_year = $('#ps_financial_year').val(),
              d.month = $('#ps_month').val(),
              d.retailer_id = $('#ps_retailer_id').val(),
              d.dealer_id = $('#ps_dealer_id').val(),
              d.product_model = $('#ps_product_model').val(),
              d.new_group = $('#ps_new_group').val(),
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
            data: 'invoiceno',
            name: 'invoiceno',
            orderable: false,
            "defaultContent": 'orderno'
          },
          {
            data: 'invoice_date',
            name: 'invoice_date',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'month',
            name: 'month',
            orderable: false,
            searchable: false,
            "defaultContent": 'month'
          },
          {
            data: 'division',
            name: 'division',
            orderable: false,
            searchable: false,
            "defaultContent": 'division'
          },
          {
            data: 'dealer',
            name: 'dealer',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'city',
            name: 'city',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'state',
            name: 'state',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'final_branch',
            name: 'final_branch',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'sales_person',
            name: 'sales_person',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'emp_code',
            name: 'emp_code',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'model_name',
            name: 'model_name',
            "defaultContent": 'final branch',
            orderable: true,
            searchable: true,
          },
          {
            data: 'product_name',
            name: 'product_name',
            "defaultContent": 'final branch',
            orderable: true,
            searchable: true,
          },
          {
            data: 'quantity',
            name: 'quantity',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'rate',
            name: 'rate',
            "defaultContent": 'product name',
            orderable: true,
            searchable: true,
          },
          {
            data: 'net_amount',
            name: 'net_amount',
            "defaultContent": 'total_qty',
            orderable: true,
            searchable: true,
          },
          {
            data: 'cgst_amount',
            name: 'cgst_amount',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'sgst_amount',
            name: 'sgst_amount',
            "defaultContent": 'tax amount',
            orderable: true,
            searchable: true,
          },
          {
            data: 'igst_amount',
            name: 'igst_amount',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'total_amount',
            name: 'total_amount',
            "defaultContent": 'total',
            orderable: true,
            searchable: true,
          },
          {
            data: 'store_name',
            name: 'store_name',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'new_group',
            name: 'new_group',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'branch',
            name: 'branch',
            "defaultContent": 'branch',
            orderable: true,
            searchable: true,
          },
          {
            data: 'new_group_name',
            name: 'new_group_name',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'product_id',
            name: 'product_id',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
        ]
      });
      $('#ps_executive_id').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_division_id').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_branch_id').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_financial_year').change(function() {
        $('#ps_month').prop('disabled', false);
        $('#ps_month').selectpicker('refresh');
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_retailer_id').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_dealer_id').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_product_model').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_new_group').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
      $('#ps_month').change(function() {
        table.draw();
        $.ajax({
          url: "{{ route('getPrimaryTotal') }}",
          data: {
            executive_id: $('#ps_executive_id').val(),
            division_id: $('#ps_division_id').val(),
            branch_id: $('#ps_branch_id').val(),
            financial_year: $('#ps_financial_year').val(),
            month: $('#ps_month').val(),
            retailer_id: $('#ps_retailer_id').val(),
            dealer_id: $('#ps_dealer_id').val(),
            product_model: $('#ps_product_model').val(),
            new_group: $('#ps_new_group').val(),
            search: $('input[type="search"]').val()
          },
          method: "GET",
          success: function(data) {
            $("#total_primary_sale_value").html(data.total_sale);
            $("#total_primary_qty").html(data.total_qty);
          }
        });
      });
    });

    $('#reset-filter').on('click', function(){
      $('#prifilfrm').find('input:text, input:password, input:file, select, textarea').val('');
      $('#prifilfrm').find('select').change();
    })
  </script>
</x-app-layout>