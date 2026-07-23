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
          <h4 class="card-title ">End User {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['end_user_download']))
                <form method="POST" action="{{ URL::to('end-user/download') }}" class="form-horizontal">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:160px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} End User"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <!-- </div> -->
                <!-- <button class="btn btn-just-icon btn-theme" type="button" data-toggle="collapse" data-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2"><i class="material-icons">menu</i></button> -->
                <!-- <div class="row">
                <div class="col"> -->
                <!-- <div class="collapse multi-collapse" id="multiCollapseExample2">
                    <div class="d-flex" style="font-size: 14px;align-items: center;justify-content: space-between;"> -->
                @if(auth()->user()->can(['transaction_history_upload']))
                <!-- <p>Upload Manual Transaction</p>
                      <form action="{{ URL::to('transaction_history_upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="d-flex">
                          <div class="fileinput-new text-center" data-provides="fileinput">
                            <span class="btn btn-just-icon btn-theme btn-file">
                              <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="hidden">
                              <input type="file" name="import_file" required accept=".xls,.xlsx" />
                            </span>
                          </div>
                          <div class="input-group-append">
                            <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Manual Transaction">
                              <i class="material-icons">cloud_upload</i>
                              <div class="ripple-container"></div>
                            </button>
                          </div>
                        </div>
                      </form> -->
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['transaction_history_template']))
                  <!-- <p>{!!  trans('panel.global.template') !!} Manual Transaction</p>
                      <a href="{{ URL::to('transaction_history_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Manual Transaction"><i class="material-icons">text_snippet</i></a> -->
                  @endif
                  @if(auth()->user()->can(['end_user_create']))
                  <!-- <p>{!!  trans('panel.global.add') !!} Transaction</p> -->
                  <!-- <a href="{{ route('damage_entries.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Damage Entry"><i class="material-icons">add_circle</i></a> -->
                  <!-- <p>{!!  trans('panel.global.add') !!} Manual Transaction</p> -->
                  <a href="{{ route('end_user.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} End User"><i class="material-icons">add_circle</i></a>
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
            <table id="getDamageEntries" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Action</th>
                <th>Status</th>
                <th>Customer Name</th>
                <th>Mobile Number</th>
                <th>Email</th>
                <th>Address</th>
                <th>State</th>
                <th>District</th>
                <th>City</th>
                <th>Pin Code</th>
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
          'url': "{{ route('end_user.index') }}",
          'data': function(d) {
            d.status = $('#status').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.scheme_name = $('#scheme_name').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
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
            data: 'status',
            name: 'status',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'customer_name',
            name: 'customer_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'customer_number',
            name: 'customer_number',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'customer_email',
            name: 'customer_email',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'customer_address',
            name: 'customer_address',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'state.state_name',
            name: 'state.state_name',
            "defaultContent": '',
            orderable: false,
            render: function(data, type, row) {
              return data && data.trim() !== '' ? data : row.customer_state;
            }
          },
          {
            data: 'district.district_name',
            name: 'district.district_name',
            "defaultContent": '',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
              return data && data.trim() !== '' ? data : row.customer_district;
            }
          },
          {
            data: 'city.city_name',
            name: 'city.city_name',
            "defaultContent": '',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
              return data && data.trim() !== '' ? data : row.customer_city;
            }
          },
          {
            data: 'pincodeDetails.pincode',
            name: 'pincodeDetails.pincode',
            "defaultContent": '',
            orderable: false,
            searchable: false
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
    });

    $('body').on('click', '.activeRecord', function() {
      var $this = $(this);
      var id = $(this).attr("id");
      var active = $(this).attr("value");
      var status = '';
      if (active == '1') {
        status = 'Incative ?';
      } else {
        status = 'Ative ?';
      }
      var token = $("meta[name='csrf-token']").attr("content");
      if (!confirm("Are You sure want " + status)) {
        return false;
      }
      $.ajax({
        url: "{{ url('end-users-active') }}",
        type: 'POST',
        data: {
          _token: token,
          id: id,
          active: active
        },
        success: function(data) {
          $('.message').empty();
          $('.alert').show();
          if (data.status == 'success') {
            var newValue = active == '1' ? '0' : '1';
            $this.val(newValue);
            $('.alert').addClass("alert-success");
          } else {
            $('.alert').addClass("alert-danger");
          }
          $('.message').append(data.message);
          table.draw();
        },
      });
    });
  </script>
</x-app-layout>