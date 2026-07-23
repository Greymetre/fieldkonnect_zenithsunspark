<x-app-layout>
  <style>
    span.select2-dropdown.select2-dropdown--below {
      z-index: 99999 !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Resignation {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['resignation_download']))
                <form method="POST" action="{{ URL::to('resignation/download') }}" class="form-horizontal">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">

                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                        <option value="">Select Division</option>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division['id'] !!}">{!! $division['division_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="user_id" id="user_id" data-style="select-with-transition" title="Select User">
                        <option value="">Select User</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <select class="selectpicker" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch['id'] !!}">{!! $branch['branch_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <div class="p-2" style="width:180px;">
                      <select class="selectpicker" name="status" id="status" data-style="select-with-transition" title="Select Status">
                        <option value="">Select Status</option>
                        <option value="0">Pendding</option>
                        <option value="1">Accepted</option>
                        <option value="2">Rejected</option>
                        <option value="3">Revoke</option>
                        <option value="4">Approved</option>
                        <option value="5">Hold</option>
                      </select>
                    </div>

                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Resignations"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['transaction_history_template']))
                  <!-- <p>{!!  trans('panel.global.template') !!} Manual Transaction</p>
                      <a href="{{ URL::to('transaction_history_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Manual Transaction"><i class="material-icons">text_snippet</i></a> -->
                  @endif
                  @if(auth()->user()->can(['resignation_create']))
                  <!-- <p>{!!  trans('panel.global.add') !!} Transaction</p> -->
                  <!-- <a href="{{ route('damage_entries.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Damage Entry"><i class="material-icons">add_circle</i></a> -->
                  <!-- <p>{!!  trans('panel.global.add') !!} Manual Transaction</p> -->
                  <a href="{{ route('resignations.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} New Resignation"><i class="material-icons">add_circle</i></a>
                  @endif
                  <!-- </div>
                  </div> -->

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
          @if(session('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_success') }}
            </span>
          </div>
          @endif
          @if(session('message_info'))
          <div class="alert alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_info') }}
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
            <table id="getDamageEntries" class="table table-striped table-hover no-wrap">
              <thead class=" text-primary">
                <th>Resignation Date</th>
                <th>Status</th>
                <th>Division</th>
                <th>Branch</th>
                <th>Employee Code</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Mobile Number</th>
                <th>Reporting Manager</th>
                <th>Date Of Joining</th>
                <th>Notice</th>
                <th>Last Working Date</th>
                <th class="lenth_text">Reason</th>
                <th>Personal Email ID</th>
                <th>Personal Mobile Number</th>
                <th>Action</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getDamageEntries').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('resignations.index') }}",
          'data': function(d) {
            d.status = $('#status').val(),
              d.division_id = $('#division_id').val(),
              d.user_id = $('#user_id').val(),
              d.branch_id = $('#branch_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
          }
        },
        columns: [{
            data: 'submit_date',
            name: 'submit_date',
            orderable: false,
            searchable: false
          },
          {
            data: 'status',
            name: 'status',
            "defaultContent": 'Pending',
            orderable: false,
            searchable: false
          },
          {
            data: 'division.division_name',
            name: 'division.division_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'branch.branch_name',
            name: 'branch.branch_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'employee_code',
            name: 'employee_code',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'user.name',
            name: 'user.name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'user.getdesignation.designation_name',
            name: 'user.getdesignation.designation_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'user.mobile',
            name: 'user.mobile',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'user.reportinginfo.name',
            name: 'user.reportinginfo.name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'date_of_joining',
            name: 'date_of_joining',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'notice',
            name: 'notice',
            defaultContent: '',
            orderable: false,
            render: function(data, type, row) {
              return data > 5 ? `${data} Days` : `${data} Month`;
            }
          },
          {
            data: 'last_working_date',
            name: 'last_working_date',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'reason',
            name: 'reason',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'persoanla_email',
            name: 'persoanla_email',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'persoanla_mobile',
            name: 'persoanla_mobile',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#division_id').change(function() {
        table.draw();
      });
      $('#user_id').change(function() {
        table.draw();
      });
      $('#branch_id').change(function() {
        table.draw();
      });
      $('#status').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
    });
  </script>
</x-app-layout>