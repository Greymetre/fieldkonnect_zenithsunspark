<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.user.title_singular') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
              @if(auth()->user()->can(['user_download']))
                <form action="{{ URL::to('users-download') }}" method="get" enctype="multipart/form-data">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:200px;">
                      <div class="dropdown bootstrap-select">
                        <select class="select2" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                          <option class="bs-title-option" value="">Division</option>
                          @foreach($divisions as $division)
                          <option value="{{$division->id}}">{{$division->division_name}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <div class="dropdown bootstrap-select">
                        <select class="select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                          <option class="bs-title-option" value="">Branch</option>
                          @foreach($branches as $branche)
                          <option value="{{$branche->id}}">{{$branche->branch_name}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <div class="dropdown bootstrap-select">
                        <select class="select2" name="department_id" id="department_id" data-style="select-with-transition" title="Select Department">
                          <option class="bs-title-option" value="">department</option>
                          @foreach($departments as $department)
                          <option value="{{$department->id}}">{{$department->name}}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <div class="dropdown bootstrap-select">
                        <select class="select2" name="user_type" id="user_type" data-style="select-with-transition" title="Select User type">
                          <option class="bs-title-option" value="employee">Employee</option>
                          <option class="bs-title-option" value="customer">Customer</option>
                        </select>
                      </div>
                    </div>
                    <div class="p-2" style="width: 250px;">
                      <select class="selectpicker" name="active" id="active" data-style="select-with-transition" title="Select User Status">
                        <option value="">Select User Status</option>
                        <option value="Y">Active</option>
                        <option value="N">Inactive</option>
                      </select>
                    </div>
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" title="Download Customers"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
              @endif
              </div>
              <div class="next-btn">
                @if(auth()->user()->can(['user_upload']))
                <form action="{{ URL::to('users-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                      <span class="btn btn-just-icon btn-theme btn-file">
                        <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden">
                        <input type="file" name="import_file" required accept=".xls,.xlsx" />
                      </span>
                    </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.user.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif

                @if(auth()->user()->can(['user_download']))
                <!-- <a href="{{ URL::to('users-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.user.title') !!}"><i class="material-icons">cloud_download</i></a> -->
                @endif
                @if(auth()->user()->can(['user_template']))
                <a href="{{ URL::to('users-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.user.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                @endif
                @if(auth()->user()->can(['user_create']))
                <a href="{{ route('users.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.user.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
        <div class="table-responsive">
          <table id="getuser" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.global.active') !!}</th>
              <th>Emp Code</th>
              <th>{!! trans('panel.global.name') !!}</th>
              <th>{!! trans('panel.sidemenu.branches') !!}</th>
              <th>Designation</th>
              <th>Division</th>
              <th>Whats App</th>
              <th>{!! trans('panel.global.mobile') !!}</th>
              <th>{!! trans('panel.global.email') !!}</th>
              <th>Reporting Person</th>
              <th>Joining Date</th>
              <th>Roles</th>
              <th>Password</th>
              <th>{!! trans('panel.global.profile') !!}</th>
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
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getuser').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          url: "{{ route('users.index') }}",
          data: function (d) {
                d.user_type = $('#user_type').val()
                d.active = $('#active').val()
                d.division_id = $('#division_id').val()
                d.branch_id = $('#branch_id').val()
                d.department_id = $('#department_id').val()
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
            data: 'active',
            name: 'active',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'employee_codes',
            name: 'employee_codes',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'name',
            name: 'name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'getBranchNames',
            name: 'getBranchNames',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'getdesignation.designation_name',
            name: 'getdesignation.designation_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'getdivision.division_name',
            name: 'getdivision.division_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'wahtsappmobile',
            name: 'wahtsappmobile',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'mobile',
            name: 'mobile',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'email',
            name: 'email',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'reportinginfo.name',
            name: 'reportinginfo.name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'userinfo.date_of_joining',
            name: 'userinfo.date_of_joining',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'roles',
            name: 'roles',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'password_string',
            name: 'password_string',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'image',
            name: 'image',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#user_type').change(function(){
        table.draw();
      });
      $('#active').change(function(){
        table.draw();
      });
      $('#division_id').change(function(){
        table.draw();
      });
      $('#branch_id').change(function(){
        table.draw();
      });
      $('#department_id').change(function(){
        table.draw();
      });

      $(document).on('click', '.edit', function() {
        var base_url = $('.baseurl').data('baseurl');
        var id = $(this).attr('id');
        $.ajax({
          url: base_url + '/users/' + id,
          dataType: "json",
          success: function(data) {
            console.log(data);
            $('#user_name').val(data.user_name);
            if (data.user_image) {
              var image = data.user_image;
            } else {
              var image = "{!! asset('assets/img/placeholder.jpg') !!}";
            }
            $("#user_image").attr({
              "src": image
            });
            $('#user_id').val(data.id);
            var title = '{!! trans('panel.global.edit ') !!}';
            $('.modal-title').text(title);
            $('#action_button').val('Edit');
            $('#createuser').modal('show');
          }
        })
      });

      $('body').on('click', '.activeRecord', function() {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if (active == 'Y') {
          status = 'Incative ?';
        } else {
          status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want " + status)) {
          return false;
        }
        $.ajax({
          url: "{{ url('users-active') }}",
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
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });

      $('.create').click(function() {
        $('#user_id').val('');
        $('#createuserForm').trigger("reset");
        $("#user_image").attr({"src": '{!! asset('assets / img / placeholder.jpg ') !!}'});
        $('.modal-title').text('{!! trans('panel.global.add ') !!}');
      });

      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('users') }}" + '/' + id,
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
            table.draw();
          },
        });
      });

    });
  </script>
</x-app-layout>