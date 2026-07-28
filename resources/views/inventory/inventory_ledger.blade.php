<x-app-layout>
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">format_align_justify</i></div>
    <h4 class="card-title">Inventory Ledger</h4>
  </div>
  <div class="card-body">
    <div class="ledger-heading mb-4">
      <h4 class="mb-1">Inventory Ledger</h4>
      <!-- <p class="text-muted mb-0">Day-to-day stock IN / OUT — who, when, quantity, before and after stock</p> -->
    </div>

    <div class="ledger-toolbar mb-4">
      <div class="ledger-search">
        <label for="ledger_search">Search</label>
        <input id="ledger_search" type="text" class="form-control" placeholder="Product, warehouse, person or reference">
      </div>
      <div class="ledger-date">
        <label for="ledger_from">From Date</label>
        <input id="ledger_from" type="date" class="form-control">
      </div>
      <div class="ledger-date">
        <label for="ledger_to">To Date</label>
        <input id="ledger_to" type="date" class="form-control">
      </div>
      <div class="ledger-movement" role="group" aria-label="Movement filter">
        <button type="button" class="active" data-movement="">All</button>
        <button type="button" data-movement="in">Stock IN</button>
        <button type="button" data-movement="out">Stock OUT</button>
      </div>
      <button type="button" id="ledger_export" class="btn btn-success ledger-export">
        <i class="material-icons">file_download</i> Export to Excel
      </button>
    </div>

    <div class="table-responsive ledger-table-wrap">
      <table id="inventory-ledger-table" class="table table-striped table-bordered responsive no-wrap">
        <thead class="text-primary"><tr>
          <th>Date</th><th>Product</th><th>Warehouse</th><th>Type</th>
          <th>Before</th><th>Change</th><th>After</th><th>Person</th><th>Reference</th>
        </tr></thead>
      </table>
    </div>
  </div>
</div></div></div>
<style>
.ledger-heading h4{font-weight:600;color:#27364a}
.ledger-toolbar{display:grid;grid-template-columns:minmax(260px,1.5fr) minmax(165px,.7fr) minmax(165px,.7fr) auto auto;gap:14px;align-items:end}
.ledger-toolbar label{display:block;font-weight:600;color:#5f6f86;margin:0 0 7px}
.ledger-toolbar .form-control{width:100%;height:46px}
.ledger-movement{height:46px;display:flex;padding:4px;border:1px solid #dce3ec;border-radius:8px;background:#f4f6fa;white-space:nowrap}
.ledger-movement button{border:0;background:transparent;color:#66758b;font-weight:600;padding:0 18px;border-radius:6px;cursor:pointer}
.ledger-movement button.active{background:#fff;color:#24466f;box-shadow:0 1px 4px rgba(31,55,86,.15)}
.ledger-export{height:46px;margin:0!important;display:flex;align-items:center;justify-content:center;gap:5px;white-space:nowrap}
.ledger-export .material-icons{font-size:20px}
.ledger-table-wrap{border:1px solid #e4e9f0;border-radius:10px;overflow:hidden}
#inventory-ledger-table{margin-bottom:0!important}
#inventory-ledger-table thead th{background:#f5f7fb;border-bottom:1px solid #e1e6ee;padding:15px 13px;white-space:nowrap}
#inventory-ledger-table tbody td{padding:14px 13px;vertical-align:middle}
.movement-badge{display:inline-flex;align-items:center;gap:7px;border-radius:18px;padding:6px 12px;font-weight:600}
.movement-badge span{width:8px;height:8px;border-radius:50%}
.movement-in{color:#1d9c58;background:#e7f7ef}.movement-in span{background:#1d9c58}
.movement-out{color:#dc4545;background:#fdebec}.movement-out span{background:#dc4545}
#inventory-ledger-table_wrapper .dataTables_info,#inventory-ledger-table_wrapper .dataTables_paginate{margin-top:14px}
@media(max-width:1250px){.ledger-toolbar{grid-template-columns:repeat(2,minmax(0,1fr))}.ledger-search{grid-column:1/-1}.ledger-export{width:100%}}
@media(max-width:767px){.ledger-toolbar{grid-template-columns:1fr}.ledger-search{grid-column:auto}.ledger-movement{width:100%}.ledger-movement button{flex:1;padding:0 8px}.ledger-export{width:100%}}
</style>
<script>
$(function(){
  let movement='';
  const table=$('#inventory-ledger-table').DataTable({
    processing:true,serverSide:true,order:[[0,'desc']],dom:'rtip',
    ajax:{url:"{{ route('inventory.ledger') }}",data:function(d){
      d.search_text=$('#ledger_search').val();
      d.date_from=$('#ledger_from').val();
      d.date_to=$('#ledger_to').val();
      d.movement=movement;
    }},
    columns:[
      {data:'movement_date',name:'il.id'},
      {data:'product_name',name:'p.product_name'},
      {data:'warehouse_name',name:'wh.warehouse_name'},
      {data:'movement_type',name:'movement_type',orderable:false,searchable:false},
      {data:'before_quantity',name:'before_quantity',orderable:false,searchable:false},
      {data:'change_quantity',name:'change_quantity',orderable:false,searchable:false},
      {data:'balance_quantity',name:'il.balance_quantity',searchable:false},
      {data:'person_name',name:'u.name'},
      {data:'reference',name:'reference',orderable:false,searchable:false}
    ]
  });

  let timer;
  $('#ledger_search').on('input',function(){clearTimeout(timer);timer=setTimeout(()=>table.ajax.reload(),350)});
  $('#ledger_from,#ledger_to').on('change',()=>table.ajax.reload());
  $('.ledger-movement button').on('click',function(){
    movement=$(this).data('movement');
    $('.ledger-movement button').removeClass('active');
    $(this).addClass('active');
    table.ajax.reload();
  });
  $('#ledger_export').on('click',function(){
    const params=$.param({
      search_text:$('#ledger_search').val(),
      date_from:$('#ledger_from').val(),
      date_to:$('#ledger_to').val(),
      movement:movement
    });
    window.location="{{ route('inventory.ledger.export') }}?"+params;
  });
});
</script>
</x-app-layout>
