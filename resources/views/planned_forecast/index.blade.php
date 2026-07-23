<x-app-layout>
  <style>
    .table-input::placeholder {
      color: rgba(0, 0, 0, 0.5);
      /* Light greyish-white for better visibility */
      opacity: 1;
      /* Ensures visibility in all browsers */
    }

    .table-input:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .hover-effect {
      cursor: pointer;
      transition: transform 0.2s ease-in-out;
    }

    .hover-effect:hover {
      transform: scale(1.05);
      /* Slightly increase size on hover */
    }

    .table-input {
      width: 100%;
      padding: 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
      outline: none;
      background: white;
      width: 150px;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Planned S&OP Forecast List
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  <div class="p-2" style="width:160px;">
                    <select class="select2 mr-2" name="division_id" id="division_id" data-style="select-with-transition" title="Select">
                      <option value="">Division</option>
                      @foreach($divisions as $division)
                      <option value="{{ $division->id }}">
                        {{ $division->category_name ?? '' }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </span>
          </h4>
          <h3 class="text-info" id="total_forecast_p" style="font-size: 18px;">Total Forecast value: <span id="total_forecast">{{round($total_forecast/100000, 2)}} Lakhs</span></h3>
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
          @if(session()->has('message_error'))
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session()->get('message_error') }}
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
            <table id="getplannedsop" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <tr>
                  <th></th>
                  <th><input type="text" class="form-control table-input" name="branch_name" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input datepicker" id="start_month"
                      name="planning_month" placeholder="S&OP Month"
                      autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="group_name" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="product_name" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="plan_next_month" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="plan_next_month" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="order_id" placeholder="Search..." autocomplete="off"></th>
                <!--   <th><input type="text" class="form-control table-input" name="category_name" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="product_code" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="product_description" placeholder="Search..." autocomplete="off"></th>
                  <th>
                    <div style="width:150px"></div>
                  </th>
                  <th><input type="text" class="form-control table-input" name="budget_for_month" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="last_month_sale" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="last_three_month_avg" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="last_year_month_sale" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="sku_unit_price" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="s_op_val" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="top_sku" placeholder="Search..." autocomplete="off"></th> -->

                  <!-- <th><input type="text" class="form-control table-input" name="created_by" placeholder="Search..." autocomplete="off"></th> -->
                </tr>
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th>Branch Name</th>
                  <th>Month</th>
                  <th>Group Name</th>
                  <th>Item Name</th>
                  <th>Forecast Qty</th>
                  <th>Forecast Value</th>
                  <th>Order Id</th>
                </tr>
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
      var showCheckbox = {{ auth()->user()->hasRole('superadmin') ? 'true' : 'false' }};
      console.log(showCheckbox);
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getplannedsop').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          type: 'POST',
          url: "{{ route('plannedSopList') }}",
          data: function(d) {
            d._token = token,
              d.order_id = $('input[name="order_id"]').val();
            d.branch_name = $('input[name="branch_name"]').val();
            d.category_name = $('input[name="category_name"]').val();
            d.division_id = $('select[name="division_id"]').val(),
              d.group_name = $('input[name="group_name"]').val();
            d.product_name = $('input[name="product_name"]').val();
            d.product_code = $('input[name="product_code"]').val();
            d.description = $('input[name="product_description"]').val();
            d.plan_next_month = $('input[name="plan_next_month"]').val();
            d.budget_for_month = $('input[name="budget_for_month"]').val();
            d.last_month_sale = $('input[name="last_month_sale"]').val();
            d.last_three_month_avg = $('input[name="last_three_month_avg"]').val();
            d.last_year_month_sale = $('input[name="last_year_month_sale"]').val();
            d.sku_unit_price = $('input[name="sku_unit_price"]').val();
            d.s_op_val = $('input[name="s_op_val"]').val();
            d.top_sku = $('input[name="top_sku"]').val();
            d.status = $('select[name="status"]').val();
            d.created_by = $('input[name="created_by"]').val();
            d.planning_month = $('input[name="planning_month"]').val();
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          // @if(auth()->user()->hasRole('superadmin'))
          //   {
          //     data: 'checkbox',
          //     name: 'checkbox',
          //     orderable: false,
          //     "defaultContent": ''
          //   },
          // @endif
          // {
          //   data: 'action',
          //   name: 'action',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'status',
          //   name: 'status',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          {
            data: 'get_branch.branch_name',
            name: 'get_branch.branch_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'planning_month',
            name: 'planning_month',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'get_product.subcategories.subcategory_name',
            name: 'get_product.subcategories.subcategory_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'get_product.product_name',
            name: 'get_product.product_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'plan_next_month',
            name: 'plan_next_month',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'plan_next_month_value',
            name: 'plan_next_month_value',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'order_id',
            name: 'order_id',
            orderable: false,
            "defaultContent": ''
          },
          // {
          //   data: 'get_product.categories.category_name',
          //   name: 'get_product.categories.category_name',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'get_product.product_code',
          //   name: 'get_product.product_code',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'get_product.description',
          //   name: 'get_product.description',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'opening_stock',
          //   name: 'opening_stock',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          
          // {
          //   data: 'budget_for_month',
          //   name: 'budget_for_month',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'last_month_sale',
          //   name: 'last_month_sale',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'last_three_month_avg',
          //   name: 'last_three_month_avg',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'last_year_month_sale',
          //   name: 'last_year_month_sale',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'sku_unit_price',
          //   name: 'sku_unit_price',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 's_op_val',
          //   name: 's_op_val',
          //   orderable: false,
          //   "defaultContent": ''
          // },
          // {
          //   data: 'top_sku',
          //   name: 'top_sku',
          //   orderable: false,
          //   "defaultContent": ''
          // },

          // {
          //   data: 'created_by',
          //   name: 'created_by',
          //   orderable: false,
          //   "defaultContent": ''
          // }
        ],      
      });

      $('.table-input').on('keyup change', function() {
        table.draw();
      });

      $('#division_id , #financial_year').on('change', function() {
        table.draw();
      })
    });

    function getForcastValue(){
      var division_id = $('#division_id').val();
      $.ajax({
        url: "{{ route('getplannedForCast') }}",
        dataType: "json",
        type: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          division_id: division_id,
        },
       success: function(res) {
          let value = (res.total_value / 100000).toFixed(2);
          $('#total_forecast').html(value + ' Lakhs');
        }
      });
    }

    $('#division_id').on('change', function() {
      getForcastValue();
    });
  </script>
</x-app-layout>