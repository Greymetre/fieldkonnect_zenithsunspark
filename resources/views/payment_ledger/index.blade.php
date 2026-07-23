<x-app-layout>
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">account_balance_wallet</i></div>
    <h4 class="card-title">Payment Ledger</h4>
  </div>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <p class="text-muted mb-0">Customer payment tracking against Sales Orders</p>
      <select id="ledger_status" class="form-control" style="max-width:220px">
        <option value="">All Statuses</option><option value="pending">Pending</option><option value="partial">Partial</option><option value="paid">Paid</option>
      </select>
    </div>
    <div class="table-responsive"><table id="payment-ledger-table" class="table table-striped table-bordered responsive no-wrap">
      <thead class="text-primary"><tr><th>Customer</th><th>SO No.</th><th>Amount</th><th>Paid</th><th>Balance</th><th>Status</th><th>Action</th></tr></thead>
    </table></div>
  </div>
</div></div></div>
<div class="modal fade" id="paymentLedgerModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document"><div class="modal-content card" id="payment-ledger-modal-content"></div></div>
</div>
<script>
$(function(){
  const table=$('#payment-ledger-table').DataTable({
    processing:true,serverSide:true,order:[[1,'desc']],
    ajax:{url:"{{ route('payment-ledger.index') }}",data:function(d){d.filter_status=$('#ledger_status').val();}},
    columns:[
      {data:'customer_name',orderable:false,searchable:false},{data:'order_number'},{data:'grand_total'},
      {data:'paid',orderable:false,searchable:false},{data:'balance',orderable:false,searchable:false},
      {data:'payment_status',orderable:false,searchable:false},{data:'action',orderable:false,searchable:false}
    ]
  });
  $('#ledger_status').on('change',()=>table.ajax.reload());
  function load(url){$('#payment-ledger-modal-content').html('<div class="p-5 text-center">Loading...</div>');$('#paymentLedgerModal').modal('show');
    $.get(url).done(html=>$('#payment-ledger-modal-content').html(html)).fail(x=>$('#payment-ledger-modal-content').html('<div class="alert alert-danger m-3">'+(x.responseJSON?.message||'Unable to load payment details.')+'</div>'))}
  $(document).on('click','.view-payment-ledger,.receive-balance-payment',function(){load($(this).data('url'))});
  $(document).on('submit','#sales-payment-form',function(e){
    e.preventDefault();const form=$(this),button=$('#confirm-sales-payment').prop('disabled',true);$('#sales-payment-errors').hide().empty();
    $.post(form.attr('action'),form.serialize()).done(r=>{table.ajax.reload();appSwalSuccess(r.message)})
      .fail(function(x){
        button.prop('disabled',false);const errors=x.responseJSON?.errors;
        const message=errors?Object.values(errors).flat().join('<br>'):(x.responseJSON?.message||'Unable to receive payment.');
        $('#sales-payment-errors').html(message).show();appSwalError(message);
      });
  });
});
</script>
</x-app-layout>
