@php
  $approved = in_array($purchaseOrder->status, ['approved','partially_received','received']);
  $received = $purchaseOrder->status === 'received';
@endphp
<style>
  #purchaseOrderViewModal .modal-content{color:#2f3948!important;background:#fff}
  #purchaseOrderViewModal .modal-header{background:#f7f9fc;border-bottom:1px solid #e1e7ef;padding:20px 26px}
  #purchaseOrderViewModal .modal-title,#purchaseOrderViewModal .modal-title strong{color:#253247!important}
  #purchaseOrderViewModal .modal-body{padding:25px 28px}
  #purchaseOrderViewModal .po-summary-card{height:100%;padding:14px 16px;background:#f7f9fc;border:1px solid #e3e8ef;border-radius:7px}
  #purchaseOrderViewModal .po-summary-card small{display:block;color:#748196!important;font-weight:600;text-transform:uppercase;margin-bottom:4px}
  #purchaseOrderViewModal .po-summary-card strong,#purchaseOrderViewModal .po-summary-card p{color:#263548!important;margin:0}
  #purchaseOrderViewModal .po-party{margin-bottom:22px}
  #purchaseOrderViewModal .po-party .po-summary-card{min-height:104px}
  #purchaseOrderViewModal table th{color:#344154!important;background:#f7f8fb}
  #purchaseOrderViewModal table td{color:#303b4c!important}
  #purchaseOrderViewModal .po-total-box{margin-left:auto;max-width:390px;padding:16px 18px;background:#f7f9fc;border-radius:7px}
  #purchaseOrderViewModal .po-total-box div{display:flex;justify-content:space-between;padding:4px 0;color:#48556a}
  #purchaseOrderViewModal .po-total-box .grand{margin-top:8px;padding-top:10px;border-top:1px solid #ccd5e1;font-size:17px}
  #purchaseOrderViewModal .modal-footer{padding:16px 25px;border-top:1px solid #e1e7ef}
</style>
<div class="modal-header">
  <div>
    <small style="color:#728096">PURCHASE ORDER</small>
    <h4 class="modal-title"><strong>{{ $purchaseOrder->po_number }}</strong></h4>
  </div>
  <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
</div>
<div class="modal-body">
  <div class="d-flex align-items-center mb-4 po-progress">
    <div class="step {{ true ? 'complete' : '' }}"><span>1</span><strong>Created</strong></div>
    <div class="line"></div>
    <div class="step {{ $approved ? 'complete' : '' }}"><span>2</span><strong>Approved</strong></div>
    <div class="line"></div>
    <div class="step {{ $received ? 'complete' : '' }}"><span>3</span><strong>Received</strong></div>
  </div>
  <div class="row po-party">
    <div class="col-md-4">
      <div class="po-summary-card">
        <small>Supplier</small>
        <strong>{{ $purchaseOrder->supplier->name }}</strong>
        <p>{{ $purchaseOrder->supplier->mobile ?: '-' }}</p>
        <p>{{ optional($purchaseOrder->supplier->customeraddress)->full_address ?: '-' }}</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="po-summary-card">
        <small>GSTIN</small>
        <strong>{{ optional($purchaseOrder->supplier->customerdetails)->gstin_no ?: '-' }}</strong>
        <small class="mt-2">PO Date</small>
        <strong>{{ $purchaseOrder->po_date->format('d M Y') }}</strong>
      </div>
    </div>
    <div class="col-md-3">
      <div class="po-summary-card">
        <small>Status</small>
        <strong>{{ ucwords(str_replace('_', ' ', $purchaseOrder->status)) }}</strong>
        <small class="mt-2">Items</small>
        <strong>{{ $purchaseOrder->items->count() }}</strong>
      </div>
    </div>
    <div class="col-md-2">
      <div class="po-summary-card">
        <small>Created By</small>
        <strong>{{ optional($purchaseOrder->creator)->name ?: '-' }}</strong>
      </div>
    </div>
  </div>
  <div class="table-responsive"><table class="table">
    <thead><tr><th>Product</th><th>Warehouse</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Amount</th></tr></thead>
    <tbody>@foreach($purchaseOrder->items as $item)<tr>
      <td>{{ $item->product->product_name }}</td><td>{{ $item->warehouse->warehouse_name }}</td>
      <td>{{ rtrim(rtrim(number_format($item->quantity,3,'.',''), '0'), '.') }}</td>
      <td>₹{{ number_format($item->rate,2) }}</td>
      <td>{{ number_format($item->gst_percent,2) }}%</td>
      <td>₹{{ number_format($item->total_amount,2) }}</td>
    </tr>@endforeach</tbody>
  </table></div>
  <div class="po-total-box mb-3">
    <div><span>Subtotal</span><strong>₹{{ number_format($purchaseOrder->subtotal,2) }}</strong></div>
    <div><span>Total GST</span><strong>₹{{ number_format($purchaseOrder->total_gst,2) }}</strong></div>
    <div class="grand"><strong>Grand Total</strong><strong>₹{{ number_format($purchaseOrder->grand_total,2) }}</strong></div>
  </div>
  <div class="input_section">
    <label>Remark By Approver</label>
    @if($purchaseOrder->status === 'draft' && auth()->user()->can('purchase_order_approve'))
      <textarea id="modal-approval-remark" class="form-control" rows="3" placeholder="Optional approval remark"></textarea>
    @else
      <div class="form-control" style="min-height:70px">{{ $purchaseOrder->approval_remark ?: '-' }}</div>
    @endif
  </div>
</div>
<div class="modal-footer">
  <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="btn btn-danger" title="Download PDF">
    <i class="material-icons">picture_as_pdf</i> Download PDF
  </a>
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  @if($purchaseOrder->status === 'draft' && auth()->user()->can('purchase_order_approve'))
    <button type="button" class="btn btn-success modal-approve-po" data-id="{{ $purchaseOrder->id }}">Approve Order</button>
  @endif
</div>
