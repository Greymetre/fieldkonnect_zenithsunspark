@php
  $productOptions = $products->map(function ($product) {
    $details = $product->productdetails->first();
    return ['id'=>$product->id, 'name'=>$product->product_name,
      'rate'=>(float)($details->price ?? $details->selling_price ?? 0), 'gst'=>(float)($details->gst ?? 0)];
  })->values()->all();
  $stockOptions = $stocks->map(function ($rows) { return $rows->map(fn ($quantity)=>(float)$quantity)->all(); })->all();
@endphp
<form id="sales-order-form" action="{{ route('sales-orders.store') }}">
  @csrf
  <input type="hidden" name="order_type" value="{{ $orderType }}">
  <div class="modal-header">
    <h4 class="modal-title"><strong>Create {{ strtoupper($orderType) }} Sales Order</strong></h4>
    <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
  </div>
  <div class="modal-body">
    <div id="sales-order-errors" class="alert alert-danger" style="display:none"></div>
    <div class="row">
      <div class="col-md-6"><div class="input_section"><label>{{ $orderType === 'b2b' ? 'Client' : 'Customer' }} <span class="text-danger">*</span></label>
        <div class="d-flex"><select name="customer_id" id="so-customer" class="form-control" required>
          <option value="">Search customer...</option>
          @foreach($customers as $customer)
            @php
              $address=$customer->customeraddress;
              $city=optional(optional($address)->cityname)->city_name;
              $fullAddress=collect([optional($address)->address1,$city])->filter()->implode(', ');
            @endphp
            <option value="{{ $customer->id }}" data-contact="{{ $customer->mobile }}" data-city="{{ $city }}" data-address="{{ $fullAddress }}" {{ (int) request('customer_id') === $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
          @endforeach
        </select>
        @if($customerFirmType)
          <a class="btn btn-theme btn-sm ml-2 text-nowrap" href="{{ route('customers.create',['firmtype'=>$customerFirmType->id,'source'=>'sales-order','order_type'=>$orderType]) }}"><i class="material-icons">person_add</i> New Customer</a>
        @endif
        </div>
      </div></div>
      <div class="col-md-6"><div class="input_section"><label>Dispatch from Warehouse <span class="text-danger">*</span></label>
        <select name="warehouse_id" id="so-warehouse" class="form-control" required>
          <option value="">Select Warehouse</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>@endforeach
        </select>
      </div></div>
    </div>
    <div id="so-customer-details" class="alert alert-secondary" style="display:none">
      <div class="row"><div class="col-md-6"><small>CONTACT</small><strong id="so-contact" class="d-block"></strong></div>
      <div class="col-md-6"><small>ADDRESS</small><strong id="so-address" class="d-block"></strong></div></div>
    </div>
    <h4>Products (Available Stock Shown)</h4>
    <div class="table-responsive"><table class="table" id="so-items-table">
      <thead class="text-primary"><tr><th style="min-width:250px">Product</th><th>In Stock</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Amount</th><th></th></tr></thead><tbody></tbody>
    </table></div>
    <button type="button" class="btn btn-link" id="so-add-item">+ Add product</button>
    <div class="row justify-content-end"><div class="col-md-4"><div class="alert alert-secondary">
      <div class="d-flex justify-content-between"><span>Sub Total</span><strong id="so-subtotal">₹0.00</strong></div>
      <div class="d-flex justify-content-between"><span>Total GST</span><strong id="so-gst-total">₹0.00</strong></div><hr>
      <div class="d-flex justify-content-between"><strong>Grand Total</strong><strong id="so-grand-total">₹0.00</strong></div>
    </div></div></div>
    <!-- <div class="alert alert-info">Order create → Payment confirm → Confirm sale → Dispatch. Stock reduces only when dispatched.</div> -->
  </div>
  <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn {{ $orderType === 'b2c' ? 'btn-warning' : 'btn-theme' }}" id="create-sales-order-button">Create {{ strtoupper($orderType) }} Order</button>
  </div>
</form>
<script>
(function(){
  const products=@json($productOptions),stocks=@json($stockOptions),selectedProductId=@json((int) request('product_id'));let index=0;
  const createProductUrl=@json(route('products.create',['source'=>'sales-order','order_type'=>$orderType]));
  const productOptions=selected=>'<option value="">Select Product</option>'+products.map(p=>`<option value="${p.id}" ${String(p.id)===String(selected)?'selected':''}>${p.name}</option>`).join('');
  function stockFor(productId){return parseFloat(stocks[$('#so-warehouse').val()]?.[productId]||0)}
  function stockBadge(row){const qty=stockFor(row.find('.so-product').val()),klass=qty<=0?'danger':(qty<10?'warning':'success');row.find('.so-stock').html(`<span class="badge badge-${klass}">● ${qty}</span>`)}
  function recalc(){let sub=0,tax=0;$('.so-item').each(function(){const row=$(this),q=+row.find('.so-qty').val()||0,r=+row.find('.so-rate').val()||0,g=+row.find('.so-gst').val()||0,b=q*r,t=b*g/100;sub+=b;tax+=t;row.find('.so-amount').text('₹'+(b+t).toFixed(2))});$('#so-subtotal').text('₹'+sub.toFixed(2));$('#so-gst-total').text('₹'+tax.toFixed(2));$('#so-grand-total').text('₹'+(sub+tax).toFixed(2))}
  function addRow(productId=null){const i=index++,row=$(`<tr class="so-item"><td><div class="d-flex align-items-start"><select name="items[${i}][product_id]" class="form-control so-product" required>${productOptions(productId)}</select>@can('product_create')<a href="${createProductUrl}" class="btn btn-theme btn-just-icon btn-sm ml-2 mb-0 flex-shrink-0" title="Create Product"><i class="material-icons">add</i></a>@endcan</div></td><td class="so-stock">—</td><td><input type="number" name="items[${i}][quantity]" class="form-control so-qty" min="0.001" step="0.001" value="1" required></td><td><input type="number" name="items[${i}][rate]" class="form-control so-rate" min="0" step="0.01" value="0" required></td><td><input type="number" name="items[${i}][gst_percent]" class="form-control so-gst" min="0" max="100" step="0.01" value="0" required></td><td class="so-amount">₹0.00</td><td><button type="button" class="btn btn-danger btn-just-icon btn-sm so-remove"><i class="material-icons">clear</i></button></td></tr>`);$('#so-items-table tbody').append(row);row.find('.so-product').select2({width:'100%',dropdownParent:$('#salesOrderModal')}).trigger('change');}
  $('#so-customer,#so-warehouse').select2({width:'100%',dropdownParent:$('#salesOrderModal')});
  $('#so-customer').on('change',function(){const o=$(this).find(':selected');if(o.val()){$('#so-customer-details').show();$('#so-contact').text((o.data('contact')||'-')+(o.data('city')?' · '+o.data('city'):''));$('#so-address').text(o.data('address')||'-')}else $('#so-customer-details').hide()});
  $('#so-customer').trigger('change');
  $('#so-warehouse').on('change',()=>$('.so-item').each(function(){stockBadge($(this))}));
  $('#so-add-item').on('click',()=>addRow());
  $(document).on('change','.so-product',function(){const p=products.find(x=>String(x.id)===String($(this).val())),row=$(this).closest('tr');if(p){row.find('.so-rate').val(p.rate);row.find('.so-gst').val(p.gst)}stockBadge(row);recalc()});
  $(document).on('input','.so-qty,.so-rate,.so-gst',recalc);
  $(document).on('click','.so-remove',function(){if($('.so-item').length>1)$(this).closest('tr').remove();recalc()});
  addRow(selectedProductId || null);
})();
</script>
