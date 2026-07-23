<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">View Claims
            <span class="pull-right">
              <div class="btn-group">
                <a href="{{ url('claim-generation') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.product.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                @if(auth()->user()->can('claim-pdf-generate'))
                    <a href="{{ route('claim-generation.pdf', ['id' => $claimGeneration->id]) }}" 
                       class="btn btn-just-icon btn-theme ml-2" title="Print">
                       <i class="material-icons">print</i>
                    </a>
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

          <div class="row">
            <div class="col-12" >
                <h4 class="mb-3" style="color: #7c7c7c;font-weight: bold !important;">{{isset($claimGeneration->service_center_details) ?  $claimGeneration->service_center_details->name : ''}} - {{$claimGeneration->claim_number ?? ''}}</h4>
                <hr>
            </div>
            <div class="table-responsive">
              <table id="getClaimsSingle" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
                <thead class=" text-primary">
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th>Comp No.</th>
                  <th>Comp Date</th>
                  <th>SB Approved Date</th>
                  <th>Claim No</th>
                  <th>Service</th>
                  <th>Prod Sr</th>
                  <th>HP</th>
                  <th>Stage</th>
                  <th>Phase</th>
                  <th>Cust Bill Date</th>
                  <th>Company Sale Bill Date</th>
                  <th>BRANCH</th>
                  <th>Repaired / Replacement</th>
                  <th>Service Location</th>
                  <th>Site Visit Category</th>
                  <th>SERVICE CHARGE</th>
                  <th>Site Visit Charge</th>
                  <th>Rewinding Charge</th>
                  <th>Local Spare Charge</th>
                  <th>Ttl Charg (W/o Tax)</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="pull-right col-md-12">

          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function(){
      getClaimsSingle();
    })

    function getClaimsSingle(){
      var claim_id = "{{$claimGeneration->id}}";
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      if ($.fn.DataTable.isDataTable('#getClaims')) {
          $('#getClaimsSingle').DataTable().destroy();
      }
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getClaimsSingle').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          type:'POST',
          url: "{{ route('getClaimsSingle') }}",
          data: function (d) {
                d._token = token,
                d.claim_id = claim_id
            }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'complaints.complaint_number',
            name: 'complaints.complaint_number',
            orderable: false,
            "defaultContent": ''
          },
         {
            data: 'complaints.complaint_date',
            name: 'complaints.complaint_date',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'serce_bill_approve_date',
            name: 'serce_bill_approve_date',
            orderable: false,
            "defaultContent": ''
          },
            {
            data: 'claim.claim_number',
            name: 'claim.claim_number',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'complaints.service_type',
            name: 'complaints.service_type',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaints.product_serail_number',
            name: 'complaints.product_serail_number',
            orderable: false,
            "defaultContent": ''
          },

           {
            data: 'complaints.specification',
            name: 'complaints.specification',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaints.product_no',
            name: 'complaints.product_no',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaints.phase',
            name: 'complaints.phase',
            orderable: false,
            "defaultContent": ''
          },
         {
            data: 'complaints.customer_bill_date',
            name: 'complaints.customer_bill_date',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'company_sale_bill_date',
            name: 'company_sale_bill_date',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaints.purchased_branch_details.branch_name',
            name: 'complaints.purchased_branch_details.branch_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaint_work_dones',
            name: 'complaint_work_dones',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaints.service_bill.service_location',
            name: 'complaints.service_bill.service_location',
            orderable: false,
            "defaultContent": ''
          },
         {
            data: 'complaints.service_bill.category',
            name: 'complaints.service_bill.category',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'service_charge',
            name: 'service_charge',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'site_visit_charge',
            name: 'site_visit_charge',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'rewinding_charge',
            name: 'rewinding_charge',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'local_spare_charges',
            name: 'local_spare_charges',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'total_changes',
            name: 'total_changes',
            orderable: false,
            "defaultContent": ''
          },
        
        ]
      });
    }
  </script>
</x-app-layout>