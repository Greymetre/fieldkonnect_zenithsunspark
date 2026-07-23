<form id="dispatch-order-form" action="{{ route('dispatch.store', $salesOrder) }}">
  @csrf
  <div class="modal-header">
    <h4 class="modal-title"><strong>Dispatch Order {{ $salesOrder->order_number }}</strong></h4>
    <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
  </div>
  <div class="modal-body">
    <div id="dispatch-errors" class="alert alert-danger" style="display:none"></div>
    <p><strong>Customer:</strong> {{ $salesOrder->customer->name }}<br>
      <strong>From Warehouse:</strong> {{ $salesOrder->warehouse->warehouse_name }}</p>
    <h5>Items to Dispatch (edit quantity for partial dispatch)</h5>
    <div class="table-responsive"><table class="table">
      <thead class="text-primary"><tr><th>Product</th><th>Pending Qty</th><th>Available Stock</th><th>Dispatch Qty</th><th>Pending After</th></tr></thead>
      <tbody>
      @foreach($salesOrder->items as $item)
        @php
          $pending = max(0, round((float)$item->quantity - (float)$item->dispatched_quantity, 3));
          $available = round((float)($stocks[$item->product_id] ?? 0), 3);
          $defaultDispatch = min($pending, $available);
        @endphp
        @if($pending > 0)
        <tr class="dispatch-item" data-pending="{{ $pending }}" data-available="{{ $available }}">
          <td><strong>{{ $item->product->product_name }}</strong>
            <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
          </td>
          <td class="pending-qty">{{ rtrim(rtrim(number_format($pending,3,'.',''),'0'),'.') }}</td>
          <td><span class="badge badge-{{ $available > 0 ? 'success' : 'danger' }}">{{ rtrim(rtrim(number_format($available,3,'.',''),'0'),'.') }}</span></td>
          <td><input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control dispatch-qty"
            min="0" max="{{ min($pending, $available) }}" step="0.001" value="{{ rtrim(rtrim(number_format($defaultDispatch,3,'.',''),'0'),'.') }}" required></td>
          <td class="pending-after">{{ rtrim(rtrim(number_format($pending-$defaultDispatch,3,'.',''),'0'),'.') }}</td>
        </tr>
        @endif
      @endforeach
      </tbody>
    </table></div>
    <div class="input_section"><label>Dispatch Remark</label><textarea name="remark" class="form-control" rows="2"></textarea></div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-warning" id="dispatch-stock-button">Dispatch Items → Stock OUT</button>
  </div>
</form>
<script>
(function(){
  $('.dispatch-qty').on('input',function(){
    const row=$(this).closest('.dispatch-item'),pending=parseFloat(row.data('pending'))||0;
    let quantity=parseFloat($(this).val())||0;
    row.find('.pending-after').text(Math.max(0,pending-quantity).toFixed(3).replace(/\.?0+$/,''));
  });
})();
</script>
