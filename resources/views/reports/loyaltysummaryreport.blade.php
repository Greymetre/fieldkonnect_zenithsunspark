<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title">Loyalty  Summary Branch Report
          <span class="">
            <div class="btn-group header-frm-btn">
              @if(auth()->user()->can(['customer_download']))
              <form method="GET" action="{{ URL::to('loyalty-summary-report-download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2">
                        <select class="select2" name="state_id" id="state_id" data-style="select-with-transition" title="Select User">
                         <option value="">Select State</option>
                        @if(@isset($states ))
                        @foreach($states as $state)
                         <option value="{!! $state['id'] !!}">{!! $state['state_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @if(auth()->user()->can(['loyalty_summary_report_download']))
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Loyalty  Summary Branch Report"><i class="material-icons">cloud_download</i></button></div>
                    @endif
                  </div>
              </form>
              @endif
            </div>
          </span>
        </h4>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
        <div class="row">
        <div class="table-responsive">
            <table id="getbranchsummary" class="table table-striped- table-bordered table-hover table-checkable ">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>State</th>
              <th>Total Retailer Registred Nos</th>
              <th>Total Retailer Under Saarthi Nos</th>
              <th>Coupon Scan Nos</th>
              <th>Mobile App Donwload Nos</th>
              <th>Provision Point</th>
              <th>Active Point</th>
              <th>Total Point</th>
              <th>Redeem Gift</th>
              <th>Redeem Neft</th>
              <th>Total Redeem</th>
              <th>Balance Active Point</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<style type="text/css">
  
  select#branch_id {
    border-bottom: 1px solid #d2d2d2 !important;
    border-radius: 0px;
    height: 38px;
    text-transform: uppercase;
    font-size: 13px!important;
    color: #000;
    font-weight: 400;
}
</style>

<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getbranchsummary').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [ {
            className: 'control',
            orderable: false,
            targets:   -1
        } ],
        "order": [ [0, 'desc'] ],
        ajax: {
          url: "{{ route('loyaltySummaryReport') }}",
          data: function (d) {
            d.state_id = $('#state_id').val();
          }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'state_name', name: 'state_name',"defaultContent": ''},
            {data: 'total_registered_retailers', name: 'total_registered_retailers',"defaultContent": ''},
            {data: 'total_registered_retailers_under_saarthi', name: 'total_registered_retailers_under_saarthi',"defaultContent": 'test3'},
            {data: 'coupon_scan_nos', name: 'coupon_scan_nos',"defaultContent": ''},
            {data: 'mobile_app_downloads', name: 'mobile_app_downloads',"defaultContent": ''},
            {data: 'provision_point', name: 'provision_point',"defaultContent": ''},
            {data: 'active_point', name: 'active_point',"defaultContent": ''},
            {data: 'total_point', name: 'total_point',"defaultContent": ''},
            {data: 'redeem_gift', name: 'redeem_gift',"defaultContent": ''},
            {data: 'redeem_neft', name: 'redeem_neft',"defaultContent": ''},
            {data: 'total_redeem', name: 'total_redeem',"defaultContent": ''},
            {data: 'balance_active_point', name: 'balance_active_point',"defaultContent": ''},
           
        ]
    });
    $('#state_id').change(function() {
      table.draw();
    });
  });
</script>
</x-app-layout>
