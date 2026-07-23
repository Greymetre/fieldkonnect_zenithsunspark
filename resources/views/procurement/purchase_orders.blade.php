<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon"><i class="material-icons">south</i></div>
        <h4 class="card-title">Purchase Orders
          @can('purchase_order_create')
          <span class="pull-right">
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-theme">
              <i class="material-icons">add_circle</i> Create Purchase Order
            </a>
          </span>
          @endcan
        </h4>
      </div>
      <div class="card-body">
        <p class="text-muted">Create supplier order → Approve → Receive Stock (GRN)</p>
        <!-- <div class="alert alert-info">
          <i class="material-icons align-middle">info</i>
          Select supplier, products, quantity, GST and warehouse. Approval makes the PO available for receiving.
        </div> -->

        <div class="row mb-3">
          <div class="col-md-3">
            <label>Search</label>
            <input type="text" id="filter_search" class="form-control" placeholder="PO number or supplier">
          </div>
          <div class="col-md-2">
            <label>Supplier</label>
            <select id="filter_supplier" class="form-control select2">
              <option value="">All Suppliers</option>
              @foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label>Warehouse</label>
            <select id="filter_warehouse" class="form-control select2">
              <option value="">All Warehouses</option>
              @foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label>Status</label>
            <select id="filter_status" class="form-control">
              <option value="">All Statuses</option>
              <option value="draft">Draft</option><option value="approved">Approved</option>
              <option value="received">Received</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="col-md-3">
            <label>Date Range</label>
            <div class="d-flex">
              <input type="date" id="filter_from" class="form-control mr-1">
              <input type="date" id="filter_to" class="form-control">
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table id="purchase-order-table" class="table table-striped table-bordered responsive no-wrap">
            <thead class="text-primary">
              <tr><th>Action</th><th>PO No.</th><th>Supplier</th><th>Date</th><th>Warehouse</th><th>Items</th><th>Amount</th><th>Status</th></tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="purchaseOrderViewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card" id="purchase-order-modal-content"></div>
  </div>
</div>
<style>
.modal-dialog.modal-xl{max-width:90%}
.po-progress .step{display:flex;align-items:center;gap:8px;color:#9aa5b5}.po-progress .step span{width:36px;height:36px;border:3px solid #cbd5e1;border-radius:50%;display:inline-flex;align-items:center;justify-content:center}.po-progress .step.complete{color:#1f78d1}.po-progress .step.complete span{background:#1f78d1;color:#fff;border-color:#dbeafe}.po-progress .line{height:3px;background:#cbd5e1;width:64px;margin:0 10px}
</style>
<script>
$(function () {
  $('.select2').select2({width: '100%'});
  const table = $('#purchase-order-table').DataTable({
    processing: true, serverSide: true, order: [[1, 'desc']],
    ajax: {
      url: "{{ route('purchase-orders.index') }}",
      data: function (d) {
        d.search_text = $('#filter_search').val();
        d.supplier_id = $('#filter_supplier').val();
        d.warehouse_id = $('#filter_warehouse').val();
        d.status = $('#filter_status').val();
        d.from_date = $('#filter_from').val();
        d.to_date = $('#filter_to').val();
      }
    },
    columns: [
      {data:'action', orderable:false, searchable:false},
      {data:'po_number', name:'po_number'},
      {data:'supplier_name', orderable:false, searchable:false},
      {data:'po_date', name:'po_date'},
      {data:'warehouses', orderable:false, searchable:false},
      {data:'item_count', orderable:false, searchable:false},
      {data:'grand_total', name:'grand_total'},
      {data:'status_badge', name:'status', orderable:false}
    ]
  });
  $('#filter_supplier,#filter_warehouse,#filter_status,#filter_from,#filter_to').on('change', () => table.ajax.reload());
  let timer; $('#filter_search').on('input', function(){ clearTimeout(timer); timer=setTimeout(() => table.ajax.reload(), 350); });

  $(document).on('click', '.view-po', function () {
    $('#purchase-order-modal-content').html('<div class="p-5 text-center">Loading...</div>');
    $('#purchaseOrderViewModal').modal('show');
    $.get("{{ url('purchase-orders') }}/"+$(this).data('id'), {modal:1})
      .done(html => $('#purchase-order-modal-content').html(html))
      .fail(() => $('#purchase-order-modal-content').html('<div class="alert alert-danger m-3">Unable to load Purchase Order.</div>'));
  });
  $(document).on('click', '.modal-approve-po', function () {
    const button=$(this);
    appSwalConfirm({
      title:'Approve Purchase Order?',
      text:'After approval, this order will be available in Receive Stock (GRN).',
      confirmButtonText:'Yes, Approve',
      confirmButtonClass:'btn btn-success'
    }).then(function(confirmed){
      if(!confirmed)return;
      button.prop('disabled',true);
      $.post("{{ url('purchase-orders') }}/"+button.data('id')+"/approve", {
        _token:"{{ csrf_token() }}", approval_remark:$('#modal-approval-remark').val()
      }).done(r => {
        $('#purchaseOrderViewModal').modal('hide'); table.ajax.reload();
        $('.receive-stock-count').text(r.pending_receive_count).toggle(r.pending_receive_count > 0);
        appSwalSuccess(r.message);
      }).fail(x => { button.prop('disabled',false); appSwalError(x.responseJSON?.message || 'Unable to approve.'); });
    });
  });
  $(document).on('click', '.delete-po', function () {
    const button=$(this);
    appSwalConfirm({
      title:'Delete Draft Purchase Order?',
      text:'This action cannot be undone.',
      confirmButtonText:'Yes, Delete',
      confirmButtonClass:'btn btn-danger'
    }).then(function(confirmed){
      if(!confirmed)return;
      $.ajax({url:"{{ url('purchase-orders') }}/"+button.data('id'), type:'DELETE', data:{_token:"{{ csrf_token() }}"}})
        .done(r => { appSwalSuccess(r.message, 'Deleted'); table.ajax.reload(); })
        .fail(x => appSwalError(x.responseJSON?.message || 'Unable to delete.'));
    });
  });
});
</script>
</x-app-layout>
