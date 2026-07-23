<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} {!! trans('panel.gift.title_singular') !!}
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['gift_access']))
                <a href="{{ url('gifts') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.gift.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          {!! Form::model($gifts,[
          'route' => $gifts->exists ? ['gifts.update', encrypt($gifts->id) ] : 'gifts.store',
          'method' => $gifts->exists ? 'PUT' : 'POST',
          'id' => 'createGiftForm',
          'files'=>true
          ]) !!}

          <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.category_name') !!}<span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="category_id" id="category_id" style="width: 100%;" required>
                      <option value="">Select {!! trans('panel.gift.fields.category_name') !!}</option>
                      @if(@isset($categories ))
                      @foreach($categories as $category)
                      <option value="{!! $category['id'] !!}" {{ old( 'category_id' , (!empty($gifts->category_id)) ? ($gifts->category_id) :('') ) == $category['id'] ? 'selected' : '' }}>{!! $category['category_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('category_id'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('category_id') }}</p>
                  </div>
                  @endif
           
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.subcategory_name') !!}<span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="subcategory_id" id="subcategory_id" style="width: 100%;" required>
                      <option value="">Select {!! trans('panel.gift.fields.subcategory_name') !!}</option>
                      @if(@isset($subcategories ))
                      @foreach($subcategories as $subcategory)
                      <option value="{!! $subcategory['id'] !!}" {{ old( 'subcategory_id' , (!empty($gifts->subcategory_id)) ? ($gifts->subcategory_id) :('') ) == $subcategory['id'] ? 'selected' : '' }}>{!! $subcategory['subcategory_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('subcategory_id'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('subcategory_id') }}</p>
                  </div>
                  @endif
                </div>
              
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.brand_name') !!}<span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="brand_id" style="width: 100%;" required>
                      <option value="">Select {!! trans('panel.gift.fields.brand_name') !!}</option>
                      @if(@isset($brands ))
                      @foreach($brands as $brand)
                      <option value="{!! $brand['id'] !!}" {{ old( 'brand_id' , (!empty($gifts->brand_id)) ? ($gifts->brand_id) :('') ) == $brand['id'] ? 'selected' : '' }}>{!! $brand['brand_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('brand_id'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('brand_id') }}</p>
                  </div>
                  @endif
                
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.unit_name') !!}<span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="unit_id" id="unit_id" style="width: 100%;" required>
                      <option value="">Select {!! trans('panel.gift.fields.unit_name') !!}</option>
                      @if(@isset($units ))
                      @foreach($units as $unit)
                      <option value="{!! $unit['id'] !!}" {{ old( 'unit_id' , (!empty($gifts->unit_id)) ? ($gifts->unit_id) :('') ) == $unit['id'] ? 'selected' : '' }}>{!! $unit['unit_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('unit_id'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('unit_id') }}</p>
                  </div>
                  @endif
              
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.customer_type') !!}<span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="customer_type_id" id="customer_type_id" style="width: 100%;" required>
                      <option value="">Select {!! trans('panel.gift.fields.customer_type') !!}</option>
                      @if(@isset($customer_types ))
                      @foreach($customer_types as $customer_type)
                      <option value="{!! $customer_type['id'] !!}" {{ old( 'customer_type_id' , (!empty($gifts->customer_type_id)) ? ($gifts->customer_type_id) :('') ) == $customer_type['id'] ? 'selected' : '' }}>{!! $customer_type['customertype_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('customer_type_id'))
                  <div class="error ">
                    <p class="text-danger">{{ $errors->first('customer_type_id') }}</p>
                  </div>
                  @endif
                </div>
           
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.product_name') !!} <span class="text-danger"> *</span></label>
       
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="product_name" class="form-control" value="{!! old( 'product_name', $gifts['product_name']) !!}" maxlength="200" required>
                    @if ($errors->has('product_name'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('product_name') }}</p>
                    </div>
                    @endif
                  </div>
            
              </div>
            </div>
            <div class="col-md-12">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.display_name') !!} <span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="display_name" class="form-control" value="{!! old( 'display_name', $gifts['display_name']) !!}" maxlength="200" required>
                    @if ($errors->has('display_name'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('display_name') }}</p>
                    </div>
                    @endif
                  </div>
                
              </div>
            </div>
            <div class="col-md-12">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.description') !!} </label>
              
                  <div class="form-group has-default bmd-form-group">
                    <textarea name="description" class="form-control" rows="5" maxlength="200" required>{!! old( 'description', $gifts['description']) !!}</textarea>
                    @if ($errors->has('description'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('description') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.mrp') !!} <span class="text-danger"> *</span></label>
                
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" name="mrp" class="form-control" value="{!! old( 'mrp', isset($gifts['mrp']) ? $gifts['mrp'] :'' ) !!}" min="0" step="0.01" required>
                    @if ($errors->has('mrp'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('mrp') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                 </div>

                 <div class="col-md-4">
                  <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.price') !!} <span class="text-danger"> *</span></label>
              
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" name="price" class="form-control" value="{!! old( 'price', isset($gifts['price']) ? $gifts['price'] :'' ) !!}" min="0" step="0.01" required>
                    @if ($errors->has('price'))
                    <div class="error ">
                      <p class="text-danger">{{ $errors->first('price') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                </div>
                <div class="col-md-4">
                  <div class="input_section">
                <label class="col-form-label">{!! trans('panel.gift.fields.points') !!} <span class="text-danger"> *</span></label>
             
                  <div class="form-group has-default bmd-form-group">
                    <input type="number" name="points" class="form-control" value="{!! old( 'points', $gifts['points']) !!}" min="0" step="0.01">
                    @if ($errors->has('points'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('points') }}</p>
                    </div>
                    @endif
                  </div>
               
              </div>
            </div>


            <div class="col-md-2">
              <div class="fileinput fileinput-new text-center" data-provides="fileinput">
            
                <div class="fileinput-new thumbnail">
                  <img src="{!! ($gifts['product_image']) ? asset('uploads/'.$gifts['product_image']) : asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                      <div class="selectThumbnail">
                  <span class="btn btn-just-icon btn-round btn-file">
                    <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                    <span class="fileinput-exists">Change</span>
                    <input type="file" name="image" class="getimage1" accept="image/*">
                  </span>
                  <br>
                  <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.gift.fields.product_image') !!}</label>
              </div>
              @if ($errors->has('image'))
              <div class="error">
                <p class="text-danger">{{ $errors->first('image') }}</p>
              </div>
              @endif
            </div>
          </div>
          <div class="pull-right">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
          </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script>
    $(document).ready(function(){
      var cat = $('#category_id').val();
      var oldsubcat = "{{ old( 'subcategory_id' , (!empty($gifts->subcategory_id)) ? ($gifts->subcategory_id) :('') ) }}";
      var oldunit = "{{ old( 'unit_id' , (!empty($gifts->unit_id)) ? ($gifts->unit_id) :('') ) }}";
      console.log(oldsubcat);
      if(cat != ''){
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
              if(v.id == oldsubcat){
                var selected = 'selected';
              }else{
                var selected = '';
              }
              select.append('<option '+selected+' value="' + v.id + '" >' + v.subcategory_name + '</option>');
            });
            select.selectpicker('refresh');
          }
        });  
      }
      if(oldsubcat != ''){
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
              if(v.id == oldunit){
                var selected = 'selected';
              }else{
                var selected = '';
              }
              select.append('<option '+selected+' value="' + v.id + '" >' + v.model_name + '</option>');
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