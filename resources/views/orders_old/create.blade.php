<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-icon card-header-theme">
           <div class="card-icon">
             <i class="material-icons">perm_identity</i>
           </div>
           <h4 class="card-title ">{{ trans('panel.global.create') }} {!! trans('panel.order.title_singular') !!}
             <span class="pull-right">
               <div class="btn-group">
                 @if(auth()->user()->can(['order_access']))
                 <a href="{{ url('orders') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.order.title') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
            {!! Form::model($orders,[
            'route' => $orders->exists ? ['orders.update', encrypt($orders->id)] : 'orders.store',
            'method' => $orders->exists ? 'PUT' : 'POST',
            'id' => 'storeOrderData',
            'files'=>true
            ]) !!}
               <div class="row">
                  <div class="col-md-2">
                     <img src="{!! asset('assets/img/logo.png') !!}" class="brand-image" width="70px" alt="Logo"> <span> {!! config('app.name') !!}</span>
                  </div>
                  <div class="col-md-4">
                     <div class="row">
                        <label class="col-md-3 col-form-label">{!! trans('panel.order.order_date') !!}</label>
                         <div class="col-md-9">
                           <div class="form-group has-default bmd-form-group">
                             <input type="text" name="order_date" class="form-control datepicker" id="order_date" value="{{ old( 'order_date' , (!empty($orders->order_date)) ? ($orders->order_date) : date('Y-m-d') ) }}" autocomplete="off" readonly>
                             @if($errors->has('order_date'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('order_date') }}
                              </div>
                              @endif
                           </div>
                         </div>
                       </div>
                  </div>
                  <div class="col-md-4">
                     <div class="row">
                        <label class="col-md-4 col-form-label">Estimated Delivery</label>
                         <div class="col-md-8">
                           <div class="form-group has-default bmd-form-group">
                              <input type="hidden" name="maxday" id="maxday" />
                              <input type="hidden" name="placedeliveryday" id="placedeliveryday" />
                             <input type="text" name="estimated_date" class="form-control" id="estimated_date" readonly>
                             @if($errors->has('estimated_date'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('estimated_date') }}
                              </div>
                              @endif
                           </div>
                         </div>
                       </div>
                  </div>
                  <div class="col-md-2">
                     <div class="row">
                        <label class="col-md-4 col-form-label">{!! trans('panel.product.fields.suc-del') !!}</label>
                         <div class="col-md-8">
                           <div class="form-group has-default bmd-form-group">
                             <input type="text" name="suc_del" class="form-control" id="suc_del"  value="{!! $orders['suc_del'] !!}">
                             @if($errors->has('suc_del'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('suc_del') }}
                              </div>
                              @endif
                           </div>
                         </div>
                       </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                      <label class="col-md-3 col-form-label">{!! trans('panel.global.bill_to') !!}<span class="text-danger"> *</span></label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                           <select class="form-control select2 seller" name="seller_id" style="width: 100%;" required onchange="sellerinfo()">
                            <option value="">Select {!! trans('panel.global.seller') !!}</option>
                            @if(@isset($sellers ))
                              @foreach($sellers as $seller)
                                  <option value="{!! $seller['id'] !!}" {{ old( 'seller_id' , (!empty($orders->seller_id)) ? ($orders->seller_id) :('') ) == $seller['id'] ? 'selected' : '' }}>{!! $seller['name'] !!}</option>
                              @endforeach
                            @endif
                         </select>
                        </div>
                        @if ($errors->has('seller_id'))
                         <div class="error col-lg-12">
                            <p class="text-danger">{{ $errors->first('seller_id') }}</p>
                         </div>
                        @endif
                      </div>
                    </div>
                    <div class="row">
                        <label class="col-md-3 col-form-label">Address : </label>
                        <span class="seller_address"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row">
                      <label class="col-md-3 col-form-label">{!! trans('panel.global.buyer') !!}<span class="text-danger"> *</span></label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                           <select class="form-control select2 buyer" name="buyer_id" style="width: 100%;" required onchange="buyerinfo()">
                            <option value="">Select {!! trans('panel.global.buyer') !!}</option>
                            @if(@isset($buyers ))
                              @foreach($buyers as $buyer)
                                  <option value="{!! $buyer['id'] !!}" {{ old( 'buyer_id' , (!empty($orders->buyer_id)) ? ($orders->buyer_id) :('') ) == $buyer['id'] ? 'selected' : '' }}>{!! $buyer['name'] !!}</option>
                              @endforeach
                            @endif
                         </select>
                        </div>
                        @if ($errors->has('seller_id'))
                         <div class="error col-lg-12">
                            <p class="text-danger">{{ $errors->first('seller_id') }}</p>
                         </div>
                        @endif
                      </div>
                    </div>
                    <div class="row">
                        <label class="col-md-3 col-form-label">Address : </label>
                        <span class="buyer_address"></span>
                     </div>
                  </div>
                  @if($orders->exists && @isset($orders['orderno']))
                  <div class="col-md-6">
                     <div class="row">
                        <label class="col-md-3 col-form-label">{!! trans('panel.order.orderno') !!}</label>
                         <div class="col-md-9">
                           <div class="form-group has-default bmd-form-group">
                             <input type="text" class="form-control" name="orderno" value="{!! old( 'orderno', $orders['orderno']) !!}" >
                             @if ($errors->has('orderno'))
                               <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('orderno') }}</p></div>
                             @endif
                           </div>
                         </div>
                       </div>
                  </div>
                  @endif
               </div>
               <br>
               <div class="row">
                  <div class="col-md-6">
                     <div class="row">
                        <label class="col-md-1"></label>
                        <div class="col-md-10">
                           <div class="form-check">
                             <label class="form-check-label">
                               <input class="form-check-input" id="withoutgst" type="checkbox" value="1">Without GST
                               <span class="form-check-sign">
                                 <span class="check"></span>
                               </span>
                             </label>
                           </div>
                         </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="row">
                      <label class="col-md-3 col-form-label">Order Taking</label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                           <select class="form-control" name="order_taking" style="width: 100%;">
                            <option value="">Select Order Taking</option>
                            <option value="MobileApp" {{ old( 'order_taking' , (!empty($orders->order_taking)) ? ($orders->order_taking) :('') ) == 'MobileApp' ? 'selected' : '' }}>MobileApp</option>
                            <option value="Web" {{ old( 'order_taking' , (!empty($orders->order_taking)) ? ($orders->order_taking) :('') ) == 'Web' ? 'selected' : '' }}>Web</option>
                            <option value="Calling" {{ old( 'order_taking' , (!empty($orders->order_taking)) ? ($orders->order_taking) :('') ) == 'Calling' ? 'selected' : '' }}>Calling</option>
                         </select>
                        </div>
                        @if ($errors->has('seller_id'))
                         <div class="error col-lg-12">
                            <p class="text-danger">{{ $errors->first('seller_id') }}</p>
                         </div>
                        @endif
                      </div>
                    </div>
                  </div>
              </div>

              <!-- new dropdown -->
              
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Employee</label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="executive_id" style="width: 100%;">
                        <option value="">Select Employee</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'executive_id' , (!empty($orders->executive_id))?($orders->executive_id):('') ) == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  @if ($errors->has('executive_id'))
                   <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('executive_id') }}</p>
                   </div>
                  @endif
                </div>
              </div>
            </div>

              <!-- new dropdown -->

               <!-- Table row -->
               <div class="row">
                  <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                     <div class="table-responsive w-100">
                        <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                           <thead>
                              <tr class="card-header-warning text-white">
                                 <th class="text-center"> # </th>
                                 <th class="text-center"> {!! trans('panel.global.products') !!} </th>
                                 <th class="text-center"> {!! trans('panel.global.product_detail') !!} </th>
                                 <th class="text-center"> {!! trans('panel.global.price') !!}</th>
                                  <th class="text-center"> {!! trans('panel.global.discount_percent') !!}</th>
                                 <th class="text-center"> {!! trans('panel.global.quantity') !!}</th>
                                 <th class="text-center"> {!! trans('panel.global.amount') !!} </th>
                                 <th class="text-center"> </th>
                              </tr>
                           </thead>
                           <tbody >
                              @if($orders->exists && isset($orders['orderdetails']))
                              @foreach($orders['orderdetails'] as $key => $rows )
                              <tr id='addr{{ $key }}' value="{{ $key +1 }}">
                                 <td>{{ $key + 1 }}</td>
                                <td>
				    <select class="form-control product rowchange select2" name="orderdetail[{{ $key }}][product_id]">
					@if ($rows['product_id'] !== null)
					    <option value="{!! $rows['product_id'] !!}">{!! $rows['products']['display_name'] !!}</option>
					@endif
				    </select>
				    <div class="error-product"></div>
				</td>
				<td>
				    <select class="form-control productdetails rowchange select2" name="orderdetail[{{ $key }}][product_detail]" onchange="getproductdetailinfo(this)">
					@if ($rows['product_detail_id'] !== null)
					    <option value="{!! $rows['product_detail_id'] !!}">{!! $rows['productdetails']['detail_title'] !!}</option>
					@endif
				    </select>
				    <span class="gst_percent" style="display:none;">{!! isset($rows['productdetails']['gst']) ? $rows['productdetails']['gst'] : '' !!}</span> <br>
				    <span class="gstamount" style="display:none;">{!! $rows['tax_amount'] !!}</span> <br>
				    <span class="linediscount" style="display:none;"></span>
				    <input type="hidden" name="orderdetail[{{ $key }}][tax_amount]" class="form-control tax_amount" value="{!! $rows['tax_amount'] !!}" readonly/>
				    <input type="hidden" name="orderdetail[{{ $key }}][discount_amount]" class="form-control discountamount" value="{!! $rows['discount_amount'] !!}" readonly/>
				</td>
                                 <td>
                                    <input type="number" name="orderdetail[{{ $key }}][price]" class="form-control price rowchange" step="0.00" min="0" value="{!! $rows['price'] !!}" />
                                    <div class='error-price'></div>
                                 </td>
                                 <td>
                                    <input type="number" name="orderdetail[{{ $key }}][discount]" class="form-control discount rowchange" step="0.00" min="0" value="{!! $rows['discount'] !!}" />
                                    <div class='error-discount'></div>
                                 </td>
                                 <td>
                                    <input type="number" name='orderdetail[{{ $key }}][quantity]' class="form-control quantity rowchange" step="0" min="0" value="{!! $rows['quantity'] !!}" />
                                    <div class='error-quantity'></div>
                                 </td>
                                 <td>
                                    <input type="number" name='orderdetail[{{ $key }}][line_total]' class="form-control total" value="{!! $rows['line_total'] !!}" readonly/>
                                 </td>
                                 <td class="td-actions text-center"><a class="remove btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td>
                              </tr>
                              @endforeach
                              @else
                              <tr id='addr0' value="1">
                                 <td>1</td>
                                 <td>
                                    <select class="form-control product rowchange select2" name="orderdetail[1][product_id]" onchange="getproductinfo(this)" data-url="{{URL::To('sales/product_detail')}}">
                                       @if(@isset($products))
                                       <option value="">Select Product</option>
                                       @foreach($products as $product )
                                       <option value="{{ $product['id'] }}">{{ $product['display_name'] }}</option>
                                       @endforeach
                                       @endif
                                    </select>
                                    <div class="error-product"></div>
                                 </td>
                                 <td>
                                    <select class="form-control productdetails rowchange select2" name="orderdetail[1][product_detail]" onchange="getproductdetailinfo(this)" >
                                       
                                    </select>
                                    <span class="gst_percent" style="display:none;"></span> <br>
                                    <span class="gstamount" style="display:none;"></span> <br>
                                    <span class="linediscount" style="display:none;"></span>
                                    <input type="hidden" name="orderdetail[1][tax_amount]" class="form-control tax_amount readonly"/>
                                    <input type="hidden" name="orderdetail[1][discount_amount]" class="form-control discountamount readonly"/>
                                 </td>
                                 <td>
                                    <input type="number" name="orderdetail[1][price]" class="form-control price rowchange" step="0.00" min="0"/>
                                    <div class='error-price'></div>
                                 </td>
                                 <td>
                                    <input type="number" name="orderdetail[1][discount]" class="form-control discount rowchange" step="0.00" min="0" />
                                    <div class='error-discount'></div>
                                 </td>
                                 <td>
                                    <input type="number" name='orderdetail[1][quantity]' class="form-control quantity rowchange" step="0" min="0" />
                                    <div class='error-quantity'></div>
                                 </td>
                                 <td>
                                    <input type="number" name='orderdetail[1][line_total]' class="form-control total" readonly/>
                                 </td>
                                 <td class="td-actions text-center"><a class="remove btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td>
                              </tr>
                              @endif
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
               <div class="baseurl" data-baseurl="{{ url('/')}}">
               </div>
               <br>
               <!-- /.row -->
               <div class="row">
                  <!-- accepted payments column -->
                  <div class="col-6">
                     <!--                       <p class="lead">{!! trans('panel.order.description') !!}</p>
                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                          <div class="form-group row">
                            <textarea class="form-control" name="description">{!! old( 'description', $orders['description']) !!}</textarea>
                        
                            @if($errors->has('description'))
                              <div class="invalid-feedback">
                                  {{ $errors->first('description') }}
                              </div>
                            @endif  
                          </div>
                        </p> -->
                  </div>
                  <!-- /.col -->
                  <div class="col-6">
                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.sub_total') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='sub_total' class="form-control" id="subtotal" readonly value="{!! old( 'sub_total', $orders['sub_total']) !!}"/>
                           @if($errors->has('sub_total'))
                           <div class="invalid-feedback">
                              {{ $errors->first('sub_total') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.total_gst') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='total_gst' id="totalgst" class="form-control" value="{!! old( 'total_gst', $orders['total_gst']) !!}" readonly/>
                           @if($errors->has('total_gst'))
                           <div class="invalid-feedback">
                              {{ $errors->first('total_gst') }}
                           </div>
                           @endif
                        </div>
                     </div>

                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.extra_discount') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <div class="input-group-prepend">
                             </div>
                             <input type="number" name='extra_discount' class="form-control" value="{!! old( 'extra_discount', $orders['extra_discount']) !!}"/>
                             <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly>
                           </div>
                           @if($errors->has('extra_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('extra_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.total_discount') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='total_discount' id="totaldiscount" class="form-control" value="{!! old( 'total_discount', $orders['total_discount']) !!}" readonly/>
                           @if($errors->has('total_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('total_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">Transportation</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='transportation_amount' id="transportation_amount" class="form-control" value="{!! old( 'transportation_amount', $orders['transportation_amount']) !!}"/>
                           @if($errors->has('transportation_amount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('transportation_amount') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-sm-4">
                          <label class="bmd-label">{!! trans('panel.order.grand_total') !!}</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="number" class="form-control" id="grandtotal" name="grand_total" value="{!! old( 'grand_total', $orders['grand_total']) !!}" readonly>
                           @if($errors->has('grand_total'))
                           <div class="invalid-feedback">
                              {{ $errors->first('grand_total') }}
                           </div>
                           @endif
                        </div>
                     </div>
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
<script src="{{ url('/').'/'.asset('assets/js/validation_orders.js') }}"></script>
<!-- <script src="{{ url('/').'/'.asset('assets/js/invoice_js') }}"></script> -->
<script type="text/javascript">
   $(document).ready(function(){
   
    var $table = $('table.kvcodes-dynamic-rows-example'),
         counter = $('#tab_logic tr:last').attr('value');
      $('a.add-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr value="'+counter+'"> <td>'+counter+'</td>'+
                  '<td><select name="orderdetail[' + counter + '][product_id]" class="form-control product rowchange select2" onchange="getproductinfo(this)"/> </select></td>' +
                  '<td> <select class="form-control productdetails rowchange select2" name="orderdetail[' + counter + '][product_detail]" onchange="getproductdetailinfo(this)" ></select><span class="gst_percent" style="display:none;"></span> <br><span class="gstamount" style="display:none;"></span> <br><span class="linediscount" style="display:none;"></span> <br><input type="hidden" name="orderdetail[' + counter + '][tax_amount]" class="form-control tax_amount readonly"/><input type="hidden" name="orderdetail[' + counter + '][discount_amount]" class="form-control discountamount readonly"/></td>'+
                  '<td><input type="number" name="orderdetail[' + counter + '][price]" class="form-control price readonly"/></td>' +
                  '<td><input type="number" name="orderdetail[' + counter + '][discount]" class="form-control discount readonly"/></td>' +
                  '<td><input type="text" name="orderdetail[' + counter + '][quantity]" class="form-control quantity rowchange" /></td>' +
                  '<td><input type="text" name="orderdetail[' + counter + '][line_total]" class="form-control total rowchange" readonly /></td>' +
                  '<td class="td-actions text-center"><a href="#" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
          $table.append(newRow);
      });
   
      $table.on('click', '.remove-rows', function() {
          $(this).closest('tr').remove();
      });
   
    $('#tab_logic tbody').on('keyup change',function(){
      calc();
    });
   });
   
    function sellerinfo()
    {
      var customer_id = $("select[name=seller_id]").val();
      if(customer_id){
          $.ajax({
              url: "{{ url('getCustomerData') }}",
              dataType: "json",
              type: "POST",
              data:{ _token: "{{csrf_token()}}", customer_id:customer_id},
              success: function(res){
                  if(res)
                  {
                      $(".seller_address").empty();
                      $('.seller_address').append(res.address1+'<br> '+res.address2+'<br>Phone : '+res.mobile+'<br>Email: '+res.email);
                  }
                  else
                  {
                      $(".seller_address").empty();
                  }
              }
          });
      }
      else
      {
         $(".buyer_address").empty(); 
      }
    }
    function buyerinfo()
    {
        var customer_id = $("select[name=buyer_id]").val();
        if(customer_id){
            $.ajax({
                url: "{{ url('getCustomerData') }}",
                dataType: "json",
                type: "POST",
                data:{ _token: "{{csrf_token()}}", customer_id:customer_id},
                success: function(res){
                    if(res)
                    {
                        $(".buyer_address").empty();
                        $('.buyer_address').append(res.address1+'<br> '+res.address2+'<br>Phone : '+res.mobile+'<br>Email: '+res.email);
                    }
                    else
                    {
                        $(".buyer_address").empty();
                    }
                }
            });
        }
        else
        {
           $(".buyer_address").empty(); 
        } 
    }
    function getproductinfo(e)
    {
      var base_url =$('.baseurl').data('baseurl'); 
      var row = $(e).parent().parent();
      var product_id = $(e).val(); 
   
      $.ajax({
         url: "{{ url('getProductInfo') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}",product_id:product_id},
         success: function(res)
         {
            row.find('.price').empty();
            row.find('.gst_percent').empty();
            row.find('.price').val(res.price);
            row.find('.gst_percent').append(res.gst);
            row.find('.discount').val(res.discount);
            row.find('.discount').attr("max",res.max_discount);
            if(res.productdetails){
              $.each(res.productdetails,function(key,value){ 
                row.find('.productdetails').append('<option value="'+value.id+'">'+value.detail_title+'</option>');
   
              });
            }
         }
     });
    }

   function getproductdetailinfo(e)
   {
      var base_url =$('.baseurl').data('baseurl'); 
      var row = $(e).parent().parent();
      var productdetail_id = $(e).val(); 
      $.ajax({
         url: "{{ url('getProductDetailInfo') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}",productdetail_id:productdetail_id},
         success: function(res)
         {
            row.find('.price').empty();
            row.find('.gst_percent').empty();
            row.find('.price').val(res.price);
            row.find('.gst_percent').append(res.gst);
            row.find('.discount').val(res.discount);
            row.find('.discount').attr("max",res.max_discount);
         }
     });
   }
   
    $('#tab_logic tbody').on('keyup change',function(){
      calc();
    });
   
    function calc()
    {
      $('#tab_logic tbody tr').each(function(i, element) {
        var html = $(this).html();
        if(html!='')
        {
            var quantity = $(this).find('.quantity').val();
            var price = $(this).find('.price').val();
            var discount = $(this).find('.discount').val();
            var total = quantity*price;
            var discount_amount = total * discount / 100;
            total = total - discount_amount;
            if($("#withoutgst").is(":checked")){
                var tax_amount = 0.00;
            }
            else
            {
               var gst = $(this).find('.gst_percent').html();
               var tax_amount = total * gst / 100;
            }
          $(this).find('.total').val((total).toFixed(2));
          $(this).find('.tax_amount').val((tax_amount).toFixed(2));
          $(this).find('.gstamount').empty();
          $(this).find('.gstamount').append(tax_amount);
          $(this).find('.discountamount').empty();
          $(this).find('.discountamount').val(discount_amount);
          calc_total();
        }
      });
    }
   
    function calc_total()
    {
      total=0;
      subtotal=0;
      taxamount=0;
      discount=0;
      transportation = 0;
      var extra_discount = $("input[name=extra_discount]").val();
      var transportamt = $("#transportation_amount").val();
      if(transportamt)
      {
         transportation = parseInt(transportamt);
      }
      var discount_amount = 0;
      $('.total').each(function() {
        total += parseInt($(this).val());
      });
      $('.tax_amount').each(function() {
        taxamount += parseInt($(this).val());
      });
      $('.discountamount').each(function() {
        discount += parseInt($(this).val());
      });
       discount_amount = total * extra_discount / 100;
       subtotal = total;
       total -= discount_amount;
      $('#subtotal').val((subtotal).toFixed(2));
      $('#totalgst').val(taxamount.toFixed(2));
      $('#totaldiscount').val(discount.toFixed(2));
      $('.extra_discount_amount').empty();
      $('.extra_discount_amount').val(discount_amount.toFixed(2));
      $('#grandtotal').val((total+taxamount+transportation).toFixed(2));
      estimatedDelivery();
    }
   
    function getProductlist()
    {
      var base_url =$('.baseurl').data('baseurl'); 
        $.ajax({
          url: "{{ url('getProductData') }}",
          dataType: "json",
          type: "POST",
          data:{ _token: "{{csrf_token()}}" },
          success: function(res){
            var table = document.getElementById(tab_logic),rIndex;
            if(res){
              $('#tab_logic tr:last').find(".product").empty();
                $('#tab_logic tr:last').find(".product").append('<option value="">Select Product</option>');
              $.each(res,function(key,value){ 
                $('#tab_logic tr:last').find('.product').append('<option value="'+value.id+'">'+value.display_name+'</option>');
   
              });
            }
            else{
               row.find(".product").empty();
            }
          }
      });
    }

   function estimatedDelivery()
   {
      totalday=1;
      var orderdate = $('#order_date').val();
      var maxday = $('#maxday').val();
      var placeday = $('#placedeliveryday').val();
      var datatoday = new Date(orderdate);
      // totalday += parseInt(placeday);
      // totalday += parseInt(maxday);
      // var datatodays = datatoday.setDate(new Date(datatoday).getDate() + totalday);
      $('#estimated_date').val(orderdate);
   }
</script>
</x-app-layout>
