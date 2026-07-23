<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Customer KYC {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="GET" action="{{ route('customer-kyc.download') }}" class="form-horizontal">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:180px;">
                      <select class="select2" placeholder="Select Branch" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="kyc_status" id="kyc_status" data-style="select-with-transition" title="Select Parent Customer">
                        <option value="">Select Kyc Status</option>
                        <option value="5">Incomplete</option>
                        <option value="0">Submited</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="customer_type" id="customer_type" data-style="select-with-transition" title="Select Scheme Type">
                        <option value="">Customer Type</option>
                        @if(@isset($customer_types ))
                        @foreach($customer_types as $customer_type)
                        <option value="{!! $customer_type->id !!}">{!! $customer_type->customertype_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    @if(auth()->user()->can(['customer_kyc_download']))
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Transaction Coupon History"><i class="material-icons">cloud_download</i></button></div>
                    @endif
                  </div>
                </form>
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
          <div class="table-responsive">
            <table id="getcustomerkyc" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>KYC Created Date</th>
                <th>CUstomer Id</th>
                <th>Customer Firm Name</th>
                <th>Customer Type</th>
                <th>Contact Person</th>
                <th>Mobile Number</th>
                <th>State</th>
                <th>District</th>
                <th>City</th>
                <th>User Name</th>
                <th>Kyc Status</th>
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
      oTable = $('#getcustomerkyc').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('customer-kyc.index') }}",
          'data': function(d) {
            d.branch_id = $('#branch_id').val(),
              d.kyc_status = $('#kyc_status').val(),
              d.customer_type = $('#customer_type').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
          }
        },
        "columns": [{
            data: 'updated_at',
            name: 'updated_at',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'customer_id',
            name: 'customer_id',
            orderable: false,
            searchable: false
          },
          {
            data: 'customer.name',
            name: 'customer.name',
            "defaultContent": '',
          },
          {
            data: 'customer.customertypes.customertype_name',
            name: 'customer.customertypes.customertype_name',
            "defaultContent": '',
          },
          {
            data: 'customer.first_name',
            name: 'customer.first_name',
            "defaultContent": '',
          },
          {
            data: 'customer.mobile',
            name: 'customer.mobile',
            "defaultContent": '',
          },
          {
            data: 'customer.customeraddress.statename.state_name',
            name: 'customer.customeraddress.statename.state_name',
          },
          {
            data: 'customer.customeraddress.districtname.district_name',
            name: 'customer.customeraddress.districtname.district_name',
          },
          {
            data: 'customer.customeraddress.cityname.city_name',
            name: 'customer.customeraddress.cityname.city_name',
          },
          {
            data: 'user_name',
            name: 'user_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#branch_id').change(function() {
        oTable.draw();
      });
      $('#kyc_status').change(function() {
        oTable.draw();
      });
      $('#customer_type').change(function() {
        oTable.draw();
      });
      $('#start_date').change(function() {
        oTable.draw();
      });
      $('#end_date').change(function() {
        oTable.draw();
      });
    });
  </script>
</x-app-layout>