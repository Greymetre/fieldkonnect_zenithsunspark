<div class="modal-header"><h4 class="modal-title"><strong>{{ $salesOrder->order_number }} — {{ $salesOrder->customer->name }}</strong></h4>
  <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button></div>
<div class="modal-body">
  <div class="row mb-3"><div class="col-md-3"><strong>Type</strong><p>{{ strtoupper($salesOrder->order_type) }}</p></div>
    <div class="col-md-3"><strong>Order Date</strong><p>{{ $salesOrder->order_date->format('d M Y') }}</p></div>
    <div class="col-md-3"><strong>Warehouse</strong><p>{{ $salesOrder->warehouse->warehouse_name }}</p></div>
    <div class="col-md-3"><strong>Status</strong><p>{{ ucwords(str_replace('_', ' ', $salesOrder->status)) }}</p></div></div>
  <div class="table-responsive"><table class="table"><thead class="text-primary"><tr><th>Product</th><th>Qty</th><th>Rate</th><th>GST%</th><th>Amount</th></tr></thead>
    <tbody>@foreach($salesOrder->items as $item)<tr><td>{{ $item->product->product_name }}</td><td>{{ rtrim(rtrim(number_format($item->quantity,3,'.',''),'0'),'.') }}</td>
      <td>₹{{ number_format($item->rate,2) }}</td><td>{{ number_format($item->gst_percent,2) }}</td><td>₹{{ number_format($item->total_amount,2) }}</td></tr>@endforeach</tbody>
  </table></div>
  <div class="text-right"><strong>Grand Total</strong><h4>₹{{ number_format($salesOrder->grand_total,2) }}</h4></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
