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

          <h4 class="card-title ">Planned S&OP List
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  @if(auth()->user()->hasRole('Sub_Admin') || auth()->user()->hasRole('PUMPCH') || auth()->user()->hasRole('superadmin'))
                  <div class="btn-group multi-a-r d-none">
                    <button class="btn btn-just-icon btn-warning mr-2"
                      title="Verify SOP"
                      id="verify_sop"
                      data-toggle="tooltip">
                      <i class="material-icons">check_circle</i>
                    </button>
                  </div>
                  @if(auth()->user()->hasRole('superadmin'))
                  <div class="btn-group multi-a-r d-none">
                    <button class="btn btn-just-icon btn-success mr-2"
                      title="Approve SOP"
                      id="approved_sop"
                      data-toggle="tooltip">
                      <i class="material-icons">done</i>
                    </button>
                  </div>
                  @endif
                  @endif
                  <div class="p-2" style="width:160px;">
                    <select class="select2 mr-2" name="division_id" id="division_id" data-style="select-with-transition" title="Select">
                      <option value="">Division</option>
                      @foreach($divisions as $division)
                      <option value="{{ $division->id }}" {{ $division->id == 1 ? 'selected' : '' }}>
                        {{ $division->category_name ?? '' }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="p-2" style="width:160px;">
                    <select class="select2" name="financial_year" id="financial_year" required data-style="select-with-transition" title="Year">
                      <option value="" disabled selected>{!! trans('panel.secondary_dashboard.year') !!}</option>
                      @foreach($years as $year)
                      @php
                      $startYear = $year - 1;
                      $endYear = $year;
                      @endphp
                      <option value="{!!$startYear!!}-{!!$endYear!!}" {{ $year-1 == date('Y') ? 'selected' : ''}}>{!! $startYear!!} - {!! $endYear !!}</option>
                      @endforeach
                    </select>
                  </div>


                  @if(auth()->user()->can(['sop_upload']))
                  <form action="{{ URL::to('planned-sop-import') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Opening Stock">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['sop_create']))
                  <a href="{{ route('planned-sop.create') }}" class="btn btn-just-icon btn-theme" title="Add Planned SOP"><i class="material-icons">add_circle</i></a>
                  @endif
                  @if(auth()->user()->can(['master_sop_download']))
                  <button class="btn btn-just-icon btn-theme mr-2" id="sale_sop_download" type="button" title="Master  S&OP Export"><i class="material-icons">cloud_download</i></button>
                  @endif
                  @if(auth()->user()->can(['sop_download']))
                  <button class="btn btn-just-icon btn-warning mr-2" id="button_download" type="button" title="Sales SOP Export"><i class="material-icons">cloud_download</i></button>
                  @endif
                  <a href="{{ URL::to('planned-sop-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Planned SOP"><i class="material-icons">text_snippet</i></a>

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
                  @if(auth()->user()->hasRole('superadmin'))
                    <th>
                      <div style="width:50px"></div>
                    </th>
                  @endif
                  <th>
                    <div style="width:150px"></div>
                  </th>
                  <th>
                    <select class="form-control table-input" name="status">
                      <option value="">Select</option>
                      <option value="0">Cancel</option>
                      <option value="1">Open</option>
                      <option value="2">Verify</option>
                      <option value="3">Aprroved</option>
                    </select>
                  </th>
                  <th><input type="text" class="form-control table-input" name="branch_name" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input datepicker" id="start_month"
                      name="planning_month" placeholder="S&OP Month"
                      autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="group_name" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="product_name" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="plan_next_month" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="order_id" placeholder="Search..." autocomplete="off"></th>
<!--                   <th><input type="text" class="form-control table-input" name="category_name" placeholder="Search..." autocomplete="off"></th>
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
                  <th><input type="text" class="form-control table-input" name="s_op_val" placeholder="Search..." autocomplete="off"></th> -->
                  <!-- <th><input type="text" class="form-control table-input" name="top_sku" placeholder="Search..." autocomplete="off"></th> -->

                  <th><input type="text" class="form-control table-input" name="created_by" placeholder="Search..." autocomplete="off"></th>
                </tr>
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  @if(auth()->user()->hasRole('superadmin'))
                    <th>#</th>
                  @endif
                  <th>{!! trans('panel.global.action') !!}</th>
                  <th>Status</th>
                  <th>Branch Name</th>
                  <th>Month</th>
                  <th>Group Name</th>
                  <th>Item Name</th>
                  <th>Forecast Qty</th>
                  <th>Order Id</th>
               <!--    <th>Division</th>
                  <th>Product Code</th>
                  <th>Product Desc.</th>
                  <th>Opening stock as on 1st (Qty)</th>
                  <th>Budget for the month (Qty.)</th>
                  <th>LM Sale (Qty.)</th>
                  <th>L3M Avg Sale (Qty.)</th>
                  <th>LY same month sale (Qty.)</th>
                  <th>SKU Unit Price</th>
                  <th>S&OP Val_L (Unit Price *Qty.)</th>
                  <th>TOP 20 SKU for the Branch (*)</th> -->
                  <th>Created By</th>
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
          @if(auth()->user()->hasRole('superadmin'))
            {
              data: 'checkbox',
              name: 'checkbox',
              orderable: false,
              "defaultContent": ''
            },
          @endif
          {
            data: 'action',
            name: 'action',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'status',
            name: 'status',
            orderable: false,
            "defaultContent": ''
          },
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

          {
            data: 'created_by',
            name: 'created_by',
            orderable: false,
            "defaultContent": ''
          }
        ],      
      });

      $('.table-input').on('keyup change', function() {
        table.draw();
      });

      $('#division_id , #financial_year').on('change', function() {
        table.draw();
      })

      $('#button_download').on('click', function() {
        let form = $('<form>', {
          method: 'GET',
          action: "{{ route('sop_download') }}"
        });

        form.append($('<input>', {
          type: 'hidden',
          name: '_token',
          value: $('input[name="_token"]').val()
        }));
        form.append($('<input>', {
          type: 'hidden',
          name: 'division_id',
          value: $('select[name="division_id"]').val()
        }));
        form.append($('<input>', {
          type: 'hidden',
          name: 'financial_year',
          value: $('select[name="financial_year"]').val()
        }));

        // Collect all filter inputs (both text and select fields)
        $('.table-input, .table-select').each(function() {
          let inputName = $(this).attr('name');
          let inputValue = $(this).val();
          form.append($('<input>', {
            type: 'hidden',
            name: inputName,
            value: inputValue
          }));
        });
        form.append($('<input>', {
          type: 'hidden',
          name: 'planning_month',
          value: $('input[id="start_month"]').val()
        }));
        $('body').append(form);
        form.submit();
      });

      $('#sale_sop_download').on('click', function() {
        let form = $('<form>', {
          method: 'GET',
          action: "{{ route('sale_sop_download') }}"
        });

        form.append($('<input>', {
          type: 'hidden',
          name: '_token',
          value: $('input[name="_token"]').val()
        }));
        form.append($('<input>', {
          type: 'hidden',
          name: 'division_id',
          value: $('select[name="division_id"]').val()
        }));
        form.append($('<input>', {
          type: 'hidden',
          name: 'financial_year',
          value: $('select[name="financial_year"]').val()
        }));

        // Collect all filter inputs (both text and select fields)
        $('.table-input, .table-select').each(function() {
          let inputName = $(this).attr('name');
          let inputValue = $(this).val();
          form.append($('<input>', {
            type: 'hidden',
            name: inputName,
            value: inputValue
          }));
        });
        form.append($('<input>', {
          type: 'hidden',
          name: 'planning_month',
          value: $('input[id="start_month"]').val()
        }));
        $('body').append(form);
        form.submit();
      });

      $(document).on('click', '#verify_sop', function() {
        const selectedValues = [];
        $('.row-checkbox:checked').each(function() {
          selectedValues.push($(this).val());
        });
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: "{{ route('planned-sop-multistatus-change') }}",
          type: 'POST',
          data: {
            _token: token,
            ids: selectedValues,
            value: 2,
          },
          success: function(data) {
            $('.message').empty();
            if (data.status == true) {
              if (data.update == true) {
                $('.alert').show();
                $('.alert').addClass("alert-success");
                $('.message').append(data.message);
              }
              if (data.update == false) {
                $('.alert').show();
                $('.alert').addClass("alert-danger");
                $('.message').append("No changes found");
              }
            }
            $(".multi-a-r").addClass('d-none');
            table.draw();
          },
        });
      })

      $(document).on('click', '#approved_sop', function() {
        const selectedValues = [];
        $('.row-checkbox:checked').each(function() {
          selectedValues.push($(this).val());
        });
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: "{{ route('planned-sop-multistatus-change') }}",
          type: 'POST',
          data: {
            _token: token,
            ids: selectedValues,
            value: 3,
          },
          success: function(data) {
            $('.message').empty();
            if (data.status == true) {
              if (data.update == true) {
                $('.alert').show();
                $('.alert').addClass("alert-success");
                $('.message').append(data.message);
              }
              if (data.update == false) {
                $('.alert').show();
                $('.alert').addClass("alert-danger");
                $('.message').append("No changes found");
              }
            }
            $(".multi-a-r").addClass('d-none');
            table.draw();
          },
        });
      })


      $(document).on('click', '.update-sop', function(e) {
        e.preventDefault(); // Prevent default button action

        let id = $(this).data('id'); // Get SOP ID
        var route = $('.update-form-' + id).attr('action');
        Swal.fire({
          title: "Are you sure?",
          text: "To cancel this Planned S&OP.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Yes, Cancel it!"
        }).then((result) => {
          if (result.value == true) {
            updateSOP(route, 'PUT', 0);
            // $('.update-form-' + id).submit(); // Submit the correct form
          }
        });
      });


      $(document).on('click', '.verify-sop', function(e) {
        e.preventDefault(); // Prevent default button action

        let id = $(this).data('id'); // Get SOP ID
        var route = $('.verify-form-' + id).attr('action');
        console.log(route);
        Swal.fire({
          title: "Are you sure?",
          text: "To Verify this Planned S&OP.",
          icon: "success",
          showCancelButton: true,
          confirmButtonColor: "#28a745", // Green color for confirm button
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Yes, Verify it!"
        }).then((result) => {
          if (result.value == true) {
            updateSOP(route, 'PUT', 2);
            // $('.verify-form-' + id).submit(); // Submit the correct form
          }
        });
      });

      $(document).on('click', '.approve-sop', function(e) {
        e.preventDefault(); // Prevent default button action

        let id = $(this).data('id'); // Get SOP ID
        var route = $('.approve-form-' + id).attr('action');
        Swal.fire({
          title: "Are you sure?",
          text: "To Approve this Planned S&OP.",
          icon: "success",
          showCancelButton: true,
          confirmButtonColor: "#28a745", // Green color for confirm button
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Yes, Approve it!"
        }).then((result) => {
          if (result.value == true) {
            updateSOP(route, 'PUT', 3);
          }
        });
      });


      $(document).on('click', '.delete-sop', function(e) {
        e.preventDefault(); // Prevent default button action

        let id = $(this).data('id'); // Get SOP ID
        var route = $('.delete-form-' + id).attr('action');
        Swal.fire({
          title: "Are you sure?",
          text: "To delete this Planned S&OP.",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.value == true) {
            updateSOP(route, 'DELETE');
            // $('.delete-form-' + id).submit(); // Submit the correct form
          }
        });
      });


      function updateSOP(url, method, value = '') {
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: url,
          type: method,
          data: {
            _token: token,
            status: value,
          },
          success: function(data) {
            $('.message').empty();
            if (data.status == true) {
              Swal.fire('success', data.message);
              table.draw();
            } else {
              Swal.fire('success', data.message);
            }
          },
        });
      }


      $(document).on('click', '.row-checkbox', function() {
        const selectedValues = [];
        $('.row-checkbox:checked').each(function() {
          selectedValues.push($(this).val());
        });
        if (selectedValues.length > 0) {
          $(".multi-a-r").removeClass('d-none');
        } else {
          $(".multi-a-r").addClass('d-none');
        }
      });

      $('#start_month').datepicker({
        dateFormat: 'M yy',
      });


    });

    $('#division_id').on('change', function() {
      var division_id = $('#division_id').val();
      if(division_id == '1'){
        $("#total_forecast_p").slideDown(500);
      }else{
        $("#total_forecast_p").slideUp(500);
      }
    }).trigger('change');
  </script>
</x-app-layout>