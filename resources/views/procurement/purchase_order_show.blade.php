<x-app-layout>
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">receipt_long</i></div>
    <h4 class="card-title">{{ $purchaseOrder->po_number }}
      <span class="pull-right">
        <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="btn btn-danger" title="Download PDF">
          <i class="material-icons">picture_as_pdf</i> PDF
        </a>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-theme">Back</a>
      </span>
    </h4>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-3"><strong>Supplier</strong><p>{{ $purchaseOrder->supplier->name }}</p></div>
      <div class="col-md-3"><strong>GSTIN</strong><p>{{ optional($purchaseOrder->supplier->customerdetails)->gstin_no ?: '-' }}</p></div>
      <div class="col-md-2"><strong>Contact</strong><p>{{ $purchaseOrder->supplier->mobile }}</p></div>
      <div class="col-md-2"><strong>PO Date</strong><p>{{ $purchaseOrder->po_date->format('d M Y') }}</p></div>
      <div class="col-md-2"><strong>Status</strong><p>{{ ucwords(str_replace('_',' ',$purchaseOrder->status)) }}</p></div>
    </div>
    <div class="table-responsive"><table class="table table-bordered">
      <thead class="text-primary"><tr><th>Product</th><th>Warehouse</th><th>Qty</th><th>Received</th><th>Rate</th><th>GST%</th><th>Amount</th></tr></thead>
      <tbody>@foreach($purchaseOrder->items as $item)<tr>
        <td>{{ $item->product->product_name }}</td><td>{{ $item->warehouse->warehouse_name }}</td>
        <td>{{ $item->quantity }}</td><td>{{ $item->received_quantity }}</td><td>₹{{ number_format($item->rate,2) }}</td>
        <td>{{ $item->gst_percent }}%</td><td>₹{{ number_format($item->total_amount,2) }}</td>
      </tr>@endforeach</tbody>
    </table></div>
    <div class="row justify-content-end"><div class="col-md-4">
      <p class="d-flex justify-content-between"><span>Subtotal</span><strong>₹{{ number_format($purchaseOrder->subtotal,2) }}</strong></p>
      <p class="d-flex justify-content-between"><span>Total GST</span><strong>₹{{ number_format($purchaseOrder->total_gst,2) }}</strong></p>
      <hr><h4 class="d-flex justify-content-between"><span>Grand Total</span><strong>₹{{ number_format($purchaseOrder->grand_total,2) }}</strong></h4>
    </div></div>
  </div>
</div></div></div>
</x-app-layout>
