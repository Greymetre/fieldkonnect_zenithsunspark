<form id="sales-payment-form" action="{{ route('sales-orders.payment.store', $salesOrder) }}">
  @csrf
  <div class="modal-header">
    <h4 class="modal-title"><strong>Receive Payment — {{ $salesOrder->order_number }}</strong></h4>
    <button type="button" class="close" data-dismiss="modal"><i class="material-icons">clear</i></button>
  </div>
  <div class="modal-body">
    <div id="sales-payment-errors" class="alert alert-danger" style="display:none"></div>
    <div class="alert alert-secondary">
      <div class="row">
        <div class="col-md-4"><small>CUSTOMER</small><strong class="d-block">{{ $salesOrder->customer->name }}</strong></div>
        <div class="col-md-4"><small>ORDER VALUE</small><strong class="d-block">₹{{ number_format($salesOrder->grand_total, 2) }}</strong></div>
        <div class="col-md-4"><small>BALANCE DUE</small><strong class="d-block">₹{{ number_format($balanceDue, 2) }}</strong></div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6"><div class="input_section"><label>Payment Date <span class="text-danger">*</span></label>
        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
      </div></div>
      <div class="col-md-6"><div class="input_section"><label>Amount Received <span class="text-danger">*</span></label>
        <input type="number" name="amount_received" class="form-control" min="0.01" max="{{ $balanceDue }}" step="0.01" value="{{ $balanceDue }}" required>
      </div></div>
      <div class="col-md-6"><div class="input_section"><label>Payment Mode <span class="text-danger">*</span></label>
        <select name="payment_mode" class="form-control" required>
          <option value="upi">UPI</option><option value="cash">Cash</option><option value="bank_transfer">Bank Transfer</option>
          <option value="card">Card</option><option value="cheque">Cheque</option>
        </select>
      </div></div>
      <div class="col-md-6"><div class="input_section"><label>Reference / Transaction ID</label>
        <input type="text" name="reference_number" class="form-control" maxlength="100" placeholder="e.g. UPI reference number">
      </div></div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-success" id="confirm-sales-payment">Confirm Payment &amp; Generate Receipt</button>
  </div>
</form>
