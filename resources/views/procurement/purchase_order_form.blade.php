<x-app-layout>
@php
  $editing = !empty($purchaseOrder);
  $existingItems = old('items', $editing ? $purchaseOrder->items->map(fn($i) => [
    'product_id'=>$i->product_id,'warehouse_id'=>$i->warehouse_id,'quantity'=>$i->quantity,
    'rate'=>$i->rate,'gst_percent'=>$i->gst_percent
  ])->values()->all() : []);
  $productOptions = $products->map(function ($product) {
    $details = $product->productdetails->first();

    return [
      'id' => $product->id,
      'name' => $product->product_name,
      'rate' => (float) ($details->price ?? $details->selling_price ?? 0),
      'gst' => (float) ($details->gst ?? 0),
    ];
  })->values()->all();
  $warehouseOptions = $warehouses->map(function ($warehouse) {
    return [
      'id' => $warehouse->id,
      'name' => $warehouse->warehouse_name,
    ];
  })->values()->all();
@endphp
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">shopping_cart</i></div>
    <h4 class="card-title">{{ $editing ? 'Edit' : 'Create' }} Purchase Order
      <span class="pull-right"><a href="{{ route('purchase-orders.index') }}" class="btn btn-danger btn-just-icon"><i class="material-icons">clear</i></a></span>
    </h4>
  </div>
  <div class="card-body">
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $editing ? route('purchase-orders.update',$purchaseOrder) : route('purchase-orders.store') }}" id="purchase-order-form">
      @csrf @if($editing) @method('PUT') @endif
      <div class="row">
        <div class="col-md-6">
          <div class="input_section">
            <label>Supplier <span class="text-danger">*</span></label>
            <div class="d-flex align-items-center">
              <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                <option value="">Search supplier...</option>
                @foreach($suppliers as $supplier)
                @php
                  $details=$supplier->customerdetails; $address=$supplier->customeraddress;
                  $location=collect([optional(optional($address)->cityname)->city_name, optional(optional($address)->statename)->state_name])->filter()->implode(', ');
                @endphp
                <option value="{{ $supplier->id }}"
                  data-gstin="{{ optional($details)->gstin_no }}" data-contact="{{ $supplier->mobile }}"
                  data-location="{{ $location }}" {{ old('supplier_id', $purchaseOrder->supplier_id ?? request('supplier_id')) == $supplier->id ? 'selected':'' }}>
                  {{ $supplier->name }}
                </option>
                @endforeach
              </select>
              @if($vendorFirmType)
              <a href="{{ route('customers.create', ['firmtype'=>$vendorFirmType->id, 'source'=>'purchase-order']) }}" class="btn btn-theme btn-sm ml-2 text-nowrap">
                <i class="material-icons">person_add</i> New Supplier
              </a>
              @endif
            </div>
          </div>
        </div>
        <div class="col-md-6"><div class="input_section">
          <label>PO Date <span class="text-danger">*</span></label>
          <input type="date" name="po_date" class="form-control" value="{{ old('po_date', $editing ? optional($purchaseOrder->po_date)->format('Y-m-d') : date('Y-m-d')) }}" required>
        </div></div>
      </div>
      <div id="supplier-details" class="alert alert-secondary mt-3" style="display:none">
        <div class="row"><div class="col-md-4"><small>GSTIN</small><strong id="supplier-gstin" class="d-block"></strong></div>
        <div class="col-md-4"><small>CONTACT</small><strong id="supplier-contact" class="d-block"></strong></div>
        <div class="col-md-4"><small>LOCATION</small><strong id="supplier-location" class="d-block"></strong></div></div>
      </div>

      <h4 class="mt-4">Products</h4>
      <div class="table-responsive"><table class="table" id="items-table">
        <thead class="text-primary"><tr><th style="min-width:260px">Product</th><th style="min-width:210px">Stock-In Warehouse</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Amount</th><th></th></tr></thead>
        <tbody></tbody>
      </table></div>
      <button type="button" id="add-item" class="btn btn-link text-primary"><i class="material-icons">add</i> Add product</button>
      <div class="row justify-content-end"><div class="col-md-4"><div class="alert alert-secondary">
        <div class="d-flex justify-content-between"><span>Sub Total</span><strong id="subtotal">₹0.00</strong></div>
        <div class="d-flex justify-content-between"><span>Total GST</span><strong id="total-gst">₹0.00</strong></div><hr>
        <div class="d-flex justify-content-between"><strong>Grand Total</strong><strong id="grand-total">₹0.00</strong></div>
      </div></div></div>
      <div class="input_section"><label>Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes',$purchaseOrder->notes ?? '') }}</textarea></div>
      <div class="alert alert-info">Saving creates a Draft PO. Approve it before stock can be received through GRN.</div>
      <div class="text-right"><a href="{{ route('purchase-orders.index') }}" class="btn btn-default">Cancel</a>
        <button class="btn btn-theme">{{ $editing ? 'Update' : 'Create' }} Purchase Order</button></div>
    </form>
  </div>
</div></div></div>

<script>
$(function(){
  const products = @json($productOptions);
  const warehouses = @json($warehouseOptions);
  const existing = @json($existingItems);
  let rowIndex=0;
  const options=(list,selected,label)=>'<option value="">Select '+label+'</option>'+list.map(x=>`<option value="${x.id}" ${String(x.id)===String(selected)?'selected':''}>${x.name}</option>`).join('');
  function initializeItemDropdowns(row) {
    $(row).find('.product-select,.warehouse-select').each(function () {
      const dropdown = $(this);
      if (dropdown.hasClass('select2-hidden-accessible')) {
        dropdown.select2('destroy');
      }
      dropdown.select2({width:'100%'});
      dropdown.trigger('change.select2');
    });
  }
  function addRow(item={}) {
    const i=rowIndex++;
    const row = $(`<tr class="item-row">
      <td><select name="items[${i}][product_id]" class="form-control product-select" required>${options(products,item.product_id,'Product')}</select></td>
      <td><select name="items[${i}][warehouse_id]" class="form-control warehouse-select" required>${options(warehouses,item.warehouse_id,'Warehouse')}</select></td>
      <td><input type="number" name="items[${i}][quantity]" class="form-control quantity" min="0.001" step="0.001" value="${item.quantity || 1}" required></td>
      <td><input type="number" name="items[${i}][rate]" class="form-control rate" min="0" step="0.01" value="${item.rate ?? 0}" required></td>
      <td><input type="number" name="items[${i}][gst_percent]" class="form-control gst" min="0" max="100" step="0.01" value="${item.gst_percent ?? 0}" required></td>
      <td class="line-total text-nowrap">₹0.00</td>
      <td><button type="button" class="btn btn-danger btn-just-icon btn-sm remove-item"><i class="material-icons">clear</i></button></td></tr>`);
    $('#items-table tbody').append(row);
    initializeItemDropdowns(row);
    recalc();
  }
  function recalc(){
    let sub=0,tax=0;
    $('.item-row').each(function(){let q=+$(this).find('.quantity').val()||0,r=+$(this).find('.rate').val()||0,g=+$(this).find('.gst').val()||0,b=q*r,t=b*g/100;sub+=b;tax+=t;$(this).find('.line-total').text('₹'+(b+t).toFixed(2));});
    $('#subtotal').text('₹'+sub.toFixed(2));$('#total-gst').text('₹'+tax.toFixed(2));$('#grand-total').text('₹'+(sub+tax).toFixed(2));
  }
  $('#supplier_id').select2({width:'100%',placeholder:'Search supplier by name'});
  function supplierDetails(){const o=$('#supplier_id option:selected');if(o.val()){$('#supplier-details').show();$('#supplier-gstin').text(o.data('gstin')||'-');$('#supplier-contact').text(o.data('contact')||'-');$('#supplier-location').text(o.data('location')||'-');}else $('#supplier-details').hide();}
  $('#supplier_id').on('change',supplierDetails);supplierDetails();
  $('#add-item').click(()=>addRow());
  $(document).on('click','.remove-item',function(){if($('.item-row').length>1){$(this).closest('tr').remove();recalc();}});
  $(document).on('input','.quantity,.rate,.gst',recalc);
  $(document).on('change','.product-select',function(){const p=products.find(x=>String(x.id)===String($(this).val()));if(p){const tr=$(this).closest('tr');tr.find('.rate').val(p.rate);tr.find('.gst').val(p.gst);recalc();}});
  // Run after the layout's document-ready initializers, matching rows added by the user.
  setTimeout(function () {
    (existing.length ? existing : [{}]).forEach(function (item) {
      addRow(item);
    });
  }, 0);
});
</script>
</x-app-layout>
