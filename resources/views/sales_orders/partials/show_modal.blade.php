<style>
  #salesOrderModal .modal-content{color:#2f3948!important;background:#fff}
  #salesOrderModal .modal-header{background:#f7f9fc;border-bottom:1px solid #e1e7ef;padding:20px 26px}
  #salesOrderModal .modal-title,#salesOrderModal .modal-title strong{color:#253247!important}
  #salesOrderModal .modal-body{padding:25px 28px}
  #salesOrderModal .so-summary-card{height:100%;padding:14px 16px;background:#f7f9fc;border:1px solid #e3e8ef;border-radius:7px}
  #salesOrderModal .so-summary-card small{display:block;color:#748196!important;font-weight:600;text-transform:uppercase;margin-bottom:4px}
  #salesOrderModal .so-summary-card strong,#salesOrderModal .so-summary-card p{color:#263548!important;margin:0}
  #salesOrderModal .so-summary-row{margin-bottom:22px}
  #salesOrderModal .so-summary-row .so-summary-card{min-height:104px}
  #salesOrderModal table th{color:#344154!important;background:#f7f8fb}
  #salesOrderModal table td{color:#303b4c!important}
  #salesOrderModal .so-total-box{margin-left:auto;max-width:390px;padding:16px 18px;background:#f7f9fc;border-radius:7px}
  #salesOrderModal .so-total-box div{display:flex;justify-content:space-between;padding:4px 0;color:#48556a}
  #salesOrderModal .so-total-box .grand{margin-top:8px;padding-top:10px;border-top:1px solid #ccd5e1;font-size:17px}
  #salesOrderModal .modal-footer{padding:16px 25px;border-top:1px solid #e1e7ef}
</style>
<div class="modal-header">
  <div>
    <small style="color:#728096">{{ strtoupper($salesOrder->order_type) }} SALES ORDER</small>
    <h4 class="modal-title"><strong>{{ $salesOrder->order_number }}</strong></h4>
  </div>
  <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
</div>
<div class="modal-body">
  <div class="row so-summary-row">
    <div class="col-md-4"><div class="so-summary-card">
      <small>{{ $salesOrder->order_type === 'b2b' ? 'Client' : 'Customer' }}</small>
      <strong>{{ $salesOrder->customer->name }}</strong>
      <p>{{ $salesOrder->customer->mobile ?: '-' }}</p>
      <p>{{ optional($salesOrder->customer->customeraddress)->full_address ?: '-' }}</p>
    </div></div>
    <div class="col-md-3"><div class="so-summary-card">
      <small>GSTIN</small>
      <strong>{{ optional($salesOrder->customer->customerdetails)->gstin_no ?: '-' }}</strong>
      <small class="mt-2">Order Date</small>
      <strong>{{ $salesOrder->order_date->format('d M Y') }}</strong>
    </div></div>
    <div class="col-md-3"><div class="so-summary-card">
      <small>Warehouse</small>
      <strong>{{ $salesOrder->warehouse->warehouse_name }}</strong>
      <small class="mt-2">Status</small>
      <strong>{{ ucwords(str_replace('_', ' ', $salesOrder->status)) }}</strong>
    </div></div>
    <div class="col-md-2"><div class="so-summary-card">
      <small>Order Type</small>
      <strong>{{ strtoupper($salesOrder->order_type) }}</strong>
      <small class="mt-2">Items</small>
      <strong>{{ $salesOrder->items->count() }}</strong>
    </div></div>
  </div>
  <div class="table-responsive"><table class="table"><thead><tr><th>Product</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Amount</th></tr></thead>
    <tbody>@foreach($salesOrder->items as $item)<tr><td>{{ $item->product->product_name }}</td><td>{{ rtrim(rtrim(number_format($item->quantity,3,'.',''),'0'),'.') }}</td>
      <td>₹{{ number_format($item->rate,2) }}</td><td>{{ number_format($item->gst_percent,2) }}%</td><td>₹{{ number_format($item->total_amount,2) }}</td></tr>@endforeach</tbody>
  </table></div>
  <div class="so-total-box">
    <div><span>Subtotal</span><strong>₹{{ number_format($salesOrder->subtotal,2) }}</strong></div>
    <div><span>Total GST</span><strong>₹{{ number_format($salesOrder->total_gst,2) }}</strong></div>
    <div class="grand"><strong>Grand Total</strong><strong>₹{{ number_format($salesOrder->grand_total,2) }}</strong></div>
  </div>
  @if($salesOrder->notes)
    <div class="so-summary-card mt-3"><small>Notes</small><p>{{ $salesOrder->notes }}</p></div>
  @endif
</div>
<div class="modal-footer">
  <a href="{{ route('sales-orders.pdf', $salesOrder) }}" class="btn btn-danger" title="Download PDF">
    <i class="material-icons">picture_as_pdf</i> Download PDF
  </a>
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
