<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Dealer Growth List
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['dealer_growth_download']))
                <form method="POST" action="{{url('dealer_growth/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- division filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="division[]" multiple id="ps_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                        <option value="" disabled>{!! trans('panel.secondary_dashboard.division') !!}</option>
                        @if(@isset($ps_divisions ))
                        @foreach($ps_divisions as $division)
                        <option value="{!! $division !!}">{!! $division !!}</option>
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
                        <option value="{!! $branch !!}">{!! $branch !!}</option>
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
                    <!-- Growth filter-->
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="remark" id="remark" data-style="select-with-transition" title="Remark">
                        <option value="">Please Select</option>
                        <option value="1">INACTIVE DEALER</option>
                        <option value="2">LY -NO SALE</option>
                        <option value="3">DE-GROWTH</option>
                        <option value="4">GROWTH DEALER</option>
                      </select>
                    </div>
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
                <th>Dealer Name</th>
                <th>City</th>
                <th>Final Branch Name</th>
                <th>LYTD</th>
                <th>CYTD</th>
                <th>Growth</th>
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
          url: "{{ route('dealer_growth.list') }}",
          data: function(d) {
            d.executive_id = $('#ps_executive_id').val(),
              d.division_id = $('#ps_division_id').val(),
              d.branch_id = $('#ps_branch_id').val(),
              d.financial_year = $('#ps_financial_year').val(),
              d.month = $('#ps_month').val(),
              d.retailer_id = $('#ps_retailer_id').val(),
              d.dealer_id = $('#ps_dealer_id').val(),
              d.product_model = $('#ps_product_model').val(),
              d.remark = $('#remark').val(),
              d.new_group = $('#ps_new_group').val(),
              d.search = $('input[type="search"]').val()
          }
        },
        columns: [
          {
            data: 'dealer',
            name: 'dealer',
            orderable: false,
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
            data: 'final_branch',
            name: 'final_branch',
            orderable: false,
            "defaultContent": 'final branch'
          },
          {
            data: 'ly_total_net_amounts',
            name: 'ly_total_net_amounts',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'cy_total_net_amounts',
            name: 'cy_total_net_amounts',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'growth',
            name: 'growth',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          }
        ]
      });
      $('#ps_executive_id').change(function() {
        table.draw();
      });
      $('#remark').change(function() {
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