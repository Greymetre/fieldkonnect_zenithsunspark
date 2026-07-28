<form id="sales-return-form" action="{{ route('sales-orders.return.store', $salesOrder) }}">
  @csrf
  <div class="modal-header">
    <h4 class="modal-title"><strong>Return Order {{ $salesOrder->order_number }}</strong></h4>
    <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
  </div>
  <div class="modal-body">
    <div id="sales-return-errors" class="alert alert-danger" style="display:none"></div>
    <p>
      <strong>Customer:</strong> {{ $salesOrder->customer->name }}<br>
      <strong>Return to Warehouse:</strong> {{ $salesOrder->warehouse->warehouse_name }}
    </p>
    <div class="input_section mb-4">
      <label><strong>Reason</strong></label>
      <textarea name="reason" class="form-control" rows="2" maxlength="2000" placeholder="Return reason" required></textarea>
    </div>
    <h5>Items Being Returned (edit quantity)</h5>
    <div class="table-responsive">
      <table class="table">
        <thead class="text-primary"><tr><th>Product</th><th>Shipped Qty</th><th>Return Qty</th></tr></thead>
        <tbody>
          @foreach($salesOrder->items as $item)
            @php($maximum = round((float) $item->dispatched_quantity, 3))
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
    <div class="alert alert-info mb-0">Stock will change only after the return is accepted.</div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-danger" id="process-sales-return">Process Return</button>
  </div>
</form>
