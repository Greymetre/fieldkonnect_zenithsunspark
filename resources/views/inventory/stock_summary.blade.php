<x-app-layout>
<div class="row"><div class="col-md-12"><div class="card">
  <div class="card-header card-header-icon card-header-theme">
    <div class="card-icon"><i class="material-icons">view_list</i></div>
    <h4 class="card-title">Stock Summary</h4>
  </div>
  <div class="card-body">
    <div class="row align-items-end stock-summary-toolbar">
      <div class="col-lg-6 col-md-12 mb-3">
        <h4 class="mb-1">Warehouse-wise Stock</h4>
        <!-- <p class="text-muted mb-0">Live stock position for all products across every warehouse</p> -->
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="input_section mb-0">
          <label for="stock_search">Search Product</label>
          <input type="text" id="stock_search" class="form-control" placeholder="Enter product name">
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-3">
        <div class="input_section mb-0">
          <label for="stock_warehouse">Warehouse</label>
          <select id="stock_warehouse" class="form-control">
            <option value="">All Warehouses</option>
            @foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>@endforeach
          </select>
        </div>
      </div>
    </div>

    <div class="stock-value-card mb-4">
      <div class="stock-value-icon"><i class="material-icons">currency_rupee</i></div>
      <div>
        <div class="stock-value-label">Total Stock Value</div>
        <div class="stock-value-amount" id="total-stock-value">₹0.00</div>
      </div>
    </div>

    <div class="table-responsive stock-summary-table-wrap"><table id="stock-summary-table" class="table table-striped table-bordered responsive no-wrap">
      <thead class="text-primary"><tr><th>Product</th><th>Warehouse</th><th>Available</th><th>Stock Value</th></tr></thead>
    </table></div>
  </div>
</div></div></div>
<style>
.stock-summary-toolbar{margin-bottom:12px}
.stock-summary-toolbar .input_section label{display:block;font-weight:600;color:#5f6f86;margin-bottom:7px}
.stock-summary-toolbar .form-control{width:100%;height:46px}
.stock-value-card{position:relative;display:flex;align-items:center;gap:18px;padding:24px 28px;border:1px solid #e2e8f0;border-left:5px solid #f39c12;border-radius:10px;background:#fff;box-shadow:0 2px 8px rgba(31,55,86,.08);overflow:hidden}
.stock-value-card:after{content:"";position:absolute;width:110px;height:110px;border-radius:50%;right:-35px;top:-45px;background:rgba(243,156,18,.08)}
.stock-value-icon{width:52px;height:52px;border-radius:10px;background:#fff3d8;color:#e99100;display:flex;align-items:center;justify-content:center;flex:0 0 52px}
.stock-value-icon .material-icons{font-size:30px}
.stock-value-label{color:#66758b;font-size:15px;font-weight:600;margin-bottom:4px}
.stock-value-amount{color:#1f2d3d!important;font-size:32px;font-weight:700;line-height:1.2}
.stock-summary-table-wrap{border:1px solid #e4e9f0;border-radius:10px;padding:0;overflow:hidden}
#stock-summary-table{margin-bottom:0!important}
#stock-summary-table thead th{background:#f5f7fb;border-bottom:1px solid #e1e6ee;padding:16px}
#stock-summary-table tbody td{padding:14px 16px;vertical-align:middle}
#stock-summary-table_wrapper .dataTables_info,#stock-summary-table_wrapper .dataTables_paginate{margin-top:14px}
@media(max-width:767px){.stock-value-card{padding:20px}.stock-value-amount{font-size:25px}.stock-summary-toolbar .col-md-6{width:100%}}
</style>
<script>
$(function(){
  const currency=new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR',minimumFractionDigits:2});
  const table=$('#stock-summary-table').DataTable({
    processing:true,serverSide:true,order:[[0,'asc'],[1,'asc']],dom:'rtip',
    ajax:{url:"{{ route('inventory.stock-summary') }}",data:function(d){
      d.search_text=$('#stock_search').val();d.warehouse_id=$('#stock_warehouse').val();
    }},
    columns:[
      {data:'product_name',name:'products.product_name'},
      {data:'warehouse_name',name:'ware_houses.warehouse_name'},
      {data:'available_quantity',name:'available_quantity',searchable:false},
      {data:'stock_value',name:'stock_value',searchable:false}
    ]
  });
  table.on('xhr.dt',function(e,settings,json){
    $('#total-stock-value').text(currency.format(parseFloat(json?.total_stock_value||0)));
  });
  let timer;$('#stock_search').on('input',function(){clearTimeout(timer);timer=setTimeout(()=>table.ajax.reload(),350)});
  $('#stock_warehouse').on('change',()=>table.ajax.reload());
});
</script>
</x-app-layout>
