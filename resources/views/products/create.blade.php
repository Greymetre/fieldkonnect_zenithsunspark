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
          @if(!$products->exists && in_array(request('source'), ['purchase-order', 'sales-order'], true))
          <input type="hidden" name="source" value="{{ request('source') }}">
          @if(request('source') === 'sales-order')
          <input type="hidden" name="return_order_type" value="{{ in_array(request('order_type'), ['b2b', 'b2c'], true) ? request('order_type') : 'b2c' }}">
          @endif
          @endif

          <div class="row">
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
