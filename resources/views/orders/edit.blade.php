<x-app-layout>
   <div class="row">
      <div class="col-md-12">
         <div class="card productlistpage">
            <div class="card-header card-header-icon card-header-theme">
               <div class="card-icon">
                  <i class="material-icons">perm_identity</i>
               </div>
               <h4 class="card-title ">Update {!! trans('panel.order.title_singular') !!}
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
               'id' => 'storeOrderData18',
               'files'=>true
               ]) !!}
               <div class="row">
                  <div class="col-md-12">
                     <div class="input_section">
                     <!-- <img src="{!! url('/').'/'.asset('assets/img/bediya.jpg') !!}" width="70">
                     <img src="{!! url('/').'/'.asset('assets/img/silver.png') !!}" width="70"> -->
                     <!-- <img src="{!! url('/').'/'.asset('assets/img/silver.png') !!}" class="brand-image" width="70px" alt="Logo"> <span> {!! config('app.name') !!}</span> -->
</div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.order.order_date') !!}</label>
                       
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

                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">Order Taking</label>
                     
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

                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Employee</label>
                        
                           
                           <div class="form-group has-default bmd-form-group">
                              <select class="form-control select2" name="executive_id" style="width: 100%;">
                                 <option value="">Select Employee</option>
                                 @if(@isset($users ))
                                 @foreach($users as $user)
                                 <option value="{!! $user['id'] !!}" {{ old( 'executive_id' , (!empty($orders->executive_id)?$orders->executive_id:($orders->created_by??'') )) == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
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
               <div class="row">
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.global.bill_to') !!}<span class="text-danger"> *</span></label>
                       
                           <div class="form-group has-default bmd-form-group">
                              <select class="form-control select2 seller" name="seller_id" style="width: 100%;" required onchange="sellerinfo()" id="seller_id">
                                 <!-- <option value="">Select {!! trans('panel.global.seller') !!}</option> -->
                                 <option value="">Select Customer</option>
                                 {{-- @if(@isset($sellers ))
                                 @foreach($sellers as $seller)

                                 <!-- <option value="{!! $seller['id'] !!}" data-allowtype="{{$seller->customertype}}"    {{ old( 'seller_id' , (!empty($orders->seller_id)) ? ($orders->seller_id) :('') ) == $seller['id'] ? 'selected' : '' }}>{!! $seller['name'] !!}</option> -->

                                 <option value="{!! $seller['id'] !!}" data-allowtype="{{$seller->customertype}}" {{ old( 'seller_id' , (!empty($orders->buyer_id)) ? ($orders->buyer_id) :('') ) == $seller['id'] ? 'selected' : '' }}>{!! $seller['name'] !!}</option>

                                 @endforeach
                                 @endif --}}
                              </select>
                           </div>
                           @if ($errors->has('seller_id'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('seller_id') }}</p>
                           </div>
                           @endif
                       
                     </div>

                       <div class="">
                        <div class="input_section">
                        <label class="col-form-label">Address : </label>
                        <br>
                        <span class="seller_address"></span>
                     </div>
                     </div>

                   
                  </div>
                  <div class="col-md-6" id="de_dis" style="display:none;">
                     <div class="input_section">
                        <!-- <label class="col-md-3 col-form-label">{!! trans('panel.global.buyer') !!}<span class="text-danger"> *</span></label> -->
                        <label class="col-form-label">Dealer/Distributer<span class="text-danger"> *</span></label>
                       
                           <div class="form-group has-default bmd-form-group">
                              <select class="form-control select2 buyer" name="buyer_id" style="width: 100%;" onchange="buyerinfo()" id="buyer_id">
                                 <!-- <option value="">Select {!! trans('panel.global.buyer') !!}</option> -->
                                 <option value="">Select Dealer/Distributer</option>
                                 {{-- @if(@isset($buyers ))
                                 @foreach($buyers as $buyer)
                                 <!-- <option value="{!! $buyer['id'] !!}" {{ old( 'buyer_id' , (!empty($orders->buyer_id)) ? ($orders->buyer_id) :('') ) == $buyer['id'] ? 'selected' : '' }}>{!! $buyer['name'] !!}</option> -->
                                 <option value="{!! $buyer['id'] !!}" {{ old( 'buyer_id' , (!empty($orders->seller_id)) ? ($orders->seller_id) :('') ) == $buyer['id'] ? 'selected' : '' }}>{!! $buyer['name'] !!}</option>
                                 @endforeach
                                 @endif --}}
                              </select>
                           </div>
                           @if($errors->has('seller_id'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('seller_id') }}</p>
                           </div>
                           @endif
                        
                     </div>
                     <div class="row">
                        <div class="input_section">
                        <label class="col-form-label">Address : </label>
                        <span class="buyer_address"></span>
                     </div>
                  </div>
                  </div>
                  @if($orders->exists && @isset($orders['orderno']))
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.order.orderno') !!}</label>
                       
                           <div class="form-group has-default bmd-form-group">
                              <input type="text" class="form-control" name="orderno" value="{!! old( 'orderno', $orders['orderno']) !!}">
                              @if ($errors->has('orderno'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('orderno') }}</p>
                              </div>
                              @endif
                           </div>
                       
                     </div>
                  </div>
                  @endif
               </div>

               <div class="row">
                  <div class="col-md-12">
                  <label class="col-form-label">Division : </label>
                 
                     <select name="product_cat_id" readonly id="product_cat_id" class="form-control select2" onchange="getProductlist()">
                        @if(count($category) > 0)
                        <option value="" disabled>Select Division</option>
                        @foreach($category as $cat)
                        <option {{(old('product_cat_id', $orders['product_cat_id']) == $cat->id)?'selected':'disabled'}} value="{{$cat->id}}">{{$cat->category_name}}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
               </div>
              
               <br>


               <!-- new dropdown -->

               <!-- Table row -->
               <div class="row">
                  <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                     <div class="table-responsive w-100">
                        <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                           <thead>
                              <tr class="text-white">
                                 <th class="text-center"> # </th>
                                 <th class="text-center"> {!! trans('panel.global.products') !!}</th>
                                 <!-- <th class="text-center"> {!! trans('panel.global.product_detail') !!} </th> -->
                                 <th class="text-center"> {!! trans('panel.global.quantity') !!}</th>
                                 <th class="text-center"> {!! trans('panel.global.list_price') !!}</th>
                                 <th class="text-center"> Tax</th>

                                 <th class="text-center"> Trade Discount%</th>
                                 <th class="text-center">Scheme Discount%</th>
                                 <th class="text-center"> {!! trans('panel.global.amount') !!} </th>


                                 <th class="text-center"> </th>
                              </tr>
                           </thead>
                           <tbody>
                              @if($orders->exists && isset($orders['orderdetails']))
                              @foreach($orders['orderdetails'] as $key => $rows )
                              <tr id='addr{{ $key }}' value="{{ $key +1 }}">
                                 <input type="hidden" name="order_detail_id" value="{{$rows->id}}">
                                 <td>{{ $key + 1 }}</td>
                                 <td>
                                    <select class="form-control product rowchange select2" name="orderdetail[{{ $key }}][product_id]">
                                       @if ($rows['product_id'] !== null)
                                       <option value="{!! $rows['product_id'] !!}">{!! $rows['products']['product_name'] !!}</option>
                                       @endif
                                    </select>
                                    <div class="error-product"></div>
                                 </td>
                                 <td style="display: none;">
                                    <select class="form-control productdetails rowchange select2" name="orderdetail[{{ $key }}][product_detail]" onchange="getproductdetailinfo(this)">
                                       @if ($rows['product_detail_id'] !== null)
                                       <option value="{!! $rows['product_detail_id'] !!}">{!! $rows['products']['productpriceinfo']['detail_title'] !!}</option>
                                       @endif
                                    </select>
                                    <span class="gst_percent" style="display:none;">{!! isset($rows['products']['productpriceinfo']['gst']) ? $rows['products']['productpriceinfo']['gst'] : '' !!}</span> <br>
                                    <span class="gstamount" style="display:none;">{!! $rows['tax_amount'] !!}</span> <br>
                                    <span class="linediscount" style="display:none;"></span>
                                    <input type="hidden" name="orderdetail[{{ $key }}][tax_amount]" class="form-control tax_amount" value="{!! $rows['tax_amount'] !!}" readonly />
                                    <input type="hidden" name="orderdetail[{{ $key }}][discount_amount]" class="form-control discountamount" value="{!! $rows['discount_amount'] !!}" readonly />
                                 </td>

                                 <td>
                                    <input type="number" name='orderdetail[{{ $key }}][quantity]' class="form-control quantity rowchange" step="0" min="0" value="{!! $rows['quantity'] !!}" />
                                    <div class='error-quantity'></div>
                                 </td>


                                 <td>
                                    <input type="number" name="orderdetail[{{ $key }}][mrp]" class="form-control price rowchange" step="0.00" min="0" value="{!! $rows['price'] !!}" readonly />
                                    <div class='error-price'></div>
                                 </td>

                                 <td>
                                    <input type="text" name="orderdetail[{{ $key }}][gst]" class="form-control gst_new rowchange" step="0.00" min="0" value="{!! $rows['gst'] !!}" readonly />
                                    <div class='error-gst'></div>
                                 </td>

                                 <td>
                                    <input type="number" name="orderdetail[{{ $key }}][discount]" class="form-control discount rowchange" step="0.00" min="0" value="{!! $rows['discount'] !!}" readonly />
                                    <div class='error-discount'></div>
                                 </td>

                                 <td>
                                    <input type="number" name='orderdetail[{{ $key }}][scheme_dis]' class="form-control total_new scheme_dis" value="{!! $rows['ebd_discount'] !!}" readonly />


                                    <!-- nnn -->

                                    <input type="text" name="orderdetail[{{ $key }}][scheme_type]" class="scheme_type" hidden>
                                    <input type="text" name="orderdetail[{{ $key }}][scheme_value_type]" class="scheme_value_type" hidden>
                                    <input type="text" name="orderdetail[{{ $key }}][minimum]" class="minimum" hidden>
                                    <input type="text" name="orderdetail[{{ $key }}][maximum]" class="maximum" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][start_date]" class="start_date" hidden>
                                    <input type="text" name="orderdetail[{{ $key }}][end_date]" class="end_date" hidden>
                                    <!-- nnn end -->

                                    <input type="text" name="orderdetail[{{ $key }}][ebd_amount]" class="ebd_amount" value="{!! $rows['schme_amount'] !!}" hidden>


                                    <input type="text" name="orderdetail[{{ $key }}][clus_amounts]" class="clus_amounts" value="{!! $rows['cluster_amount'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][clustered_dis]" class="clustered_dis" value="{!! $rows['cluster_discount'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][ebd_dis]" class="ebd_dis" value="{!! $rows['ebd_dis'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][ebd_amounts]" class="ebd_amounts" value="{!! $rows['ebd_amount'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][deal_amounts]" class="deal_amounts" value="{!! $rows['deal_amount'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][deal_dis]" class="deal_dis" value="{!! $rows['deal_discount'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][special_dis]" class="special_dis" value="{!! $rows['special_dis'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][special_amounts]" class="special_amounts" value="{!! $rows['special_amounts'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][distributot_amounts]" class="distributot_amounts" value="{!! $rows['distributor_amount'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][distributot_dis]" class="distributot_dis" value="{!! $rows['distributor_discount'] !!}" hidden>


                                    <input type="text" name="orderdetail[{{ $key }}][frieght_dis]" class="frieght_dis" value="{!! $rows['frieght_discount'] !!}" hidden>

                                    <input type="text" name="orderdetail[{{ $key }}][frieght_amounts]" class="frieght_amounts" value="{!! $rows['frieght_amount'] !!}" hidden>



                                    <?php

                                    $gst_amount_fives = 0;
                                    $gst_amount_twels = 0;
                                    $gst_amount_eighteens = 0;
                                    $gst_amount_twenty_eights = 0;

                                    if ($rows['gst'] == 5) {
                                       //$gst_amount_fives+= $rows['gst_amount']; 
                                       $gst_amount_fives = $rows['tax_amount'];

                                    ?>
                                       <input type="text" name="orderdetail[{{ $key }}][five_gst]" class="five_gst" value="{!! $gst_amount_fives !!}" hidden>

                                    <?php } elseif ($rows['gst'] == 12) {
                                       //$gst_amount_twels+= $rows['gst_amount']; 
                                       $gst_amount_twels = $rows['tax_amount'];
                                    ?>

                                       <input type="text" name="orderdetail[{{ $key }}][twelve_gst]" class="twelve_gst" value="{!! $gst_amount_twels !!}" hidden>

                                    <?php } elseif ($rows['gst'] == 18) {
                                       //$gst_amount_eighteens+= $rows['gst_amount']; 
                                       $gst_amount_eighteens = $rows['tax_amount'];
                                    ?>

                                       <input type="text" name="orderdetail[{{ $key }}][eighteen_gst]" class="eighteen_gst" value="{!! $gst_amount_eighteens !!}" hidden>

                                    <?php } else {
                                       //$gst_amount_twenty_eights+= $rows['gst_amount']; 
                                       $gst_amount_twenty_eights = $rows['tax_amount'];
                                    ?>

                                       <input type="text" name="orderdetail[{{ $key }}][twenti_eight_gst]" class="twenti_eight_gst" value="{!! $gst_amount_twenty_eights !!}" hidden>
                                    <?php
                                    }

                                    ?>

                                 </td>

                                 <td>
                                    <input type="number" name='orderdetail[{{ $key }}][line_total]' class="form-control total" value="{!! $rows['line_total'] !!}" readonly />
                                 </td>

                                 <td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td>
                              </tr>
                              @endforeach
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
                     <!-- <p class="lead">{!! trans('panel.order.description') !!}</p>
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

                     <?php

                     $ebd_dicount_sum = 0;
                     $scheme_dicount_sum = 0;
                     $clustor_dicount_sum = 0;
                     $deal_dicount_sum = 0;
                     $distributor_dicount_sum = 0;
                     $frieght_dicount_sum = 0;


                     $cluster_discount = 0;
                     $deal_discount = 0;
                     $distributor_discount = 0;
                     $frieght_discount = 0;

                     $gst_amount_5 = 0;
                     $gst_amount_12 = 0;
                     $gst_amount_18 = 0;
                     $gst_amount_28 = 0;


                     if (!empty($orders['orderdetails'])) {

                        foreach ($orders['orderdetails'] as $keys => $rowss) {

                           $ebd_dicount_sum += $rowss['ebd_amounts'];
                           $scheme_dicount_sum += $rowss['scheme_amount'];
                           $clustor_dicount_sum += $rowss['cluster_amount'];
                           $deal_dicount_sum += $rowss['deal_amount'];
                           $distributor_dicount_sum += $rowss['distributor_amount'];
                           $frieght_dicount_sum += $rowss['frieght_amount'];



                           $cluster_discount = $rowss['cluster_discount'];
                           $deal_discount = $rowss['deal_discount'];
                           $distributor_discount = $rowss['distributor_discount'];
                           $frieght_discount = $rowss['frieght_discount'];

                           if ($rowss['gst'] == 5) {

                              //$gst_amount_5+= $rowss['gst_amount'];
                              $gst_amount_5 += $rowss['tax_amount'];
                           } elseif ($rowss['gst'] == 12) {

                              //$gst_amount_12+= $rowss['gst_amount'];
                              $gst_amount_12 += $rowss['tax_amount'];
                           } elseif ($rowss['gst'] == 18) {

                              //$gst_amount_18+= $rowss['gst_amount'];
                              $gst_amount_18 += $rowss['tax_amount'];
                           } else {

                              //$gst_amount_28+= $rowss['gst_amount'];
                              $gst_amount_28 += $rowss['tax_amount'];
                           }
                        }
                     }

                     ?>

                     <div id="all-discount-div-pump">
                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">Scheme Discount</label>
                           </div>
                           <div class="col-sm-8">
                              <!-- <input type="number" name='scheme_discount' id="scheme_discount" class="form-control scheme_discount" value="{!! old( 'scheme_discount', $orders['scheme_discount']) !!}" readonly/> -->

                              <input type="number" name='scheme_discount' id="scheme_discount" class="form-control scheme_discount" value="{!! old( 'scheme_discount', $scheme_dicount_sum) !!}" readonly />
                              @if($errors->has('scheme_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('scheme_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">EBD Discount%</label>
                           </div>
                           <div class="col-sm-4">
                              <select name='ebd_discount' class="form-control ebd_discount">
                                 <option value="">Select EBD Discount</option>
                                 <option value="1" {{($orders['ebd_discount'] == '1')?'selected':''}}>1%</option>
                                 <option value="2" {{($orders['ebd_discount'] == '2')?'selected':''}}>2%</option>
                                 <option value="3" {{($orders['ebd_discount'] == '3')?'selected':''}}>3%</option>
                              </select>
                           </div>
                           <div class="col-sm-4">
                              <input type="text" name="extra_ebd_discount" class="extra_ebd_discount form-control" value="{{$orders['ebd_amount']}}" readonly>
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">MOU Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>

                                 <input type="number" name='distributor_discount' class="form-control distributor_discount" value="{!! old( 'distributor_discount', $orders['distributor_discount']) !!}" />

                                 <input type="number" step="00.01" name="distributor_discount_amount" class="form-control distributor_discount_amount" readonly value="{!! old( 'distributor_discount_amount', $orders['distributor_amount']) !!}">
                              </div>
                              @if($errors->has('distributor_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('distributor_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">Special Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>
                                 <input type="number" name='special_discount' class="form-control special_discount" value="{!! old( 'special_discount', $orders['special_discount']) !!}" />

                                 <input type="text" name="special_discount_amount" class="form-control special_discount_amount" value="{!! old( 'special_discount_amount', $orders['special_amount']) !!}" readonly>
                              </div>
                              @if($errors->has('special_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('special_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">frieght Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>

                                 <div class="col-sm-4">
                                    <select name='frieght_discount' class="form-control frieght_discount">
                                       <option value="">Select Frieght Discount</option>
                                       <option value="1" {!! old( 'frieght_discount' , $orders['frieght_discount'])=='1' ?'selected':'' !!}>1%</option>
                                    </select>
                                 </div>
                                 <div class="col-sm-4">
                                    <input type="text" name="frieght_discount_amount" class="form-control frieght_discount_amount" readonly value="{!! old( 'frieght_discount_amount', $orders['frieght_amount']) !!}">
                                 </div>
                              </div>
                              @if($errors->has('frieght_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('frieght_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">Cluster Discount%</label>
                           </div>
                           <div class="col-sm-4">
                              <select name='cluster_discount' class="form-control cluster_discount">
                                 <option value="">Select Cluster Discount</option>
                                 <option value="1" <?php if ($orders['cluster_discount'] == 1) {
                                                      echo "selected";
                                                   } ?>>1%</option>
                                 <option value="2" <?php if ($orders['cluster_discount'] == 2) {
                                                      echo "selected";
                                                   } ?>>2%</option>
                                 <option value="3" <?php if ($orders['cluster_discount'] == 3) {
                                                      echo "selected";
                                                   } ?>>3%</option>
                              </select>
                           </div>
                           <div class="col-sm-4">
                              <!-- <input type="text" name="extra_cluster_discount" class="extra_cluster_discount" readonly>   -->
                              <input type="text" name="extra_cluster_discount" class="extra_cluster_discount form-control" readonly value="{!! old( 'extra_cluster_discount', $orders['cluster_amount']) !!}">
                           </div>

                        </div>


                        <div class="form-group row">
                           <div class="col-sm-4">
                              <!-- <label class="bmd-label">{!! trans('panel.order.extra_discount') !!}</label> -->
                              <label class="bmd-label">Deal Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>
                                 <!-- <input type="number" name='extra_discount' class="form-control deal_discnt" value="{!! old( 'extra_discount', $orders['extra_discount']) !!}"/> -->

                                 <!-- <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly> -->

                                 <input type="number" name='extra_discount' class="form-control deal_discnt" value="{!! old( 'extra_discount', $deal_discount) !!}" />

                                 <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly value="{!! old( 'extra_discount_amount', $orders['deal_amount']) !!}">
                              </div>
                              @if($errors->has('extra_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('extra_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                     <div id="all-discount-div-fan">

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">DOD%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>
                                 <input type="number" step="0.01" name='dod_discount' class="form-control dod_discount" value="{!! old( 'dod_discount', $orders['dod_discount']) !!}" />
                              </div>
                              @if($errors->has('dod_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('dod_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">Special Distribution Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>
                                 <input type="number" step="0.01" name='special_distribution_discount' class="form-control special_distribution_discount" value="{!! old( 'special_distribution_discount', $orders['special_distribution_discount']) !!}" />
                              </div>
                              @if($errors->has('special_distribution_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('special_distribution_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <!-- <label class="bmd-label">{!! trans('panel.order.extra_discount') !!}</label> -->
                              <label class="bmd-label">Distribution Margin Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>
                                 <input type="number" step="0.01" name='distribution_margin_discount' class="form-control distribution_margin_discount" value="{!! old( 'distribution_margin_discount', $orders['distribution_margin_discount']) !!}" />

                              </div>
                              @if($errors->has('distribution_margin_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('distribution_margin_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <label class="bmd-label">Cash Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>

                                 <input type="number" name='cash_discount' class="form-control cash_discount" value="{!! old( 'cash_discount', $orders['cash_discount']) !!}" />

                              </div>
                              @if($errors->has('cash_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('cash_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="form-group row">
                           <div class="col-sm-4">
                              <!-- <label class="bmd-label">{!! trans('panel.order.extra_discount') !!}</label> -->
                              <label class="bmd-label">Total Discount%</label>
                           </div>
                           <div class="col-sm-8">
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                 </div>
                                 <input readonly type="number" step="0.01" name='total_fan_discount' class="form-control total_fan_discount" step="0.01" value="{!! old( 'total_fan_discount', $orders['total_fan_discount']) !!}" />
                                 <input type="text" name="total_fan_discount_amount" class="form-control total_fan_discount_amount" value="{!! old( 'total_fan_discount_amount', $orders['total_fan_discount_amount']) !!}" readonly>
                              </div>
                              @if($errors->has('total_fan_discount'))
                              <div class="invalid-feedback">
                                 {{ $errors->first('total_fan_discount') }}
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>


                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">{!! trans('panel.order.sub_total') !!}</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="number" name='sub_total' class="form-control" id="subtotal" readonly value="{!! old( 'sub_total', $orders['sub_total']) !!}" />
                           @if($errors->has('sub_total'))
                           <div class="invalid-feedback">
                              {{ $errors->first('sub_total') }}
                           </div>
                           @endif
                        </div>
                     </div>



                     <!-- for tax start -->
                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">5%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="text" name='5_gst' class="form-control 5_gst" value="{!! old( '5_gst', $orders['gst5_amt']) !!}" readonly />
                           </div>
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">12%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="number" name='12_gst' class="form-control 12_gst" readonly value="{!! old( '12_gst', $orders['gst12_amt']) !!}" readonly />
                           </div>
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">18%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="number" name='18_gst' class="form-control 18_gst" readonly value="{!! old( '18_gst', $orders['gst18_amt']) !!}" readonly />
                           </div>
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">28%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="number" name='28_gst' class="form-control 28_gst" readonly value="{!! old( '28_gst', $orders['gst28_amt']) !!}" readonly />
                           </div>
                        </div>
                     </div>

                     <!-- for tax end -->



                     <!-- <div class="form-group row"> 
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
                     </div> -->


                     <div class="form-group row" hidden>
                        <div class="col-sm-4">
                           <label class="bmd-label">{!! trans('panel.order.total_discount') !!}</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="number" name='total_discount' id="totaldiscount" class="form-control" value="{!! old( 'total_discount', $orders['total_discount']) !!}" readonly />
                           @if($errors->has('total_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('total_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <!-- <div class="form-group row">
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
                     </div> -->
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


                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">Remark</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="text" name='order_remark' id="order_remark" class="form-control" value="{!! old( 'order_remark',$orders['order_remark']) !!}" />
                           @if($errors->has('order_remark'))
                           <div class="invalid-feedback">
                              {{ $errors->first('order_remark') }}
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
   <!-- <script src="{{ url('/').'/'.asset('assets/js/validation_orders.js') }}"></script> -->
   <!-- <script src="{{ url('/').'/'.asset('assets/js/invoice_js') }}"></script> -->
   <script type="text/javascript">




      $(document).ready(function() {


         var $table = $('table.kvcodes-dynamic-rows-example'),
            counter = $('#tab_logic tr:last').attr('value');
         $('a.add-rows').click(function(event) {
            event.preventDefault();
            counter++;
            var newRow =
               '<tr value="' + counter + '"> <td>' + counter + '</td>' +
               '<td><select name="orderdetail[' + counter + '][product_id]" class="form-control product rowchange select2" onchange="getproductinfo(this)"/> </select></td>' +
               '<td style="display:none;"> <select class="form-control productdetails rowchange select2" name="orderdetail[' + counter + '][product_detail]" onchange="getproductdetailinfo(this)" ></select><span class="gst_percent" style="display:none;"></span> <br><span class="gstamount" style="display:none;"></span> <br><span class="linediscount" style="display:none;"></span> <br><input type="hidden" name="orderdetail[' + counter + '][tax_amount]" class="form-control tax_amount readonly"/><input type="hidden" name="orderdetail[' + counter + '][discount_amount]" class="form-control discountamount readonly"/></td>' +

               '<td><input type="text" name="orderdetail[' + counter + '][quantity]" class="form-control quantity rowchange" /></td>' +

               '<td><input type="number" name="orderdetail[' + counter + '][mrp]" class="form-control price "readonly/></td>' +

               '<td><input type="text" name="orderdetail[' + counter + '][gst]" class="form-control gst_new "readonly/></td>' +
               '<td><input type="number" name="orderdetail[' + counter + '][discount]" class="form-control discount" readonly/></td>' +
               '<td><input type="text" name="orderdetail[' + counter + '][scheme_dis]"  class="scheme_dis form-control" readonly> <input type="text" name="orderdetail[' + counter + '][scheme_amount]" class="ebd_amount" hidden> <input type="text" name="orderdetail[' + counter + '][scheme_name]" class="scheme_name" hidden> <input type="text" name="orderdetail[' + counter + '][scheme_type]" class="scheme_type" hidden><input type="text" name="orderdetail[' + counter + '][scheme_value_type]" class="scheme_value_type" hidden><input type="text" name="orderdetail[' + counter + '][minimum]" class="minimum" hidden><input type="text" name="orderdetail[' + counter + '][maximum]" class="maximum" hidden><input type="text" name="orderdetail[' + counter + '][start_date]" class="start_date" hidden><input type="text" name="orderdetail[' + counter + '][end_date]" class="end_date" hidden></td>' +

               '<td><input type="text" name="orderdetail[' + counter + '][line_total]" class="form-control total rowchange" readonly /></td>' +
               '<td hidden> <input type="text" name="orderdetail[' + counter + '][clustered_dis]" class="clustered_dis"> <input type="text" name="orderdetail[' + counter + '][clus_amounts]" class="clus_amounts" hidden> </td>' +
               '<td hidden> <input type="text" name="orderdetail[' + counter + '][ebd_dis]" class="ebd_dis"> <input type="text" name="orderdetail[' + counter + '][ebd_amounts]" class="ebd_amounts" hidden> </td>' +
               '<td hidden><input type="text" name="orderdetail[' + counter + '][deal_dis]" class="deal_dis"><input type="text" name="orderdetail[' + counter + '][deal_amounts]" class="deal_amounts" hidden></td>' +
               '<td hidden><input type="text" name="orderdetail[' + counter + '][special_dis]" class="special_dis"><input type="text" name="orderdetail[' + counter + '][special_amounts]" class="special_amounts" hidden></td>' +
               '<td hidden><input type="text" name="orderdetail[' + counter + '][distributot_dis]" class="distributot_dis"><input type="text" name="orderdetail[' + counter + '][distributot_amounts]" class="distributot_amounts" hidden><input type="text" name="orderdetail[' + counter + '][frieght_dis]" class="frieght_dis"><input type="text" name="orderdetail[' + counter + '][frieght_amounts]" class="frieght_amounts"> <input type="text" name="orderdetail[' + counter + '][five_gst]" class="five_gst" hidden> <input type="text" name="orderdetail[' + counter + '][twelve_gst]" class="twelve_gst" hidden> <input type="text" name="orderdetail[' + counter + '][eighteen_gst]" class="eighteen_gst" hidden> <input type="text" name="orderdetail[' + counter + '][twenti_eight_gst]" class="twenti_eight_gst" hidden></td>' +
               '<td class="td-actions text-center"><a href="#" class="remove-rows btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td> </tr>';
            $table.append(newRow);
            $('.select2').select2({
               //theme: 'bootstrap4'
               minimumResultsForSearch: 10
            })


            //new 

            // setTimeout(function() {
            $('.cluster_discount').change();
            $('.ebd_discount').change();
            $('.deal_discnt').keyup();
            $('.distributor_discount').keyup();
            $('.special_discount').keyup();
            $('.frieght_discount').change();
            //}, 100);

            //end new  

         });

         $table.on('click', '.remove-rows', function() {
            $(this).closest('tr').remove();
            calc($(this));
            var detailID = $(this).closest('tr').find('input[name="order_detail_id"]').val();
            if (detailID != undefined) {
               $.ajax({
                  url: "{{ url('order-detail-delete') }}",
                  data: {
                     "detailID": detailID,
                  },
                  success: function(res) {
                     console.log(res);
                  }
               });
            }
         });
      });


      $('.cluster_discount').on('change', function() {
         var cls_dis = $(this).val();
         $('.clustered_dis').val(cls_dis);
         // calc();
      }).trigger('change');

      $('.ebd_discount').on('change', function() {
         var ebd_dis = $(this).val();
         if (ebd_dis && ebd_dis > 0) {
            $('.deal_discnt').prop('disabled', true);
         } else {
            $('.deal_discnt').prop('disabled', false);
         }
         $('.ebd_dis').val(ebd_dis);
         // calc();
      }).trigger('change');


      $('.deal_discnt').on('keyup', function() {
         var deal_discnt = $(this).val();
         if (deal_discnt && deal_discnt > 0) {
            $('.ebd_discount').prop('disabled', true);
         } else {
            $('.ebd_discount').prop('disabled', false);
         }
         $('.deal_dis').val(deal_discnt);
      }).trigger('keyup');


      $('.distributor_discount').on('keyup', function() {
         var distributot_dis = $(this).val();
         $('.distributot_dis').val(distributot_dis);
      }).trigger('keyup');

      $('.frieght_discount').on('change', function() {
         var frieght_discount = $(this).val();
         // $('#tab_logic tbody tr').find('.clustered_dis').val(cls_ter);
         $('.frieght_dis').val(frieght_discount);
      }).trigger('change');

      $('.special_discount').on('keyup', function() {
         var special_discount = $(this).val();
         $('.special_dis').val(special_discount);
      }).trigger('keyup');


      //new 

      sellerinfo();

      function sellerinfo() {
         var customer_id = $("select[name=seller_id]").val();

         if (customer_id) {
            $.ajax({
               url: "{{ url('getCustomerData') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  customer_id: customer_id
               },
               success: function(res) {
                  if (res) {
                     $(".seller_address").empty();

                     $('.seller_address').append(res.address1 + '<br> ' + res.address2 + '<br>Phone : ' + res.mobile + '<br>Email: ' + res.email);

                     var category = $('#product_cat_id').val();
                     if (res.customertype == '2') {
                        $('#de_dis').show();
                        $('#all-discount-div-pump').css('opacity', '0');
                        $('#all-discount-div-pump').css('height', '0px');
                        $('#all-discount-div-fan').css('opacity', '0');
                        $('#all-discount-div-fan').css('height', '0px');
                     } else {
                        if (category == '1') {
                           $('#all-discount-div-pump').css('opacity', '1');
                           $('#all-discount-div-pump').css('height', 'auto');
                           $('#all-discount-div-fan').css('opacity', '0');
                           $('#all-discount-div-fan').css('height', '0px');
                        } else if (category == '2') {
                           $('#all-discount-div-fan').css('opacity', '1');
                           $('#all-discount-div-fan').css('height', 'auto');
                           $('#all-discount-div-pump').css('opacity', '0');
                           $('#all-discount-div-pump').css('height', '0px');
                        } else {
                           $('#all-discount-div-pump').css('opacity', '0');
                           $('#all-discount-div-pump').css('height', '0px');
                           $('#all-discount-div-fan').css('opacity', '0');
                           $('#all-discount-div-fan').css('height', '0px');
                        }
                        $('#de_dis').hide();
                     }


                  } else {
                     $(".seller_address").empty();


                  }
               }
            });
         } else {
            $(".buyer_address").empty();
         }


      }



      function buyerinfo() {
         var customer_id = $("select[name=buyer_id]").val();
         if (customer_id) {
            $.ajax({
               url: "{{ url('getCustomerData') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  customer_id: customer_id
               },
               success: function(res) {
                  if (res) {
                     $(".buyer_address").empty();
                     $('.buyer_address').append(res.address1 + '<br> ' + res.address2 + '<br>Phone : ' + res.mobile + '<br>Email: ' + res.email);
                  } else {
                     $(".buyer_address").empty();
                  }
               }
            });
         } else {
            $(".buyer_address").empty();
         }
      }

      function getproductinfo(e) {
         var base_url = $('.baseurl').data('baseurl');
         var row = $(e).parent().parent();
         var product_id = $(e).val();

         $.ajax({
            url: "{{ url('getProductInfo') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               product_id: product_id
            },
            success: function(res) {
               row.find('.price').empty();
               row.find('.gst_percent').empty();

               //row.find('.price').val(res.price);
               row.find('.price').val(res.mrp);
               row.find('.gst_percent').append(res.gst);
               row.find('.gst_new').val(res.gst);
               row.find('.discount').val(res.discount);
               row.find('.discount').attr("max", res.max_discount);
               row.find('.scheme_dis').val(res.scheme_discount);
               row.find('.scheme_name').val(res.scheme_name);

               row.find('.scheme_type').val(res.scheme_type);
               row.find('.scheme_value_type').val(res.scheme_value_type);
               row.find('.minimum').val(res.minimum);
               row.find('.maximum').val(res.maximum);

               row.find('.start_date').val(res.start_date);
               row.find('.end_date').val(res.end_date);



               if (res.productdetails) {
                  $.each(res.productdetails, function(key, value) {
                     row.find('.productdetails').append('<option style value="' + value.id + '">' + value.detail_title + '</option>');

                  });
               }
            }
         });
      }

      function getproductdetailinfo(e) {
         var base_url = $('.baseurl').data('baseurl');
         var row = $(e).parent().parent();
         var productdetail_id = $(e).val();
         $.ajax({
            url: "{{ url('getProductDetailInfo') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               productdetail_id: productdetail_id
            },
            success: function(res) {
               row.find('.price').empty();
               row.find('.gst_percent').empty();
               //row.find('.price').val(res.price);
               row.find('.price').val(res.mrp);

               row.find('.gst_percent').append(res.gst);
               row.find('.gst_new').val(res.gst);
               row.find('.discount').val(res.discount);
               row.find('.discount').attr("max", res.max_discount);
            }
         });
      }


      $('#tab_logic tbody').on('keyup change', function() {
         calc();
      });


      function calc() {

         $('#tab_logic tbody tr').each(function(i, element) {

            var html = $(this).html();
            if (html != '') {

               var quantity = $(this).find('.quantity').val();
               var price = $(this).find('.price').val();
               var discount = $(this).find('.discount').val();
               var total = quantity * price;
               var discount_amount = total * discount / 100;
               total = total - discount_amount;



               //code for scheme
               var ebd_dis = 0;
               var ebd_discount = 0;

               var scheme_value_type = $(this).find('.scheme_value_type').val();

               if (scheme_value_type == 'percentage') {
                  ebd_discount = $(this).find('.scheme_dis').val();
                  ebd_dis = total * ebd_discount / 100;
                  total = total - ebd_dis;
                  var ebd_amount = $(this).find('.ebd_amount').val((ebd_dis).toFixed(2));

               }

               if (scheme_value_type == 'value') {
                  ebd_discount = $(this).find('.scheme_dis').val();
                  ebd_dis = ebd_discount;
                  total = total - ebd_discount;
                  var ebd_amount = $(this).find('.ebd_amount').val(ebd_dis);

               }



               //   ebd_discount = $(this).find('.scheme_dis').val();   
               // // var ebd_dis = total * ebd_discount / 100;
               //  ebd_dis = total * ebd_discount / 100;
               //  total = total - ebd_dis;
               //  var ebd_amount = $(this).find('.ebd_amount').val((ebd_dis).toFixed(2)); 

               //code scheme end

               //new code for EBD
               var ebd_discount = $(this).find('.ebd_dis').val();
               var ebd_dis = total * ebd_discount / 100;
               total = total - ebd_dis;

               $(this).find('.ebd_amounts').val((ebd_dis).toFixed(2));

               //end EBD 

               // start code for distributor
               var distributots_dis = $(this).find('.distributot_dis').val();
               var distributots_dis_cal = total * distributots_dis / 100;
               total = total - distributots_dis_cal;
               $(this).find('.distributot_amounts').val((distributots_dis_cal).toFixed(2));
               // end distributor

               // start special discount
               var special_dis = $(this).find('.special_dis').val();
               var special_dis_cal = total * special_dis / 100;
               total = total - special_dis_cal;

               $(this).find('.special_amounts').val((special_dis_cal).toFixed(2));
               // end special discount 

               // start frieght_discount

               var frieght_dis = $(this).find('.frieght_dis').val();
               var frieght_dis_cal = total * frieght_dis / 100;
               total = total - frieght_dis_cal;
               $(this).find('.frieght_amounts').val((frieght_dis_cal).toFixed(2));

               // frieght_discount end   



               //new code for clustor
               var clusters_discount = $(this).find('.clustered_dis').val();
               var clus_dis = total * clusters_discount / 100;
               total = total - clus_dis;

               $(this).find('.clus_amounts').val((clus_dis).toFixed(2));

               //end clustor 


               // start deal discount
               var deal_dis = $(this).find('.deal_dis').val();
               var deal_dis_cal = total * deal_dis / 100;
               total = total - deal_dis_cal;


               $(this).find('.deal_amounts').val((deal_dis_cal).toFixed(2));
               // end deal discount

               var gst = $(this).find('.gst_percent').html();
               var tax_amount = total * gst / 100;

               if (gst == 5) {
                  var five_amount = total * gst / 100;
                  $(this).find('.five_gst').val((five_amount).toFixed(2));
               } else if (gst == 12) {

                  var twelve_amount = total * gst / 100;
                  $(this).find('.twelve_gst').val((twelve_amount).toFixed(2));

               } else if (gst == 18) {
                  var eighteen_amount = total * gst / 100;
                  $(this).find('.eighteen_gst').val((eighteen_amount).toFixed(2));

               } else {
                  var twenty_eight_amount = total * gst / 100;
                  $(this).find('.twenti_eight_gst').val((twenty_eight_amount).toFixed(2));
               }


               $(this).find('.total').val((total).toFixed(2));


               //$('#tab_logic tr:last').find('.total').val((total).toFixed(2));
               //$('#tab_logic tr:last').find(".total").empty();

               //$('#tab_logic tr:last').find('.total').val((total).toFixed(2));
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



      function calc_total() {

         total_new_ebd = 0;
         total_scheme = 0;

         clus_amounts_new = 0;
         total_slustor_amount_new = 0;

         deal_amounts_new = 0;
         total_deal_amounts_new = 0;

         distributot_amounts_new = 0;
         total_distributot_amounts_new = 0;


         five_gst_new = 0;
         total_five_gst_new = 0;

         twelve_gst_new = 0;
         total_twelve_gst_new = 0;


         eighteen_gst_new = 0;
         total_eighteen_gst_new = 0;

         twenti_eight_gst_new = 0;
         total_twenti_eight_gst_new = 0;


         five_gst = 0;
         twelve_gst = 0;
         eighteen_gst = 0;
         twenti_eight_gst = 0;

         clus_amounts = 0;
         ebd_amounts = 0;
         scheme_discount = 0;
         deal_amounts = 0;
         special_amounts = 0;
         cluster_amnt = 0;
         ebd_amnt = 0;
         distributot_amounts = 0;
         frieght_amounts = 0;

         total = 0;
         subtotal = 0;
         taxamount = 0;
         discount = 0;
         transportation = 0;
         var extra_discount = $("input[name=extra_discount]").val();


         discount_amount_cluster = 0;


         var transportamt = $("#transportation_amount").val();
         if (transportamt) {
            transportation = parseInt(transportamt);
         }
         var discount_amount = 0;
         $('.total').each(function() {
            // total += parseInt($(this).val());
            total += parseFloat($(this).val());
         });

         //scheme discount start
         $('.ebd_amount').each(function() {
            // scheme_discount += parseInt($(this).val());
            scheme_discount += parseFloat($(this).val());
         });


         //new skm

         $('.ebd_amount_new').each(function() {
            total_new_ebd += parseFloat($(this).val());
         });


         total_scheme = total_new_ebd + scheme_discount;

         //new skm end

         //$('#scheme_discount').val((scheme_discount).toFixed(2));
         if (total_scheme > 0) {
            $('#scheme_discount').val((total_scheme).toFixed(2));
         }
         //scheme discount end

         //cluster discout start
         var cluster_amnt = $(".cluster_discount").val();
         discount_amount_cluster_new = total * cluster_amnt / 100;
         // $('.extra_cluster_discount').val(discount_amount_cluster_new);
         //$('.extra_cluster_discount').val(discount_amount_cluster_new.toFixed(2));

         $('.clus_amounts').each(function() {
            clus_amounts += parseFloat($(this).val());
            // $('.extra_cluster_discount').val(clus_amounts.toFixed(2));
         });


         $('.clus_amounts_new').each(function() {
            clus_amounts_new += parseFloat($(this).val());
         });

         total_slustor_amount_new = clus_amounts + clus_amounts_new;

         $('.extra_cluster_discount').val(clus_amounts.toFixed(2));


         //cluster discout end

         //ebd discout start
         var ebd_amnt = $(".ebd_discount").val();
         discount_amount_ebd_new = total * ebd_amnt / 100;
         $('.ebd_amounts').each(function() {
            ebd_amounts += parseFloat($(this).val());
            $('.extra_ebd_discount').val(ebd_amounts.toFixed(2));
         });

         //ebd discout end


         //deal discount
         $('.deal_amounts').each(function() {
            deal_amounts += parseFloat($(this).val());
            // $('.extra_discount_amount').val(deal_amounts.toFixed(2));
         });

         $('.deal_amounts_new').each(function() {
            deal_amounts_new += parseFloat($(this).val());
         });

         total_deal_amounts_new = deal_amounts + deal_amounts_new;


         $('.extra_discount_amount').val(deal_amounts.toFixed(2));


         //deal discoun

         //special discount
         $('.special_amounts').each(function() {
            special_amounts += parseFloat($(this).val());
            $('.special_discount_amount').val(special_amounts.toFixed(2));
         });
         //special discoun


         //distributor discount
         $('.distributot_amounts').each(function() {
            distributot_amounts += parseFloat($(this).val());
            // $('.distributor_discount_amount').val(distributot_amounts.toFixed(2));
         });

         $('.distributot_amounts_new').each(function() {
            distributot_amounts_new += parseFloat($(this).val());
         });

         total_distributot_amounts_new = distributot_amounts + distributot_amounts_new;

         $('.distributor_discount_amount').val(distributot_amounts.toFixed(2));

         //end distributor discount


         //frieght_discount start

         $('.frieght_amounts').each(function() {
            frieght_amounts += parseFloat($(this).val());
            $('.frieght_discount_amount').val(frieght_amounts.toFixed(2));
         });


         //frieght_discount  end







         $('.tax_amount').each(function() {
            taxamount += parseInt($(this).val());
         });

         $('.discountamount').each(function() {
            discount += parseInt($(this).val());
         });


         //new calculation

         subtotal = total;

         //Fan calculations start

         var distribution_margin_discount = ($('.distribution_margin_discount').val() && $('.distribution_margin_discount').val() != '') ? $('.distribution_margin_discount').val() : 0;
         var special_distribution_discount = ($('.special_distribution_discount').val() && $('.special_distribution_discount').val() != '') ? $('.special_distribution_discount').val() : 0;
         var dod_discount = $('.dod_discount').val() && $('.dod_discount').val() != '' ? $('.dod_discount').val() : 0;
         var cash_discount = $('.cash_discount').val() && $('.cash_discount').val() != '' ? $('.cash_discount').val() : 0;

         var ttdis = parseFloat(dod_discount) + parseFloat(special_distribution_discount) + parseFloat(distribution_margin_discount) + parseFloat(cash_discount);
         $('.total_fan_discount').val(ttdis.toFixed(2));
         $('.total_fan_discount_amount').val((total * ttdis) / 100);
         subtotal = ((total - (total * ttdis) / 100));

         //Fan calculations end

         //end ne calculation

         discount_amount = total * extra_discount / 100;

         //conditions for tax start

         $('.five_gst').each(function() {
            five_gst += parseFloat($(this).val()) || 0;
            $('.5_gst').val(five_gst.toFixed(2));
         });


         $('.twelve_gst').each(function() {
            twelve_gst += parseFloat($(this).val()) || 0;
            $('.12_gst').val(twelve_gst.toFixed(2));
         });


         $('.eighteen_gst').each(function() {
            eighteen_gst += parseFloat($(this).val()) || 0;
            $('.18_gst').val(eighteen_gst.toFixed(2));
         });


         $('.twenti_eight_gst').each(function() {
            twenti_eight_gst += parseFloat($(this).val()) || 0;
            $('.28_gst').val(twenti_eight_gst.toFixed(2));
         });

         //conditions for tax  end 

         $('#subtotal').val((subtotal).toFixed(2));
         $('#totalgst').val(taxamount.toFixed(2));
         $('#totaldiscount').val(discount.toFixed(2));

         //$('.extra_discount_amount').empty();
         //$('.extra_discount_amount').val(discount_amount.toFixed(2));
         //$('#grandtotal').val((total+taxamount+transportation).toFixed(2));
         //$('#grandtotal').val((total+five_gst+transportation).toFixed(2));

         $('#grandtotal').val((subtotal + five_gst + twelve_gst + eighteen_gst + twenti_eight_gst).toFixed(2));

         estimatedDelivery();
      }




      ///*****************new code ********************

      ///****************new code end**************************




      function getProductlist() {
         var base_url = $('.baseurl').data('baseurl');
         $.ajax({
            url: "{{ url('getProductData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}"
            },
            success: function(res) {
               var table = document.getElementById(tab_logic),
                  rIndex;
               if (res) {
                  $('#tab_logic tr:last').find(".product").empty();
                  $('#tab_logic tr:last').find(".product").append('<option value="">Select Product</option>');
                  $.each(res, function(key, value) {

                     if (value.product_code) {
                        var productcode = value.product_code
                     } else {
                        var productcode = '';
                     }

                     $('#tab_logic tr:last').find('.product').append('<option value="' + value.id + '">' + value.product_name + productcode + '</option>');

                  });
               } else {
                  row.find(".product").empty();
               }
            }
         });
      }

      function estimatedDelivery() {
         totalday = 1;
         var orderdate = $('#order_date').val();
         var maxday = $('#maxday').val();
         var placeday = $('#placedeliveryday').val();
         var datatoday = new Date(orderdate);
         // totalday += parseInt(placeday);
         // totalday += parseInt(maxday);
         // var datatodays = datatoday.setDate(new Date(datatoday).getDate() + totalday);
         $('#estimated_date').val(orderdate);
      }



      $('#product_cat_id').on('change', function() {
         var category = $(this).val();
         if (category == '1') {
            $('#all-discount-div-pump').css('opacity', '1');
            $('#all-discount-div-pump').css('height', 'auto');
            $('#all-discount-div-fan').css('opacity', '0');
            $('#all-discount-div-fan').css('height', '0px');
            $('#all-discount-div-agri').css('opacity', '0');
            $('#all-discount-div-agri').css('height', '0px');
         } else if (category == '2') {
            $('#all-discount-div-fan').css('opacity', '1');
            $('#all-discount-div-fan').css('height', 'auto');
            $('#all-discount-div-pump').css('opacity', '0');
            $('#all-discount-div-pump').css('height', '0px');
            $('#all-discount-div-agri').css('opacity', '0');
            $('#all-discount-div-agri').css('height', '0px');
         } else if (category == '4') {
            $('#all-discount-div-agri').css('opacity', '1');
            $('#all-discount-div-agri').css('height', 'auto');
            $('#all-discount-div-pump').css('opacity', '0');
            $('#all-discount-div-pump').css('height', '0px');
            $('#all-discount-div-fan').css('opacity', '0');
            $('#all-discount-div-fan').css('height', '0px');
         } else {
            $('#all-discount-div-pump').css('opacity', '0');
            $('#all-discount-div-pump').css('height', '0px');
            $('#all-discount-div-fan').css('opacity', '0');
            $('#all-discount-div-fan').css('height', '0px');
            $('#all-discount-div-agri').css('opacity', '0');
            $('#all-discount-div-agri').css('height', '0px');
         }
      }).trigger('change');



      // setTimeout(() => {
      //  var initialized = false; // Flag to track initialization   
      // $('#seller_id').select2({
      //     placeholder: 'Please Select...',
      //     allowClear: true,
      //     ajax: {
      //         url: "{{ route('getCustomerDataSelect') }}",
      //         dataType: 'json',
      //         delay: 250,
      //         data: function(params) {
      //             return {
      //                 term: params.term || '',
      //                 page: params.page || 1
      //             };
      //         },
      //         cache: true
      //     }
      // }).on('select2:open', function() {
      //     // Check if already initialized
      //     if (!initialized) {
      //         var oldValues = ['16', '12', '17', '19'];
      //         $('#seller_id').val(oldValues).trigger('change');
      //         initialized = true; // Set flag to true
      //     }
      // });

      // }, 1000);
   </script>

@section('script')
   <script type="">

      $(document).ready(function () {
         function initializeSelect2(selector, is_seller, selectedId = null) {
            // Initialize select2
            $(selector).select2({
               ajax: {
                  url: "{{ url('getSellerBuyer') }}",
                  type: "POST",
                  dataType: "json",
                  delay: 250, // Delay to prevent excessive requests
                  data: function (params) {
                     return {
                           _token: "{{ csrf_token() }}",
                           q: params.term, // Search term
                           page: params.page || 1, // Pagination
                           is_seller: is_seller,
                           selected: selectedId // Include selected ID for pre-loading
                     };
                  },
                  processResults: function (data, params) {
                     params.page = params.page || 1;
                 
                     // If the search term is empty, fetch the first 10 records
                     if (!params.term) {
                           return {
                              results: $.map(data.results, function (item) {
                                 return { id: item.id, text: item.name };
                              }),
                              pagination: { more: data.pagination.more }
                           };
                     }

                     // If there is a search term, return matching records
                     return {
                           results: $.map(data.results, function (item) {
                              return { id: item.id, text: item.name };
                           }),
                           pagination: { more: data.pagination.more }
                     };
                  },
                  cache: true
               },
               placeholder: "Search...",
        });


            // Load pre-selected value if available
            if (selectedId) {
                  $.ajax({
                     url: "{{ url('getSellerBuyer') }}",
                     type: "POST",
                     dataType: "json",
                     data: {
                        _token: "{{ csrf_token() }}",
                        selected: selectedId,
                        is_seller: is_seller
                     },
                     success: function (res) {
                        // Append the selected user
                        if (selectedId) {
                           let selectedItem = res.results.find(item => item.id == selectedId); // Find the selected item from the response
                           if (selectedItem) {
                              let newOption = new Option(selectedItem.name, selectedItem.id, true, true);
                              $(selector).append(newOption).trigger('change'); // Append the selected user
                           }
                        }

                        // Append the rest of the users (10 others), avoiding duplicate selected user
                        res.results.forEach(function (item) {
                           if (item.id !== selectedId) {
                              let newOption = new Option(item.name, item.id, false, false);
                              $(selector).append(newOption).trigger('change');
                           }
                        });
                     }
                  });
            }
         }

         // Get selected IDs from the backend
         var buyer_id = "{{ $orders->buyer_id ?? '' }}";
         var seller_id = "{{ $orders->seller_id ?? '' }}";

         // Initialize Select2 for seller and buyer with selected IDs
         initializeSelect2("#seller_id", 1, buyer_id);
         initializeSelect2("#buyer_id", 0, seller_id);
      });
   </script>
@endsection

</x-app-layout>