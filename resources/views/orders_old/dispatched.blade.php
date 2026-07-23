<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-icon card-header-theme">
           <div class="card-icon">
             <i class="material-icons">perm_identity</i>
           </div>
           <h4 class="card-title "> Order Partially Dispatched </h4>
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
         <div class="row">
             <div class="col-12">
               <h4>
                 <img src="{!! asset('assets/img/logo.png') !!}" class="brand-image" width="70px" alt="Logo"> <span> {!! config('app.name') !!}</span>
                 <small class="float-right">Date: {!! date("d-M-Y", strtotime($orders['order_date'])) !!}</small>
               </h4>
             </div>
         </div>
         <hr>
         <div class="row invoice-info">
            <div class="col-sm-6 invoice-col">
               From
               <address>
                 <strong>{!! isset($orders['sellers']['name']) ? $orders['sellers']['name'] :'' !!} </strong><br>
                {!! $orders['sellers']['customeraddress']['address1'] !!} {!! $orders['sellers']['customeraddress']['address2'] !!}<br>
                 {!! $orders['sellers']['customeraddress']['cityname']['city_name'] !!} {!! $orders['sellers']['customeraddress']['pincodename']['pincode'] !!}<br>
                 Phone: {!! $orders['sellers']['mobile'] !!}<br>
                 Email: {!! $orders['sellers']['email'] !!}
               </address>
            </div>
            <div class="col-sm-6 invoice-col">
               <strong>To </strong>
               <address>
                 <strong>{!! $orders['buyers']['name'] !!}</strong><br>
                {!! $orders['buyers']['customeraddress']['address1'] !!} ,{!! $orders['buyers']['customeraddress']['address2'] !!}<br>{!! isset($orders['buyers']['customeraddress']['cityname']['city_name']) ? $orders['buyers']['customeraddress']['cityname']['city_name'] :'' !!} {!! isset($orders['buyers']['customeraddress']['pincodename']['pincode']) ? $orders['buyers']['customeraddress']['pincodename']['pincode'] :'' !!}<br>
                 Phone: {!! $orders['buyers']['mobile'] !!}<br>
                 Email: {!! $orders['buyers']['email'] !!}
               </address>
            </div>
         </div>
         <hr>
         <div class="row invoice-info">
             <div class="col-sm-6 invoice-col">
                <div class="row">
                   <div class="col-md-4">Order ID</div>
                   <div class="col-md-8">{!! $orders['id'] !!}</div>
                </div>
                <div class="row">
                   <div class="col-md-4">Order No</div>
                   <div class="col-md-8">{!! $orders['orderno'] !!}</div>
                </div>
             </div>
             <div class="col-sm-6 invoice-col">
                <div class="row">
                   <div class="col-md-4">Order Date</div>
                   <div class="col-md-8">{!! $orders['order_date'] !!}</div>
                </div>
                <div class="row">
                   <div class="col-md-4">Order Status</div>
                   <div class="col-md-8">{!! isset($orders['status']['status_name']) ? $orders['status']['status_name'] :'' !!}</div>
                </div>
             </div>
          </div>
         <hr>
            {!! Form::open(['url' => 'submit-dispatched' , 'method' => 'POST']) !!}
               <input type="hidden" name="buyer_id" value="{!! $orders['buyer_id'] !!}">
               <input type="hidden" name="seller_id" value="{!! $orders['seller_id'] !!}">
               <input type="hidden" name="order_id" value="{!! $orders['id'] !!}">
               <input type="hidden" name="orderno" value="{!! $orders['orderno'] !!}">
               <div class="row invoice-info">
                  <div class="col-sm-6 invoice-col">
                     <div class="row">
                        <label class="col-md-4">Invoice Date</label>
                         <div class="col-md-9">
                           <div class="form-group has-default bmd-form-group">
                              <input type="text" name="invoice_date" class="form-control datepicker" value="{!! old( 'invoice_date') !!}" autocomplete="off" readonly>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-6 invoice-col">
                     <div class="row">
                        <label class="col-md-4">Invoice No</label>
                         <div class="col-md-9">
                           <div class="form-group has-default bmd-form-group">
                              <input type="text" class="form-control" name="invoice_no" value="{!! old( 'invoice_no') !!}" required>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <br>
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
                              @foreach($orders['orderdetails'] as $index => $rows )
                                 @if($rows['quantity']-$rows['shipped_qty'] >= 1 )
                                 <tr id='addr0'>
                                    <td>{!! $index +1 !!}</td>
                                    <td>
                                       <input type="hidden" name="orderdetail[{!! $index !!}][product_id]" value="{!! $rows['product_id'] !!}">
                                       {!! $rows['products']['display_name'] !!} <br>
                                    </td>
                                    <td>
                                       <input type="hidden" name="orderdetail[{!! $index !!}][product_detail]" value="{!! $rows['product_detail_id'] !!}">
                                       {!! isset($rows['productdetails']['detail_title']) ? $rows['productdetails']['detail_title'] :'' !!}
                                       <span class="gst_percent" style="display:none;">
                                       {!! isset($rows['productdetails']['gst']) ? $rows['productdetails']['gst'] :'' !!}</span> <br>
                                       <span class="gstamount" style="display:none;">
                                          {!! isset($rows['tax_amount']) ? $rows['tax_amount'] :'' !!}</span> <br>
                                       <span class="linediscount" style="display:none;">{!! isset($rows['discount_amount']) ? $rows['discount_amount'] :'' !!}</span>
                                       <input type="hidden" name="orderdetail[{!! $index !!}][tax_amount]" class="form-control tax_amount readonly" value="{!! isset($rows['tax_amount']) ? $rows['tax_amount'] :'' !!}" />
                                       <input type="hidden" name="orderdetail[{!! $index !!}][discount_amount]" class="form-control discountamount readonly" value="{!! isset($rows['discount_amount']) ? $rows['discount_amount'] :'' !!}"/>
                                    </td>
                                    <td>
                                       <input type="number" name="orderdetail[{!! $index !!}][price]" class="form-control price rowchange" step="0.01" min="0" value="{!! $rows['price'] !!}" />
                                       <div class='error-price'></div>
                                    </td>
                                    <td>
                                       <input type="number" name="orderdetail[{!! $index !!}][discount]" class="form-control discount rowchange" step="0.01" min="0" value="{!! $rows['discount'] !!}" />
                                       <div class='error-discount'></div>
                                    </td>
                                    <td>
                                       <input type="number" name='orderdetail[{!! $index !!}][quantity]' class="form-control quantity rowchange" step="0" min="0" max="{!! $rows['quantity']-$rows['shipped_qty'] !!}" value="{!! $rows['quantity']-$rows['shipped_qty'] !!}" />
                                       <div class='error-quantity'></div>
                                    </td>
                                    <td>
                                       <input type="number" name='orderdetail[{!! $index !!}][line_total]' class="form-control total" value="{!! $rows['line_total'] !!}" readonly/>
                                    </td>
                                    <td class="td-actions text-center"><a class="remove btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td>
                                 </tr>
                                 <tr id='addr1'></tr>
                                 @endif
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
                  <div class="col-6">
                    
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
          counter = 1;
      $('a.add-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr> <td>'+counter+'</td>'+
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
      var extra_discount = $("input[name=extra_discount]").val();
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
      $('#grandtotal').val((total+taxamount).toFixed(2));
    }
   
    function getProductlist()
    {
      var base_url =$('.baseurl').data('baseurl'); 
        $.ajax({
          url: "{{ url('getProductData') }}",
          dataType: "json",
          type: "POST",
          data:{ _token: "{{csrf_token()}}"},
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
</script>
</x-app-layout>