<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">{!! trans('panel.customers.title') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <button class="btn btn-info mb-3 float-right" type="button" data-toggle="collapse" data-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
              <i class="material-icons">tune</i> Filters
              </button>
              <div class="collapse" id="filterSection">
                @if(auth()->user()->can(['customer_download']))
                <form method="GET" action="{{ URL::to('customers-download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    @if(!isCustomerUser())
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                        <option value="">Select Division</option>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division['id'] !!}" {{ old( 'division_id') == $division->id ? 'selected' : '' }}>{!! $division['division_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                        <option value="">Select User</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;">
                      <select class="selectpicker" multiple name="branch_id[]" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch['id'] !!}">{!! $branch['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @endif
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="state_id" id="state_id" data-style="select-with-transition" title="Select State">
                        <option value="">Select State</option>
                        @if(@isset($states ))
                        @foreach($states as $state)
                        <option value="{!! $state->id !!}" {{ old( 'state_id') == $state->id ? 'selected' : '' }}>{!! $state->state_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="city_id" id="city_id" data-style="select-with-transition" title="Select City">
                        <option value="">Select City</option>
                        @if(@isset($cities ))
                        @foreach($cities as $city)
                        <option value="{!! $city->id !!}" {{ old( 'city_id') == $city->id ? 'selected' : '' }}>{!! $city->city_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    @if(!isCustomerUser())
                    <div class="p-2" style="width:200px;">
                      <select name="parent_id" id="parent_id" class="select2 form-control"></select>
                    </div>
                    @endif
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="customertype" id="customertype" data-style="select-with-transition" title="Customer Type">
                        <option value="">Select Customer Type</option>
                        @if(@isset($customertype ))
                        @foreach($customertype as $type)
                        <option value="{!! $type->id !!}" {{ old( 'customertype') == $type->id ? 'selected' : '' }}>{!! $type->customertype_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="created_by" id="created_by" data-style="select-with-transition" title="Created By">
                        <option value="">Select Created By</option>
                        <option value="other">Others</option>
                        <option value="self">Self</option>
                      </select>
                    </div>

                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="active" id="active" data-style="select-with-transition" title="Status">
                        <option value="">Select Status</option>
                        <option value="Y">Active</option>
                        <option value="N">Inactive</option>
                      </select>
                    </div>

                    <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.customers.title') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                  @if(auth()->user()->can(['customer_upload']))
                  <form action="{{ URL::to('customers-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.customers.title') !!}">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['customer_template']))
                  <a href="{{ URL::to('customers-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.customers.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['customer_create']))
                  <a href="{{ route('customers.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.customers.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
          <!-- <div class="row">
        <div class="col col1">
            <label class="bmd-label-floating">User</label>
            <div class="form-group has-default bmd-form-group">
              <select class="form-control" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                 <option value="">Select User</option>
                @if(@isset($users ))
                @foreach($users as $user)
                 <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                @endforeach
                @endif
              </select>
            </div>
          </div>


          <div class="col col2">
            <label class="bmd-label-floating">Branch</label>
            <div class="form-group has-default bmd-form-group">
              <select class="selectpicker" multiple name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                 <option value="">Select Branch</option>
                @if(@isset($branches ))
                @foreach($branches as $branch)
                  <option value="{!! $branch['id'] !!}">{!! $branch['name'] !!}</option>
                @endforeach
                @endif
              </select>
            </div>
          </div>


          <div class="col col3">
            <label class="bmd-label-floating">State</label>
            <div class="form-group has-default bmd-form-group">
              <select class="form-control" name="state_id" id="state_id" data-style="select-with-transition" title="Select State">
                 <option value="">Select State</option>
                @if(@isset($states ))
                @foreach($states as $state)
                 <option value="{!! $state->id !!}" {{ old( 'state_id') == $state->id ? 'selected' : '' }}>{!! $state->state_name !!}</option>
                @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="col col4">
            <label class="bmd-label-floating">City</label>
            <div class="form-group has-default bmd-form-group">
              <select class="form-control" name="city_id" id="city_id" data-style="select-with-transition" title="Select City">
                 <option value="">Select City</option>
                @if(@isset($cities ))
                @foreach($cities as $city)
                 <option value="{!! $city->id !!}" {{ old( 'city_id') == $city->id ? 'selected' : '' }}>{!! $city->city_name !!}</option>
                @endforeach
                @endif
              </select>
            </div>
          </div>
          <div class="col col4">
            <label class="bmd-label-floating">Customer Type</label>
            <div class="form-group has-default bmd-form-group">
              <select class="form-control" name="customertype" id="customertype" data-style="select-with-transition" title="Select Customer Type">
                 <option value="">Select Customer Type</option>
                @if(@isset($customertype ))
                @foreach($customertype as $type)
                 <option value="{!! $type->id !!}" {{ old( 'customertype') == $type->id ? 'selected' : '' }}>{!! $type->customertype_name !!}</option>
                @endforeach
                @endif
              </select>
            </div>
          </div>

        </div>-->
          <div class="table-responsive">
            <table id="getcustomers" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <!-- <th>{!! trans('panel.global.no') !!}</th> -->
                <!-- <th><input type="checkbox" class="allCustomerschecked"/></th> -->
                <th>{!! trans('panel.global.action') !!}</th>
                <th>BP Code</th>
                <th>Firm Name</th>
                <th>Contact Person</th>
                <th>{!! trans('panel.customers.fields.mobile') !!}</th>
                <th>{!! trans('panel.customers.fields.customertype') !!}</th>
                <th>{!! trans('panel.global.created_by') !!}</th>
                <th>City Name</th>
                <th>Address</th>
                <th>{!! trans('panel.customers.fields.shop_image') !!}</th>
                <th>{!! trans('panel.customers.fields.profile_image') !!}</th>
                <th>{!! trans('panel.global.created_at') !!}</th>
                <!-- <th>Beat Name</th> -->
                <th>ID</th>
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
      var table = $('#getcustomers').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
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
          url: "{{ route('customers.index') }}",
          data: function(d) {
            d.executive_id = $('#executive_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val(),
              d.parent_id = $('#parent_id').val(),
              d.branch_id = $('#branch_id').val(),
              d.state_id = $('#state_id').val(),
              d.city_id = $('#city_id').val(),
              d.active = $('#active').val(),
              d.customertype = $('#customertype').val(),
              d.division_id = $('#division_id').val(),
              d.created_by = $('#created_by').val(),
              d.search = $('input[type="search"]').val()
          }
        },
        columns: [
          // { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
          // { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'sap_code',
            name: 'sap_code',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'name',
            name: 'name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'contact_person',
            name: 'contact_person',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'mobile',
            name: 'mobile',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'customertypes.customertype_name',
            name: 'customertypes.customertype_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'createdbyname.name',
            name: 'createdbyname.name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'customeraddress.cityname.city_name',
            name: 'customeraddress.cityname.city_name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'full_address',
            name: 'full_address',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'image',
            name: 'image',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'profileimage',
            name: 'profileimage',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
          // {data: 'beat_name', name: 'beat_name',"defaultContent": ''},
          {
            data: 'id',
            name: 'id',
            "defaultContent": ''
          },

        ],
        dom: 't<"bottom"lip>',
      });

      $('#executive_id').change(function() {
        table.draw();
      });
      $('#division_id').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
        $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });
      $('#active').change(function() {
        table.draw();
      });
      $('#parent_id').change(function() {
        table.draw();
      });
      $('#created_by').change(function() {
        table.draw();
      });
      $('#state_id').change(function() {
        table.draw();
      });
      $('#city_id').change(function() {
        table.draw();
      });
      $('#customertype').change(function() {
        table.draw();
      });
      $('#branch_id').change(function() {
        table.draw();
      });

      $('body').on('click', '.customerActive', function() {
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
          url: "{{ url('customers-active') }}",
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

      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('customers') }}" + '/' + id,
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
      setTimeout(() => {
        var $customerSelect = $('#parent_id').select2({
          placeholder: 'Select Parent',
          allowClear: true,
          ajax: {
            url: "{{ route('getDealerDisDataSelect') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
              return {
                term: params.term || '',
                page: params.page || 1
              }
            },
            cache: true
          }
        });
      }, 1500);
    });
  </script>
</x-app-layout>