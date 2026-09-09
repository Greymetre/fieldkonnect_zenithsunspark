<x-app-layout>
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">local_shipping</i></div>
    <h4 class="card-title">Dispatch Desk</h4>
  </div>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-end flex-wrap mb-3">
      <!-- <p class="text-muted mb-0">Confirmed orders ready for dispatch — warehouse stock remains unchanged until dispatch.</p> -->
      <div class="d-flex">
        <select id="dispatch_status" class="form-control mr-2"><option value="pending">Pending Dispatch</option><option value="dispatched">Fully Dispatched</option><option value="all">All</option></select>
        <input id="dispatch_search" class="form-control mr-2" placeholder="Order no. or customer">
        <input type="date" id="dispatch_from" class="form-control mr-2">
        <input type="date" id="dispatch_to" class="form-control">
      </div>
    </div>
    <div class="table-responsive"><table id="dispatch-table" class="table table-striped table-bordered responsive no-wrap">
      <thead class="text-primary"><tr><th>Order No.</th><th>Order Date</th><th>Type</th><th>Customer</th><th>From Warehouse</th><th>Items</th><th>Remaining Qty</th><th>Status</th><th>Payment</th><th>Action</th></tr></thead>
    </table></div>
  </div>
</div></div></div>
<div class="modal fade" id="dispatchOrderModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document"><div class="modal-content card" id="dispatch-modal-content"></div></div>
</div>
<style>#dispatchOrderModal .modal-dialog.modal-xl{max-width:90%}</style>
<script>
$(function(){
  const table=$('#dispatch-table').DataTable({
    processing:true,serverSide:true,order:[[0,'desc']],
    ajax:{url:"{{ route('dispatch.index') }}",data:function(d){d.search_text=$('#dispatch_search').val();d.from_date=$('#dispatch_from').val();d.to_date=$('#dispatch_to').val();d.dispatch_status=$('#dispatch_status').val()}},
    columns:[
      {data:'order_number'},{data:'order_date'},{data:'order_type'},{data:'customer_name',orderable:false,searchable:false},
      {data:'warehouse_name',orderable:false,searchable:false},{data:'item_count',orderable:false,searchable:false},
      {data:'pending_quantity',orderable:false,searchable:false},{data:'order_status',orderable:false,searchable:false},
      {data:'payment_mode',orderable:false,searchable:false},{data:'action',orderable:false,searchable:false}
    ]
  });
  let timer;$('#dispatch_search').on('input',function(){clearTimeout(timer);timer=setTimeout(()=>table.ajax.reload(),350)});
  $('#dispatch_from,#dispatch_to,#dispatch_status').on('change',()=>table.ajax.reload());
  $(document).on('click','.open-dispatch,.dispatch-history',function(){
    $('#dispatch-modal-content').html('<div class="p-5 text-center">Loading...</div>');
    $('#dispatchOrderModal').modal('show');
    $.get($(this).data('url')).done(html=>$('#dispatch-modal-content').html(html))
      .fail(xhr=>$('#dispatch-modal-content').html('<div class="alert alert-danger m-3">'+(xhr.responseJSON?.message||'Unable to load dispatch.')+'</div>'));
  });
  $(document).on('click','.delete-dispatch',function(){
    const button=$(this),historyUrl=button.data('history-url');
    appSwalConfirm({
      title:'Delete this Dispatch?',
      text:'This dispatch quantity will return to stock and become pending on the SO. The SO and its payments will remain.',
      confirmButtonText:'Yes, Delete Dispatch',confirmButtonClass:'btn btn-danger'
    }).then(function(confirmed){
      if(!confirmed)return;
      $('.delete-dispatch').prop('disabled',true);
      $.ajax({url:button.data('url'),type:'DELETE',data:{_token:"{{ csrf_token() }}"}})
        .done(function(response){
          table.ajax.reload(null,false);
          $('.dispatch-count').text(response.pending_dispatch_count).toggle(response.pending_dispatch_count>0);
          $.get(historyUrl).done(html=>$('#dispatch-modal-content').html(html))
            .fail(()=>$('#dispatchOrderModal').modal('hide'));
          appSwalSuccess(response.message);
        })
        .fail(function(xhr){
          $('.delete-dispatch').prop('disabled',false);
          appSwalError(xhr.responseJSON?.message||'Unable to delete dispatch.');
        });
    });
  });
  $(document).on('submit','#dispatch-order-form',function(e){
    e.preventDefault();
    const form=$(this);
    appSwalConfirm({
      title:'Dispatch Stock?',
      text:'Entered quantities will be deducted from warehouse stock.',
      confirmButtonText:'Yes, Dispatch',
      confirmButtonClass:'btn btn-warning'
    }).then(function(confirmed){
      if(!confirmed)return;
      const button=$('#dispatch-stock-button').prop('disabled',true);$('#dispatch-errors').hide().empty();
      $.post(form.attr('action'),form.serialize()).done(function(response){
        $('#dispatchOrderModal').modal('hide');table.ajax.reload();
        $('.dispatch-count').text(response.pending_dispatch_count).toggle(response.pending_dispatch_count>0);
        appSwalSuccess(response.message);
      }).fail(function(xhr){
        button.prop('disabled',false);const errors=xhr.responseJSON?.errors;
        const message=errors?Object.values(errors).flat().join('<br>'):(xhr.responseJSON?.message||'Unable to dispatch stock.');
        $('#dispatch-errors').html(message).show();
        appSwalError(message);
      });
    });
  });
});
</script>
</x-app-layout>
