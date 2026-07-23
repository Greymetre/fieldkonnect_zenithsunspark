<x-app-layout>
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">check</i></div>
    <h4 class="card-title">Receive Stock (GRN)</h4>
  </div>
  <div class="card-body">
    <p class="text-muted">Approved Purchase Orders pending for stock receipt.</p>
    <div class="row mb-3">
      <div class="col-md-4"><label>Search</label><input id="receive_search" class="form-control" placeholder="PO number or supplier"></div>
      <div class="col-md-4"><label>From Date</label><input type="date" id="receive_from" class="form-control"></div>
      <div class="col-md-4"><label>To Date</label><input type="date" id="receive_to" class="form-control"></div>
    </div>
    <div class="table-responsive"><table id="receive-stock-table" class="table table-striped table-bordered responsive no-wrap">
      <thead class="text-primary"><tr><th>Action</th><th>PO No.</th><th>Supplier</th><th>PO Date</th><th>Approve Date</th><th>Warehouse</th><th>Items</th><th>Qty</th><th>Status</th></tr></thead>
    </table></div>
  </div>
</div></div></div>
<div class="modal fade" id="receiveStockModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card" id="receive-stock-modal-content"></div>
  </div>
</div>
<style>
#receiveStockModal .modal-dialog.modal-xl{max-width:90%}
.po-progress .step{display:flex;align-items:center;gap:8px;color:#9aa5b5}
.po-progress .step span{width:36px;height:36px;border:3px solid #cbd5e1;border-radius:50%;display:inline-flex;align-items:center;justify-content:center}
.po-progress .step.complete{color:#1f78d1}.po-progress .step.complete span{background:#1f78d1;color:#fff;border-color:#dbeafe}
.po-progress .line{height:3px;background:#cbd5e1;width:64px;margin:0 10px}.po-progress .line.complete{background:#22a55b}
</style>
<script>
$(function(){
  const table=$('#receive-stock-table').DataTable({
    processing:true,serverSide:true,order:[[4,'desc']],
    ajax:{url:"{{ route('procurement.receive-stock') }}",data:d=>{d.search_text=$('#receive_search').val();d.from_date=$('#receive_from').val();d.to_date=$('#receive_to').val();}},
    columns:[
      {data:'action',orderable:false,searchable:false},{data:'po_number'},{data:'supplier_name',orderable:false,searchable:false},
      {data:'po_date'},{data:'approved_at'},{data:'warehouses',orderable:false,searchable:false},
      {data:'item_count',orderable:false,searchable:false},{data:'total_quantity',orderable:false,searchable:false},
      {data:'status_badge',orderable:false,searchable:false}
    ]
  });
  let timer;$('#receive_search').on('input',()=>{clearTimeout(timer);timer=setTimeout(()=>table.ajax.reload(),350)});
  $('#receive_from,#receive_to').on('change',()=>table.ajax.reload());

  $(document).on('click','.receive-po,.view-receipt',function(){
    $('#receive-stock-modal-content').html('<div class="p-5 text-center">Loading...</div>');
    $('#receiveStockModal').modal('show');
    $.get($(this).data('url'))
      .done(function(html){
        $('#receive-stock-modal-content').html(html);
        $('#receive-stock-modal-content .receive-warehouse').select2({width:'100%',dropdownParent:$('#receiveStockModal')});
      })
      .fail(function(xhr){
        $('#receive-stock-modal-content').html('<div class="alert alert-danger m-3">'+(xhr.responseJSON?.message || 'Unable to load receipt.')+'</div>');
      });
  });

  $(document).on('input','.receive-quantity',function(){
    const row=$(this).closest('.receive-item'),qty=parseFloat($(this).val())||0;
    const rate=parseFloat(row.data('rate'))||0,gst=parseFloat(row.data('gst'))||0;
    row.find('.receive-amount').text('₹'+(qty*rate*(1+gst/100)).toFixed(2));
  });

  $(document).on('submit','#receive-stock-form',function(event){
    event.preventDefault();
    const form=$(this);
    appSwalConfirm({
      title:'Receive Stock?',
      text:'Entered quantities will be added to the selected warehouses.',
      confirmButtonText:'Yes, Receive Stock',
      confirmButtonClass:'btn btn-success'
    }).then(function(confirmed){
      if(!confirmed)return;
      const button=$('#confirm-receive-stock').prop('disabled',true);
      $('#receive-stock-errors').hide().empty();
      $.post(form.attr('action'),form.serialize())
        .done(function(response){
          $('#receiveStockModal').modal('hide');
          table.ajax.reload();
          $('.receive-stock-count').text(response.pending_receive_count).toggle(response.pending_receive_count>0);
          appSwalSuccess(response.message);
        })
        .fail(function(xhr){
          button.prop('disabled',false);
          const errors=xhr.responseJSON?.errors;
          const message=errors ? Object.values(errors).flat().join('<br>') : (xhr.responseJSON?.message || 'Unable to receive stock.');
          $('#receive-stock-errors').html(message).show();
          appSwalError(message);
        });
    });
  });
});
</script>
</x-app-layout>
