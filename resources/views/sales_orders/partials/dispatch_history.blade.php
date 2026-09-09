<div class="modal-header">
  <h4 class="modal-title">Dispatch History — {{ $salesOrder->order_number }}</h4>
  <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
  <p><strong>SO Status:</strong> {{ $salesOrder->status === 'confirmed' ? 'Ready to Dispatch' : ucwords(str_replace('_', ' ', $salesOrder->status)) }}</p>
  <div class="table-responsive"><table class="table">
    <thead><tr><th>Product</th><th>Ordered</th><th>Dispatched</th><th>Remaining</th></tr></thead>
    <tbody>@foreach($salesOrder->items as $item)
      <tr><td>{{ optional($item->product)->product_name }}</td><td>{{ (float) $item->quantity }}</td>
        <td>{{ (float) $item->dispatched_quantity }}</td><td>{{ round($item->quantity - $item->dispatched_quantity, 3) }}</td></tr>
    @endforeach</tbody>
  </table></div>
  @if($salesOrder->returns->isNotEmpty())
    <div class="alert alert-info">This SO has a return request. Individual dispatch deletion is unavailable because returns are recorded against the SO.</div>
  @endif
  @forelse($dispatches as $dispatch)
    <div class="border rounded p-3 mb-3">
      <div class="d-flex justify-content-between align-items-center">
        <strong>{{ $dispatch->dispatch_number }} · {{ optional($dispatch->dispatch_date)->format('d M Y') }}</strong>
        @if($salesOrder->returns->isEmpty() && auth()->user()->can('sales_order_delete'))
          <button type="button" class="btn btn-danger btn-sm delete-dispatch" data-url="{{ route('dispatch.destroy', [$salesOrder, $dispatch]) }}" data-history-url="{{ route('dispatch.history', $salesOrder) }}">Delete Dispatch</button>
        @endif
      </div>
      <div class="table-responsive"><table class="table mb-0">
        <thead><tr><th>Product</th><th>Dispatched Qty</th></tr></thead>
        <tbody>@foreach($dispatch->items as $item)
          <tr><td>{{ optional($item->product)->product_name }}</td><td>{{ (float) $item->quantity }}</td></tr>
        @endforeach</tbody>
      </table></div>
    </div>
  @empty
    <div class="alert alert-info">No dispatches remain. Pending quantities are available for dispatch.</div>
  @endforelse
</div>
<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div>
