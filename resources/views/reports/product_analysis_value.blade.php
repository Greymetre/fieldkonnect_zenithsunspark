<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Product Analysis Value
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['product_analysis_value_download']))
                <form method="POST" action="{{url('product_analysis_value/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- division filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="division[]" multiple id="ps_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                        <option value="">{!! trans('panel.secondary_dashboard.division') !!}</option>
                        @if(@isset($ps_divisions ))
                        @foreach($ps_divisions as $division)
                        <option value="{!! $division->division !!}">{!! $division->division !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- branch filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="branch_id" id="ps_branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                        @if(@isset($ps_branches ))
                        @foreach($ps_branches as $branch)
                        <option value="{!! $branch->final_branch !!}">{!! $branch->final_branch !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
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
                      <select class="selectpicker" multiple name="month[]" id="ps_month" disabled data-style="select-with-transition" title="Month">
                        <option value="">{!! trans('panel.secondary_dashboard.month') !!}</option>
                        @for ($month = 1; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                          @endfor
                      </select>
                    </div>
                    <!-- dealer/distributors filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="dealer" id="ps_dealer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" selected>{!! trans('panel.secondary_dashboard.dealers_and_distibutors') !!}</option>
                        @if(@isset($ps_dealers ))
                        @foreach($ps_dealers as $dealer)
                        <option value="{!! $dealer->dealer !!}">{!! $dealer->dealer !!}</option>
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
                        <option value="{!! $sales_person->sales_person !!}">{!! $sales_person->sales_person !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- product models filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="product_model" id="ps_product_model" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.product_model') !!}">
                        <option value="" selected>Model Name</option>
                        @if(@isset($ps_product_models ))
                        @foreach($ps_product_models as $product)
                        <option value="{!! $product->model_name !!}">{!! $product->model_name !!}</option>
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
                        <option value="{!! $product->new_group !!}">{!! $product->new_group !!}</option>
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

          <div class="table-responsive">
            <table id="getprimarysales" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>Product</th>
                @if(count($months) > 0)
                @foreach($months as $month)
                <th>{{$month}}</th>
                @endforeach
                @endif
                <th>Total</th>
                <th>Val Wise Cont %</th>
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
          url: "{{ route('product_analysis_value.list') }}",
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
        columns: [{
            data: 'model_name',
            name: 'model_name',
            orderable: false,
            "defaultContent": 'final branch'
          },
          {
            data: 'month1_sale',
            name: 'month1_sale',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'month2_sale',
            name: 'month2_sale',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'month3_sale',
            name: 'month3_sale',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'total_net_amounts',
            name: 'total_net_amounts',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'sale_wise',
            name: 'sale_wise',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          }
        ]
      });
      $('#ps_executive_id').change(function() {
        table.draw();
      });
      $('#ps_division_id').change(function() {
        table.draw();
      });
      $('#ps_branch_id').change(function() {
        table.draw();
      });
      $('#ps_financial_year').change(function() {
        $('#ps_month').prop('disabled', false);
        $('#ps_month').selectpicker('refresh');
        table.draw();
      });
      $('#ps_retailer_id').change(function() {
        table.draw();
      });
      $('#ps_dealer_id').change(function() {
        table.draw();
      });
      $('#ps_product_model').change(function() {
        table.draw();
      });
      $('#ps_new_group').change(function() {
        table.draw();
      });
      $('#ps_month').change(function() {
        table.draw();
      });
    });
  </script>
</x-app-layout>