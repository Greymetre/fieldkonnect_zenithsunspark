<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ $product->exists?trans('panel.global.edit') : trans('panel.global.create') }} Service Charge Product
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['brand_access']))
                <a href="{{ url('service-charge/products') }}" class="btn btn-just-icon btn-theme" title="Service Charge Product {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                @endif
              </div>
            </span>
          </h4>
        </div>
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
        <div class="card-body">

          {!! Form::model($product,[
          'route' => $product->exists ? ['servicecharge.products.update'] : 'servicecharge.products.add',
          'method' => $product->exists ? 'PUT' : 'POST',
          'id' => 'createServiceChargeProduct',
          'files'=>true
          ]) !!}
          <input type="hidden" name="id" value="{{$product->exists ? $product->id : ''}}">
          <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Charge Type <span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <select name="charge_type_id" id="charge_type_id" class="select2">
                      <option value="">Select Charge Type</option>
                      @if(count($chargetypes) > 0)
                      @foreach($chargetypes as $chargetype)
                      <option value="{{$chargetype->id}}" {{old('charge_type_id', $product->charge_type_id) == $chargetype->id?'selected':''}}>{{$chargetype->charge_type}}</option>
                      @endforeach
                      @endif
                    </select>
                    @if ($errors->has('charge_type_id'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('charge_type_id') }}</p>
                    </div>
                    @endif
              
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Product Name(Description) <span class="text-danger"> *</span></label>
             
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="product_name" class="form-control" value="{!! old( 'product_name', $product['product_name']) !!}" maxlength="200" required>
                    @if ($errors->has('product_name'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('product_name') }}</p>
                    </div>
                    @endif
                  </div>
           
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Division <span class="text-danger"> *</span></label>
               
                  <div class="form-group has-default bmd-form-group">
                    <select name="division_id" id="division_id" class="select2">
                      <option value="">Select Division</option>
                      @if(count($divisions) > 0)
                      @foreach($divisions as $division)
                      <option value="{{$division->id}}" {{old('division_id', $product->division_id) == $division->id?'selected':''}}>{{$division->division_name}}</option>
                      @endforeach
                      @endif
                    </select>
                    @if ($errors->has('division_id'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('division_id') }}</p>
                    </div>
                    @endif
               
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Category <span class="text-danger"> *</span></label>
            
                  <div class="form-group has-default bmd-form-group">
                    <select name="category_id" id="category_id" class="select2">
                      <option value="">Select Division First</option>
                    </select>
                    @if ($errors->has('category_id'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('category_id') }}</p>
                    </div>
                    @endif
               
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Price(Charge) <span class="text-danger"> *</span></label>
             
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="price" id="price" class="form-control" value="{!! old( 'price', $product['price']) !!}">
                    @if ($errors->has('price'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('price') }}</p>
                    </div>
                    @endif
                 
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Other Charges <span class="text-danger"> *</span></label>
              
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="other_charge" id="other_charge" class="form-control" value="{!! old( 'other_charge', $product['other_charge']) !!}">
                    @if ($errors->has('other_charge'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('other_charge') }}</p>
                    </div>
                    @endif
                  </div>
                
              </div>
            </div>
          </div>
          <div class="pull-right">
            {{ Form::submit($product->exists?'Update':'Submit', array('class' => 'btn btn-theme')) }}
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script>
    $("#division_id").on("change", function() {
      var id = $(this).val();
      var cat = "{{$product->category_id??''}}";
      $.ajax({
        url: "{{url('/getServiceCategory')}}",
        data: {
          'division_id': id
        },
        success: function(data) {
          var html = '<option value="">Select Category</option>';
          $.each(data, function(k, v) {
            var selected = '';
            if (cat && cat != '') {
              if (cat == v.id) {
                selected = 'selected';
              }
            }
            html += '<option ' + selected + ' value="' + v.id + '">' + v.category_name + '</option>';
          });
          $('#category_id').html(html);
        }
      });
    }).trigger("change");
  </script>
</x-app-layout>