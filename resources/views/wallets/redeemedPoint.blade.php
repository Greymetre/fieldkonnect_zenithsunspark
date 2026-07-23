<x-app-layout>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-tabs card-header-warning">
        <div class="nav-tabs-navigation">
          <div class="nav-tabs-wrapper">
            <h4 class="card-title ">{!! trans('panel.wallet.title_singular') !!}{!! trans('panel.global.list') !!}
            @if(auth()->user()->can(['redeemedpoint_create']))
              <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                <li class="nav-item">
                  
                </li>
              </ul>
            @endif
          </h4>
          </div>
        </div>
      </div>
      <div class="card-body">
        @if(count($errors) > 0)
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
          </span>
        </div>
        @endif
        <div class="table-responsive">
          <table id="getwallet" class="table table-striped- table-bwalleted table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.global.buyer_id') !!}</th>
              <th>{!! trans('panel.global.buyer_name') !!}</th>
              <th>{!! trans('panel.wallet.fields.points') !!}</th>
              <th>{!! trans('panel.wallet.fields.transaction_type') !!}</th>
              <th>{!! trans('panel.wallet.fields.scheme') !!}</th>
              <th>{!! trans('panel.wallet.fields.transaction_at') !!}</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    oTable = $('#getwallet').DataTable({
        "processing": true,
        "serverSide": true,
        "wallet": [ [0, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('wallets.redeemedPoint') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '', walletable: false, searchable: false},
            {data: 'customer_id', name: 'customer_id',"defaultContent": ''},
            {data: 'customers.name', name: 'customers.name',"defaultContent": ''},
            {data: 'points', name: 'points',"defaultContent": ''},
            {data: 'transaction_type', name: 'transaction_type',"defaultContent": ''},
            {data: 'schemes.scheme_name', name: 'schemes.scheme_name',"defaultContent": ''},
            {data: 'transaction_at', name: 'transaction_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
