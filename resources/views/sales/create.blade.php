<x-app-layout>

  <div class="row">
    <div class="col-md-12">
      <div class="card pt-0 mt-0">
        <div class="card-header card-header-tabs card-header-warning m-0">
          <div class="nav-tabs-navigation">
            <div class="nav-tabs-wrapper new_id">
              <h4 class="card-title ">{!! trans('panel.sale.title_singular') !!}
                    </h4>
                @if(auth()->user()->can(['sale_access']))
                <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('sales') }}">
                      <i class="material-icons">next_plan</i> {!! trans('panel.sale.title') !!}
                      <div class="ripple-container"></div>
                    </a>
                  </li>
                </ul>
                @endif
          
            </div>
          </div>
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
          {!! Form::model($sales,[
          'route' => $sales->exists ? ['sales.update', encrypt($sales->id)] : 'sales.store',
          'method' => $sales->exists ? 'PUT' : 'POST',
          'id' => 'storeSalesdData',
          'files'=>true
          ]) !!}
          <div class="row">
            <div class="col-md-2">
              <div class="input_section">
              
                  <input type="hidden" name="sales_id" class="form-control datepicker" value="{!! old( 'id', $sales['id']) !!}" autocomplete="off" >
                  <input type="text" name="invoice_date" class="form-control datepicker" value="{!! old( 'invoice_date', $sales['invoice_date']) !!}" autocomplete="off" >
                  @if($errors->has('invoice_date'))
                  <div class="invalid-feedback">
                    {{ $errors->first('invoice_date') }}
                  </div>
                  @endif
               
              </div>
            </div>
            <!-- /.col -->
          </div>
          <div class="row invoice-info">
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.global.bill_from') !!}</label>
              <address>
                <strong>
                  <select class="form-control select2 buyer" name="seller_id" style="width: 100%;" required onchange="sellerinfo()">
                    <option value="" selected disabled>Select {!! trans('panel.global.seller') !!} <span class="text-danger"> *</span></option>
                    @if(@isset($sellers ))
                    @foreach($sellers as $seller)
                    <option value="{!! $seller['id'] !!}" {!! ($sales['seller_id']==$seller['id']) ? 'selected' : '' !!}>{!! $seller['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </strong><br>
                <address>
                  Address:<span class="seller_address"></span>
                </address>
            </div>
            </div>
                <div class="col-md-4">
              <div class="input_section"> <label class="col-form-label">{!! trans('panel.global.bill_to') !!}</label>
              
              <strong>
                <select class="form-control select2 buyer" name="buyer_id" style="width: 100%;" required onchange="buyerinfo()">
                  <option value="" selected disabled>Select {!! trans('panel.global.buyer') !!} <span class="text-danger"> *</span></option>
                  @if(@isset($buyers ))
                  @foreach($buyers as $buyer)
                  <option value="{!! $buyer['id'] !!}" {!! ($sales['buyer_id']==$buyer['id']) ? 'selected' : '' !!}>{!! $buyer['name'] !!}</option>
                  @endforeach
                  @endif
                </select>
                @if($errors->has('buyer_id'))
                <div class="invalid-feedback">
                  {{ $errors->first('buyer_id') }}
                </div>
                @endif
              </strong>
              <address>
                Address:<span class="buyer_address"></span>
              </address>
            </div>
             </div>
                 <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Invoice</label>
                
                  <input type="text" readonly class="form-control" name="invoice_no" value="{!! old( 'invoice_no', $sales['invoice_no']) !!}" required>
                  @if($errors->has('invoice_no'))
                  <div class="invalid-feedback">
                    {{ $errors->first('invoice_no') }}
                  </div>
                  @endif
                </div>
              </div>
          <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Order No</label>
                  <input type="text" readonly class="form-control" name="orderno" value="{!! old( 'orderno', $sales['orderno']) !!}" required>
                  @if($errors->has('orderno'))
                  <div class="invalid-feedback">
                    {{ $errors->first('orderno') }}
                  </div>
                  @endif
              </div>
            </div>

             <div class="col-md-4">
              <div class="input_section">
              <label class="col-form-label">Transport Name</label>
                <div class="form-group has-default bmd-form-group">
                  <textarea class="form-control" name="transport_details" id="transport_details" cols="30" rows="3">{!! old( 'transport_details', $sales['transport_details']) !!}</textarea>
                </div>
              </div>
            </div>

            <div class="col-md-4">
            <div class="input_section">
              <label class="col-form-label">LR No</label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="lr_no" class="form-control" value="{!! old( 'lr_no', $sales['lr_no']) !!}" autocomplete="off" required>
                </div>
              </div>
               </div>
               <div class="col-md-4">
<div class="input_section">
              <label class="col-form-label">Dispatch Date</label>
          
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="dispatch_date" class="form-control datepicker"  value="{!! old( 'dispatch_date', $sales['dispatch_date']) !!}" autocomplete="off" required>
                </div>
          </div>
            </div>



  </div>

            <!-- /.col -->

            <!-- /.col -->

            </div>
            <!-- /.col -->
          </div>


          <div class="row">
            <div class="container-fluid mt-5 d-flex justify-content-centerbg-dark w-100">
              <div class="table-responsive w-100">
                <table class="table kvcodes-dynamic-rows-example mb-0" id="tab_logic">
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
                  <tbody>
                    @if($sales->exists && isset($sales['saledetails']))
                    @foreach($sales['saledetails'] as $index => $rows )
                    <tr id='addr0'>
                      <td>{!! $index +1 !!}</td>
                      <td>
                        <div class="input_section">
                        <select class="form-control product rowchange select2" name="saledetail[{!! $index !!}][product_id]">
                          <option value="{!! $rows['product_id'] !!}">{!! $rows['products']['display_name'] !!}</option>
                        </select>
                        <div class="error-product"></div>
                      </div>
                      </td>
                      <td>
                        <div class="input_section">
                        GST Percent :
                        <span class="gst_percent">
                          {!! isset($rows['products']['productdetails']['gst']) ? $rows['products']['productdetails']['gst'] : '' !!}
                        </span>
                        <br>
                        GST Amount :
                        <span class="gstamount">
                          {!! $rows['tax_amount'] !!}
                        </span>
                        <br>
                        Discount :
                        <span class="gstamount">
                          {!! $rows['discount_amount'] !!} </span>
                        <input type="hidden" name="saledetail[{!! $index !!}[tax_amount]" class="form-control tax_amount" value="{!! $rows['tax_amount'] !!}" readonly />
                        <input type="hidden" name="saledetail[{!! $index !!}[discount_amount]" class="form-control discountamount" value="{!! $rows['discount_amount'] !!}" readonly />
                      </div>
                      </td>
                      <td width="20%">
                        <div class="input_section">
                        <input type="number" readonly name="saledetail[{!! $index !!}][price]" class="form-control price rowchange" step="0.00" min="0" value="{!! $rows['price'] !!}" />
                        <div class='error-price'></div>
                      </div>
                      </td>
                      <td>
                        <div class="input_section">
                        <input type="number" readonly name="saledetail[{!! $index !!}[discount]" class="form-control discount rowchange" step="0.00" min="0" value="{!! $rows['discount'] !!}" />
                        <div class='error-discount'></div>
                      </div>
                      </td>
                      <td width="15%">
                        <div class="input_section">
                        <input type="number" readonly name='saledetail[{!! $index !!}][quantity]' class="form-control quantity rowchange" step="0" min="0" value="{!! $rows['quantity'] !!}" />
                        <div class='error-quantity'></div>
                      </div>
                      </td>

                      <td width="15%">
                        <div class="input_section">
                        <input type="number" name='saledetail[{!! $index !!}][line_total]' class="form-control total" value="{!! $rows['line_total'] !!}" readonly />
                      </div>
                      </td>
                      <td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></td>
                    </tr>
                    <tr id='addr1'></tr>
                    @endforeach
                    @else
                    <tr id='addr0' value="1">
                      <td>1</td>
                      <td>
                        <div class="input_section">
                        <select class="form-control product rowchange select2" name="saledetail[1][product_id]" onchange="getproductinfo(this)" data-url="{{URL::To('sales/product_detail')}}">
                          @if(@isset($products))
                          <option value="">Select Product</option>
                          @foreach($products as $product )
                          <option value="{{ $product['id'] }}">{{ $product['display_name'] }}</option>
                          @endforeach
                          @endif
                        </select>
                        <div class="error-product"></div>
                      </div>
                      </td>
                      <td>
                        <div class="input_section">
                        <select class="form-control productdetails rowchange select2" name="saledetail[1][product_detail]" onchange="getproductdetailinfo(this)">

                        </select>
                        <span class="gst_percent" style="display:none;"></span> <br>
                        <span class="gstamount" style="display:none;"></span> <br>
                        <span class="linediscount" style="display:none;"></span>
                        <input type="hidden" name="saledetail[1][tax_amount]" class="form-control tax_amount readonly" />
                        <input type="hidden" name="saledetail[1][discount_amount]" class="form-control discountamount readonly" />
                      </div>
                      </td>
                      <td>
                        <div class="input_section">
                        <input type="number" name="saledetail[1][price]" class="form-control price rowchange" step="0.00" min="0" />
                        <div class='error-price'></div>
                      </div>
                      </td>
                      <td>
                        <div class="input_section">
                        <input type="number" name="saledetail[1][discount]" class="form-control discount rowchange" step="0.00" min="0" />
                        <div class='error-discount'></div>
                      </div>
                      </td>
                      <td>
                        <div class="input_section">
                        <input type="number" name='saledetail[1][quantity]' class="form-control quantity rowchange" step="0" min="0" />
                        <div class='error-quantity'></div>
                      </div>
                      </td>
                      <td>
                        <div class="input_section">
                        <input type="number" name='saledetail[1][line_total]' class="form-control total" readonly />
                      </div>
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
              <table class="table">
                <tbody>
                  <tr>
                    <td class="td-actions">
                      <a href="#" title="" class="btn btn-success btn-just-icon btn-sm add-rows" onclick="getProductlist()"> <i class="fa fa-plus"></i> </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="baseurl" style="background-color: #fff" data-baseurl="{{ url('/')}}">
            <div class="row">
              <!-- accepted payments column -->
              <!-- <div class="col-6"> -->
                <!-- <p class="lead">{!! trans('panel.sale.fields.description') !!}</p>
                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                <div class="form-group row">
                  <textarea class="form-control" name="description">{!! old( 'description', $sales['description']) !!}</textarea>

                  @if($errors->has('description'))
                  <div class="invalid-feedback">
                    {{ $errors->first('description') }}
                  </div>
                  @endif
                </div>
                </p> -->
              <!-- </div> -->
              <!-- /.col -->
              <div class="col-md-6">
                <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.sale.fields.sub_total') !!}</label>
                    <input type="number" name='sub_total' class="form-control" id="subtotal" readonly value="{!! old( 'sub_total', $sales['sub_total']) !!}" />
                    @if($errors->has('sub_total'))
                    <div class="invalid-feedback">
                      {{ $errors->first('sub_total') }}
                    </div>
                    @endif
                 
                </div>
                </div>


                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.sale.fields.total_gst') !!}</label>
               
                    <input type="number" name='total_gst' id="totalgst" class="form-control" value="{!! old( 'total_gst', $sales['total_gst']) !!}" readonly />
                    @if($errors->has('total_gst'))
                    <div class="invalid-feedback">
                      {{ $errors->first('total_gst') }}
                    </div>
                    @endif
                  </div>
                </div>
                <!-- <div class="form-group row">
                  <div class="col-sm-4">
                    <label class="bmd-label">{!! trans('panel.sale.fields.extra_discount') !!}</label>
                  </div>
                  <div class="col-sm-8">
                    <div class="input-group">
                      <div class="input-group-prepend">
                      </div>
                      <input type="number" name='extra_discount' class="form-control" value="{!! old( 'extra_discount', $sales['extra_discount']) !!}" />
                      <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly>
                    </div>
                    @if($errors->has('extra_discount'))
                    <div class="invalid-feedback">
                      {{ $errors->first('extra_discount') }}
                    </div>
                    @endif
                  </div>
                </div> -->
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.sale.fields.total_discount') !!}</label>
                 
                    <input type="number" name='total_discount' id="totaldiscount" class="form-control" value="{!! old( 'total_discount', $sales['total_discount']) !!}" readonly />
                    @if($errors->has('total_discount'))
                    <div class="invalid-feedback">
                      {{ $errors->first('total_discount') }}
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.sale.fields.grand_total') !!}</label>
                    <input type="number" class="form-control" id="grandtotal" name="grand_total" value="{!! old( 'grand_total', $sales['grand_total']) !!}" readonly>
                    @if($errors->has('grand_total'))
                    <div class="invalid-feedback">
                      {{ $errors->first('grand_total') }}
                    </div>
                    @endif
                  </div>
                </div>
                       <div class="col-md-12 pull-right">
              {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
            </div>
              </div>

            </div>


         
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
    <!-- <script src="{{ url('/').'/'.asset('assets/js/invoice_js') }}"></script> -->
    <script src="{{ url('/').'/'.asset('assets/js/validation_sales.js') }}"></script>
    <script type="text/javascript">
      $(document).ready(function() {

        var $table = $('table.kvcodes-dynamic-rows-example'),
          counter = $('#tab_logic tr:last').attr('value');
        if (!counter) {
          counter = 0;
        }
        $('a.add-rows').click(function(event) {
          event.preventDefault();
          counter++;
          var newRow =
            '<tr value="' + counter + '"> <td>' + counter + '</td>' +
            '<td><div class="input_section"><select name="saledetail[' + counter + '][product_id]" class="form-control product rowchange select2" onchange="getproductinfo(this)"/> </select></div></td>' +
            '<td><div class="input_section"> <select class="form-control productdetails rowchange select2" name="saledetail[' + counter + '][product_detail]" onchange="getproductdetailinfo(this)" ></select><span class="gst_percent" style="display:none;"></span> <span class="gstamount" style="display:none;"></span> <span class="linediscount" style="display:none;"></span> <input type="hidden" name="saledetail[' + counter + '][tax_amount]" class="form-control tax_amount readonly"/><input type="hidden" name="saledetail[' + counter + '][discount_amount]" class="form-control discountamount readonly"/></div></td>' +
            '<td><div class="input_section"><input type="number" name="saledetail[' + counter + '][price]" class="form-control price readonly"/></div></td>' +
            '<td><div class="input_section"><input type="number" name="saledetail[' + counter + '][discount]" class="form-control discount readonly"/></td>' +
            '<td><input type="text" name="saledetail[' + counter + '][quantity]" class="form-control quantity rowchange" /></div></td>' +
            '<td><div class="input_section"><input type="text" name="saledetail[' + counter + '][line_total]" class="form-control total rowchange" readonly /></div></td>' +
            '<td class="td-actions text-center"><a href="#" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
          $table.append(newRow);
        });

        $table.on('click', '.remove-rows', function() {
          $(this).closest('tr').remove();
        });

        $('#tab_logic tbody').on('keyup change', function() {
          calc();
        });
      });

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
              } else {
                $(".seller_address").empty();
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
            row.find('.price').val(res.price);
            row.find('.gst_percent').append(res.gst);
            row.find('.discount').val(res.discount);
            row.find('.discount').attr("max", res.max_discount);
            if (res.productdetails) {
              $.each(res.productdetails, function(key, value) {
                row.find('.productdetails').append('<option value="' + value.id + '">' + value.detail_title + '</option>');

              });
            }
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
            if ($("#withoutgst").is(":checked")) {
              var tax_amount = 0.00;
            } else {
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

      function calc_total() {
        total = 0;
        subtotal = 0;
        taxamount = 0;
        discount = 0;
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
        $('#grandtotal').val((total + taxamount).toFixed(2));
      }

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
                $('#tab_logic tr:last').find('.product').append('<option value="' + value.id + '">' + value.display_name + '</option>');

              });
            } else {
              row.find(".product").empty();
            }
          }
        });
      }
    </script>

</x-app-layout>