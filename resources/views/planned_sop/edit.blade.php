<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.edit') }} Planned S&OP
            <span class="pull-right">
              <div class="btn-group">
                <!-- @if(auth()->user()->can(['product_access'])) -->
                <a href="{{ url('planned-sop') }}" class="btn btn-just-icon btn-theme" title="Planned S&OP"><i class="material-icons">next_plan</i></a>
                <!-- @endif -->
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
          {!! Form::model($plannedsop,[
          'route' => $plannedsop->exists ? ['planned-sop.update', encrypt($plannedsop->id) ] : 'planned-sop.store',
          'method' => $plannedsop->exists ? 'PUT' : 'POST',
          'id' => 'editesopForm',
          'files'=>true
          ]) !!}

          <div class="row">

          </div>
        <div class="row">
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Month<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                 <input type="text" class="form-control datepicker" id="start_month" 
                         name="planning_month" placeholder="S&OP Month" 
                         autocomplete="off" readonly required
                         value="{{ old('planning_month', \Carbon\Carbon::parse($plannedsop->planning_month)->format('F Y')) }}">
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
                  <select class="form-select select2" name="branch_id" id="branch_id" >
                         <option value=''>Select Branch</option>
                         @foreach($branches as $branch)
                              <option value="{{ $branch->id }}" {{isset($plannedsop->branch_id) && $plannedsop->branch_id == $branch->id ? "Selected" : '' }}>{{ $branch->branch_name}}</option>
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
                              <option value="{{ $division->id }}"  {{isset($plannedsop->getproduct->category_id) && $plannedsop->getproduct->category_id == $division->id ? "Selected" : '' }}>{{ $division->category_name}}</option>
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
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="product_id" id="product_id" >
                         <option value=''>Select Product</option>
                         @foreach($products as $product)
                              <option value="{{ $product->id }}" {{isset($plannedsop->product_id) && $plannedsop->product_id == $product->id ? "Selected" : '' }}>{{ $product->product_name}}</option>
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
                <label class="col-form-label">Product Group Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_group_name" id="product_group_name" value="{{$plannedsop->getproduct->subcategories->subcategory_name ?? ''}}" readonly>
                  @if ($errors->has('product_group_name'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_group_name') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Code<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_code" id="product_code" value="{{$plannedsop->getproduct->product_code ?? ''}}" readonly>
                  @if ($errors->has('product_code'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_code') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Description<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_description" id="product_description" value="{{$plannedsop->getproduct->description ?? ''}}" readonly>
                  @if ($errors->has('product_description'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_description') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Opening stock as on 1st (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="opening_stock" id="opening_stock" value="" readonly>
                  @if ($errors->has('opening_stock'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('opening_stock') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Plan for Next running month (M+1) (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="plan_next_month" id="plan_next_month" value="{{$plannedsop->plan_next_month ?? ''}}">
                  @if ($errors->has('plan_next_month'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('plan_next_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Budget for the month (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="budget_for_month" id="budget_for_month" value="{{$plannedsop->budget_for_month ?? ''}}" readonly>
                  @if ($errors->has('budget_for_month'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('budget_for_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">LM Sale (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="last_month_sale" id="last_month_sale" value="{{$plannedsop->last_month_sale ?? ''}}" readonly>
                  @if ($errors->has('last_month_sale'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('last_month_sale') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">L3M Avg Sale (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="last_three_month_avg" id="last_three_month_avg" value="{{$plannedsop->last_three_month_avg ?? ''}}" readonly>
                  @if ($errors->has('last_three_month_avg'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('last_three_month_avg') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">LY same month sale (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="last_year_month_sale" id="last_year_month_sale" value="{{$plannedsop->last_year_month_sale ?? ''}}" readonly>
                  @if ($errors->has('last_year_month_sale'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('last_year_month_sale') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
             <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">SKU Unit Price<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="sku_unit_price" id="sku_unit_price" value="{{$plannedsop->sku_unit_price ?? ''}}" readonly>
                  @if ($errors->has('sku_unit_price'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('sku_unit_price') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Val_L (Unit Price *Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="s_op_val" id="s_op_val" value="{{$plannedsop->s_op_val ?? ''}}" readonly>
                  @if ($errors->has('s_op_val'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('s_op_val') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">TOP 20 SKU for the Branch (*)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="top_sku" id="top_sku" value="{{$plannedsop->top_sku ?? ''}}" readonly>
                  @if ($errors->has('top_sku'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('top_sku') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div> 
          <div class="pull-right col-md-12">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
<script>

  $(document).ready(function (){
    var product_id = "{{$plannedsop->product_id ?? ''}}"
    getProductlist(product_id);
    $("#start_month").datepicker({
        dateFormat: "MM yy", // Show only month and year
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        closeText: "Select", // Custom text for closing
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 1), // Allow till next month
        onClose: function(dateText, inst) {
            var month = parseInt($("#ui-datepicker-div .ui-datepicker-month option:selected").val());
            var year = parseInt($("#ui-datepicker-div .ui-datepicker-year option:selected").val());

            var selectedDate = new Date(year, month, 1);
            $(this).val($.datepicker.formatDate('MM yy', selectedDate));
        }
    }).focus(function () {
        $(".ui-datepicker-calendar").hide(); // Hide date picker
    });

    $('#editesopForm').validate({
        rules:{
          product_id:
          {
            required:true,
          },
          branch_id:
          {
            required:true,
          },
          start_month:{
            required : true
          },
          plan_next_month : {
            required : true,
            number : true
          },

        },
        highlight: function(element) {
          $(element).closest('.error').css("display", "none");
        },
        unhighlight: function(element) {
          $(element).closest('.error').css("display", "block");
        },
        messages:{
          name:{
            minlength: "Please enter a valid Award Name.",
            required: "Please enter Award Name",
          },
          description:{
            required: "Please enter Description",
          },
        }
      });

    $(document).on("change", "#product_id", function() {
        var branch_id = $("#branch_id").val(); 
        if(branch_id == '' || branch_id == null ){
            alert("Please select a Branch .");
            $(this).val('');
            return ;
        }
        var product_id = $(this).val();
        if (product_id != null && product_id != '') {
            $.ajax({
                url: "{{ url('getProductInfo') }}",
                dataType: "json",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: product_id
                },
                success: function(res) {
                    $('#plan_next_month').val(0).trigger('change');
                    $("#product_description").val(res.product_description);
                    $("#product_division").val(res.categories.category_name);
                    $("#product_code").val(res.product_code);
                    $("#product_group_name").val(res.subcategories.subcategory_name);
                    $("#budget_for_month").val(res.budget_for_month);
                    $("#top_sku").val(res.top_sku);
                    $("#sku_unit_price").val(res.price).trigger('change');
                    $("#opening_stock").val(res.opening_stock.opening_stocks);
                    
                    // Set read-only fields

                    $("#product_group_name, #product_description, #sku_unit_price, #opening_stock, #product_division, #product_code, #budget_for_month, #top_sku").prop('readonly', true);
                    getSaledata();
                }
            });
        } else {
            // Clear only the fields in this row
            $("#product_description, #product_division, #product_code, #product_group_name, #opening_stock, #budget_for_month, #top_sku, #sku_unit_price").val('').trigger('change');
            $("#product_group_name, #product_description, #sku_unit_price, #product_division, #product_code, #opening_stock, #budget_for_month, #top_sku").prop('readonly', true);
        }
    });
   
   
    $(document).on('change', '#sku_unit_price, #plan_next_month', function () {
        var sku_unit_price = $('#sku_unit_price').val();
        var plan_next_month = $('#plan_next_month').val();

        if (sku_unit_price !== '' && plan_next_month !== '') {
            var total = parseFloat(sku_unit_price) * parseFloat(plan_next_month);
            $('#s_op_val').val(total);
        } else {
            $('#s_op_val').val('');
        }
    });
  });

  function getProductlist(product_id = '') {
     var category = $('#product_division').val();

     $.ajax({
        url: "{{ url('getProductData') }}",
        dataType: "json",
        type: "POST",
        data: {
           _token: "{{csrf_token()}}",
           category: category
        },
        success: function(res) {
          if (res) {

              $("#product_id").empty();
              $("#product_id").append('<option value="">Select Product</option>');
              if (product_id == null || product_id == ''){
                  $('#plan_next_month').val(0).trigger('change');
                  $("#product_description, #product_code, #product_group_name, #budget_for_month, #top_sku, #sku_unit_price , #last_month_sale , #last_three_month_avg , #last_year_month_sale").val('').trigger('change');
              }
              $.each(res, function(key, value) {
                  let text = '';
                  if (product_id != null && product_id !== '') {
                      text = value.id == product_id ? 'selected' : '';
                  }
                  console.log(text);

                  let productcode = value.product_code ? value.product_code : '';

                  $("#product_id").append('<option value="' + value.id + '" ' + text + '>' + value.product_name + ' ' + productcode + '</option>');
              });
          } else {
              $("#product_id").empty();
           }
        }
     });
  }

  function getSaledata() {
      var product_id = $("#product_id").val(); // Get product ID from the row
      var date = $("#start_month").val(); // Get product ID from the row
      var branch_id = $('#branch_id').val();

      if (product_id != null && product_id != '' && branch_id != '' && branch_id != null) {
          $.ajax({
              url: "{{ url('getSaledata') }}",
              dataType: "json",
              type: "POST",
              data: {
                  _token: "{{ csrf_token() }}",
                  product_id: product_id,
                  date      : date,
                  branch_id : branch_id
              },
              success: function(res) {
                  $("#last_month_sale").val(res.last_month_sale).prop('readonly', true);
                  $("#last_three_month_avg").val(res.last_three_month_avg).prop('readonly', true);
                  $("#last_year_month_sale").val(res.last_year_same_month).prop('readonly', true);
              }
          });
      } else {
          $("#last_month_sale, #last_three_month_avg, #last_year_month_sale").val('').prop('readonly', true);
      }
  }
</script>
</x-app-layout>