<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.sales_users.target_users') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['sales_target_users_download']))
                <form method="post" action="{{ URL::to('sales_users/target_users_download/download') }}" class="form-horizontal">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">


                    <div class="p-2" style="width:160px;">
                      <select class="selectpicker" name="branch_id" id="branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.sales_users.branch') !!}</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <select class="select2" name="user" id="user" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.sales_users.user_name') !!}</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user->id !!}">{!! $user->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <select class="selectpicker" name="division" id="division" data-style="select-with-transition" title="{!! trans('panel.sales_users.select-divisions') !!}">
                        <option value="" disabled selected>{!! trans('panel.sales_users.select-divisions') !!}</option>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division->id !!}">{!! $division->division_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <select class="select2" name="type" id="type" data-style="select-with-transition" title="{!! trans('panel.sales_users.type') !!}">
                        <option value="" disabled selected>{!! trans('panel.sales_users.type') !!}</option>
                        <option value="primary">Primary</option>
                        <option value="secondary">Secondary</option>
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <select class="selectpicker" name="month" id="month" data-style="select-with-transition" title="Month">
                        <option value="" disabled selected>{!! trans('panel.sales_users.month') !!}</option>
                        @for ($month = 1; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                          @endfor
                      </select>
                    </div>
                    <div class="p-2" style="width:160px;">
                      <select class="selectpicker" name="financial_year" id="financial_year" required data-style="select-with-transition" title="Year">
                        <option value="" disabled selected>{!! trans('panel.sales_users.year') !!}</option>
                        @foreach($years as $year)
                        @php
                        $startYear = $year - 1;
                        $endYear = $year;
                        @endphp
                        <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="display:flex;">
                      @if(auth()->user()->can(['sales_target_users_download']))
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.sales_target_user.title') !!}" name="export_user" value="true"><i class="material-icons">cloud_download</i></button>
                      @endif
                    </div>
                    <div class="p-2" style="display:flex;">
                      @if(auth()->user()->can(['sales_branch_report_download']))
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.sales_target_branch.title_singular') !!}" name="export_branch" value="true"><i class="material-icons">cloud_download</i></button>
                      @endif
                    </div>

                    <div class="p-2" style="display:flex;">
                      @if(auth()->user()->can(['cy_ly_sales_target_report_download']))
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.cy_and_ly_sales_target.title') !!}" name="cy_ly_sales_report" value="true"><i class="material-icons">cloud_download</i></button>
                      @endif
                    </div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['sales_target_users_upload']))
                  <form action="{{ URL::to('sales_users/target_users_upload/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group" id="collapse-btn" style="flex-wrap: nowrap; gap: 5px;">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File" name="import_file" style="flex-wrap: nowrap; gap: 5px;" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append" style="flex-wrap: nowrap;">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.sales_target_user.title') !!}">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  <!-- sales target user template creation -->
                  @if(auth()->user()->can(['sales_target_users_template']))
                  <a href="{{ URL::to('sales-target-users-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.sales_target_user.title') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif

                  <!-- sales achievemnts download-->
                  @if(auth()->user()->can(['sales_achievement_download']))
                  <button class="btn btn-just-icon btn-theme mr-2" title="{!!  trans('panel.global.download') !!} {!! trans('panel.sales_achievement.title') !!}"><i class="material-icons">cloud_download</i></button>
                  @endif
                  <!-- sales achievemnts upload-->
                  @if(auth()->user()->can(['sales_achievement_upload']))
                  <form action="{{ URL::to('sales/achievement/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group" style="flex-wrap:nowrap;">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File" name="import_file" style="flex-wrap: nowrap;" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.sales_achievement.title') !!}">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  <!-- sales achievemnts template creation-->
                  @if(auth()->user()->can(['sales_achievement_template']))
                  <a href="{{ URL::to('sales-achievement-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.sales_achievement.title') !!}"><i class="material-icons">text_snippet</i></a>
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
            <table id="getSalesTargetUser" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.sales_users.employee_code') !!}</th>
                <th>{!! trans('panel.sales_users.user_name') !!}</th>
                <th>{!! trans('panel.sales_users.designation') !!}</th>
                <th>{!! trans('panel.sales_users.branch_name') !!}</th>
                <th>{!! trans('panel.sales_users.sales_type') !!}</th>
                <th>{!! trans('panel.sales_users.month') !!}</th>
                <th>{!! trans('panel.sales_users.year') !!}</th>
                <th>{!! trans('panel.sales_users.target') !!}</th>
                <th>{!! trans('panel.sales_users.achievement') !!}</th>
                <th>{!! trans('panel.sales_users.achievement_percent') !!}</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade bd-example-modal-lg" id="editSalesTargetUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.edit') !!} </span> {!! trans('panel.sales_users.target_users') !!}
            <span class="pull-right">
              <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{ route('sales-target-users.store') }}" enctype="multipart/form-data" id="updateSalesTargetForm">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.sales_target_user.fields.user') !!} <span class="text-danger"> *</span></label>
                
                    <div class="form-group has-default bmd-form-group">
                      <select class="select2" name="user_id" id="user_id" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="form-control" disabled selected>{!! trans('panel.sales_users.target_users') !!}</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user->id !!}">{!! $user->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                      @if ($errors->has('user_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('user_id') }}</p>
                      </div>
                      @endif
                    </div>
                </div>
              </div>
  <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.sales_target_user.fields.type') !!} <span class="text-danger"> *</span></label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="type" id="type" class="form-control" value="{!! old('type') !!}" readonly>
                      @if ($errors->has('type'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('type') }}</p>
                      </div>
                      @endif
                    </div>
               
                </div>
              </div>
      <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.sales_target_user.fields.month') !!} <span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control" name="month" id="month" title="{!! trans('panel.sales_target_user.fields.month') !!}">
                        <option value="" disabled selected>{!! trans('panel.sales_users.month') !!}</option>
                        @for ($month = 1; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                          @endfor
                      </select>
                      @if ($errors->has('month'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('month') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
            
              </div>
  <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.sales_target_user.fields.year') !!} <span class="text-danger"> *</span></label>
               
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control" name="year" id="year" title="{!! trans('panel.sales_target_user.fields.year') !!}">
                        <option value="" disabled selected>{!! trans('panel.sales_users.year') !!}</option>
                        @foreach($years as $year)
                        <option value="{!! $year !!}">{!! $year !!}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('year'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('year') }}</p>
                      </div>
                      @endif
                </div>
              </div>
            </div>
                <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.sales_target_user.fields.target') !!} <span class="text-danger"> *</span></label>
                  
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="target" id="target" class="form-control" value="{!! old( 'target') !!}" maxlength="200">
                      @if ($errors->has('target'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('target') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
              <input type="hidden" name="id" id="sales_target_id" />
              <button class="btn btn-info save"> {!! trans('panel.sales_target_user.fields.update') !!}</button>
          </form>
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
      var table = $('#getSalesTargetUser').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'type': 'POST',
          'url': "{{ url('sales_users/target_users/list') }}",
          'data': function(d) {
            d._token = token,
              d.user_id = $('#user').val(),
              d.branch_id = $('#branch_id').val(),
              d.division = $('#division').val(),
              d.type = $('#type').val(),
              d.month = $('#month').val(),
              d.year = $('#financial_year').val()
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
            "defaultContent": ''
          },
          {
            data: 'user.employee_codes',
            name: 'employee_code',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'user.name',
            name: 'name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'user.getdesignation.designation_name',
            name: 'designation',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'branch.branch_name',
            name: 'branch',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'type',
            name: 'type',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'month',
            name: 'month',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'year',
            name: 'year',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'target',
            name: 'target',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'achievement',
            name: 'achievement',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'achievement_percent',
            name: 'achievement_percent',
            "defaultContent": ''
          },

        ]
      });


      $('#branch_id').change(function() {
        table.draw();
      });

      $('#user').change(function() {
        table.draw();
      });

      $('#division').change(function() {
        table.draw();
      });

      $('#type').change(function() {
        table.draw();
      });

      $('#month').change(function() {
        table.draw();
      });

      $('#financial_year').change(function() {
        table.draw();
      });

      // edit sales target user
      $(document).on('click', '.edit', function() {
        var base_url = $('.baseurl').data('baseurl');
        var id = $(this).attr('id');
        $.ajax({
          url: base_url + '/sales-target-users/' + id,
          dataType: "json",
          success: function(data) {
            $('#user_id').val(data.user_id);
            $('#month').val(data.month);
            $('#year').val(data.year);
            $('#target').val(data.target);
            $('#type').val(data.type);
            $('#sales_target_id').val(data.id);
            
            $('#month option[value="' + data.month + '"]').prop('selected', true);
            $('#year option[value="' + data.year + '"]').prop('selected', true);
            $('#user_id').trigger('change');
            
            $('#action_button').val('Edit');
            $('#editSalesTargetUser').modal('show');
            $('.select2').select2();
          }
        })
      });

      // delete sales target user
      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('sales_users/target_user/delete') }}",
          type: 'GET',
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
            table.draw();
          },
        });
      });
    });
  </script>
</x-app-layout>