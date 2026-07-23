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
<div class="row"><div class="col-md-12"><div class="card po-form-card">
  <div class="card-header card-header-icon card-header-theme po-form-header">
    <div class="card-icon"><i class="material-icons">shopping_cart</i></div>
    <h4 class="card-title">{{ $editing ? 'Edit' : 'Create' }} Purchase Order</h4>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-danger btn-just-icon po-close-button" title="Close">
      <i class="material-icons">close</i>
    </a>
  </div>
  <div class="card-body po-form-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ $editing ? route('purchase-orders.update',$purchaseOrder) : route('purchase-orders.store') }}" id="purchase-order-form">
      @csrf @if($editing) @method('PUT') @endif

      <div class="row po-top-fields">
        <div class="col-lg-6">
          <div class="input_section">
            <label>Supplier <span class="text-danger">*</span></label>
            <div class="po-supplier-field">
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
              <a href="{{ route('customers.create', ['firmtype'=>$vendorFirmType->id, 'source'=>'purchase-order']) }}" class="btn btn-theme po-new-supplier">
                <i class="material-icons">person_add</i><span>New Supplier</span>
              </a>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="input_section">
            <label>PO Date <span class="text-danger">*</span></label>
            <input type="date" name="po_date" class="form-control" value="{{ old('po_date', $editing ? optional($purchaseOrder->po_date)->format('Y-m-d') : date('Y-m-d')) }}" required>
          </div>
        </div>
      </div>

      <div id="supplier-details" class="po-supplier-details" style="display:none">
        <div><small>GSTIN</small><strong id="supplier-gstin"></strong></div>
        <div><small>Contact</small><strong id="supplier-contact"></strong></div>
        <div><small>Location</small><strong id="supplier-location"></strong></div>
      </div>

      <div class="po-products-section">
        <div class="table-responsive">
          <table class="table" id="items-table">
            <thead><tr>
              <th class="po-product-column">Product</th>
              <th class="po-warehouse-column">Stock-In Warehouse</th>
              <th class="po-qty-column">Qty</th>
              <th class="po-rate-column">Rate</th>
              <th class="po-gst-column">GST%</th>
              <th class="po-amount-column">Amount</th>
              <th class="po-action-column"></th>
            </tr></thead>
            <tbody></tbody>
          </table>
        </div>
        <button type="button" id="add-item" class="btn btn-theme po-add-product">
          <i class="material-icons">add</i> Add Product
        </button>
      </div>

      <div class="row po-summary-section">
        <div class="col-lg-6 order-lg-1 order-2">
          <div class="po-draft-message">
            <i class="material-icons">info</i>
            <span>Saving creates a Draft PO. Approve it before stock can be received through GRN.</span>
          </div>
        </div>
        <div class="col-lg-6 order-lg-2 order-1">
          <div class="po-totals">
            <div><span>Sub Total</span><strong id="subtotal">₹0.00</strong></div>
            <div><span>Total GST</span><strong id="total-gst">₹0.00</strong></div>
            <div class="po-grand-total"><span>Grand Total</span><strong id="grand-total">₹0.00</strong></div>
          </div>
          <div class="input_section po-notes">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="4">{{ old('notes',$purchaseOrder->notes ?? '') }}</textarea>
          </div>
        </div>
      </div>

      <div class="po-form-actions">
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-default">Cancel</a>
        <button class="btn btn-theme">{{ $editing ? 'Update' : 'Create' }} Purchase Order</button>
      </div>
    </form>
  </div>
</div></div></div>

<style>
.po-form-card{min-height:calc(100vh - 155px);overflow:visible}
.po-form-header{position:relative;padding-right:85px}
.po-close-button{position:absolute!important;right:22px;top:18px;margin:0!important}
.po-form-body{padding:32px 28px 28px!important}
.po-top-fields{margin-top:12px}
.po-top-fields .input_section label{display:block;color:#69758a;font-weight:600;margin-bottom:8px}
.po-top-fields .form-control,.po-top-fields .select2-selection--single{height:46px!important}
.po-top-fields .select2-selection__rendered{line-height:44px!important}
.po-top-fields .select2-selection__arrow{height:44px!important}
.po-supplier-field{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:start}
.po-new-supplier{height:46px;margin:0!important;display:inline-flex;align-items:center;gap:5px;white-space:nowrap}
.po-new-supplier .material-icons{font-size:19px}
.po-supplier-details{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin:8px 0 24px;padding:18px 22px;border:1px dashed #ccd7e5;border-radius:8px;background:#f7f9fc}
.po-supplier-details small{display:block;text-transform:uppercase;color:#8b99ad;font-weight:600;margin-bottom:4px}
.po-supplier-details strong{display:block;color:#263548}
.po-products-section{margin-top:48px}
#items-table{table-layout:fixed;margin-bottom:12px}
#items-table thead th{background:#f7f8fb;color:#344154;font-weight:600;border-bottom:1px solid #e5e9ef;padding:14px 12px;white-space:nowrap}
#items-table tbody td{padding:8px 12px;vertical-align:middle;border-bottom:1px solid #e5e9ef}
#items-table .po-product-column{width:22%}
#items-table .po-warehouse-column{width:19%}
#items-table .po-qty-column{width:12%}
#items-table .po-rate-column{width:13%}
#items-table .po-gst-column{width:10%}
#items-table .po-amount-column{width:15%}
#items-table .po-action-column{width:52px}
#items-table .form-control,#items-table .select2-selection--single{height:44px!important}
#items-table .select2-selection__rendered{line-height:42px!important}
#items-table .select2-selection__arrow{height:42px!important}
#items-table .line-total{font-weight:600;color:#27364a}
.po-add-product{margin:8px 0 0!important;min-width:170px}
.po-add-product .material-icons{font-size:18px;vertical-align:middle}
.po-summary-section{margin-top:34px;align-items:end}
.po-draft-message{display:flex;align-items:flex-start;gap:10px;max-width:470px;padding:18px 20px;border-radius:6px;background:#13bfd1;color:#fff;box-shadow:0 8px 20px rgba(19,191,209,.18)}
.po-draft-message .material-icons{font-size:21px}
.po-totals{margin-left:auto;max-width:430px;padding:8px 14px}
.po-totals>div{display:flex;justify-content:space-between;gap:25px;padding:5px 0;color:#596273}
.po-totals strong{color:#394150}
.po-totals .po-grand-total{margin-top:10px;padding-top:14px;border-top:1px solid #cfd5dd;font-size:17px;font-weight:700}
.po-notes{margin-top:28px}
.po-notes label{color:#69758a;font-weight:600}
.po-notes textarea{resize:vertical;min-height:105px}
.po-form-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:24px;padding-top:22px;border-top:1px solid #edf0f4}
.po-form-actions .btn{min-width:130px}
.po-form-actions .btn-theme{min-width:215px}
@media(max-width:1199px){
  #items-table{min-width:1050px}
  .po-products-section{margin-top:30px}
}
@media(max-width:991px){
  .po-supplier-field{grid-template-columns:1fr}
  .po-new-supplier{justify-content:center;width:100%}
  .po-summary-section{margin-top:24px}
  .po-totals{max-width:none;margin-bottom:24px}
  .po-draft-message{max-width:none;margin-top:22px}
}
@media(max-width:767px){
  .po-form-body{padding:24px 16px 20px!important}
  .po-supplier-details{grid-template-columns:1fr;gap:12px}
  .po-form-actions{flex-direction:column-reverse}
  .po-form-actions .btn{width:100%;min-width:0}
}
</style>

<script>
$(function(){
  const products = @json($productOptions);
  const warehouses = @json($warehouseOptions);
  const existing = @json($existingItems);
  const money = new Intl.NumberFormat('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
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
    $('.item-row').each(function(){
      let q=+$(this).find('.quantity').val()||0,r=+$(this).find('.rate').val()||0,g=+$(this).find('.gst').val()||0,b=q*r,t=b*g/100;
      sub+=b;tax+=t;$(this).find('.line-total').text('₹'+money.format(b+t));
    });
    $('#subtotal').text('₹'+money.format(sub));
    $('#total-gst').text('₹'+money.format(tax));
    $('#grand-total').text('₹'+money.format(sub+tax));
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
