<x-app-layout>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    .pream_entry .btn {
      border-radius: 50px;
      margin-right: 12px;
      font-size: 13px;
      font-weight: 500;
      text-transform: capitalize;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .pream_entry .btn i.material-icons {
      height: auto;
    }

    .pream_entry a.exportbtn {
      background: #fff !important;
      color: #787575 !important;
      border: 1px solid #787575 !important;
      box-shadow: unset !important;
    }

    .pream_entry {
      margin-top: 20px;
    }

    .pream_entry a.exportbtn i.material-icons {
      color: #787575 !important;
    }

    .table {
      background-color: #fff !important;
      border: 1px solid #E8E8E8 !important;
      border-radius: 5px !important;
    }

    .table thead tr th {
      font-size: 14px;
      font-weight: 500 !important;
      color: #262A2A;
      letter-spacing: 0px;
      font-family: 'Poppins', sans-serif !important;
    }

    input.searchbox::placeholder {
      color: #6F6F6F;
      font-size: 13px;
      font-weight: 500;
      font-family: 'Poppins', sans-serif !important;
    }

    .table>tbody>tr>td a {
      font-size: 14px !important;
      font-weight: 400 !important;
      color: #262A2A !important;
      letter-spacing: 0px;
      text-transform: capitalize;
      font-family: 'Poppins', sans-serif !important;
    }

    .table>tbody>tr>td {
      font-size: 14px !important;
      font-weight: 400 !important;
      color: #262A2A !important;
      letter-spacing: 0px;
      text-transform: capitalize;
      border-color: #E8E8E8 !important;
      max-width: 120px !important;
      font-family: 'Poppins', sans-serif !important;
    }

    .table thead tr th:last-child {
      width: 170px !important;
    }

    table.dataTable thead .sorting:after {
      display: none;
    }

    table.dataTable thead .sorting:before {
      display: none;
    }

    table.dataTable thead .sorting_desc:before {
      display: none;
    }

    body .main-panel>.content {
      margin-top: 25px;
    }

    .badge {
      border-radius: 10px;
      text-transform: capitalize;
      font-size: 14px;
      font-weight: 400;
    }

    .table thead {
      background-color: #fff !important;
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
    }

    .table tbody tr {
      box-shadow: 0px 4px 4px 0px #DBDBDB40 !important;
    }

    .search_inner {
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border: 1px solid #E8E8E8;
      height: 42px;
      border-radius: 5px;
      padding: 4px 11px;
    }

    .search {
      width: 100%;
      max-width: 400px;
    }

    .search_inner button {
      border: 0px;
      outline: 0px;
      background: transparent;
    }

    .dataTables_length label {
      color: #6F6F6F !important;
      font-size: 14px;
      font-family: 'Poppins', sans-serif !important;
    }

    #getLeads_wrapper .bottom {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }

    nav.navbar.navbar-expand-lg.navbar-transparent.navbar-absolute.fixed-top {
      position: sticky;
    }

    select.custom-select {
      border-radius: 5px !important;
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border: 1px solid #E8E8E8;
      background: #fff;
    }

    .search_inner input.searchbox {
      border: 0px;
      outline: 0px;
      font-size: 13px;
      font-weight: 500;
      font-family: 'Poppins', sans-serif !important;
      width: 95%;
    }

    .pream_entry {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
    }

    .well {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    div#getLeads_info {
      display: none;
    }

    button#dropdownMenuButton {
      color: #000;
    }

    .bmd-form-group {
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border-radius: 5px;
    }

    button.sort_btns {
      background: transparent !important;
      color: #495057;
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border: 1px solid #E8E8E8;
      border-radius: 5px;
      padding: 11px 10px;
    }

    .dd {
      display: flex;
      flex-direction: row;
      justify-content: flex-start;
      align-items: center;
    }

    .sort_btns.filter_btn {
      background: #F2F2F4 !important;
      border: 1px solid #919191;
    }

    @media (max-width: 991px) {
      .pream_entry {
        flex-direction: column;
      }

      body .main-panel>.content {
        padding-top: 25px !important;
      }

      .well {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        width: 100%;
      }

      .search_inner input.searchbox {
        width: auto !important;
      }
    }

    #del_btn {
      border: 1px solid #000;
      background: #f2f2f4 !important;
      color: #000 !important;
    }

    span.brig {
      font-size: 12px;
      color: #3777B5;
      font-weight: 600;
      background: #D7F4FF;
      border-radius: 5px;
      width: auto;
      height: 27px;
      display: inline-flex;
      text-align: center;
      padding: 0px 9px;
    }

    .btn-group.kim button {
      border-radius: 50px;
      padding: 5px 20px;
      text-transform: capitalize;
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%) !important;
      color: #fff !important;
      border-color: transparent;
    }

    .select2.select2-container--default{
      width: 170% !important;
    }
  </style>

  <!-- <style>
    .badge-primary {
      background: #007bff;
      font-size: 14px;
      padding: 5px 10px;
      border-radius: 12px;
    }

    .selectpicker, .select2, .form-control {
      border-radius: 6px !important;
    }

    .filter-box {
      background: #f9f9f9;
      border: 1px solid #e5e5e5;
    }

    .search_inner {
      display: flex;
      border: 1px solid #ddd;
      border-radius: 6px;
      overflow: hidden;
      background: #fff;
    }
    .search_inner button {
      background: transparent;
      border: none;
      padding: 6px 8px;
    }
    .search_inner input {
      border: none;
      flex: 1;
      padding: 6px 10px;
      font-size: 14px;
    }

    .btn i {
      font-size: 18px;
    }

    .gap-2 {
      gap: 0.5rem;
    }

  </style> -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <!-- <div class="card-icon">
             <i class="material-icons">perm_identity</i> 
          </div> -->
          {{--<div class="card p-3 shadow-sm">
            <!-- Header Row -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center flex-wrap gap-2">
                <h4 class="mb-0 mr-3 text-dark">Leads 
                  <span class="badge badge-primary brig ml-2">123</span>
                </h4>
                <select name="status" id="status" class="form-control form-control-sm selectpicker">
                  <option value="">All</option>
                  @if($status->count() > 0)
                    @foreach($status as $stat)
                      <option value="{{$stat['id']}}">{{$stat['display_name']}}</option>
                    @endforeach
                  @endif
                </select>
              </div>

              <button class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
                <i class="material-icons mr-1">tune</i> Filters
              </button>
            </div>

            <!-- Filter Section -->
            <div class="collapse" id="filterSection">
              <div class="filter-box p-3 rounded">
                <!-- Search -->
                <div class="search mb-3">
                  <div class="search_inner">
                    <button type="button"><img src="https://expertfromindia.in/bediya/public/assets/img/search.svg"></button>
                    <input type="search" class="searchbox" id="search_lead" placeholder="Search Lead">
                  </div>
                </div>

                <!-- Dropdown Filters -->
                <div class="row mb-3">
                  <div class="col-md-3 mb-2">
                    <select name="user_id_search" id="user_id_search" class="form-control select2">
                      <option value="">Assign To</option>
                      @isset($users)
                        @foreach($users as $user)
                          <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                        @endforeach
                      @endisset
                    </select>
                  </div>
                  <div class="col-md-3 mb-2">
                  <select class="form-control select2 state" name="state_id" id="state_id" onchange="getDistrictList()" style="width: 100%;">
                      @if(isset($address) && $address->state_id)
                      <option value="{!!  $address->state_id !!}">{!! $address->statename->state_name??'' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.state') !!}</option>
                      @if($states && count($states) > 0)
                      @foreach($states as $state)
                      <option value="{!! $state->id !!}">{!! $state->state_name !!}</option>
                      @endforeach
                      @endif
                      @endif
                    </select>
                  </div>
                  <div class="col-md-3 mb-2">
                    <select class="form-control select2 district" name="district_id" id="district_id" onchange="getCityList()" style="width: 100%;">
                      @if(isset($lead) && $lead->address && $lead->address->district_id)
                      <option value="{!!  $lead->address->district_id !!}" selected>{!! $lead->address->districtname->district_name ?? '' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.district') !!}</option>
                      @endif
                    </select>
                  </div>
                  <div class="col-md-3 mb-2">
                    <select class="form-control select2 city" name="city_id" id="city_id" style="width: 100%;">
                      @if(isset($lead) && $lead->address && $lead->address->city_id)
                      <option value="{!!  $lead->address->city_id !!}" selected>{!! $lead->address->cityname->city_name??'' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.city') !!}</option>
                      @endif
                    </select>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap gap-2">
                  <a href="{{route('leads.create')}}" class="btn btn-primary btn-sm">
                    <i class="material-icons mr-1">add_circle</i> Add Lead
                  </a>
                  <a href="{{route('leads-exportLeads')}}" class="btn btn-success btn-sm" id="export_button">
                    <i class="material-icons mr-1">cloud_download</i> Export
                  </a>

                  @if(auth()->user()->can(['lead_template']))
                    <a href="{{ URL::to('lead-template') }}" class="btn btn-secondary btn-sm" title="Template Leads">
                      <i class="material-icons">text_snippet</i>
                    </a>
                  @endif

                  @if(auth()->user()->can(['lead_upload']))
                    <form action="{{ URL::to('lead-upload') }}" method="post" enctype="multipart/form-data" class="d-flex align-items-center">
                      {{ csrf_field() }}
                      <label class="btn btn-outline-primary btn-sm m-0">
                        <i class="material-icons">attach_file</i> Upload
                        <input type="file" name="import_file" hidden required accept=".xls,.xlsx" />
                      </label>
                      <button class="btn btn-primary btn-sm ml-2" title="Upload Leads">
                        <i class="material-icons">cloud_upload</i>
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          </div>--}}
          <h4 class="card-title ">Leads<span class="brig ml-2"></span><br>
            <div class="btn-group kim" style="width: 120px;">
              <select name="status" id="status" class="form-control selectpicker">
                <option value="">All</option>
                <!-- <option value="0">Pending</option> -->
                @if($status->count() > 0)
                @foreach($status as $stat)
                <option value="{{$stat['id']}}"> {{$stat['display_name']}} </option>
                @endforeach
                @endif
              </select>
            </div>
            <span class="">
            <button class="btn btn-info mb-3 float-right" type="button" data-toggle="collapse" data-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
              <i class="material-icons">tune</i> Filters
              </button>
              <div class="collapse" id="filterSection">
                <div class="d-flex" style="justify-content: space-between;">
                <div>
                  <select name="user_id_search" id="user_id_search" class="form-control select2">
                    <option value="">Assign To</option>
                    @if(@isset($users ))
                    @foreach($users as $user)
                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                <div>
                  <select class="form-control select2 " name="state_id" id="state_id" onchange="getDistrictList()" style="width: 100%;">
                      <option value="">Select {!! trans('panel.global.state') !!}</option>
                      @if($states && count($states) > 0)
                      @foreach($states as $state)
                      <option value="{!! $state->id !!}" {{ old('state_id', isset($lead) && $lead->address ? $lead->address->state_id : '') == $state->id ? 'selected' : '' }}>{!! $state->state_name !!}</option>
                      @endforeach
                      @endif
                  </select>
                </div>
                <div>
                  <select class="form-control select2 district" name="district_id" id="district_id" onchange="getCityList()" style="width: 100%;">
                      @if(isset($lead) && $lead->address && $lead->address->district_id)
                      <option value="{!!  $lead->address->district_id !!}" selected>{!! $lead->address->districtname->district_name ?? '' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.district') !!}</option>
                      @endif
                    </select>
                </div>
                <div>
                  <select class="form-control select2 city" name="city_id" id="city_id" onchange="getPincodeList()" style="width: 100%;">
                      @if(isset($lead) && $lead->address && $lead->address->city_id)
                      <option value="{!!  $lead->address->city_id !!}" selected>{!! $lead->address->cityname->city_name??'' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.city') !!}</option>
                      @endif
                    </select>
                </div>
                </div>
              <div class="pream_entry">

                <div class="search">
                  <div class="search_inner">
                    <button type="button"> <img src="https://expertfromindia.in/bediya/public/assets/img/search.svg"></button>
                    <input type="search" class="searchbox" id="search_lead" placeholder="Search Lead">
                  </div>
                </div>

                <div class="both_btn d-flex">
                <a href="{{route('leads.create')}}"><button type="button" class="btn btn-primary btn-sm btn-icon-split float-right">
                    <span class="icon text-white-50">
                      <i class="material-icons">add_circle</i>
                    </span>
                    <span class="text">Add Lead</span>
                  </button></a>

                  <a href="{{route('leads-exportLeads')}}" class="btn exportbtn btn-primary btn-sm btn-icon-split float-right" id="export_button">
                    <span class="icon text-white-50">
                      <i class="material-icons">cloud_download</i>
                    </span>
                    <span class="text">Export</span>
                  </a>
                  @if(auth()->user()->can(['lead_template']))
                  <a href="{{ URL::to('lead-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Leads"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['lead_upload']))
                  <form action="{{ URL::to('lead-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Leads">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
                  @endif
                </div>
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
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_success') !!}
            </span>
          </div>
          @endif
          <!--  -->
          <div class="well">
            <div class="dd">
              {{--<div class="sort_btn">
                <div class="btn-group">
                  <button class="btn sort_btns  dropdown-toggle"
                    type="button"
                    id="dropdownMenuButton"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <img src="https://expertfromindia.in/bediya/public/assets/img/sort.png"> Sort
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#"> Range</a>
                    <a class="dropdown-item" href="#"> limit</a>

                  </div>
                </div>
              </div>--}}
              <div class="date_sarch">
                {!! Form::open(['method' => 'POST', 'class' => 'form-inline', 'id' => 'frmFilter']) !!}

                <div class="form-group mr-sm-2 col-md-12 pl-2 p-0">
                  {!! Form::text('datetime', old('datetime'), ['class' => 'form-control','placeholder'=> __('MM/DD/YYYY - MM/DD/YYYY'), 'autocomplete' => 'off', 'style' => 'width : 100%;']) !!}
                </div>

                <!--   <button type="submit" class="btn btn-responsive btn-primary mr-sm-2 mb-2">{{ __('Filter') }}</button>
                <a href="javascript:;" onclick="resetFilter();" class="btn btn-responsive btn-danger mb-2">{{ __('Reset') }}</a> -->
                {!! Form::close() !!}
              </div>
            </div>

            <div class="sort_btn d-flex">
              <div class="ass_del d-none">
                <div class="btn-group" style="width: 240px;">
                  <select name="user_id" id="user_id" class="form-control">
                    <option value="">Assign Lead</option>
                    @if(@isset($users ))
                    @foreach($users as $user)
                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                <button type="button" class="btn mr-3 ml-3" id="del_btn"><i class="material-icons icon mr-1">delete</i>Delete</button>
                <button type="button" class="btn mr-3 ml-3" id="convert_btn"><i class="material-icons icon mr-1">transcribe</i>Convert to Customer</button>
              </div>
              {{--<div class="btn-group">
                <button class="btn sort_btns filter_btn  dropdown-toggle"
                  type="button"
                  id="dropdownMenuButton"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <img src="https://expertfromindia.in/bediya/public/assets/img/filter_ss.png"> Filter
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" href="#"> id</a>
                  <a class="dropdown-item" href="#">range </a>
                  <a class="dropdown-item" href="#">class </a>
                </div>
              </div>--}}
            </div>
          </div>
          <!--  -->
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getLeads" class="table text-wrap">
              <thead class=" text-primary">
                <tr>
                  <th><input type="checkbox" id="checkAll"></th>
                  <th>Date</th>
                  <th>Company Name</th>
                  <th>Contact</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>City</th>
                  <th>Status</th>
                  <th>Assigned To</th>
                  <th>Note</th>
                  <th>Others</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--  -->
  {{-- Add New Lead Modal --}}
  <!-- <div class="modal fade" id="addLeadModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('leads.store') }}" id="frmLeadsCreate" enctype="multipart/form-data" class="w-100">
        @csrf

        <div class="modal-content lead-modal">
          {{-- Header --}}
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title font-weight-bold text-uppercase mb-0">ADD NEW LEAD</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          {{-- Body --}}
          <div class="modal-body pt-3">

            {{-- Lead Type --}}
            <div class="form-group mb-2">
              <select name="status" id="status" class="custom-select" required>
                <option value="" disabled selected>Lead Type</option>
                @foreach($status as $opt)
                <option value="{{ $opt->id }}" {{ old('status')==$opt->id ? 'selected' : '' }}>{{ $opt->display_name }}</option>
                @endforeach
              </select>
              @error('status') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            {{-- Firm Name --}}
            <div class="form-group mb-2">
              <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                class="form-control form-control-lg" placeholder="Firm Name" required>
              @error('company_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            {{-- Customer Name --}}
            <div class="form-group mb-2">
              <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                class="form-control form-control-lg" placeholder="Customer Name" required>
              @error('contact_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            {{-- Mobile --}}
            <div class="form-group mb-2">
              <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                class="form-control form-control-lg" placeholder="Mobile Number" maxlength="15" required>
              @error('phone_number') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            {{-- Email --}}
            <div class="form-group mb-2">
              <input type="email" name="email" id="email" value="{{ old('email') }}"
                class="form-control form-control-lg" placeholder="Email Id">
              @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            {{-- Address --}}
            <div class="form-group mb-2">
              <input type="text" name="address" id="address" value="{{ old('address') }}"
                class="form-control form-control-lg" placeholder="Address">
              @error('address') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            {{-- Pin / City --}}
            <div class="form-row">
              <div class="form-group col-6 mb-2">
                <select class="form-control pincode select2" name="pincode_id" id="pincode_id" onchange="getAddressData()" style="width: 100%;">
                  <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                  @if(@isset($pincodes ))
                  @foreach($pincodes as $pincode)
                  <option value="{!! $pincode['id'] !!}" @if(isset($address) && $address->pincode_id==$pincode['id']) selected @endif >{!! $pincode['pincode'] !!}</option>
                  @endforeach
                  @endif
                </select>
                @error('pin_code') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
              </div>
              <div class="form-group col-6 mb-2">
                <select class="form-control select2 city" name="city_id" id="city_id" onchange="getPincodeList()" style="width: 100%;">
                  @if(isset($address) && $address->city_id)
                  <option value="{!!  $address->city_id !!}">{!! $address->cityname->city_name??'' !!}</option>
                  @else
                  <option value="">Select {!! trans('panel.global.city') !!}</option>
                  @endif
                </select>
                @error('city') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
              </div>
            </div>

            {{-- State / State --}}
            <div class="form-row">
              <div class="form-group col-6 mb-2">
                <select class="form-control select2 district" name="district_id" id="district_id" onchange="getCityList()" style="width: 100%;">
                  @if(isset($address) && $address->district_id)
                  <option value="{!!  $address->district_id !!}">{!! $address->cityname->city_name??'' !!}</option>
                  @else
                  <option value="">Select {!! trans('panel.global.district') !!}</option>
                  @endif
                </select>
                @error('state') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
              </div>
              <div class="form-group col-6 mb-2">
                <select class="form-control select2 state" name="state_id" id="state_id" onchange="getDistrictList()" style="width: 100%;">
                  @if(isset($address) && $address->state_id)
                  <option value="{!!  $address->state_id !!}">{!! $address->statename->state_name??'' !!}</option>
                  @else
                  <option value="">Select {!! trans('panel.global.state') !!}</option>
                  @endif
                </select>
                @error('state_alt') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
              </div>
            </div>

            {{-- Other / Lead Source --}}
            <div class="form-row">
              <div class="form-group col-6 mb-2">
                <input type="text" name="other" id="other" value="{{ old('other') }}"
                  class="form-control form-control-lg" placeholder="Other">
                @error('other') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
              </div>
              <div class="form-group col-6 mb-2">
                <select name="lead_source" id="lead_source" class="custom-select" required>
                  <option value="" disabled selected>Lead Source</option>
                  @foreach($lead_sources as $src)
                  <option value="{{ $src }}" {{ old('lead_source')==$src ? 'selected' : '' }}>{{ $src }}</option>
                  @endforeach
                </select>
                @error('lead_source') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
              </div>
            </div>

            {{-- Assigned To --}}
            <div class="form-group mb-2">
              <select name="assign_to" id="assign_to" class="custom-select" required>
                <option value="" disabled selected>Assigned To</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('assign_to')==$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
              </select>
              @error('assign_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>

            {{-- Note --}}
            <div class="form-group mb-2">
              <textarea name="note" id="note" rows="3" class="form-control" placeholder="Note">{{ old('note') }}</textarea>
              @error('note') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
            </div>
          </div>

          {{-- Footer --}}
          <div class="modal-footer border-0">
            <button type="submit" class="btn btn-info btn-block lead-submit">SUBMIT</button>
          </div>
        </div>
      </form>
    </div>
  </div> -->

  <!--  -->
  <link href="{{ url('/').'/'.asset('vendor/bootstrap-daterange/daterangepicker.css') }}" rel="stylesheet">
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <!-- Load jQuery and moment.js -->
  <!-- <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> -->

  <script src="{{ url('/').'/'.asset('vendor/bootstrap-daterange/daterangepicker.min.js') }}"></script>

  <script>
    jQuery(document).ready(function() {
      getLeads();

      jQuery('#frmFilter').submit(function() {
        getLeads();
        return false;
      });

      jQuery('#frmLeadsCreate').validate({
        rules: {
          company_name: {
            required: true
          },
          contact_name: {
            required: true
          },
        }

      });


      $(document).on('change', '#checkAll', function() {
        console.log('checkAll', this.checked);
        $('.lead-checkbox').prop('checked', this.checked);
        let selectedIds = [];

        $('.checkbox_cls:checked').each(function() {
          selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0) {
          $('.ass_del').removeClass('d-none');
        } else {
          $('.ass_del').addClass('d-none');
        }
      });


      jQuery('#frmFilter [name="datetime"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
          cancelLabel: 'Clear'
        }
      }).val("{{ old('datetime') }}");
      jQuery('#frmFilter [name="datetime"]').on('apply.daterangepicker', function(ev, picker) {
        jQuery(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        var this_val = jQuery(this).val();
        var assign_to = jQuery('#user_id_search').val();
        var export_button_url = "{{route('leads-exportLeads')}}?datetime=" + this_val + "&assign_to=" + assign_to;
        $('#export_button').attr('href', export_button_url);
        //console.log(jQuery(this).val());
        getLeads();
      }).on('cancel.daterangepicker', function(ev, picker) {
        jQuery(this).val('');
      });


    });



    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('#company_name, #contact_name').on('keyup', function() {
      var company_name = $('#company_name').val();
      var contact_name = $('#contact_name').val();
      $.post("{{route('leads.searchExistsLead')}}", {
        company_name: company_name,
        contact_name: contact_name
      }, function(response) {
        $('#lead_exist_data').html(response);
      });
    });



    function getLeads() {
      jQuery('#getLeads').dataTable().fnDestroy();
      jQuery('#getLeads tbody').empty();
      var datetime = jQuery('#frmFilter [name=datetime]').val();
      var search = jQuery('#search_lead').val();
      var assign_to = jQuery('#user_id_search').val();
      var state_id = jQuery('#state_id').val();
      var district_id = jQuery('#district_id').val();
      var city_id = jQuery('#city_id').val();
      var status = jQuery('#status').val();
      var table = jQuery('#getLeads').DataTable({
        processing: false,
        serverSide: true,
        searching: false,
        ajax: {
          url: '{{ route('leads.getLeads') }}',
          method: 'POST',
          data: {
            datetime: datetime,
            search: jQuery('#search_lead').val(),
            status: status,
            assign_to: assign_to,
            state_id: state_id,
            district_id: district_id,
            city_id: city_id
          }
        },
        columns: [{
            data: 'checkbox',
            name: 'checkbox',
            orderable: false,
            searchable: false
          },
          {
            data: 'created_at',
            name: 'created_at'
          },
          {
            data: 'company_name',
            name: 'company_name'
          },
          {
            data: 'contacts',
            name: 'contacts',
            orderable: false,
            searchable: false
          },
          {
            data: 'phone',
            name: 'phone',
            orderable: false,
            searchable: false
          },
          {
            data: 'email',
            name: 'email',
            orderable: false,
            searchable: false
          },
          {
            data: 'city',
            name: 'city',
            orderable: false,
            searchable: false
          },
          {
            data: 'status',
            name: 'status',
            searchable: false
          },
          {
            data: 'assign_to',
            name: 'assign_to',
            searchable: false
          },
          {
            data: 'note',
            name: 'note',
            orderable: false,
            searchable: false
          },
          {
            data: 'others',
            name: 'others',
            orderable: false,
            searchable: false
          },
        ],
        order: [
          [1, 'desc']
        ],
        dom: 't<"bottom"lip>',

      });
      table.on('xhr', function(e, settings, json) {
          if (json && json.records_filtered_count !== undefined) {
              jQuery('.brig.ml-2').text(json.records_filtered_count + ' Leads');
          }
      });
    }

    $('#search_lead').on('keyup', function() {
      getLeads();
    });
    $('#user_id_search').on('change', function() {
      var this_val = jQuery('#frmFilter [name="datetime"]').val();
      var assign_to = jQuery(this).val();
      var export_button_url = "{{route('leads-exportLeads')}}?datetime=" + this_val + "&assign_to=" + assign_to;
      $('#export_button').attr('href', export_button_url);
      getLeads();
    });
    $('#state_id').on('change', function() {
      getLeads();
    })
    $('#district_id').on('change', function() {
      getLeads();
    })
    $('#city_id').on('change', function() {
      getLeads();
    })
    $('#status').on('change', function() {
      getLeads();
    });

    function resetFilter() {
      jQuery('#frmFilter :input:not(:button, [type="hidden"])').val('');
      getLeads();
    }

    $(document).on('change', '.checkbox_cls', function() {
      let selectedIds = [];

      $('.checkbox_cls:checked').each(function() {
        selectedIds.push($(this).val());
      });

      if (selectedIds.length > 0) {
        $('.ass_del').removeClass('d-none');
      } else {
        $('.ass_del').addClass('d-none');
      }
    });

    $('#user_id').on('change', function() {
      var user_id = $(this).val();
      let selectedIds = [];
      $('.checkbox_cls:checked').each(function() {
        selectedIds.push($(this).val()); // or $(this).data('id')
      });
      if (selectedIds.length > 0) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't assign this leads!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes'
        }).then((result) => {
          if (result.value) {
            $.ajax({
              url: "{{ route('leads.assignLead') }}",
              method: 'POST',
              data: {
                lead_id: selectedIds,
                user_id: user_id,
                _token: '{{ csrf_token() }}'
              },
              success: function(res) {
                if (res.status == 'success') {
                  getLeads();
                  $('.ass_del').addClass('d-none');
                  Swal.fire({
                    icon: 'success',
                    title: res.message
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: res.message
                  });
                }
              }
            })
          }
        })
      } else {
        // None selected
        $('.ass_del').addClass('d-none');
        // $('#deleteButton').prop('disabled', true);
      }
    });

    $('#del_btn').on('click', function() {
      let selectedIds = [];
      $('.checkbox_cls:checked').each(function() {
        selectedIds.push($(this).val()); // or $(this).data('id')
      });
      if (selectedIds.length > 0) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't delete this leads!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.value) {
            $.ajax({
              url: "{{ route('leads.deleteLead') }}",
              method: 'POST',
              data: {
                lead_id: selectedIds,
                _token: '{{ csrf_token() }}'
              },
              success: function(res) {
                if (res.status == 'success') {
                  getLeads();
                  $('.ass_del').addClass('d-none');
                  Swal.fire({
                    icon: 'success',
                    title: res.message
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: res.message
                  });
                }
              }
            })
          }
        })
      } else {
        // None selected
        $('.ass_del').addClass('d-none');
        // $('#deleteButton').prop('disabled', true);
      }
    });
    $('#convert_btn').on('click', function() {
      let selectedIds = [];
      $('.checkbox_cls:checked').each(function() {
        selectedIds.push($(this).val()); // or $(this).data('id')
      });
      if (selectedIds.length > 0) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't convert leads into Customer!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, convert it!'
        }).then((result) => {
          if (result.value) {
            $.ajax({
              url: "{{ route('leads.convert') }}",
              method: 'POST',
              data: {
                lead_id: selectedIds,
                _token: '{{ csrf_token() }}'
              },
              success: function(res) {
                if (res.status == 'success') {
                  getLeads();
                  $('.ass_del').addClass('d-none');
                  Swal.fire({
                    icon: 'success',
                    title: res.message
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: res.message
                  });
                }
              }
            })
          }
        })
      } else {
        // None selected
        $('.ass_del').addClass('d-none');
        // $('#deleteButton').prop('disabled', true);
      }
    });
  </script>

</x-app-layout>