$(document).on('click','.delete-sales-order',function(){
  const button=$(this);
  appSwalConfirm({
    title:'Delete Sales Order?',
    text:'This removes the entire SO from Sales and Dispatch. Net dispatched stock will be restored after adjusting accepted returns. Received payments remain in the ledger as refund pending; no automatic refund is made.',
    confirmButtonText:'Yes, Delete SO',
    confirmButtonClass:'btn btn-danger'
  }).then(function(confirmed){
    if(!confirmed)return;
    button.prop('disabled',true);
    $.ajax({url:"{{ url('sales-orders') }}/"+button.data('id'),type:'DELETE',data:{_token:"{{ csrf_token() }}"}})
      .done(function(response){
        table.ajax.reload(null,false);
        $('.sales-order-count').text(response.pending_sales_order_count).toggle(response.pending_sales_order_count>0);
        $('.dispatch-count').text(response.pending_dispatch_count).toggle(response.pending_dispatch_count>0);
        appSwalSuccess(response.message,'Deleted');
      })
      .fail(function(xhr){
        button.prop('disabled',false);
        appSwalError(xhr.responseJSON?.message||'Unable to delete order.');
      });
  });
});
