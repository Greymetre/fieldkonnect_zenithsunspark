<x-app-layout>
  <style>
    a.custom-btn.create {
      background-color: #00aadb !important;
      font-size: 12px;
      padding: 13px 7px;
      font-weight: 500;
      border-radius: 5px;
      color: #000 !important;
      margin: 2px;
      text-align: center;
      line-height: normal;
      cursor: pointer;
      height: 42px;
      margin-top: 7px;
    }

    /* div#submitAttendance{
      z-index: 9;
    }*/
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Attendance Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['attendance_download']))
                <form method="GET" action="{{ URL::to('attendance-download') }}">
                  <div class="d-flex flex-row">

                    <div class="p-2" style="width: 250px;">
                      <select class="selectpicker" multiple name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branche)
                        <option value="{!! $branche['id'] !!}" {{ old( 'branch_id') == $branche['id'] ? 'selected' : '' }}>{!! $branche['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <div class="p-2" style="width: 250px;">
                    <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                     <option value="">Select Division</option>
                    @if(@isset($divisions ))
                    @foreach($divisions as $division)
                     <option value="{!! $division['id'] !!}" {{ old( 'division') == $division['id'] ? 'selected' : '' }}>{!! $division['division_name'] !!}</option>
                    @endforeach
                    @endif
                   </select>
                  </div>

                    <div class="p-2" style="width: 250px;">
                      <select class="select2" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                        <option value="">Select User</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>

                    <div class="p-2" style="width: 250px;">
                      <select class="select2" name="status" id="status" data-style="select-with-transition" title="Select User">
                        <option value="">Select Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                      </select>
                    </div>

                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Attendance">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                  <div class="btn-group multi-a-r d-none">
                    <button class="btn btn-success btn-sm multiChange mr-1" data-status="1"  title="Approve">Approve</button>
                    <button class="btn btn-danger btn-sm multiChange mr-2" data-status="2" title="Reject">Reject</button>
                  </div>
                  @if(auth()->user()->can(['attendance_create']))

                  <a data-toggle="modal" data-target="#submitAttendance" class="custom-btn create" title="Punch In">
                    Punch In
                  </a>
                  @endif
                  <a href="{{ URL::to('attendance-location') }}" class="btn btn-just-icon btn-theme  d-none" title="Update Location"><i class="material-icons">add_location</i></a>
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>No</th>
                <th>#</th>
                <th>User ID</th>
                <th>Status</th>
                <th>User Name</th>
                <th>Punch in Date</th>
                <th>Punch In Time</th>
                <th>Punch In Address</th>
                <th>Punch Out Time</th>
                <th>Punch Out Address</th>
                <th>Working Time</th>
                <th>Punch In summary</th>
                <th>Working Type</th>
                <th>Attendance Status</th>
                <th>Remark</th>
                <th>Action</th>
                <th>From</th>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade bd-example-modal-lg" id="submitAttendance" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">
            <span class="modal-title">Submit </span> Attendance <span class="pull-right">
              <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                <i class="material-icons">clear</i>
              </a>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('submitAttendances') }}" enctype="multipart/form-data" id="createleadstagesForm"> @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">User</label>
                  <select class="form-control select2" name="user_id" id="user_id" style="width: 100%;" required>
                    <option value="">Select User</option>
                    @if(@isset($users))
                    @foreach($users as $user)
                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Punch In</label>
                  <input type="text" name="punchin_date" id="punchin_date" class="form-control datetimepicker" value="{!! old( 'punchin_date') !!}" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Tour Plan</label>
                  <input type="text" readonly name="tour_name" id="tour_name" class="form-control" value="{!! old( 'tour_name') !!}" required>
                  <input type="hidden" readonly name="tourid" id="tourid" class="form-control" value="{!! old( 'tourid') !!}" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Working Type</label>
                  <select class="form-control " name="working_type" id="working_type" style="width: 100%;" required>
                    <option value="">Select Working Type</option>
                    <option value="Tour" data-is-city="true">Tour</option>
                    <option value="Office Work" data-is-city="true">Office Work</option>
                    <option value="Plumber Meet" data-is-city="true">Plumber Meet</option>
                    <option value="Service Center Visit" data-is-city="true">Service Center Visit</option>
                    <option value="Local Market Visit" data-is-city="true">Local Market Visit</option>
                    <option value="Retailer Meet" data-is-city="true">Retailer Meet</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6" id="city_div" style="display: none;">
                <label class="col-form-label">Select City</label>
                <select class="form-control select2" name="city" id="city">

                </select>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Punch In Summary</label>
                  <input type="text" name="punchin_summary" id="punchin_summary" class="form-control" value="{!! old( 'punchin_summary') !!}">
                </div>
              </div>
              <span id="tour_error" class="alert alert-danger d-none">No tour added for selected date</span>
              <span id="date_error" class="alert alert-danger d-none">You can punch in only today date.</span>
            </div>
            <button id="add_attend" class="btn btn-info save pull-right mt-2"> Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- new model for reject attendance -->


  <div class="modal fade bd-example-modal-lg" id="rejec_attendance" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">
            <span class="modal-title">Submit </span> Remark <span class="pull-right">
              <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                <i class="material-icons">clear</i>
              </a>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('rejectAttendance') }}" enctype="multipart/form-data" id="createleadstagesForm_new"> @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Remark</label>
                  <input type="text" name="remark_status" id="remark_status" class="form-control" value="{!! old( 'remark_status') !!}" required> <br><br>
                  <input type="text" name="attendance_id" id="attendance_id" class="form-control" hidden>

                </div>
              </div>
            </div>
            <button class="btn btn-info save"> Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- end model for attendance -->




  <script type="text/javascript">
    $(document).ready(function() {

      //new user filters  starts
      var isSuperAdmin = @json(Auth::user()->hasRole('superadmin'));

      console.log(isSuperAdmin);

      var columns = [
        {
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false
        },
        { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
        {
          data: 'user_id',
          name: 'user_id',
          "defaultContent": ''
        },
        {
          data: 'action_status',
          name: 'action_status',
          "defaultContent": '',
          className: 'td-actions text-center',
          orderable: false,
          searchable: false
        },
        {
          data: 'users.name',
          name: 'users.name',
          "defaultContent": '',
          orderable: false
        },
        {
          data: 'punchin_date',
          name: 'punchin_date',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'punchin_time',
          name: 'punchin_time',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'punchin_address',
          name: 'punchin_address',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'punchout_time',
          name: 'punchout_time',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'punchout_address',
          name: 'punchout_address',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'worked_time',
          name: 'worked_time',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'punchin_summary',
          name: 'punchin_summary',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'working_type',
          name: 'working_type',
          "defaultContent": '',
          orderable: false
        },
        {
          data: 'current_status',
          name: 'current_status',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'remark_status',
          name: 'remark_status',
          "defaultContent": '',
          orderable: false,
          searchable: false
        },
        {
          data: 'action',
          name: 'action',
          "defaultContent": '',
          className: 'td-actions text-center',
          orderable: false,
          searchable: false
        },
      ];

      if (isSuperAdmin) {
        columns.push({
          data: 'punchin_from',
          name: 'punchin_from',
          defaultContent: '',
          className: 'td-actions text-center',
          orderable: false,
          searchable: false
        });
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getattendance').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "retrieve": true,
        ajax: {
          url: "{{url('reports/attendancereport')}}",
          data: function(d) {
            d.division_id = $('#division_id').val(),
            d.executive_id = $('#executive_id').val(),
              d.active = $('#active').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val(),
              d.status = $('#status').val()
          }
        },
        columns: columns

      });

      $('#division_id').change(function() {
        table.draw();
      });
      $('#executive_id').change(function() {
        table.draw();
      });
      $('#active').change(function() {
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


      //new user filters end



      $('body').on('click', '.removePunchout', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to remove Punchout")) {
          return false;
        }
        $.ajax({
          url: "{{ url('removePunchout') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            //oTable.draw();
            table.draw();
          },
        });
      });

      $('body').on('click', '.punchoutnow', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want Punchout Now?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('punchoutnow') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            //oTable.draw();
            table.draw();
          },
        });
      });

      $('body').on('click', '.deleteAttendance', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('attendances') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            //oTable.draw();
            table.draw();
          },
        });
      });

      $('body').on('click', '.multiChange', function () {
      const selectedValues = [];
        $('.row-checkbox:checked').each(function () {
            selectedValues.push($(this).val());
        });
        if(selectedValues.length == 0){
          alert("Please select at least one record");
          return false;
        }
        const status = $(this).data('status');

        var token = $("meta[name='csrf-token']").attr("content");
        if(status == 1){
          if(!confirm("Are You sure want to approve "+selectedValues.length+" attaendance?")) {
             return false;
          }
          $.ajax({
            url: "{{ url('approveAttendance')}}",
            type: 'POST',
            data: {
              _token: token,
              id: selectedValues.toString()
            },
            success: function(data) {
              table.draw();
              $('.message').empty();
              $('.alert').show();
              if (data.status == 'success') {
                $('.alert').addClass("alert-success");
              } else {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
            },
          });
        }else{
          if(!confirm("Are You sure want to reject "+selectedValues.length+" attaendance?")) {
             return false;
          }
          $('#attendance_id').val(selectedValues);
          $("#rejec_attendance").modal();
        }        
    });

      //approve
      $('body').on('click', '.approve_status', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want Approve Attendance")) {
          return false;
        }

        $.ajax({
          url: "{{ url('approveAttendance')}}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            console.log(data);
            table.draw();
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
          },
        });
      });


      $('body').on('click', '.reject_status', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want reject Attendance")) {
          return false;
        } else {
          $('#attendance_id').val(id);
          $("#rejec_attendance").modal();
        }

      });



    });

    $("#branch_id").on('change', function() {
      var search_branches = $(this).val();
      $.ajax({
        url: "{{ url('reports/attendancereport') }}",
        data: {
          "search_branches": search_branches
        },
        success: function(res) {
          if (res.status == true) {
            var select = $('#executive_id');
            select.empty();
            select.append('<option>Select User</option>');
            $.each(res.users, function(k, v) {
              select.append('<option value="' + v.id + '" >' + v.name + '</option>');
            });
            select.selectpicker('refresh');
          }
        }
      });

    })

    $(document).on("dp.change", "#punchin_date", function(e) {
      var formatedValue = moment(e.date).format('YYYY-MM-DD');
      var todayDate = moment().format('YYYY-MM-DD');

      var user_id = $("#user_id").val();
      if (user_id && user_id != null && user_id != '') {
        if (user_id == '{{auth()->user()->id}}') {
          if (formatedValue === todayDate) {
            $("#date_error").addClass('d-none');
            $("#add_attend").prop('disabled', false);
          } else {
            $("#date_error").removeClass('d-none');
            $("#add_attend").prop('disabled', true);
            return false;
          }
        }
        var tour_plan = getTourPlanByUserAndDate(formatedValue, user_id);
      }
    });

    $(document).on("change", "#user_id", function(e) {
      var selectedDate = $('#punchin_date').val();
      if (selectedDate && selectedDate != null && selectedDate != '') {
        var formatedValue = moment(selectedDate).format('YYYY-MM-DD');
        console.log(formatedValue);
        var todayDate = moment().format('YYYY-MM-DD');

        var user_id = $(this).val();
        if (user_id == '{{auth()->user()->id}}') {
          if (formatedValue === todayDate) {
            $("#date_error").addClass('d-none');
            $("#add_attend").prop('disabled', false);
          } else {
            $("#date_error").removeClass('d-none');
            $("#add_attend").prop('disabled', true);
            return false;
          }
        }
        var tour_plan = getTourPlanByUserAndDate(formatedValue, user_id);
      }
    });

    $("#working_type").on("change", function() {
      var selectedOption = $(this).find('option:selected');
      var is_city = selectedOption.data('is-city');

      if (is_city == true) {
        var user_id = $("#user_id").val();
        if (user_id && user_id != null && user_id != '') {
          $.ajax({
            url: "{{ url('userCityList') }}",
            dataType: "json",
            type: "POST",
            data: {
              _token: "{{csrf_token()}}",
              user_id: user_id
            },
            success: function(res) {
              if (res.status == 'success') {
                $("#city_div").show();
                var html = '<option value="">Select City</option>';
                $.each(res.data, function(index, element) {
                  console.log(element);
                  html += '<option value="' + element.id + '">' + element.city_name + '</option>';
                });
                $("#city").html(html);
              }

            }
          });
        }
      }
    });


    function getTourPlanByUserAndDate(date, user_id) {
      $.ajax({
        url: "{{ url('getTourPlanByUserAndDate') }}",
        dataType: "json",
        type: "POST",
        data: {
          _token: "{{csrf_token()}}",
          date: date,
          user_id: user_id
        },
        success: function(res) {
          if (res.status == true) {
            $("#tour_error").addClass("d-none");
            $("#add_attend").prop('disabled', false);
            $("#tour_name").val(res.data.town);
            $("#tourid").val(res.data.id);
          } else {
            $("#tour_error").removeClass("d-none");
            $("#add_attend").prop('disabled', true);
          }
        }
      });
    }

    $(document).on('click', '.row-checkbox', function () {
        
        const selectedValues = [];
        $('.row-checkbox:checked').each(function () {
            selectedValues.push($(this).val());
        });

        if(selectedValues.length > 0){
          $(".multi-a-r").removeClass('d-none');
        }else{
          $(".multi-a-r").addClass('d-none');
        }
    });

    $("#checkAll").on("click", function () {
    $('input:checkbox').not(this).prop('checked', this.checked);
    $(".multi-a-r").toggleClass('d-none');
});


  </script>
</x-app-layout>