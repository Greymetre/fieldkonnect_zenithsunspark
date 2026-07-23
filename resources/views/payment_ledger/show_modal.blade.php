<div class="modal-header"><h4 class="modal-title"><strong>Payment Details</strong></h4>
  <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button></div>
<div class="modal-body">
  <p><strong>Customer:</strong> {{ $salesOrder->customer->name }}</p>
  <p><strong>Order No.:</strong> {{ $salesOrder->order_number }}</p>
  <p><strong>Order Amount:</strong> ₹{{ number_format($salesOrder->grand_total,2) }}</p>
  @if($salesOrder->payments->isEmpty())
    <div class="alert alert-warning">No payment received yet.</div>
  @else
    <div class="table-responsive"><table class="table"><thead class="text-primary"><tr><th>Receipt</th><th>Date</th><th>Mode</th><th>Reference</th><th>Amount</th></tr></thead>
      <tbody>@foreach($salesOrder->payments as $payment)<tr><td>{{ $payment->receipt_number }}</td><td>{{ $payment->payment_date->format('d M Y') }}</td>
        <td>{{ strtoupper(str_replace('_',' ',$payment->payment_mode)) }}</td><td>{{ $payment->reference_number ?: '-' }}</td><td>₹{{ number_format($payment->amount_received,2) }}</td></tr>@endforeach</tbody>
    </table></div>
  @endif
  <div class="row"><div class="col-md-6"><strong>Amount Paid:</strong> ₹{{ number_format($paidAmount,2) }}</div>
    <div class="col-md-6 text-right"><strong>Balance Due:</strong> ₹{{ number_format($balanceDue,2) }}</div></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  @if($balanceDue > 0 && auth()->user()->can('sales_order_payment'))
    <button type="button" class="btn btn-success receive-balance-payment" data-url="{{ route('sales-orders.payment.modal',$salesOrder) }}">Receive Balance Payment</button>
  @endif
</div>
