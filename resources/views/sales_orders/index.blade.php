<x-app-layout>
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">north</i></div>
    <h4 class="card-title">Sales Orders</h4>
  </div>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start flex-wrap">
      <div>
        <p class="text-muted">B2B &amp; B2C — Order → Payment → Confirm → Dispatch</p>
        @can('sales_order_create')
          <button class="btn btn-warning open-sales-order" data-type="b2c">+ B2C Order</button>
          <button class="btn btn-theme open-sales-order" data-type="b2b">+ B2B Order</button>
        @endcan
      </div>
      <div class="row">
        <div class="col"><input id="sales_search" class="form-control" placeholder="Order no. or customer"></div>
        <div class="col"><input type="date" id="sales_from" class="form-control"></div>
        <div class="col"><input type="date" id="sales_to" class="form-control"></div>
        <div class="col"><select id="sales_status" class="form-control">
          <option value="">All Statuses</option><option value="payment_pending">Payment Pending</option>
          <option value="payment_partial">Partial Payment</option><option value="payment_received">Payment Received</option><option value="confirmed">Confirmed</option>
          <option value="partially_dispatched">Partially Dispatched</option><option value="dispatched">Dispatched</option><option value="cancelled">Cancelled</option>
        </select></div>
      </div>
    </div>
    <!-- <div class="alert alert-info mt-3"><i class="material-icons align-middle">info</i>
      Order create → Payment confirm → Confirm sale → Dispatch. Warehouse stock will reduce only on dispatch.
    </div> -->
    <div class="table-responsive"><table id="sales-order-table" class="table table-striped table-bordered responsive no-wrap">
      <thead class="text-primary"><tr><th>Action</th><th>Order No.</th><th>Order Date</th><th>Type</th><th>Customer</th><th>Warehouse</th><th>Amount</th><th>Status</th></tr></thead>
    </table></div>
  </div>
</div></div></div>

<div class="modal fade" id="salesOrderModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document"><div class="modal-content card" id="sales-order-modal-content"></div></div>
</div>
<style>#salesOrderModal .modal-dialog.modal-xl{max-width:92%}</style>
<script>
$(function(){
  const table=$('#sales-order-table').DataTable({
    processing:true,serverSide:true,order:[[1,'desc']],
    ajax:{url:"{{ route('sales-orders.index') }}",data:function(d){
      d.search_text=$('#sales_search').val();d.from_date=$('#sales_from').val();
      d.to_date=$('#sales_to').val();d.status=$('#sales_status').val();
    }},
    columns:[
      {data:'action',orderable:false,searchable:false},{data:'order_number'},{data:'order_date'},
      {data:'order_type'},{data:'customer_name',orderable:false,searchable:false},
      {data:'warehouse_name',orderable:false,searchable:false},{data:'grand_total'},{data:'status_badge',orderable:false,searchable:false}
    ]
  });
  let timer;$('#sales_search').on('input',function(){clearTimeout(timer);timer=setTimeout(()=>table.ajax.reload(),350)});
  $('#sales_from,#sales_to,#sales_status').on('change',()=>table.ajax.reload());

  function loadModal(url){
    $('#sales-order-modal-content').html('<div class="p-5 text-center">Loading...</div>');
    $('#salesOrderModal').modal('show');
    $.get(url).done(html=>$('#sales-order-modal-content').html(html))
      .fail(xhr=>$('#sales-order-modal-content').html('<div class="alert alert-danger m-3">'+(xhr.responseJSON?.message||'Unable to load Sales Order.')+'</div>'));
  }
  $('.open-sales-order').on('click',function(){loadModal("{{ route('sales-orders.create') }}?type="+$(this).data('type'))});
  @if(in_array(request('create_type'), ['b2b', 'b2c'], true) && (request()->filled('customer_id') || request()->filled('product_id')))
  @php
    $salesOrderCreateParameters = ['type' => request('create_type')];
    if (request()->filled('customer_id')) {
      $salesOrderCreateParameters['customer_id'] = (int) request('customer_id');
    }
    if (request()->filled('product_id')) {
      $salesOrderCreateParameters['product_id'] = (int) request('product_id');
    }
    $salesOrderCreateUrl = route('sales-orders.create', $salesOrderCreateParameters);
  @endphp
  loadModal(@json($salesOrderCreateUrl));
  @endif
  $(document).on('click','.view-sales-order',function(){loadModal($(this).data('url'))});
  $(document).on('click','.receive-sales-payment',function(){loadModal($(this).data('url'))});
  $(document).on('click','.open-sales-return,.accept-sales-return',function(){loadModal($(this).data('url'))});
  $(document).on('submit','#sales-order-form',function(e){
    e.preventDefault();const form=$(this),button=$('#create-sales-order-button').prop('disabled',true);
    $('#sales-order-errors').hide().empty();
    $.post(form.attr('action'),form.serialize()).done(function(response){
      table.ajax.reload();
      $('.sales-order-count').text(parseInt($('.sales-order-count').text()||0)+1).show();appSwalSuccess(response.message);
    }).fail(function(xhr){
      button.prop('disabled',false);const errors=xhr.responseJSON?.errors;
      $('#sales-order-errors').html(errors?Object.values(errors).flat().join('<br>'):(xhr.responseJSON?.message||'Unable to create order.')).show();
    });
  });
  $(document).on('click','.delete-sales-order',function(){
    const button=$(this);
    appSwalConfirm({
      title:'Delete Sales Order?',
      text:'This payment-pending order will be permanently deleted.',
      confirmButtonText:'Yes, Delete',
      confirmButtonClass:'btn btn-danger'
    }).then(function(confirmed){
      if(!confirmed)return;
      $.ajax({url:"{{ url('sales-orders') }}/"+button.data('id'),type:'DELETE',data:{_token:"{{ csrf_token() }}"}})
        .done(r=>{table.ajax.reload();appSwalSuccess(r.message,'Deleted')})
        .fail(x=>appSwalError(x.responseJSON?.message||'Unable to delete order.'));
    });
  });
  $(document).on('submit','#sales-payment-form',function(e){
    e.preventDefault();const form=$(this),button=$('#confirm-sales-payment').prop('disabled',true);
    $('#sales-payment-errors').hide().empty();
    $.post(form.attr('action'),form.serialize()).done(function(response){
      table.ajax.reload();
      const count=response.pending_sales_order_count;
      $('.sales-order-count').text(count).toggle(count>0);appSwalSuccess(response.message);
    }).fail(function(xhr){
      button.prop('disabled',false);const errors=xhr.responseJSON?.errors;
      $('#sales-payment-errors').html(errors?Object.values(errors).flat().join('<br>'):(xhr.responseJSON?.message||'Unable to confirm payment.')).show();
    });
  });
  $(document).on('click','.confirm-sales-order',function(){
    const button=$(this);
    appSwalConfirm({
      title:'Confirm Sale?',
      text:'This order will be sent to the Dispatch Desk.',
      confirmButtonText:'Yes, Confirm Sale',
      confirmButtonClass:'btn btn-success'
    }).then(function(confirmed){
      if(!confirmed)return;
      button.prop('disabled',true);
      $.post("{{ url('sales-orders') }}/"+button.data('id')+"/confirm",{_token:"{{ csrf_token() }}"})
        .done(response=>appSwalSuccess(response.message).then(()=>{window.location=response.redirect_url}))
        .fail(xhr=>{button.prop('disabled',false);appSwalError(xhr.responseJSON?.message||'Unable to confirm sale.')});
    });
  });
  $(document).on('submit','#sales-return-form',function(e){
    e.preventDefault();
    const form=$(this),button=$('#process-sales-return').prop('disabled',true);
    $('#sales-return-errors').hide().empty();
    $.post(form.attr('action'),form.serialize()).done(function(response){
      $('#salesOrderModal').modal('hide');
      table.ajax.reload(null,false);
      appSwalSuccess(response.message);
    }).fail(function(xhr){
      button.prop('disabled',false);
      const errors=xhr.responseJSON?.errors;
      $('#sales-return-errors').html(errors?Object.values(errors).flat().join('<br>'):(xhr.responseJSON?.message||'Unable to submit return.')).show();
    });
  });
  $(document).on('submit','#accept-sales-return-form',function(e){
    e.preventDefault();
    const form=$(this),button=$('#accept-sales-return').prop('disabled',true);
    $('#accept-sales-return-errors').hide().empty();
    appSwalConfirm({
      title:'Accept Returned Stock?',
      text:'This is final. Accepted quantities will be added to warehouse stock.',
      confirmButtonText:'Yes, Accept Return',
      confirmButtonClass:'btn btn-success'
    }).then(function(confirmed){
      if(!confirmed){button.prop('disabled',false);return;}
      $.post(form.attr('action'),form.serialize()).done(function(response){
        $('#salesOrderModal').modal('hide');
        table.ajax.reload(null,false);
        appSwalSuccess(response.message);
      }).fail(function(xhr){
        button.prop('disabled',false);
        const errors=xhr.responseJSON?.errors;
        $('#accept-sales-return-errors').html(errors?Object.values(errors).flat().join('<br>'):(xhr.responseJSON?.message||'Unable to accept return.')).show();
      });
    });
  });
});
</script>
</x-app-layout>
