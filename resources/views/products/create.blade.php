<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} {!! trans('panel.product.title_singular') !!}
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['product_access']))
                <a href="{{ url('products') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.product.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          {!! Form::model($products,[
          'route' => $products->exists ? ['products.update', encrypt($products->id) ] : 'products.store',
          'method' => $products->exists ? 'PUT' : 'POST',
          'id' => 'createProductForm',
          'files'=>true
          ]) !!}

          <div class="row">
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Product Stage<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="product_no" class="form-control" value="{!! old( 'product_no', $products['product_no']) !!}" maxlength="200" required>
                  @if ($errors->has('product_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">kW<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="part_no" class="form-control" value="{!! old( 'part_no', $products['part_no']) !!}" maxlength="200" required>
                  @if ($errors->has('part_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('part_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.description') !!} <span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <textarea name="description" class="form-control" rows="5" maxlength="200" required>{!! old( 'description', $products['description']) !!}</textarea>
                  @if ($errors->has('description'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('description') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <!-- New added coloumns for invoice -->

            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">HSN/SAC</label>
                <div class="form-group has-default bmd-form-group">
                  <select name="hsn_sac" id="hsn_sac" class="form-control select2">
                    <option value="">Select HSN/SAC</option>
                    <option value="HSN" {{($products && $products['hsn_sac'] == 'HSN')?'selected':''}}>HSN</option>
                    <option value="SAC" {{($products && $products['hsn_sac'] == 'SAC')?'selected':''}}>SAC</option>                    
                  </select>
                  @if ($errors->has('hsn_sac'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('hsn_sac') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">HSN/SAC No></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="number" name="hsn_sac_no" class="form-control" step="1" value="{!! old( 'hsn_sac_no', $products['hsn_sac_no']) !!}">
                  @if ($errors->has('hsn_sac_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('hsn_sac_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- New added coloumns for invoice -->
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">HP<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="specification" class="form-control" rows="4" maxlength="200" required value="{!! old( 'specification', $products['specification']) !!}">
                  @if ($errors->has('specification'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('specification') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Phase<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="phase" class="form-control" rows="4" maxlength="200" required value="{!! old( 'phase', $products['phase']) !!}">
                  @if ($errors->has('phase'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('phase') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Model</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="model_no" class="form-control" value="{!! old( 'model_no', $products['model_no']) !!}" maxlength="200">
                  @if ($errors->has('model_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('model_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Product Code</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="product_code" id="product_code" class="form-control" value="{!! old( 'product_code', $products['product_code']) !!}" maxlength="200">
                  @if ($errors->has('product_code'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_code') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.brand_name') !!}<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" name="brand_id" style="width: 100%;" required>
                    <option value="">Select {!! trans('panel.product.fields.brand_name') !!}</option>
                    @if(@isset($brands ))
                    @foreach($brands as $brand)
                    <option value="{!! $brand['id'] !!}" {{ old( 'brand_id' , (!empty($products->brand_id)) ? ($products->brand_id) :('') ) == $brand['id'] ? 'selected' : '' }}>{!! $brand['brand_name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                @if ($errors->has('brand_id'))
                <div class="error col-lg-12">
                  <p class="text-danger">{{ $errors->first('brand_id') }}</p>
                </div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">New Group</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="new_group" class="form-control" value="{!! old( 'new_group', $products['new_group']) !!}" maxlength="200">
                  @if ($errors->has('new_group'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('new_group') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Sub Group</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="sub_group" class="form-control" value="{!! old( 'sub_group', $products['sub_group']) !!}" maxlength="200">
                  @if ($errors->has('sub_group'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('sub_group') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.category_name') !!}<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" name="category_id" style="width: 100%;" required>
                    <option value="">Select {!! trans('panel.product.fields.category_name') !!}</option>
                    @if(@isset($categories ))
                    @foreach($categories as $category)
                    <option value="{!! $category['id'] !!}" {{ old( 'category_id' , (!empty($products->category_id)) ? ($products->category_id) :('') ) == $category['id'] ? 'selected' : '' }}>{!! $category['category_name'] !!}</option>
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
                <label class="col-form-label">{!! trans('panel.product.fields.subcategory_name') !!}<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" name="subcategory_id" style="width: 100%;" required>
                    <option value="">Select {!! trans('panel.product.fields.subcategory_name') !!}</option>
                    @if(@isset($subcategories ))
                    @foreach($subcategories as $subcategory)
                    <option value="{!! $subcategory['id'] !!}" {{ old( 'subcategory_id' , (!empty($products->subcategory_id)) ? ($products->subcategory_id) :('') ) == $subcategory['id'] ? 'selected' : '' }}>{!! $subcategory['subcategory_name'] !!}</option>
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
                <label class="col-form-label">Branch</label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" multiple name="branch_id" style="width: 100%;>
                    <option value="">Select Branch</option>
                     @if(@isset($branches))
                        @foreach($branches as $branch)
                            <option value="{!! $branch['id'] !!}" 
                                {{ old('branch_id', !empty($products->branch_id) ? explode(',', $products->branch_id) : []) 
                                    && in_array($branch['id'], explode(',', $products->branch_id ?? '')) ? 'selected' : '' }}>
                                {!! $branch['branch_name'] !!}
                            </option>
                        @endforeach
                    @endif
                  </select>
                </div>
                @if ($errors->has('branch_id'))
                <div class="error">
                  <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                </div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.unit_name') !!}<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" name="unit_id" style="width: 100%;" required>
                    <option value="">Select {!! trans('panel.product.fields.unit_name') !!}</option>
                    @if(@isset($units ))
                    @foreach($units as $unit)
                    <option value="{!! $unit['id'] !!}" {{ old( 'unit_id' , (!empty($products->unit_id)) ? ($products->unit_id) :('') ) == $unit['id'] ? 'selected' : '' }}>{!! $unit['unit_name'] !!}</option>
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
                <label class="col-form-label">{!! trans('panel.product.fields.suc-del') !!} <span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="suc_del" class="form-control" value="{!! old( 'suc_del', $products['suc_del']) !!}" maxlength="200" required>
                  @if ($errors->has('suc_del'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('suc_del') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.product_name') !!} <span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="product_name" class="form-control" value="{!! old( 'product_name', $products['product_name']) !!}" maxlength="200" required>
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
                <label class="col-form-label">{!! trans('panel.product.fields.expiry_interval') !!} <span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select name="expiry_interval" class="form-control" required>
                    <option value="" disabled selected>Please select expiry interval</option>
                    <option {{($products && $products['expiry_interval'] == 'Day')?'selected':''}} value="Day">Day</option>
                    <option {{($products && $products['expiry_interval'] == 'Month')?'selected':''}} value="Month">Month</option>
                    <option {{($products && $products['expiry_interval'] == 'Year')?'selected':''}} value="Year">Year</option>
                  </select>
                  @if ($errors->has('expiry_interval'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('expiry_interval') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.expiry_interval_preiod') !!} <span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="number" name="expiry_interval_preiod" class="form-control" value="{!! old( 'expiry_interval_preiod', $products['expiry_interval_preiod']) !!}" min="0" required>
                  @if ($errors->has('expiry_interval_preiod'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('expiry_interval_preiod') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <!--             <div class="col-md-6">
                <div class="row">
                <label class="col-md-3 col-form-label">{!! trans('panel.product.fields.display_name') !!} <span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="display_name" class="form-control" value="{!! old( 'display_name', $products['display_name']) !!}" maxlength="200" required>
                    @if ($errors->has('display_name'))
                      <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('display_name') }}</p></div>
                    @endif
                  </div>
                </div>
              </div>
            </div> -->
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.gst') !!}</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="number" name="gst" class="form-control" value="{!! old( 'gst', !empty($products['productdetails']->pluck('gst')->first() ) ? $products['productdetails']->pluck('gst')->first() :'' ) !!}" min="0" step="0.01">
                  @if ($errors->has('gst'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('gst') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.product.fields.discount') !!}</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="number" name="discount" id="discount" class="form-control" value="{!! old( 'discount', !empty($products['productdetails']->pluck('discount')->first() ) ? $products['productdetails']->pluck('discount')->first() :'' ) !!}" min="0" step="0.01">
                  @if ($errors->has('discount'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('discount') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
           
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">SAP Code</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="sap_code" id="sap_code" class="form-control" value="{!! old( 'sap_code', $products['sap_code']) !!}" min="0" step="0.01">
                  @if ($errors->has('sap_code'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('sap_code') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
             <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Budget for the month (Qty.)</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="number" name="budget_for_month" id="budget_for_month" class="form-control" value="{!! old( 'budget_for_month', !empty($products['productpriceinfo']['budget_for_month'] ) ? $products['productpriceinfo']['budget_for_month'] :'' ) !!}" min="0" step="0.01">
                  @if ($errors->has('budget_for_month'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('budget_for_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">TOP 20 SKU for the Branch</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="top_sku" id="top_sku" class="form-control" value="{!! old( 'top_sku', !empty($products['productpriceinfo']['top_sku']) ? $products['productpriceinfo']['top_sku'] :'' ) !!}">
                  @if ($errors->has('top_sku'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('top_sku') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">RMC(Raw Material Cost)</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="number" name="rmc" id="rmc" class="form-control" value="{!! old( 'rmc', !empty($products['productpriceinfo']['rmc']) ? $products['productpriceinfo']['rmc'] :'' ) !!}" step="0.01" min="0">
                  @if ($errors->has('rmc'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('rmc') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-3">
              <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                  <img src="{!! ($products['product_image']) ? asset('/uploads/'.$products['product_image']) : asset('public/assets/img/placeholder.jpg') !!}" class="imagepreview1">
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
                <label class="bmd-label-floating">{!! trans('panel.product.fields.product_image') !!}</label>
              </div>
              @if ($errors->has('image'))
              <div class="error">
                <p class="text-danger">{{ $errors->first('image') }}</p>
              </div>
              @endif
            </div>
          </div>
        
        <div class="Menu_table">
          <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
            <thead>
              <tr>
                <th width="3%"> # </th>
                <!--                      <th class="text-center">Detail Title</th>
                     <th class="text-center">Description</th> -->
                <th width="15%">List Price</th>
                <th width="15%">Price</th>
                <!--                      <th width="15%">Selling Price</th>
                     <th width="15%">Min Selling Price</th> -->
                <!--                      <th width="2%"> </th> -->
              </tr>
            </thead>
            <tbody>
              @if($products->exists && isset($products['productdetails']))
              @foreach($products['productdetails'] as $index => $rows)
              <tr id='addr{!! $index+1 !!}' value="{!! $index+1 !!}">
                <td class="rowcount">{!! $index+1 !!}
                  <input type="hidden" name="detail[{!! $index+1 !!}][detail_id]" class="form-control" value="{!! $rows['id'] !!}" />
                </td>
                <!--                      <td>
                      <input type="text" name="detail[{!! $index+1 !!}][detail_title]" class="form-control" value="{!! $rows['detail_title'] !!}"/>
                     </td>
                     <td>
                       <input type="text" name="detail[{!! $index+1 !!}][detail_description]" class="form-control" value="{!! $rows['detail_description'] !!}"/>
                     </td> -->
                <td>
                  <input type="number" name="detail[{!! $index+1 !!}][mrp]"  class="form-control" value="{!! $rows['mrp'] !!}" step="0.01" min="0" />
                </td>
                <td><input type="number" readonly name="detail[{!! $index+1 !!}][price]" class="form-control" value="{!! $rows['price'] !!}" step="0.00" min="0" /></td>
                <!--                      <td>
                        <input type="number" name="detail[{!! $index+1 !!}][selling_price]" class="form-control" value="{!! $rows['selling_price'] !!}" step="0.00" min="0" />
                     </td>
                     <td>
                        <input type="number" name="detail[{!! $index+1 !!}][min_selling_price]" class="form-control" value="{!! $rows['min_selling_price'] !!}" step="0.00" min="0" />
                     </td> -->
                <!--                      <td class="td-actions text-center">
                      <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                        <a href="#" class="btn btn-danger btn-just-icon btn-sm remove-rows" value="1313" title="Delete Customer">
                          <i class="material-icons">clear</i>
                        </a>
                      </div>
                    </td> -->
              </tr>
              @endforeach
              @else
              <tr id='addr1' value="1">
                <td class="rowcount">1</td>
                <!--  <td>
                      <input type="text" name="detail[1][detail_title]" class="form-control"/>
                     </td>
                     <td>
                       <input type="text" name="detail[1][detail_description]" class="form-control"/>
                     </td> -->
                <td>
                  <div class="input_section">
                    <input type="number" name="detail[1][mrp]" class="form-control" step="0.00" min="0" />
                  </div>
                </td>
                <td>
                  <div class="input_section"><input type="number" readonly name="detail[1][price]" class="form-control" step="0.00" min="0" /></div>
                </td>
                <!--                      <td>
                        <input type="number" name="detail[1][selling_price]" class="form-control discount rowchange" step="0.00" min="0" />
                     </td>
                     <td>
                        <input type="number" name="detail[1][min_selling_price]" class="form-control rowchange" step="0.00" min="0" />
                     </td> -->
                <!--                      <td class="td-actions text-center">
                      <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                        <a href="#" class="btn btn-danger btn-just-icon btn-sm remove-rows" value="1313" title="Delete Customer">
                          <i class="material-icons">clear</i>
                        </a>
                      </div>
                    </td> -->
              </tr>
              @endif

              <!-- <tr id='addr1'>
                     <td class="rowcount">1</td>
                     <td>
                      <input type="text" name="detail[1][detail_title]" class="form-control"/>
                     </td>
                     <td>
                       <input type="text" name="detail[1][detail_description]" class="form-control"/>
                     </td>
                     <td>
                        <input type="number" name="detail[1][mrp]" class="form-control" step="0.00" min="0"/>
                     </td>
                     <td><input type="number" name="detail[1][price]" class="form-control" step="0.00" min="0"/></td>
                     <td>
                        <input type="number" name="detail[1][selling_price]" class="form-control discount rowchange" step="0.00" min="0" />
                     </td>
                     <td class="td-actions text-center">
                      <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                        <a href="#" class="btn btn-danger btn-just-icon btn-sm remove-rows" value="1313" title="Delete Customer">
                          <i class="material-icons">clear</i>
                        </a>
                      </div>
                    </td>
                  </tr> -->
            </tbody>
          </table>
        </div>
        <!--             <div class="row clearfix">
              <table class="table">
                <tr>
                  <td class="td-actions">
                    <a href="#" class="btn btn-success btn-just-icon btn-sm add-rows">
                      <i class="material-icons">add</i>
                    </a>
               </td>
                </tr>
              </table>
             </div> -->
        <div class="pull-right col-md-12">
          {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
        </div>
      </div>

      {{ Form::close() }}
    </div>
  </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      var $table = $('table.kvcodes-dynamic-rows-example'),
        counter = $('#tab_logic tr:last').attr('value');
      $('a.add-rows').click(function(event) {
        event.preventDefault();
        counter++;
        var newRow =
          '<tr value="' + counter + '"><td>' + counter + '</td>' +
          '<td><div class="input_section"><input type="text" name="detail[' + counter + '][detail_title]" class="form-control"/></div></td>' +
          '<td><div class="input_section"><input type="text" name="detail[' + counter + '][detail_description]" class="form-control"/></div></td>' +
          '<td><div class="input_section"><input type="number" name="detail[' + counter + '][mrp]" class="form-control" step="0.00" min="0"/></div></td>' +
          '<td><div class="input_section"><input type="number" name="detail[' + counter + '][price]" class="form-control" step="0.00" min="0"/></div></td>' +
          '<td><div class="input_section"><input type="number" name="detail[' + counter + '][selling_price]" class="form-control discount rowchange" step="0.00" min="0" /></div></td>' +
          '<td class="td-actions text-center"><div class="btn-group btn-group-sm" role="group" aria-label="Small button group"><a href="#" class="btn btn-danger btn-just-icon btn-sm remove-rows"><i class="material-icons">clear</i></a></div></td>' +
          '</tr>';
        $table.append(newRow);
      });

      $table.on('click', '.remove-rows', function() {
        $(this).closest('tr').remove();
      });
    });
  </script>
</x-app-layout>