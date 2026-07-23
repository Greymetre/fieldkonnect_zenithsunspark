<x-app-layout>
  <style>
    table tbody tr {
      font-size: 12px !important;
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
          <h4 class="card-title">Marketing Master
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['marketing_master_download']))
                <form method="POST" action="{{url('marketings/download')}}" id="prifilfrm">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <!-- state filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="state" placeholder="state" id="state" data-style="select-with-transition" title="Select State">
                        <option value="" disabled selected>Select State</option>
                        @if(@isset($states ))
                        @foreach($states as $state)
                        <option value="{!! $state->state !!}">{!! $state->state !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- district filter -->
                    <div class="p-2" style="width:180px;">
                      <select class="select2" name="district" id="district" data-style="select-with-transition" title="Select District">
                        <option value="" disabled selected>Select District</option>
                        @if(@isset($event_districts ))
                        @foreach($event_districts as $event_district)
                        <option value="{!! $event_district->event_district !!}">{!! $event_district->event_district !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Event Under Names filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="event_under" id="event_under" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" selected>Select Event Under</option>
                        @if(@isset($event_under_names ))
                        @foreach($event_under_names as $event_under_name)
                        <option value="{!! $event_under_name->event_under_name !!}">{!! $event_under_name->event_under_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Division filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="division" id="division" data-style="select-with-transition" title="Select Division">
                        <option value="" selected>Select Division</option>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division->division !!}">{!! $division->division !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Event Center filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="event_center" id="event_center" data-style="select-with-transition" title="Select City">
                        <option value="" selected>Select City</option>
                        @if(@isset($cities ))
                        @foreach($cities as $citie)
                        <option value="{!! $citie->event_center !!}">{!! $citie->event_center !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Branch filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="branch" id="branch" data-style="select-with-transition" title="Select Branch">
                        <option value="" selected>Select Branch</option>
                        @if(@isset($branchs ))
                        @foreach($branchs as $branch)
                        <option value="{!! $branch->branch !!}">{!! $branch->branch !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Branding Team Member filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="branding_team_member" id="branding_team_member" data-style="select-with-transition" title="Select Branding Team">
                        <option value="" selected>Select Branding Team</option>
                        @if(@isset($branding_team_members ))
                        @foreach($branding_team_members as $branding_team_member)
                        <option value="{!! $branding_team_member->branding_team_member !!}">{!! $branding_team_member->branding_team_member !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Category filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="category_of_participant" id="category_of_participant" data-style="select-with-transition" title="Select Category">
                        <option value="" selected>Select Category</option>
                        @if(@isset($categories ))
                        @foreach($categories as $categorie)
                        <option value="{!! $categorie->category_of_participant !!}">{!! $categorie->category_of_participant !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Created By filter -->
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="created_by" id="created_by" data-style="select-with-transition" title="Select Category">
                        <option value="" selected>Select Created By</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user->id !!}">{!! $user->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <!-- Date Range filter -->
                    <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>

                    <div class="p-2" style="width:200px;">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Marketing Master">
                        <i class="material-icons">cloud_download</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                @endif
                <div class="row next-btn">
                  @if(auth()->user()->can(['marketing_master_upload']))
                  <a href="{{ route('marketing.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Marketing Master"><i class="material-icons">add_circle</i></a>
                  @endif

                  @if(auth()->user()->can(['marketing_master_template']))
                  <a href="{{ URL::to('marketings_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Marketing Master"><i class="material-icons">text_snippet</i></a>
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
          <div class="col-md-12 p-3">
            <div class="row">
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text">Total</h4>
                    <h5 class="card-title" id="total_count"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text text-center">Plumber</h4>
                    <h5 class="card-title" id="plumber_count"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text text-center">Mechanic</h4>
                    <h5 class="card-title" id="mechanic_count"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text text-center">Retailer</h4>
                    <h5 class="card-title" id="retailer_count"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text text-center">Village Influencer</h4>
                    <h5 class="card-title" id="village_influencer_count"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text text-center">Electrician</h4>
                    <h5 class="card-title" id="electrician_count"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1">
                  <div class="card-body">
                    <h4 class="card-text text-center">Exhibition Visitors</h4>
                    <h5 class="card-title" id="exhibition_count"></h5>
                  </div>
                </div>
              </div> 
            </div>
          </div>
          <div class="table-responsive">
            <table id="getmarketing" class="table table-striped table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>S. No</th>
                <th>Action</th>
                <th>Event Date</th>
                <th>Division</th>
                <th>Event Center</th>
                <th>Event District</th>
                <th>State</th>
                <th>Event Type</th>
                <th>Event Under </th>
                <th>Branch</th>
                <th>TM/ ASM Name Responsible for Event</th>
                <th>Branding Team Member</th>
                <th>Name of Participant</th>
                <th>Category of Participant</th>
                <th>Place of Participant</th>
                <th>Mob. No. of Participant</th>
                <th>No. of Participant</th>
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
      var table = $('#getmarketing').DataTable({
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
          url: "{{ route('marketing.index') }}",
          data: function(d) {
            d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val(),
              d.state = $('#state').val(),
              d.event_center = $('#event_center').val(),
              d.branch = $('#branch').val(),
              d.event_under = $('#event_under').val(),
              d.district = $('#district').val(),
              d.division = $('#division').val(),
              d.branding_team_member = $('#branding_team_member').val(),
              d.category_of_participant = $('#category_of_participant').val()
              d.created_by = $('#created_by').val()
          }
        },
        columns: [{
            data: 'id',
            name: 'id',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'event_date',
            name: 'event_date',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'division',
            name: 'division',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'event_center',
            name: 'event_center',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'event_district',
            name: 'event_district',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'state',
            name: 'state',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'event_under_type',
            name: 'event_under_type',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'event_under_name',
            name: 'event_under_name',
            orderable: false,
            searchable: false,
            "defaultContent": ''
          },
          {
            data: 'branch',
            name: 'branch',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'responsible_for_event',
            name: 'responsible_for_event',
            "defaultContent": '',
            orderable: true,
            searchable: false,
          },
          {
            data: 'branding_team_member',
            name: 'branding_team_member',
            "defaultContent": '',
            orderable: true,
            searchable: false,
          },
          {
            data: 'name_of_participant',
            name: 'name_of_participant',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'category_of_participant',
            name: 'category_of_participant',
            "defaultContent": 'final branch',
            orderable: true,
            searchable: true,
          },
          {
            data: 'place_of_participant',
            name: 'place_of_participant',
            "defaultContent": 'final branch',
            orderable: true,
            searchable: true,
          },
          {
            data: 'mob_no_of_participant',
            name: 'mob_no_of_participant',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          },
          {
            data: 'count_of_participant',
            name: 'count_of_participant',
            "defaultContent": '',
            orderable: true,
            searchable: true,
          }
        ]
      });
      $('#state').change(function() {
        getCounts();
        table.draw();
      });
      $('#district').change(function() {
        getCounts();
        table.draw();
      });
      $('#division').change(function() {
        getCounts();
        table.draw();
      });
      $('#event_under').change(function() {
        getCounts();
        table.draw();
      });
      $('#event_center').change(function() {
        getCounts();
        table.draw();
      });
      $('#branch').change(function() {
        getCounts();
        table.draw();
      });
      $('#category_of_participant').change(function() {
        getCounts();
        table.draw();
      });
      $('#branding_team_member').change(function() {
        getCounts();
        table.draw();
      });
      $('#created_by').change(function() {
        getCounts();
        table.draw();
      });
      $('#start_date').change(function() {
        getCounts();
        table.draw();
      });
      $('#end_date').change(function() {
        getCounts();
        table.draw();
      });
    });

    $('#reset-filter').on('click', function() {
      $('#prifilfrm').find('input:text, input:password, input:file, select, textarea').val('');
      $('#prifilfrm').find('select').change();
    })

    $(document).ready(function(){
      getCounts();
    })

    function getCounts() {
     var state = $('#state').val();
     var created_by = $('#created_by').val();
     var district = $('#district').val();
     var event_under = $('#event_under').val();
     var event_center = $('#event_center').val();
     var branch = $('#branch').val();
     var category_of_participant = $('#category_of_participant').val();
     var start_date = $('#start_date').val();
     var end_date = $('#end_date').val();
     $.ajax({
       url: "{{ route('marketing.getCounts') }}",
       dataType: "json",
       type: "POST",
       data: {
         _token: "{{csrf_token()}}",
         state: state,
         district: district,
        //  created_by: created_by,
         event_under: event_under,
         event_center: event_center,
         branch: branch,
         category_of_participant: category_of_participant,
         start_date: start_date,
         end_date: end_date
       },
       success: function(res) {
        console.log(res);
         $('#total_count').html(res.total);
         $('#plumber_count').html(res.plumber_count);
         $('#mechanic_count').html(res.mechanic_count);
         $('#retailer_count').html(res.retailer_count);
         $('#village_influencer_count').html(res.village_influencer_count);
         $('#electrician_count').html(res.electrician_count);
         $('#exhibition_count').html(res.exhibition_count);
       }
     });
    }
  </script>
</x-app-layout>