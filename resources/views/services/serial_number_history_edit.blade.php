<x-app-layout>
  <style>
    .pcb{
      font-weight: bold;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.edit') }} Serial Number
            <span class="pull-right">
              <div class="btn-group">
                <a href="{{ url('services/serial_number_history') }}" class="btn btn-just-icon btn-theme" title="Serial Number History {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          {!! Form::model($serialNumberHistory,[
          'route' => 'service.serial_number_history.update',
          'method' => 'PUT',
          'id' => 'editSerialNumberForm',
          'files'=>true
          ]) !!}
            <input type="hidden" name="service_id" value="{{$serialNumberHistory->id}}">
          <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Serial Number<span class="text-danger"> *</span></label>
              
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="serial_no" class="form-control" value="{!! old( 'serial_no', $serialNumberHistory['serial_no']) !!}" maxlength="200" required>
                    @if ($errors->has('serial_no'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('serial_no') }}</p>
                    </div>
                    @endif
                
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Product<span class="text-danger"> *</span></label>
                                <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="product_code" id="product_code" style="width: 100%;" required>
                      <option value="">Select Product</option>
                      @if(@isset($products ))
                      @foreach($products as $product)
                      <option value="{!! $product['product_code'] !!}" {{ old( 'product_code' , (!empty($serialNumberHistory->product_code)) ? ($serialNumberHistory->product_code) :('') ) == $product['product_code'] ? 'selected' : '' }}><span class="pcb">[{!! $product['product_code'] !!}]</span>-{!! $product['product_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('product_code'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_code') }}</p>
                  </div>
                  @endif
                </div>
             
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Party Name<span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="party_name" class="form-control" value="{!! old( 'party_name', $serialNumberHistory['party_name']) !!}" maxlength="200" required>
                    @if ($errors->has('party_name'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('party_name') }}</p>
                    </div>
                    @endif
                  </div>
            
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Branch Code<span class="text-danger"> *</span></label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="branch_code" class="form-control" value="{!! old( 'branch_code', $serialNumberHistory['branch_code']) !!}" maxlength="200" required>
                    @if ($errors->has('branch_code'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('branch_code') }}</p>
                    </div>
                    @endif
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Invoice Date<span class="text-danger"> *</span></label>
               
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="invoice_date" class="form-control datepicker" value="{!! old( 'invoice_date', $serialNumberHistory['invoice_date']) !!}" maxlength="200" required>
                    @if ($errors->has('invoice_date'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('invoice_date') }}</p>
                    </div>
                    @endif
                  </div>
                
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Invoice Number<span class="text-danger"> *</span></label>
        
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="invoice_no" class="form-control" value="{!! old( 'invoice_no', $serialNumberHistory['invoice_no']) !!}" maxlength="200" required>
                    @if ($errors->has('invoice_no'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('invoice_no') }}</p>
                    </div>
                    @endif
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Narration</label>
                
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="narration" class="form-control" value="{!! old( 'narration', $serialNumberHistory['narration']) !!}" maxlength="200">
                    @if ($errors->has('narration'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('narration') }}</p>
                    </div>
                    @endif
                  
                </div>
              </div>
            </div>
            <!-- <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Invoice Number<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="invoice_no" class="form-control" value="{!! old( 'invoice_no', $serialNumberHistory['invoice_no']) !!}" maxlength="200" required>
                    @if ($errors->has('invoice_no'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('invoice_no') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div> -->
          </div>
        </div>
        <div class="card-footer pull-right">
          {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
  <script>
    $(document).ready(function() {
      var cat = $('#category_id').val();
      var oldsubcat = "{{ old( 'subcategory_id' , (!empty($gifts->subcategory_id)) ? ($gifts->subcategory_id) :('') ) }}";
      var oldunit = "{{ old( 'unit_id' , (!empty($gifts->unit_id)) ? ($gifts->unit_id) :('') ) }}";
      console.log(oldsubcat);
      if (cat != '') {
        $.ajax({
          url: "/getGiftSubCategoryData",
          data: {
            'cat_id': cat
          },
          success: function(data) {
            var select = $('#subcategory_id');
            select.empty();
            select.append('<option>Select Subcategory Name</option>');
            $.each(data, function(k, v) {
              if (v.id == oldsubcat) {
                var selected = 'selected';
              } else {
                var selected = '';
              }
              select.append('<option ' + selected + ' value="' + v.id + '" >' + v.subcategory_name + '</option>');
            });
            select.selectpicker('refresh');
          }
        });
      }
      if (oldsubcat != '') {
        $.ajax({
          url: "/getGiftModelData",
          data: {
            'cat_id': oldsubcat
          },
          success: function(data) {
            var select = $('#unit_id');
            select.empty();
            select.append('<option>Select Model Name</option>');
            $.each(data, function(k, v) {
              if (v.id == oldunit) {
                var selected = 'selected';
              } else {
                var selected = '';
              }
              select.append('<option ' + selected + ' value="' + v.id + '" >' + v.model_name + '</option>');
            });
            select.selectpicker('refresh');
          }
        });
      }
    })
    $(document).on('change', '#category_id', function() {
      var cat = $(this).val();
      $.ajax({
        url: "/getGiftSubCategoryData",
        data: {
          'cat_id': cat
        },
        success: function(data) {
          var select = $('#subcategory_id');
          select.empty();
          select.append('<option>Select Subcategory Name</option>');
          $.each(data, function(k, v) {
            select.append('<option value="' + v.id + '" >' + v.subcategory_name + '</option>');
          });
          select.selectpicker('refresh');
        }
      });
    }).trigger('change');

    $(document).on('change', '#subcategory_id', function() {
      var cat = $(this).val();
      $.ajax({
        url: "/getGiftModelData",
        data: {
          'cat_id': cat
        },
        success: function(data) {
          var select = $('#unit_id');
          select.empty();
          select.append('<option>Select Model Name</option>');
          $.each(data, function(k, v) {
            select.append('<option value="' + v.id + '" >' + v.model_name + '</option>');
          });
          select.selectpicker('refresh');
        }
      });
    }).trigger('change');
  </script>
</x-app-layout>