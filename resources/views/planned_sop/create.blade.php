<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} Planned S&OP
            <span class="pull-right">
              <div class="btn-group">
                <!-- @if(auth()->user()->can(['product_access'])) -->
                <a href="{{ url('planned-sop') }}" class="btn btn-just-icon btn-theme" title="Planned S&OP List"><i class="material-icons">next_plan</i></a>
                <!-- @endif -->
              </div>
            </span>
          </h4>
          <h3 class="text-info">Total Forecast value: <span id="total_forecast">0.00</span></h3>
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
          {!! Form::model($plannedsop,[
          'route' => $plannedsop->exists ? ['planned-sop.update', encrypt($plannedsop->id) ] : 'planned-sop.store',
          'method' => $plannedsop->exists ? 'PUT' : 'POST',
          'id' => 'createsopForm',
          'files'=>true
          ]) !!}

          <div class="row">
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Month<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control datepicker" id="start_month"
                    name="planning_month" placeholder="S&OP Month"
                    autocomplete="off" readonly required
                    value="{{ old('planning_month', now()->addMonth()->format('F Y')) }}">
                  @if ($errors->has('planning_month'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('planning_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Branch Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="branch_id" id="branch_id" required>
                    <option value=''>Select Branch</option>
                    @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->branch_name}}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('product_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Division<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="product_division" id="product_division" onchange="getProductlist()" required>
                    <option value=''>Select Division</option>
                    @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->category_name}}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('product_division'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_division') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>

            @if(isset(auth()->user()->roles[0]->name) && auth()->user()->roles[0]->name == 'superadmin')
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Show Only</label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="view_only[]" id="view_only" multiple data-placeholder="Select an option">
                    <option value=''></option> <!-- Empty option needed for placeholder -->
                    @foreach($main_divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->division_name }}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('view_only'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('view_only') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            @endif
          </div>

          <div class="row">
            <div class="container-fluid mt-5 d-flex justify-content-center w-100">
              <div class="table-responsive" style="max-height: 500px; overflow-y: auto; width: 100%;">
                <table class="table kvcodes-dynamic-rows-example" id="tab_logic" style="min-width: 1800px;">
                  <thead>
                    <tr class="text-white">
                      <th class="text-center" style="width: 50px !important;">#</th>
                      <th class="text-center">
                        <div style="width: 20px !important;">-</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 350px !important;">Products<div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">Product Group<div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">Product Code</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">Product Description<div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">Opening stock as on 1st (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">S&OP Plan for Next running month (M+1) (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">Budget for the month (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">LM Sale (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">L3M Avg Sale (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">LY same month sale (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">SKU Unit Price</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">S&OP Val_L (Unit Price *Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">TOP 20 SKU for the Branch (*)</div>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="sopTableBody">

                  </tbody>
                </table>

                <table class="table kvcodes-dynamic-rows-example" id="tab_logic1" style="min-width: 1800px; display: none;">
                  <thead>
                    <tr class="text-white" id="tableHeadings">
                      <th class="text-center" style="width: 50px !important;">#</th>
                      <th class="text-center">
                        <div style="width: 20px !important;">-</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 350px !important;">Product Group<div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">Product<div>
                      </th>

                      <th class="text-center">
                        <div style="width: 200px !important;">Opening stock as on 1st (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">S&OP Plan for Next running month (M+1) (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">Budget for the month (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">LM Sale (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">L3M Avg Sale (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">LY same month sale (Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">SKU Unit Price</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">S&OP Val_L (Unit Price *Qty.)</div>
                      </th>
                      <th class="text-center">
                        <div style="width: 200px !important;">TOP 20 SKU for the Branch (*)</div>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="sopTableBody1">

                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="row clearfix">
            <div class="col-md-12">
              <table>
                <tbody>
                  <tr>
                    <td class="td-actions text-center">
                      <a href="#" title="" class="btn btn-success btn-xs add-rows" onclick="getProductlist()"> <i class="fa fa-plus"></i> </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="pull-right col-md-12">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      var $table = $('table.kvcodes-dynamic-rows-example'),
        counter = parseInt($('#tab_logic tr:last').attr('value')) || 0;

      $('a.add-rows').click(function(event) {
        var branch_id = $("#branch_id").val();
        var category = $('#product_division').val();
        if (branch_id == null || branch_id == '') {
          alert('Select Branch First');
          return;
        }
        if (category == null || category == '') {
          alert('Select Division First');
          return;
        }
        getProductlist();
        var category = $('#product_division').val();
        if (category == 1) {
          event.preventDefault();
          counter++;

          var newRow = `
              <tr id='addr${counter}' value='${counter}'>
                <td class="row-count">${counter}</td>
                <td class="td-actions text-center">
                    <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-minus"></i></button>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <select class="form-select select2 product_group_name" name="product_group_name[]" required>
                                <option value=''>Select Product Group</option>
                            </select>
                            @if ($errors->has('product_group_name'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('product_group_name') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <select class="form-select select2 product_1" name="product_id[]" required>
                                <option value=''>Select Product</option>
                            </select>
                            @if ($errors->has('product_id'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('product_id') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
                @for ($i = 1; $i <= 12; $i++)
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <input type="text" class="form-control year_month_{{ $i }}" name="year_month_{{ $i }}[]" readonly>
                        </div>
                    </div>
                </td>
                @endfor
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <input type="text" class="form-control min" name="min[]" readonly>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <input type="text" class="form-control max" name="max[]" readonly>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <input type="text" class="form-control avg" name="avg[]" readonly>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <input type="text" class="form-control open_order_qty" name="open_order_qty[]" readonly>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                         <input type="text" class="form-control open_order_value" name="open_order_value[]" readonly>    
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                         <input type="text" class="form-control last_three_month_avg" name="last_three_month_avg[]" readonly>    
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                         <input type="text" class="form-control last_year_this_month" name="last_year_this_month[]" readonly>    
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                         <input type="text" class="form-control last_twelve_month_avg" name="last_twelve_month_avg[]" readonly>    
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                         <input type="text" class="form-control forecast_reccomendation" name="forecast_reccomendation[]" readonly>    
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <input type="hidden" class="form-control for_production_value" name="for_production_value[]" required readonly>
                            <input type="hidden" class="form-control for_production_qty" name="for_production_qty[]" required readonly>
                            <input type="hidden" class="form-control opening_stock" name="opening_stock[]" readonly>
                            <input type="hidden" class="form-control opening_stock_value" name="opening_stock_value[]" readonly>
                            <input type="hidden" class="form-control price" name="price[]" required>
                            <input type="text" class="form-control plan_next_month_1" name="plan_next_month[]" required>
                            @if ($errors->has('plan_next_month'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('plan_next_month') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input_section">
                        <div class="form-group has-default bmd-form-group">
                            <input type="text" class="form-control plan_next_month_value" name="plan_next_month_value[]" required readonly>
                            @if ($errors->has('plan_next_month_value'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('plan_next_month_value') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
              
            </tr>`;

          $('#sopTableBody1').append(newRow);

          // Initialize Select2 for new row
          $('.select2').select2({
            minimumResultsForSearch: 10
          });
        } else {
          event.preventDefault();
          counter++;

          var newRow = `
              <tr id='addr${counter}' value="${counter}">
                  <td class="row-count">${counter}</td>
                  <td class="td-actions text-center">
                      <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-minus"></i></button>
                  </td>
                  <td>
                      <div class="input_section">
                          <div class="form-group has-default bmd-form-group">
                              <select class="form-select select2 product" name="product_id[]" required>
                                  <option value=''>Select Product</option>
                              </select>
                          </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group">
                             <input type="text" class="form-control product_group_name" name="product_group_name[]" readonly>
                          </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control product_code" name="product_code[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control product_description" name="product_description[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control opening_stock" name="opening_stock[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control plan_next_month" name="plan_next_month[]" required> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control budget_for_month" name="budget_for_month[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control last_month_sale" name="last_month_sale[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control last_three_month_avg" name="last_three_month_avg[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control last_year_month_sale" name="last_year_month_sale[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control sku_unit_price" name="sku_unit_price[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control s_op_val" name="s_op_val[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control top_sku" name="top_sku[]" readonly> </div>
                      </div>
                  </td>
              </tr>`;

          $('#sopTableBody').append(newRow);

          // Initialize Select2 for new row
          $('.select2').select2({
            minimumResultsForSearch: 10
          });
        }

        var tableContainer = $('.table-responsive');
        tableContainer.animate({
          scrollTop: tableContainer[0].scrollHeight
        }, 500);

        updateRowNumbers();
      });

      $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        counter--;

        updateRowNumbers();
      });

      function updateRowNumbers() {
        $('#sopTableBody tr').each(function(index) {
          $(this).attr('value', index + 1);
          $(this).find('.row-count').text(index + 1);
        });
        counter = $('#sopTableBody tr').length;
      }
    });
    $(document).ready(function() {
      $('#product_division').change(function() {
        let selectedValue = $(this).val();

        if (selectedValue == 1) {
          resetTable('#tab_logic'); // Reset tab_logic before hiding
          $('#tab_logic').hide();
          $('#tab_logic1').show();
        } else {
          resetTable('#tab_logic1'); // Reset tab_logic1 before hiding
          $('#tab_logic1').hide();
          $('#tab_logic').show();
        }
      });

      function resetTable(tableId) {
        let table = $(tableId);
        table.find('tbody').empty(); // Remove all rows inside tbody
      }
      getProductlist();
      $("#start_month").datepicker({
        dateFormat: "MM yy", // Show only month and year
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        closeText: "Select", // Custom text for closing
        minDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 1), // Start from next month
        yearRange: new Date().getFullYear() + ":" + (new Date().getFullYear() + 1), // Limit year range to current year +1
        maxDate: new Date(new Date().getFullYear() + 1, 11, 1), // Allow till December of next year
        onClose: function(dateText, inst) {
          var month = parseInt($("#ui-datepicker-div .ui-datepicker-month option:selected").val());
          var year = parseInt($("#ui-datepicker-div .ui-datepicker-year option:selected").val());

          var selectedDate = new Date(year, month, 1);
          $(this).val($.datepicker.formatDate('MM yy', selectedDate));
        }
      }).focus(function() {
        $(".ui-datepicker-calendar").hide(); // Hide date picker
      });


      $(document).on("change", ".product_group_name , #branch_id", function() {
        var $row = $(this).closest("tr"); // Get the row of the changed select box
        var product_subcategory = $(this).val();
        var branch_id = $("#branch_id").val();
        var category = $('#product_division').val();
        if (category != 1) {
          return;
        }
        if (product_subcategory != null && product_subcategory != '' && branch_id != null && branch_id != '') {
          $('#tab_logic1 tr:last').find(".product_1").empty();
          $.ajax({
            url: "{{ url('getProductInfoListBySubcategory') }}",
            dataType: "json",
            type: "POST",
            data: {
              _token: "{{ csrf_token() }}",
              product_subcategory: product_subcategory,
              branch_id: branch_id
            },
            success: function(res) {
              let selectedValues = [];

              $('.product_1').each(function() {
                let values = $(this).val();
                if (values) {
                  selectedValues = selectedValues.concat(values);
                }
              });
              var table = document.getElementById(tab_logic1),
                rIndex;
              if (res) {

                $('#tab_logic1 tr:last').find(".product_1").html('<option value="">Select Product</option>');
                var data = res.map(item => ({
                  id: item.id,
                  text: item.product_name,
                  disabled: selectedValues.includes(item.id.toString())
                }));

                $('.product_1').select2({
                  data: data
                });
              } else {
                $('#tab_logic1 tr:last').find(".product_1").empty();
              }
            }
          });
        } else {
          // Clear only the fields in this row
          $row.find(".product_description, .product_division, .product_code, .opening_stock, .product_group_name, .budget_for_month, .top_sku, .sku_unit_price").val('').trigger('change');
          $row.find(".product_group_name, .product_description, .sku_unit_price, .opening_stock, .product_division, .product_code, .budget_for_month, .top_sku").prop('readonly', true);
        }
      });

      $(document).on("change", ".product , .product_1", function() {
        var category = $('#product_division').val();
        var branch_id = $("#branch_id").val();
        if (branch_id == '' || branch_id == null) {
          alert("Please select a Branch .");
          $('.product').val('');
          return;
        }
        if (category != 1) {
          var $row = $(this).closest("tr"); // Get the row of the changed select box
          var product_id = $(this).val();

          if (product_id != null && product_id != '') {
            $.ajax({
              url: "{{ url('getProductInfo') }}",
              dataType: "json",
              type: "POST",
              data: {
                _token: "{{ csrf_token() }}",
                product_id: product_id,
                branch_id: branch_id
              },
              success: function(res) {
                $row.find(".product_description").val(res.product_description);
                $row.find(".product_division").val(res.categories.category_name);
                $row.find(".product_code").val(res.product_code);
                $row.find(".product_group_name").val(res.subcategories.subcategory_name);
                $row.find(".budget_for_month").val(res.budget_for_month);
                $row.find(".top_sku").val(res.top_sku);
                $row.find(".sku_unit_price").val(res.price).trigger('change');
                $row.find(".opening_stock").val(res.opening_stock);

                // Set read-only fields
                $row.find(".product_group_name, .product_description, .sku_unit_price, .opening_stock, .product_division, .product_code, .budget_for_month, .top_sku").prop('readonly', true);

                // Fetch and update sales data only for this row
                getSaledata($row);
              }
            });
          } else {
            // Clear only the fields in this row
            $row.find(".product_description, .product_division, .product_code, .opening_stock, .product_group_name, .budget_for_month, .top_sku, .sku_unit_price").val('').trigger('change');
            $row.find(".product_group_name, .product_description, .sku_unit_price, .opening_stock, .product_division, .product_code, .budget_for_month, .top_sku").prop('readonly', true);
          }
        } else {
          var $row = $(this).closest("tr"); // Get the row of the changed select box
          var product_id = $(this).val();
          var date = $("#start_month").val() ?? '';
          if (product_id != null && product_id != '') {
            $.ajax({
              url: "{{ url('getFullDetailsOfProduct') }}",
              dataType: "json",
              type: "POST",
              data: {
                _token: "{{ csrf_token() }}",
                product_id: product_id,
                branch_id: branch_id,
                date: date,
              },
              success: function(res) {
                if (Object.keys(res.sales_by_month).length > 0) {
                  var sales = res.sales_by_month; // Object of month-year keys
                  var quantities = []; // Array to store total_qty values
                  var i = 1; // Counter for field assignment
                  var total = 0;

                  $.each(sales, function(month, total_qty) {
                    // Set the corresponding input field value
                    $row.find(".year_month_" + i).val(total_qty);

                    // Store the total_qty for min, max, avg calculations
                    quantities.push(total_qty);
                    total += Number(total_qty);
                    i++;
                  });

                  if (quantities.length > 0) {
                    let nonZeroQuantities = quantities.filter(qty => qty > 0);

                    let minQty = nonZeroQuantities.length > 0 ? Math.min(...nonZeroQuantities) : 0;
                    let maxQty = Math.max(...quantities);
                    let avgQty = (total / 12).toFixed(0);

                    // Set min, max, avg values in respective fields
                    $row.find(".min").val(minQty);
                    $row.find(".max").val(maxQty);
                    $row.find(".avg").val(avgQty);
                  }
                } else {
                  console.log("No sales data available.");
                }
                $row.find(".last_three_month_avg").val(parseInt(res.threeMonthAvg ?? 0));
                $row.find(".last_year_this_month").val(parseInt(res.sameMonthLastYearSales ?? 0));
                $row.find(".last_twelve_month_avg").val(parseInt(res.twelveMonthAvg ?? 0));
                $row.find(".forecast_reccomendation").val(parseInt(res.forecast_reccomendation ?? 0));
                let openingStock = parseInt(res.opening_stock?.opening_stocks ?? 0);
                let price = parseInt(res.product?.productdetails[0].price ?? 0);
                let open_oder_qty = parseInt(res.branchOprningQuantity?.plan_next_month ?? 0);
                let new_price = 0;
                if (price) {
                  new_price = (price / 100) * 41;
                  price = price - new_price;
                }
                let openingStockValue = openingStock * price;
                let openderValue = open_oder_qty * price;
                $row.find(".opening_stock").val(openingStock);
                $row.find('.opening_stock_value').val(openingStockValue);
                $row.find('.open_order_qty').val(open_oder_qty);
                $row.find('.open_order_value').val(openderValue);
                $row.find('.price').val(price);
              }
            });
          }
        }
      });

      $(document).on('change', '.sku_unit_price, .plan_next_month', function() {
        var $row = $(this).closest('tr'); // Adjust selector if needed

        var sku_unit_price = $row.find('.sku_unit_price').val();
        var plan_next_month = $row.find('.plan_next_month').val();

        if (sku_unit_price !== '' && plan_next_month !== '') {
          var total = parseFloat(sku_unit_price) * parseFloat(plan_next_month);
          $row.find('.s_op_val').val(total);
        } else {
          $row.find('.s_op_val').val('');
        }
      });

      $(document).on('input', '.plan_next_month_1', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        var $row = $(this).closest('tr'); // Get the parent row

        var planNextMonth = $row.find('.plan_next_month_1').val() || 0; // Ensure number
        var opening_stock = $row.find('.opening_stock').val() || 0;
        var open_order_qty = $row.find('.open_order_qty').val() || 0;
        // (Forecast_Qty - (Opening_Stock_Qty - Open_Order_Qty)
        var forproduction_qty = planNextMonth - (opening_stock - open_order_qty);
        var price = $row.find('.price').val() || 0; // Ensure number

        var planNextMonthValue = planNextMonth * price; // Calculate total
        var forproduction_value = Math.abs(forproduction_qty) * price;
        // Set the computed value in the correct field
        $row.find('.plan_next_month_value').val(planNextMonthValue.toFixed(2));
        $row.find('.for_production_qty').val(forproduction_qty);
        $row.find('.for_production_value').val(forproduction_value.toFixed(2));
      });

      $(document).on('change', '.plan_next_month_value', function() {
        //Calculate sum all plan next month value
        var sum = 0;
        $('.plan_next_month_value').each(function() {
          sum += parseFloat($(this).val());
        });
        let formattedValue = new Intl.NumberFormat('en-IN', {
          maximumFractionDigits: 2
        }).format(sum / 100000);
        if (formattedValue == 'NaN') {
          formattedValue = '0.00';
        }
        $('#total_forecast').html(formattedValue + " Lakh");
      });




      function generateFinancialYearMonths(selectedMonth) {
        let monthYear = selectedMonth.split(' '); // Example: ['April', '2025']
        let monthName = monthYear[0]; // Extract the month name
        let selectedYear = parseInt(monthYear[1]); // Extract the year

        // Define months of the financial year (April to March)
        let months = [
          "April", "May", "June", "July", "August", "September", "October",
          "November", "December", "January", "February", "March"
        ];

        // Find the index of the selected month
        let monthIndex = months.indexOf(monthName);

        // Determine the previous financial year
        let previousStartYear = (monthIndex >= 0 && monthIndex <= 8) ? selectedYear - 1 : selectedYear - 2;
        let previousEndYear = previousStartYear + 1;

        // Generate table headers for the previous financial year
        let previousYearHeadings = months.map((month, index) => {
          let year = (index < 9) ? previousStartYear : previousEndYear; // First 9 months in previousStartYear, last 3 in previousEndYear
          return `<th class="text-center"><div style="width: 100px !important;">${month} ${year}</div></th>`;
        });

        // Inject the table headers into the table
        $("#tableHeadings").html(`
              <th class="text-center"><div style="width: 20px !important;">#</div></th>
              <th class="text-center"><div style="width: 20px !important;">-</div></th>
              <th class="text-center"><div style="width: 350px !important;">Product Group</div></th>
              <th class="text-center"><div style="width: 200px !important;">Product</div></th>
              ${previousYearHeadings.join("")}
              <th class="text-center"><div style="width: 100px !important;">Min</div></th>
              <th class="text-center"><div style="width: 100px !important;">Max</div></th>
              <th class="text-center"><div style="width: 100px !important;">Avg</div></th>
              <th class="text-center"><div style="width: 200px !important;">Open Order qty (Last Month)</div></th>
              <th class="text-center"><div style="width: 200px !important;">Open Order Value (Last Month)</div></th>
              <th class="text-center"><div style="width: 200px !important;">Last three month avg (qty)</div></th>
              <th class="text-center"><div style="width: 200px !important;">Last year this month (qty)</div></th>
              <th class="text-center"><div style="width: 200px !important;">12 month avg (qty)</div></th>
              <th class="text-center"><div style="width: 200px !important;">Forecast Reccomendation (qty)</div></th>
              <th class="text-center"><div style="width: 200px !important;">Forecast qty (Sales plan) </div></th>
              <th class="text-center"><div style="width: 200px !important;">Forecast Value (Sales plan) </div></th>

          `);

        /*
            <th class="text-center"><div style="width: 200px !important;">Forecast qty (Sales plan) </div></th>
            <th class="text-center"><div style="width: 200px !important;">Forecast Value (Sales plan) </div></th>
            <th class="text-center"><div style="width: 200px !important;">Branch Stock qty</div></th>
            <th class="text-center"><div style="width: 200px !important;">Branch Stock Value</div></th>
            <th class="text-center"><div style="width: 200px !important;">Open Order qty (Prod.)</div></th>
            <th class="text-center"><div style="width: 200px !important;">Open Order Value (Prod.)</div></th>
            <th class="text-center"><div style="width: 200px !important;">For Prodcution qty</div></th>
            <th class="text-center"><div style="width: 200px !important;">For Prodcution Value </div></th>
        */
      }

      // Trigger generation on page load
      generateFinancialYearMonths($("#start_month").val());

      // If the datepicker changes (assuming you allow changes later)
      $("#start_month").change(function() {
        generateFinancialYearMonths($(this).val());
      });
    });

    $(document).on('change', '#branch_id', function() {
      var category = $('#product_division').val();
      let table = $('#tab_logic1');
      table.find('tbody tr').each(function() {
        $(this).find('input, select, textarea').val(''); // Clear values in inputs, selects, and textareas
      });
      getProductlist();
    })

    function getProductlist() {
      var lastValue = $('.product_group_name').last().val();
      var category = $('#product_division').val();
      var branch_id = $("#branch_id").val();
      if (category == 1) {

        if (branch_id == '' || branch_id == null) {
          alert("Please select a Branch .");
          $('.product').val('');
          return;
        }
        $.ajax({
          url: "{{ url('getSubCategory') }}",
          dataType: "json",
          type: "POST",
          data: {
            _token: "{{csrf_token()}}",
            category: category,
            branch_id: branch_id
          },
          success: function(res) {
            var table = document.getElementById(tab_logic1),
              rIndex;
            $('#tab_logic1 tr:last').find(".product_group_name").empty();
            if (res) {
              $('#tab_logic1 tr:last').find(".product_group_name").append('<option value="">Select Product Group</option>');
              $.each(res, function(key, value) {
                var isSelected = (value.id == lastValue) ? 'selected' : '';
                $('#tab_logic1 tr:last').find('.product_group_name').append(
                  '<option value="' + value.id + '" ' + isSelected + '>' + value.subcategory_name + '</option>'
                );
                if (isSelected == 'selected') {
                  $('#tab_logic1 tr:last').find('.product_group_name').trigger('change');
                }
              });

            } else {
              row.find(".product_group_name").empty();
            }
          }
        });
      } else {
        $.ajax({
          url: "{{ url('getProductData') }}",
          dataType: "json",
          type: "POST",
          data: {
            _token: "{{csrf_token()}}",
            category: category,
            branch_id: branch_id
          },
          success: function(res) {
            var table = document.getElementById(tab_logic),
              rIndex;
            if (res) {
              // row.find(".product").empty();
              $('#tab_logic tr:last').find(".product").empty();
              $('#tab_logic tr:last').find(".product").append('<option value="">Select Product</option>');
              $.each(res, function(key, value) {
                var isSelected = (value.id == lastValue) ? 'selected' : '';
                if (value.product_code) {
                  var productcode = value.product_code
                } else {
                  var productcode = '';
                }

                $('#tab_logic tr:last').find('.product').append('<option value="' + value.id + ' ' + isSelected + '">' + value.product_name + ' ' + value.product_code + '</option>');

              });
            } else {
              row.find(".product").empty();
            }
          }
        });
      }

    }


    function getSaledata($row) {
      var product_id = $row.find(".product").val(); // Get product ID from the row
      var date = $("#start_month").val(); // Get product ID from the row
      var branch_id = $("#branch_id").val(); // Get product ID from the row

      if (product_id != null && product_id != '' && branch_id != '' && branch_id != null) {
        $.ajax({
          url: "{{ url('getSaledata') }}",
          dataType: "json",
          type: "POST",
          data: {
            _token: "{{ csrf_token() }}",
            product_id: product_id,
            date: date,
            branch_id: branch_id
          },
          success: function(res) {
            $row.find(".last_month_sale").val(res.last_month_sale).prop('readonly', true);
            $row.find(".last_three_month_avg").val(res.last_three_month_avg).prop('readonly', true);
            $row.find(".last_year_month_sale").val(res.last_year_same_month).prop('readonly', true);
          }
        });
      } else {
        $row.find(".last_month_sale, .last_three_month_avg, .last_year_month_sale").val('').prop('readonly', true);
      }
    }
  </script>
</x-app-layout>