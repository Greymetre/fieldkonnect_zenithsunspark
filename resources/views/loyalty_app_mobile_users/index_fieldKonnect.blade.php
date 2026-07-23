<x-app-layout>
  <style>
    .multi_login_class {
      cursor: pointer;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">User App Details List
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="post" action="{{ URL::to('user_app_details/login_list/download') }}" class="form-horizontal">
                  @csrf
                  <div class="d-flex flex-row">
                    {{--<div class="p-2" style="width:160px; display: none;">
                      <select class="select2" name="user" id="user" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.sales_users.user_name') !!}</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user->id !!}">{!! $user->first_name !!} {!! $user->last_name  !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>--}}
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      @if(auth()->user()->can(['mobile_app_login_details_download']))
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.mobile_app_login.mobile_app_login_details_download') !!}" name="export_branch" value="true"><i class="material-icons">cloud_download</i></button>
                      @endif
                    </div>
                  </div>
                </form>
                <div class="next-btn">

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
          @if (session('success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            {{ session('success') }}
          </div>
          @endif
          @if (session('error'))
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            {{ session('error') }}
          </div>
          @endif
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getMobileAppLoginUser" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>User Name</th>
                <th>{!! trans('panel.mobile_app_login.mobile_number') !!}</th>
                <th>Branch</th>
                <th>{!! trans('panel.mobile_app_login.app_version') !!}</th>
                <th>{!! trans('panel.mobile_app_login.device_name') !!}</th>
                <th>{!! trans('panel.mobile_app_login.first_login_date') !!}</th>
                <th>{!! trans('panel.mobile_app_login.last_login_date') !!}</th>
                <th>{!! trans('panel.mobile_app_login.login_status') !!}</th>
                <th>Multi Login</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/validation_sales.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getMobileAppLoginUser').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'type': 'POST',
          'url': "{{ url('user_app_details/list') }}",
          'data': function(d) {
            d._token = token,
              d.user_id = $('#user').val(),
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
            data: 'user.name',
            name: 'user.name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'user.mobile',
            name: 'user.mobile',
            orderable: true,
            "defaultContent": ''
          },
          {
            data: 'user.getbranch.branch_name',
            name: 'user.getbranch.branch_name',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'app_version',
            name: 'app_version',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'device_name',
            name: 'device_name',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },

          {
            data: 'first_login_date',
            name: 'first_login_date',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'last_login_date',
            name: 'last_login_date',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'login_status1',
            name: 'login_status1',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'multi_login',
            name: 'multi_login',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          }
        ]
      });

      $('#user').change(function() {
        table.draw();
      });

      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });

      $(document).on('click', '.multi_login_class', function(e) {
        var userId = $(this).attr('data-id');
        var status = $(this).attr('data-multi');
        var msg = 'You want to Remove UUID ?';


        swal.fire({
          title: "Are you sure?",
          text: msg,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, change it!",
          closeOnConfirm: false
        }).then(function(result) {
          if (result.value) {
            console.log(result.value);
            $.ajax({
              url: "{{ url('user_app_details/multi_login') }}",
              type: "POST",
              data: {
                '_token': token,
                'user_id': userId,
                'multi_login': status
              },
              success: function(data) {
                table.draw();
                if (data.status == 'success') {
                  swal.fire("Success!", data.message, "success");
                } else {
                  swal.fire("Error!", 'Please wait for some time, Working on it !!', "error");
                }
              }
            });
          } else {
            swal.fire("Cancelled", "Your imaginary file is safe :)", "error");
          }
        });
      });

    });
  </script>
</x-app-layout>