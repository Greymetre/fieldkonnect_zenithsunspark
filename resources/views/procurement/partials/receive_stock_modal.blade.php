@php($received = $purchaseOrder->status === 'received')
<form id="receive-stock-form" action="{{ route('procurement.receive-stock.store', $purchaseOrder) }}">
  @csrf
  <div class="modal-header">
    <h4 class="modal-title"><strong>{{ $purchaseOrder->po_number }} — {{ $purchaseOrder->supplier->name }}</strong></h4>
    <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
  </div>
  <div class="modal-body">
    <div class="d-flex align-items-center mb-4 po-progress">
      <div class="step complete"><span>✓</span><strong>Created</strong></div>
      <div class="line complete"></div>
      <div class="step complete"><span>2</span><strong>Approved</strong></div>
      <div class="line {{ $received ? 'complete' : '' }}"></div>
      <div class="step {{ $received ? 'complete' : '' }}"><span>3</span><strong>Received</strong></div>
    </div>

    <div id="receive-stock-errors" class="alert alert-danger" style="display:none"></div>
    <div class="table-responsive">
      <table class="table">
        <thead class="text-primary"><tr><th>Product</th><th>Warehouse</th><th>Qty</th><th class="text-right">Amount</th></tr></thead>
        <tbody>
          @foreach($purchaseOrder->items as $index => $item)
          <tr class="receive-item" data-rate="{{ $item->rate }}" data-gst="{{ $item->gst_percent }}">
            <td>
              {{ $item->product->product_name }}
              <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
            </td>
            <td>
              @if($received)
                {{ optional($item->warehouse)->warehouse_name }}
              @else
                <select name="items[{{ $index }}][warehouse_id]" class="form-control receive-warehouse" required>
                  @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ $item->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->warehouse_name }}</option>
                  @endforeach
                </select>
              @endif
            </td>
            <td style="max-width:140px">
              @if($received)
                {{ rtrim(rtrim(number_format($item->quantity, 3, '.', ''), '0'), '.') }}
              @else
                <input type="number" name="items[{{ $index }}][quantity]" class="form-control receive-quantity"
                  min="0.001" step="0.001" value="{{ rtrim(rtrim(number_format($item->quantity, 3, '.', ''), '0'), '.') }}" required>
              @endif
            </td>
            <td class="text-right receive-amount">₹{{ number_format($item->total_amount, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="input_section">
      <label>Remark (Receive)</label>
      @if($received)
        <div class="form-control" style="min-height:70px">{{ $purchaseOrder->receive_remark ?: '-' }}</div>
      @else
        <textarea name="receive_remark" class="form-control" rows="3" placeholder="Optional receipt remark"></textarea>
      @endif
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    @if(!$received && auth()->user()->can('receive_stock_create'))
      <button type="submit" class="btn btn-warning" id="confirm-receive-stock">Receive Stock</button>
    @endif
  </div>
</form>
