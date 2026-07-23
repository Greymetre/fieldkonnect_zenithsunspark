<x-app-layout>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Primary Scheme Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['primary_scheme_report_download']))
                <form method="POST" action="{{url('primary_scheme_report/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row align-items-end">
                    <!-- Division filter -->
                    <div class="p-2" style="width:180px;">
                      <label for="division">Division</label>
                      <select class="select2" name="division[]" multiple id="division" data-style="select-with-transition" required title="panel.sales_users.branch">
                        <!-- <option value="" disabled>Division</option> -->
                        @if(@isset($primary_divs ))
                        @foreach($primary_divs as $primary_div)
                        <option value="{!! $primary_div->division !!}">{!! $primary_div->division !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
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
                    <!-- Scheme filter -->
                    <div class="p-2 new_see" style="width:180px;">
                      <label for="scheme_id">Primary Scheme</label>
                      <select class="select2" name="scheme_id" id="scheme_id" data-style="select-with-transition" required title="panel.sales_users.branch">
                        <option value="" disabled>Primary Scheme</option>
                      </select>
                    </div>
                    <div class="p-2 new_see" style="width:180px;">
                      <label for="types">Type</label>
                      <select class="select2" name="types" id="types" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>Type</option>
                        <option value="qualified">Qualified</option>
                        <option value="unqualified">Unqualified</option>
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
            <!-- <table id="getprimarysales" class="d-none table table-striped- table-bordered table-hover table-checkable no-wrap">
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
            </table> -->
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
        var div = $("#division").val();
        if(div != 'FAN'){
          $('#quarter').prop('disabled', false);
          $('#quarter').selectpicker('refresh');
        }else{
          $('#quarter').val('');
          $('#quarter').prop('disabled', true);
          $('#quarter').selectpicker('refresh');
        }
        // table.draw();
      });
      $('#quarter').change(function() {
        $.ajax({
            //url: "/getProductData",
            url: "{{url('/getPrimarySachme')}}",
            data: {
               'quater': $(this).val(),
               'division': $("#division").val()
            },
            success: function(data) {
               var html = '';
               $.each(data.data, function(k, v) {
                  html += '<option value="' + v.id + '">' + v.scheme_name + '</option>';
               });
               $("#scheme_id").html(html);
            }
         });
        table.draw();
      });
    });

    $("#division").on("change", function(){
      var div = $(this).val();
      if(div == 'FAN'){
        $('#quarter').val('');
        $('#quarter').prop('disabled', true);
        $('#quarter').selectpicker('refresh');
        $.ajax({
            //url: "/getProductData",
            url: "{{url('/getPrimarySachme')}}",
            data: {
               'division': $(this).val()
            },
            success: function(data) {
            console.log(data.data.length);
               var html = '';
               $.each(data.data, function(k, v) {
                  html += '<option value="' + v.id + '">' + v.scheme_name + '</option>';
               });
               $("#scheme_id").html(html);
            }
         });
      }else{
        $("#scheme_id").html('');
      }
    })
  </script>
</x-app-layout>