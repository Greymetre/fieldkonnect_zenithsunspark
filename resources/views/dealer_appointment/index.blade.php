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
          <h4 class="card-title ">Dealer / Distributor Appointment {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['dealer_appointment_download']))
                <form method="POST" action="{{ URL::to('/dealer-appointment/download') }}" class="form-horizontal">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:160px;">
                      <div class="form-group">
                        <!-- <label class="bmd-label-floating">Status</label> -->
                        <select class="form-control select2" name="status_id" id="status_id" style="width: 100%;">
                          <option value="">Select Status</option>
                          <option value="0">Pending</option>
                          <option value="1">Approved By Sales Team</option>
                          <option value="2">Approved By Account</option>
                          <option value="3">Approved By HO</option>
                          <option value="4">Rejected</option>
                        </select>
                        @if ($errors->has('status_id'))
                        <div class="error col-lg-12">
                          <p class="text-danger">{{ $errors->first('status_id') }}</p>
                        </div>
                        @endif
                      </div>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <div class="form-group">
                        <!-- <label class="bmd-label-floating">Status</label> -->
                        <select class="form-control select2" name="division_id" id="division_id" style="width: 100%;">
                          <option value="">Select Division</option>
                          @foreach($divisions as $division)
                          <option value="{{$division}}">{{$division}}</option>
                          @endforeach                          
                        </select>
                        @if ($errors->has('division_id'))
                        <div class="error col-lg-12">
                          <p class="text-danger">{{ $errors->first('division_id') }}</p>
                        </div>
                        @endif
                      </div>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <div class="form-group">
                        <!-- <label class="bmd-label-floating">Status</label> -->
                        <select class="form-control select2" name="branch_id" id="branch_id" style="width: 100%;">
                          <option value="">Select Branch</option>
                          @foreach($branches as $branch)
                          <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                          @endforeach                          
                        </select>
                        @if ($errors->has('branch_id'))
                        <div class="error col-lg-12">
                          <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                        </div>
                        @endif
                      </div>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <div class="form-group">
                        <!-- <label class="bmd-label-floating">Status</label> -->
                        <select class="form-control select2" name="created_by" id="created_by" style="width: 100%;">
                          <option value="">Select Created By</option>
                          @foreach($users as $user)
                          <option value="{{$user->id}}">{{$user->name}}</option>
                          @endforeach                          
                        </select>
                        @if ($errors->has('created_by'))
                        <div class="error col-lg-12">
                          <p class="text-danger">{{ $errors->first('created_by') }}</p>
                        </div>
                        @endif
                      </div>
                    </div>

                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Dealer Appointment"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif

                <div class="next-btn">
                  <a class="btn btn-just-icon btn-theme" href="{{url('/dealer-appointment-form')}}" title="{!!  trans('panel.global.add') !!} Dealer Appointment"><i class="material-icons">add</i></a>
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
            <table id="getDamageEntries" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Action</th>
                <th>Certificate</th>
                <th>Created By</th>
                <th>Appointment {!! trans('panel.expenses.fields.date') !!}</th>
                <th>Branch</th>
                <th>City</th>
                <th>Firm Name</th>
                <th>Customer Type</th>
                <th>Division</th>
                <th>BP code</th>
                <th>Status</th>
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
          'url': "{{ route('dealer-appointment') }}",
          'data': function(d) {
            d.status = $('#status').val(),
              d.status_id = $('#status_id').val(),
              d.division_id = $('#division_id').val(),
              d.branch_id = $('#branch_id').val(),
              d.created_by = $('#created_by').val(),
              d.startdate = $('#start_date').val(),
              d.enddate = $('#end_date').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'certificate',
            name: 'certificate',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'createdbyname.name',
            name: 'createdbyname.name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'appointment_date',
            name: 'appointment_date',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'branch_details.branch_name',
            name: 'branch_details.branch_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'city_details.city_name',
            name: 'city_details.city_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'firm_name',
            name: 'firm_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'customertype',
            name: 'customertype',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'division',
            name: 'division',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'appointment_kyc_detail.dealer_code',
            name: 'appointment_kyc_detail.dealer_code',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'approval_status',
            name: 'approval_status',
            "defaultContent": '',
            orderable: false
          },
        ]
      });
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
        $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('#status_id').change(function() {
        table.draw();
      });
      $('#division_id').change(function() {
        table.draw();
      });
      $('#branch_id').change(function() {
        table.draw();
      });
      $('#created_by').change(function() {
        table.draw();
      });
    });

    $(document).ready(function() {
      $.ajax({
        url: "{{ url('getDistrict') }}",
        dataType: "json",
        type: "POST",
        data: {
          _token: "{{csrf_token()}}"
        },
        success: function(res) {
          var options = '<option value="">Select District</option>';
          $.each(res, function(key, val) {
            options += '<option value="' + val.id + '">' + val.district_name + '</option>';
          })
          $("#district_id").html(options);
        }
      });

      $.ajax({
        url: "{{ url('getCity') }}",
        dataType: "json",
        type: "POST",
        data: {
          _token: "{{csrf_token()}}"
        },
        success: function(res) {
          var options = '<option value="">Select City</option>';
          $.each(res, function(key, val) {
            options += '<option value="' + val.id + '">' + val.city_name + '</option>';
          })
          $("#city_id").html(options);
        }
      });
    });

    function confirmDeletion() {
      return confirm('Are you sure you want to delete this appointment?');
    }
  </script>
</x-app-layout>