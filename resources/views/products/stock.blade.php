<x-app-layout>
  <style>
    table tbody tr{
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
          <h4 class="card-title">Branch Stock
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['stock_download']))
                <form method="POST" action="{{url('stock/download')}}">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- branch filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Primary Sales">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                <div class="row next-btn">
                  @if(auth()->user()->can(['stock_upload']))
                  <form action="{{ URL::to('stock/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Customer Outstanting">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  <!-- primary sales import -->
                  @if(auth()->user()->can(['stock_template']))
                  <!-- primary sales template creation -->
                  <a href="{{ URL::to('stock_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Customer Outstanting"><i class="material-icons">text_snippet</i></a>
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
          
          <div class="table-responsive">
            <table id="getprimarysales" class="table table-striped table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>Branch</th>
                <th>Ware House</th>
                <th>Division</th>
                <th>Year</th>
                <th>Quarter</th>
                <th>0-30</th>
                <th>31-60</th>
                <th>61-90</th>
                <th>91-150</th>
                <th>>150</th>
                <th>Total Stock</th>
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
          url: "{{ route('stock') }}",
          data: function(d) {
              d.branch_id = $('#branch_id').val(),
              d.search = $('input[type="search"]').val()
          }
        },
        columns: [
          {
            data: 'branch.branch_name',
            name: 'branch.branch_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'warehouse.warehouse_name',
            name: 'warehouse.warehouse_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'division.division_name',
            name: 'division.division_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'year',
            name: 'year',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'quarter',
            name: 'quarter',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'first_slot',
            name: 'first_slot',
            orderable: false,
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
      $('#branch_id').change(function() {
        table.draw();
      });
    });

    $('#reset-filter').on('click', function(){
      $('#prifilfrm').find('input:text, input:password, input:file, select, textarea').val('');
      $('#prifilfrm').find('select').change();
    })
  </script>
</x-app-layout>