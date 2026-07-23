<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">User Incentive
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['user_incentive_download']))
                <form method="POST" action="{{url('user_incentive/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- branch filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="branch_id" id="ps_branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- financial year filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="financial_year" id="ps_financial_year" required data-style="select-with-transition" title="Year">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.year') !!}</option>
                        @foreach($years as $year)
                        @php
                        $startYear = $year - 1;
                        $endYear = $year;
                        @endphp
                        <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
                        @endforeach
                      </select>
                    </div>
                    <!-- quarter filter-->
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="quarter" required id="quarter" disabled data-style="select-with-transition" title="Quarter">
                        <option value="">Quarter</option>
                        <option value="1">Q1(Apr,May,Jun)</option>
                        <option value="2">Q2(Jul,Aug,Sep)</option>
                        <option value="3">Q3(Oct,Nov,Dec)</option>
                        <option value="4">Q4(Jan,Feb,Mar)</option>
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} New Dealer Sales(From This Yesr)">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                    <!-- <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" name="last_year" value="true" title="{!!  trans('panel.global.download') !!} New Dealer Sales(From Last Yesr)">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div> -->
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
          
          <div class="table-responsive">
            <table id="getprimarysales" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>Branch</th>
                <th>Emp Code</th>
                <th>Name</th>
                <th>Joining Date</th>
                <th>Targer</th>
                <th>Achievement</th>
                <th>Fresh Sales Return</th>
                <th>Net Sales</th>
                <th>Target Achievement (%)</th>
                <th>Outstanding Value (>60 Days) %</th>
                <th>Stock Value (>90 Days) %</th>
                <th>Total Incentive</th>
                <th>Total Incentive as per weightage</th>
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
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getprimarysales').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [{
          className: 'control',
          orderable: false,
          targets: -1
        }],
        "order": [
          [0, 'desc']
        ],
        "retrieve": true,
        ajax: {
          url: "{{ route('user_incentive.list') }}",
          data: function(d) {
              d.branch_id = $('#ps_branch_id').val(),
              d.financial_year = $('#ps_financial_year').val(),
              d.quarter = $('#quarter').val(),
              d.search = $('input[type="search"]').val()
          }
        },
        columns: [
          {
            data: 'branch.branch_name',
            name: 'branch.branch_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'user.employee_codes',
            name: 'user.employee_codes',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'user.name',
            name: 'user.name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'user.userinfo.date_of_joining',
            name: 'user.userinfo.date_of_joining',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'total_target',
            name: 'total_target',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'achiv',
            name: 'achiv',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'fsr',
            name: 'fsr',
            orderable: false,
            searchable: false,
            "defaultContent": '0'
          },
          {
            data: 'achiv',
            name: 'achiv',
            orderable: false,
            searchable: false,
            "defaultContent": '0'
          },
          {
            data: 'taper',
            name: 'taper',
            orderable: false,
            searchable: false,
            "defaultContent": '0'
          },
          {
            data: 'ovper',
            name: 'ovper',
            orderable: false,
            searchable: false,
            "defaultContent": '0'
          },
          {
            data: 'svper',
            name: 'svper',
            orderable: false,
            searchable: false,
            "defaultContent": '0'
          },
          {
            data: 'total_inc',
            name: 'total_inc',
            orderable: false,
            searchable: false,
            "defaultContent": '0'
          },
          {
            data: 'total_inc_w',
            name: 'total_inc_w',
            orderable: false,
            searchable: false,
            "defaultContent": '0'
          }
        ]
      });
      $('#ps_branch_id').change(function() {
        table.draw();
      });
      $('#ps_financial_year').change(function() {
        $('#quarter').prop('disabled', false);
        $('#quarter').selectpicker('refresh');
        // table.draw();
      });
      $('#quarter').change(function() {
        table.draw();
      });
    });
  </script>
</x-app-layout>