@php($returnData = $return->returnData())
<form id="accept-sales-return-form" action="{{ route('sales-orders.return.accept', [$salesOrder, $return]) }}">
  @csrf
  <div class="modal-header">
    <h4 class="modal-title"><strong>Accept Return {{ $return->dispatch_number }}</strong></h4>
    <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
  </div>
  <div class="modal-body">
    <div id="accept-sales-return-errors" class="alert alert-danger" style="display:none"></div>
    <p>
      <strong>Sales Order:</strong> {{ $salesOrder->order_number }}<br>
      <strong>Customer:</strong> {{ $salesOrder->customer->name }}<br>
      <strong>Warehouse:</strong> {{ $salesOrder->warehouse->warehouse_name }}<br>
      <strong>Reason:</strong> {{ $returnData['reason'] ?? '-' }}
    </p>
    <h5>Items to Accept (confirm quantity)</h5>
    <div class="table-responsive">
      <table class="table">
        <thead class="text-primary"><tr><th>Product</th><th>Requested Qty</th><th>Accept Qty</th></tr></thead>
        <tbody>
          @foreach($return->items as $item)
            @php($maximum = round((float) $item->quantity, 3))
            <tr>
              <td>
                <strong>{{ $item->product->product_name }}</strong>
                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
              </td>
              <td>{{ rtrim(rtrim(number_format($maximum, 3, '.', ''), '0'), '.') }}</td>
              <td>
                <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control"
                  min="0" max="{{ $maximum }}" step="0.001"
                  value="{{ rtrim(rtrim(number_format($maximum, 3, '.', ''), '0'), '.') }}" required>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="alert alert-warning mb-0">Accepting is final. Accepted quantities will be added to stock and the inventory ledger.</div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-success" id="accept-sales-return">Accept Return → Stock IN</button>
  </div>
</form>
