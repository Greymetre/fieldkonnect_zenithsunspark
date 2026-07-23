<x-app-layout>
  <style>
    table tbody tr {
      font-size: 14px !important;
      font-weight: 100 !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Customer Outstanding
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="POST" action="{{url('reports/customer_outstanting/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- division filter -->
                    {{-- <div class="p-2" style="width:200px;">
                      <label for="division">Division</label>
                      <select class="select2" name="division[]" placeholder="Division" multiple id="ps_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                        <option value="" disabled>{!! trans('panel.secondary_dashboard.division') !!}</option>
                        @if(@isset($ps_divisions ))
                        @foreach($ps_divisions as $division)
                        <option value="{!! $division->division !!}">{!! $division->division !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div> --}}
                    <!-- Date filter -->
                    <div class="p-2" style="width:200px;">
                      <input type="text" class="form-control datepicker" id="balance_date" name="balance_date" placeholder="Date" autocomplete="off">
                      <div class="ripple-container"></div>
                    </div>

                    <!-- Customer filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="customer_id" id="customer_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>Customer</option>
                        @if(@isset($customers ))
                        @foreach($customers as $customer)
                        <option value="{!! $customer->id !!}">{!! $customer->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <!-- branchs filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>Branch</option>
                        @if(@isset($branchs ))
                        @foreach($branchs as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                     <!-- divisions filter -->
                     <div class="p-2" style="width:180px;">
                      <select class="select2" name="division_id" id="division_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>Division</option>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division->id !!}">{!! $division->division_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    @if(auth()->user()->can(['customer_outstanting_download']))
                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" name="download" value="excel" title="{!!  trans('panel.global.download') !!} Customer Outstanding">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                    @endif
                    @if(auth()->user()->can(['generate_balance_confirmation']))
                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" name="download" value="pdf" title="Genrate Balance Confirmation">
                        <i class="material-icons">picture_as_pdf</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                    @endif
                  </div>
                </form>
                <div class="row next-btn">
                  @if(auth()->user()->can(['customer_outstanting_upload']))
                  <form action="{{ URL::to('reports/customer_outstanting/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group" style="flex-wrap:nowrap;">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input title="Please select a file for upload data" type="file" title="Select file for upload data" name="import_file" style="flex-wrap: nowrap;" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Customer Outstanding">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  <!-- primary sales import -->
                  @if(auth()->user()->can(['customer_outstanting_template']))
                  <!-- primary sales template creation -->
                  <a href="{{ URL::to('reports/customer_outstanting_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Customer Outstanding"><i class="material-icons">text_snippet</i></a>
                  @endif
                </div>
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

          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session()->get('message_success') }}
            </span>
          </div>
          @endif

          <div class="table-responsive">
            <table id="getprimarysales" class="table table-striped table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>Branch</th>
                <th>Balance Confirmations</th>
                <th>Customer Name</th>
                <th>Year</th>
                <th>Quarter</th>
                <th>0-30</th>
                <th>31-60</th>
                <th>61-90</th>
                <th>91-150</th>
                <th>>150</th>
                <th>Total Outstanding</th>
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
          url: "{{ route('reports.customer_outstanting') }}",
          data: function(d) {
            d.customer_id = $('#customer_id').val(),
            d.search = $('input[type="search"]').val(),
            d.branch_id = $('#branch_id').val(),
            d.division_id = $('#division_id').val()
          }
        },
        columns: [{
            data: 'branch.branch_name',
            name: 'branch.branch_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'balance_confirmations',
            name: 'balance_confirmations',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'customer.name',
            name: 'customer.name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'year',
            name: 'year',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'quarter',
            name: 'quarter',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'first_slot',
            name: 'first_slot',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'second_slot',
            name: 'second_slot',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'thired_slot',
            name: 'thired_slot',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'fourth_slot',
            name: 'fourth_slot',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'fifth_slot',
            name: 'fifth_slot',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'total_amounts',
            name: 'total_amounts',
            searchable: false,
            "defaultContent": ''
          }
        ]
      });
      $('#customer_id').change(function() {
        table.draw();
      });
      $('#branch_id').change(function() {
        table.draw();
      });
      $('#division_id').change(function() {
        table.draw();
      });
    });

    $('#reset-filter').on('click', function() {
      $('#prifilfrm').find('input:text, input:password, input:file, select, textarea').val('');
      $('#prifilfrm').find('select').change();
    })
  </script>
</x-app-layout>