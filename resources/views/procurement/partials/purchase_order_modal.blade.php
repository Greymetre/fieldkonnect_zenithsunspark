@php
  $approved = in_array($purchaseOrder->status, ['approved','partially_received','received']);
  $received = $purchaseOrder->status === 'received';
@endphp
<div class="modal-header">
  <h4 class="modal-title"><strong>{{ $purchaseOrder->po_number }} — {{ $purchaseOrder->supplier->name }}</strong></h4>
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
  <div class="table-responsive"><table class="table">
    <thead class="text-primary"><tr><th>Product</th><th>Warehouse</th><th>Qty</th><th>Amount</th></tr></thead>
    <tbody>@foreach($purchaseOrder->items as $item)<tr>
      <td>{{ $item->product->product_name }}</td><td>{{ $item->warehouse->warehouse_name }}</td>
      <td>{{ rtrim(rtrim(number_format($item->quantity,3,'.',''), '0'), '.') }}</td>
      <td>₹{{ number_format($item->total_amount,2) }}</td>
    </tr>@endforeach</tbody>
  </table></div>
  <div class="row">
    <div class="col-md-6"><strong>PO Date</strong><p>{{ $purchaseOrder->po_date->format('d M Y') }}</p></div>
    <div class="col-md-6 text-right"><strong>Grand Total</strong><h4>₹{{ number_format($purchaseOrder->grand_total,2) }}</h4></div>
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
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  @if($purchaseOrder->status === 'draft' && auth()->user()->can('purchase_order_approve'))
    <button type="button" class="btn btn-success modal-approve-po" data-id="{{ $purchaseOrder->id }}">Approve Order</button>
  @endif
</div>
