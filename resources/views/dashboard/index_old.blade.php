<x-app-layout>
   <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
         {{ __('Dashboard') }}
      </h2>
   </x-slot>
   @if(auth()->user()->hasRole('Customer Dealer'))
   <div class="nav-wrapper position-relative end-0">
      <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" id="tabs" role="tablist">
         <li class="nav-item">
            <a class="nav-link active show" data-toggle="tab" href="#sliderTab" role="tablist">
               <i class="material-icons">tune</i> Slider
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#test1" role="tablist">
               <i class="material-icons">check_box_outline_blank</i> Test
            </a>
         </li>
      </ul>
   </div>
   <div class="tab-content tab-space tab-subcategories">
      <div class="tab-pane active show" id="sliderTab">
         @if($dealer_poster_setting->slider == 'Y')
         <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
               @if($dealer_poster_setting->exists && $dealer_poster_setting->getMedia('dealer_portal_slider_image')->count() > 0 && Storage::disk('s3')->exists($dealer_poster_setting->getMedia('dealer_portal_slider_image')[0]->getPath()))
               @foreach($dealer_poster_setting->getMedia('dealer_portal_slider_image') as $k => $media)
               <li data-target="#carouselExampleIndicators" data-slide-to="{{$k}}" class="{{$k==0?'active':''}}"></li>
               @endforeach
               @endif
            </ol>
            <div class="carousel-inner">
               @if($dealer_poster_setting->exists && $dealer_poster_setting->getMedia('dealer_portal_slider_image')->count() > 0 && Storage::disk('s3')->exists($dealer_poster_setting->getMedia('dealer_portal_slider_image')[0]->getPath()))
               @foreach($dealer_poster_setting->getMedia('dealer_portal_slider_image') as $k => $media)
               <div class="carousel-item {{$k==0?'active':''}}">
                  <img class="d-block w-100" src="{{ $media->getFullUrl() }}" alt="{{$media->name}}">
               </div>
               @endforeach
               @endif
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
               <span class="carousel-control-prev-icon" aria-hidden="true"></span>
               <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
               <span class="carousel-control-next-icon" aria-hidden="true"></span>
               <span class="sr-only">Next</span>
            </a>
         </div>
         @endif
      </div>
      <div class="tab-pane active show" id="test1">Second menu</div>
   </div>
   @else
   <div class="nav-wrapper position-relative end-0">
      <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" id="tabs" role="tablist">
         <li class="nav-item">
            <a class="nav-link active show" data-toggle="tab" href="#kpistab" role="tablist" onclick="getDashboardData()">
               <i class="material-icons">home</i> KPIs
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#primarysalestab" role="tablist" onclick="getPrimarySalesDashboard()">
               <i class="material-icons">monetization_on</i> Primary Sales Dashboard
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#primarysaleskpitab" role="tablist" onclick="getPrimarySalesKpi()">
               <i class="material-icons">home</i> Primary KPI
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#secondarysaleskpi" role="tablist" onclick="getSecondarySalesKpi()">
               <i class="material-icons">monetization_on</i> Secondary Sales KPIS
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#secondarysalesreport" role="tablist" onclick="">
               <i class="material-icons">home</i> Secondary Order Detailed Report
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#saarthi" role="tablist" onclick="getPrimarySalesDashboard()">
               <i class="material-icons">home</i> Saarthi
            </a>
         </li>
         <li class="nav-item" style="display: none;">
            <a class="nav-link" data-toggle="tab" href="#travelsummarytab" role="tablist" onclick="travelSummaryDashboard()">
               <i class="material-icons">flight</i> Travel
            </a>
         </li>
         <li class="nav-item" style="display: none;">
            <a class="nav-link" data-toggle="tab" href="#visitsummarytab" role="tablist" onclick="visitSummaryDashboard()">
               <i class="material-icons">location_city</i> Visit
            </a>
         </li>
         <li class="nav-item" style="display: none;">
            <a class="nav-link" data-toggle="tab" href="#couponsummarytab" role="tablist" onclick="couponSummaryDashboard()">
               <i class="material-icons">payment</i> Coupons
            </a>
         </li>
         <li class="nav-item" style="display: none;">
            <a class="nav-link" data-toggle="tab" href="#ordersummarytab" id="li_ordertab" role="tablist" onclick="orderSummaryDashboard()">
               <i class="material-icons">business_center</i> Orders
            </a>
         </li>
         <li class="nav-item" style="display: none;">
            <a class="nav-link" data-toggle="tab" href="#salessummarytab" id="li_salestab" role="tablist" onclick="saleSummaryDashboard()">
               <i class="material-icons">monetization_on</i> Sales
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#activitytab" id="li_activitytab" role="tablist" onclick="activityDashboard()">
               <i class="material-icons">verified_user</i> Activity
            </a>
         </li>
      </ul>
   </div>
   <div class="row" id="main_dashboard_filters">
      <!--  <div class="col col1">
      <label class="bmd-label-floating">User</label>
      <div class="form-group has-default bmd-form-group">
        <select class="form-control select2" name="user_id" id="user_id" data-style="select-with-transition" title="Select User">
           <option value="">Select User</option>
          @if(@isset($users ))
          @foreach($users as $user)
           <option value="{!! $user['id'] !!}" {{ old( 'user_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!} ({{(count($user->getRoleNames())>0)?$user->getRoleNames()[0]:''}})</option>
          @endforeach
          @endif
        </select>
      </div>
    </div> -->
      <!--  <div class="col col2">
      <label class="bmd-label-floating">From Date</label>
      <div class="form-group has-default bmd-form-group">
        <input type="text" class="form-control datepicker" id="fromdate" name="fromdate" autocomplete="off" readonly>
      </div>
    </div> -->
      <!--  <div class="col col3">
      <label class="bmd-label-floating">To Date</label>
       <div class="form-group has-default bmd-form-group">
          <input type="text" class="form-control datepicker" id="todate" name="todate" autocomplete="off" readonly>
       </div>
    </div> -->
      <!-- <div class="col col4">
       <label class="bmd-label-floating">Division</label>
       <select class="form-control select2" name="division_id" id="division_id" data-style="select-with-transition" title="Select User">
           <option value="">Division</option>
          @if(@isset($divisions ))
          @foreach($divisions as $division)
           <option value="{!! $division['id'] !!}" {{ old( 'division_id') == $division->id ? 'selected' : '' }}>{!! $division['division_name'] !!} </option>
          @endforeach
          @endif
        </select>
    </div> -->
      <!--  <div class="col col5">
       <label class="bmd-label-floating">Branch</label>
       <select class="form-control select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select User">
           <option value="">Branch</option>
          @if(@isset($branches ))
          @foreach($branches as $branch)
           <option value="{!! $branch['id'] !!}" {{ old( 'branch_id') == $branch->id ? 'selected' : '' }}>{!! $branch['branch_name'] !!}</option>
          @endforeach
          @endif
        </select>
    </div> -->
      <!-- <div class="col col6">
       <label class="bmd-label-floating">Financial Year</label>
        <select class="form-control select2" name="financial_year" id="financial_year" required data-style="select-with-transition" title="Year">
          <option value="" disabled selected>Financial Year</option>
          @foreach($years as $year)
          @php 
          $startYear = $year - 1;
          $endYear = $year;
          @endphp
          <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
          @endforeach
        </select>
    </div> -->
      <!--     <div class="col col7">
      <label class="bmd-label-floating">Branch</label>
       <select class="form-control select2" name="month" id="month" data-style="select-with-transition" title="Month">
         <option value="" disabled selected>Month</option>
         @for ($month = 1; $month <= 12; $month++)
         <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
         @endfor
       </select>
    </div> -->
      <!-- <div class="col col5">
       <label class="bmd-label-floating">Sales Person</label>
       <select class="form-control select2" name="sales_id" id="sales_id" data-style="select-with-transition" title="Select User">
           <option value="">Sales Person</option>
          @if(@isset($branches ))
          @foreach($branches as $branch)
           <option value="{!! $branch['id'] !!}" {{ old( 'sales_id') == $branch->id ? 'selected' : '' }}>$value</option>
          @endforeach
          @endif
        </select>
    </div> -->
   </div>
   <div class="tab-content tab-space tab-subcategories">
      <div class="tab-pane active show" id="kpistab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">KPIs</h4>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color text-center">
                        <div class="card-body">
                           <h4 class="card-title visittargetcount">0</h4>
                           <p class="card-text">Visit Target</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title visitedcount">0</h4>
                           <p class="card-text text-center">Visited</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center beatadherancecount">0</h4>
                           <p class="card-text text-center">Adherance %</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center beatproductivitycount">0</h4>
                           <p class="card-text text-center">Productivity %</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center totaldealerscount">0</h4>
                           <p class="card-text text-center">Total Dealers</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center totalStockistcount">0</h4>
                           <p class="card-text text-center">Total Stockist</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center totalFleetOwnercount">0</h4>
                           <p class="card-text text-center">Total Fleet Owner</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title text-center totalMechanicscount">0</h4>
                           <p class="card-text text-center">Total Mechanic</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center activedealerscount">0</h4>
                           <p class="card-text text-center">Active Dealers</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center activeStockistcount">0</h4>
                           <p class="card-text text-center">Active Stockist</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center activeFleetOwnercount">0</h4>
                           <p class="card-text text-center">Active Fleet Owner</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center activeMechanicscount">0</h4>
                           <p class="card-text text-center">Active Mechanic</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">{!! date('F') !!} Beat Adherence</h4>
                           <div id="BeatAdherenceBar" class="ct-chart"></div>
                        </div>
                        <div class="card-footer">
                           <div class="row">

                              <div class="col-md-12 pr-6">
                                 <i class="fa fa-circle text-info pr-6"></i> Visit Target
                                 <i class="fa fa-circle text-danger pr-6"></i> Visited Counters
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title">{!! date('F') !!} Beat Productivity</h4>
                           <div id="BeatProductivityBar" class="ct-chart"></div>
                        </div>
                        <div class="card-footer">
                           <div class="row">

                              <div class="col-md-12 pr-6">
                                 <i class="fa fa-circle text-info pr-6"></i> Visited Counters
                                 <i class="fa fa-circle text-danger pr-6"></i> Productive Counters
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">{!! date('Y') !!} Adherence</h4>
                           <div id="YearBeatAdherenceBar" class="ct-chart"></div>
                        </div>
                        <div class="card-footer">
                           <div class="row">

                              <div class="col-md-12 pr-6">
                                 <i class="fa fa-circle text-info pr-6"></i> Visit Target
                                 <i class="fa fa-circle text-danger pr-6"></i> Visited Counters
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">{!! date('Y') !!} Productivity</h4>
                           <div id="YearBeatProductivityBar" class="ct-chart"></div>
                        </div>
                        <div class="card-footer">
                           <div class="row">
                              <div class="col-md-12 pr-6">
                                 <i class="fa fa-circle text-info pr-6"></i> Visited Counters
                                 <i class="fa fa-circle text-danger pr-6"></i> Productive Counters
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- primary sales dashboard -->
      <div class="tab-pane" id="primarysalestab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Primary Sales</h4>
               <div class="flex-container">
                  <form method="GET" action="">
                     <div class="d-flex flex-wrap flex-row">
                        <!-- division filter -->
                        <div class="p-2" style="width:200px;">
                           <select class="select2" name="division" id="ps_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                              <option value="" disabled selected>{!! trans('panel.secondary_dashboard.division') !!}</option>
                              @if(@isset($ps_divisions ))
                              @foreach($ps_divisions as $division)
                              <option value="{!! $division->division !!}">{!! $division->division !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                        <!-- branch filter -->
                        <div class="p-2" style="width:180px;">
                           <select class="select2" name="branch_id" id="ps_branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                              <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                              @if(@isset($ps_branches ))
                              @foreach($ps_branches as $branch)
                              <option value="{!! $branch->final_branch !!}">{!! $branch->final_branch !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                        <!-- financial year filter -->
                        <div class="p-2" style="width:200px;">
                           <select class="select2" name="financial_year" id="ps_financial_year" required data-style="select-with-transition" title="Year">
                              <option value="" disabled selected>{!! trans('panel.secondary_dashboard.year') !!}</option>
                              @foreach($years as $year)
                              @php
                              $startYear = $year - 1;
                              $endYear = $year;
                              @endphp
                              <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
                              @endforeach
                           </select>
                        </div>
                        <!-- month filter-->
                        <div class="p-2" style="width:200px;">
                           <select class="selectpicker" name="month" id="ps_month" disabled data-style="select-with-transition" title="Month">
                              <option value="" selected>{!! trans('panel.secondary_dashboard.month') !!}</option>
                              @for ($month = 1; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                                 @endfor
                           </select>
                        </div>
                        <!-- dealer/distributors filter -->
                        <div class="p-2" style="width:200px;">
                           <select class="select2" name="dealer" id="ps_dealer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                              <option value="" selected>{!! trans('panel.secondary_dashboard.dealers_and_distibutors') !!}</option>
                              @if(@isset($ps_dealers ))
                              @foreach($ps_dealers as $dealer)
                              <option value="{!! $dealer->dealer !!}">{!! $dealer->dealer !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                        <!-- sales persons filter -->
                        <div class="p-2" style="width:200px;">
                           <select class="select2" name="sales_person" id="ps_executive_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                              <option value="" selected>{!! trans('panel.secondary_dashboard.sales_person') !!}</option>
                              @if(@isset($ps_sales_persons ))
                              @foreach($ps_sales_persons as $sales_person)
                              <option value="{!! $sales_person->sales_person !!}">{!! $sales_person->sales_person !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                        <!-- product models filter -->
                        <div class="p-2" style="width:200px;">
                           <select class="select2" name="product_model" id="ps_product_model" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.product_model') !!}">
                              <option value="" selected>{!! trans('panel.secondary_dashboard.product_model') !!}</option>
                              @if(@isset($ps_product_models ))
                              @foreach($ps_product_models as $product)
                              <option value="{!! $product->product_name !!}">{!! $product->product_name !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                        <!-- new group name filter -->
                        <div class="p-2" style="width:200px;">
                           <select class="select2" name="new_group" id="ps_new_group" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.new_group_name') !!}">
                              <option value="" selected>{!! trans('panel.secondary_dashboard.new_group_name') !!}</option>
                              @if(@isset($ps_new_group_names ))
                              @foreach($ps_new_group_names as $product)
                              <option value="{!! $product->new_group !!}">{!! $product->new_group !!}</option>
                              @endforeach
                              @endif
                           </select>
                        </div>
                     </div>
                  </form>
                  <!-- primary sales import -->
                  <!-- sales achievemnts download-->
                  <div class="row next-btn">
                     @if(auth()->user()->can(['primary_sales_upload']))
                     <form action="{{ URL::to('primary_sales/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="input-group" style="flex-wrap:nowrap;">
                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <span class="btn btn-just-icon btn-theme btn-file">
                                 <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                                 <span class="fileinput-exists">Change</span>
                                 <input type="hidden">
                                 <input type="file" title="Select file for upload data" name="import_file" style="flex-wrap: nowrap;" required accept=".xls,.xlsx" />
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
                     <!-- primary sales import -->
                     @if(auth()->user()->can(['primary_sales_template']))
                     <!-- primary sales template creation -->
                     <a href="{{ URL::to('primary_sales_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Primary Sales"><i class="material-icons">text_snippet</i></a>
                     @endif
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="row">
                     <div class="col-sm">
                        <div class="card bg-color  text-center">
                           <div class="card-body">
                              <h4 class="card-title" id="total_primary_sale_value">{{$total_sale}}</h4>
                              <p class="card-text">Total Sale Value</p>
                           </div>
                        </div>
                     </div>
                     <div class="col-sm">
                        <div class="card bg-color  text-center">
                           <div class="card-body">
                              <h4 class="card-title" id="total_primary_qty">{{$total_qty}}</h4>
                              <p class="card-text text-center">Total Quantity</p>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="table-responsive">
                  <table id="getprimarysales" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
                     <thead class=" text-primary">
                        <th>S. No</th>
                        <th>{!! trans('panel.primary_dashboard.invoice_no') !!}</th>
                        <th>{!! trans('panel.primary_dashboard.invoice_date') !!}</th>
                        <th>{!! trans('panel.primary_dashboard.month') !!}</th>
                        <th>DIV</th>
                        <th>Dealer</th>
                        <th>{!! trans('panel.primary_dashboard.city') !!}</th>
                        <th>{!! trans('panel.primary_dashboard.state') !!}</th>
                        <th>Final Branch</th>
                        <th>Sales person</th>
                        <th>Model Name</th>
                        <th>Product Name</th>
                        <th>Qty.</th>
                        <th>Rate</th>
                        <th>Net Amount</th>
                        <th>CGST Amt</th>
                        <th>SGST Amt</th>
                        <th>IGST Amt</th>
                        <th>Total</th>
                        <th>Store Name</th>
                        <th>Group</th>
                        <th>Branch</th>
                        <th>New Group Name</th>
                        <th>Product ID</th>
                     </thead>
                     <tbody>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      <!-- primary sales dashboard -->
      <!-- primary sales kpi -->
      <div class="tab-pane" id="primarysaleskpitab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Primary KPI</h4>
            </div>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card bg-color text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color   text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card bg-color  text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- primary sales kpi -->
      <div class="tab-pane" id="travelsummarytab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Travel Summary</h4>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title daystouredcount">0</h4>
                           <p class="card-text">Days Toured</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title citiescoveredcount">0</h4>
                           <p class="card-text text-center">Cities Covered</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center daysCentralMarketcount">0</h4>
                           <p class="card-text text-center">Days Central Market</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center daysSuburbancount">0</h4>
                           <p class="card-text text-center">Days Suburban</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center daysOfficeWorkCount">0</h4>
                           <p class="card-text text-center">Days Office Work</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-9">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Travel Summary in {!! date('F') !!}</h4>
                           <div id="MonthlyCitiesTours" class="ct-chart"></div>
                        </div>
                        <!-- <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Toured
                              <i class="fa fa-circle text-danger pr-6"></i> Central Market
                              <i class="fa fa-circle text-warning pr-6"></i> Suburban
                              <i class="fa fa-circle text-primary pr-6"></i> Office Work
                           </div>
                        </div>
                     </div> -->
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Travel Summary in {!! date('Y') !!}</h4>
                           <div id="YearCitiesTours" class="ct-chart ct-perfect-fourth"></div>
                        </div>
                        <div class="card-footer">
                           <div class="row">
                              <div class="col-md-12 pr-6">
                                 <i class="fa fa-circle text-info pr-6"></i> Toured
                                 <i class="fa fa-circle text-danger pr-6"></i> Central Market
                                 <i class="fa fa-circle text-warning pr-6"></i> Suburban
                                 <!-- <i class="fa fa-circle text-primary pr-6"></i> Office Work -->
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="secondarysaleskpi">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Secondary KPIS</h4>
            </div>
          
               <div class="col-md-2">
                  <label class="bmd-label-floating">User</label>
                  <div class="form-group has-default">
                     <select class="form-control select2" name="user_id" id="user_id" data-style="select-with-transition" title="Select User">
                        <option value="">Select User</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'user_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!} ({{(count($user->getRoleNames())>0)?$user->getRoleNames()[0]:''}})</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
               </div>
               <div class="col-md-2">
                  <label class="bmd-label-floating">From Date</label>
                  <div class="form-group has-default bmd-form-group">
                     <input type="text" placeholder="From Date"  class="form-control datepicker" id="fromdate" name="fromdate" autocomplete="off" readonly>
                  </div>
               </div>
               <div class="col-md-2">
                  <label class="bmd-label-floating">To Date</label>
                  <div class="form-group has-default bmd-form-group">
                     <input type="text" placeholder="To Date" class="form-control datepicker" id="todate" name="todate" autocomplete="off" readonly>
                  </div>
               </div>
               <div class="col-md-2">
                  <label class="bmd-label-floating">Division</label>
                  <select class="form-control select2 division_test" name="division_id" id="division_id" data-style="select-with-transition" title="Select User">
                     <option value="">Division</option>
                     @if(@isset($divisions ))
                     @foreach($divisions as $division)
                     <option value="{!! $division['id'] !!}" {{ old( 'division_id') == $division->id ? 'selected' : '' }}>{!! $division['division_name'] !!} </option>
                     @endforeach
                     @endif
                  </select>
               </div>
               <div class="col-md-2">
                  <label class="bmd-label-floating">Branch</label>
                  <select class="form-control select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select User">
                     <option value="">Branch</option>
                     @if(@isset($branches ))
                     @foreach($branches as $branch)
                     <option value="{!! $branch['id'] !!}" {{ old( 'branch_id') == $branch->id ? 'selected' : '' }}>{!! $branch['branch_name'] !!}</option>
                     @endforeach
                     @endif
                  </select>
               </div>
               <div class="col-md-2">
                  <label class="bmd-label-floating">Financial Year</label>
                  <select class="form-control select2" name="financial_year" id="financial_year" required data-style="select-with-transition" title="Year">
                     <option value="" disabled selected>Financial Year</option>
                     @foreach($years as $year)
                     @php
                     $startYear = $year - 1;
                     $endYear = $year;
                     @endphp
                     <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
                     @endforeach
                  </select>
               </div>
               <div class="col-md-2">
                  <label class="bmd-label-floating">Branch</label>
                  <select class="form-control select2" name="month" id="month" data-style="select-with-transition" title="Month">
                     <option value="" disabled selected>Month</option>
                     @for ($month = 1; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                        @endfor
                  </select>
               </div>
               <div class="col-md-2">
                  <label class="bmd-label-floating">Sales Person</label>
                  <select class="form-control select2" name="sales_id" id="sales_id" data-style="select-with-transition" title="Select User">
                     <option value="">Sales Person</option>
                     @if(@isset($branches ))
                     @foreach($branches as $branch)
                     <option value="{!! $branch['id'] !!}" {{ old( 'sales_id') == $branch->id ? 'selected' : '' }}>$value</option>
                     @endforeach
                     @endif
                  </select>
               </div>

            <div class="col-md-12">
               <div class="row">
                  <div class="col-md-3">
                     <div class="card bg-color text-center">
                        <div class="card-body">
                           <h4 class="card-title registredretailercount">0</h4>
                           <p class="card-text">Registred Retailer</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title activeretailercount">0</h4>
                           <p class="card-text text-center">Active Retailer</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center activeRetailerPercent">0</h4>
                           <p class="card-text text-center">Active Retailer %</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center newretaileradcount">0</h4>
                           <p class="card-text text-center">New Retailer Ad</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center nosOfRetailerRegistredSaarthi">0</h4>
                           <p class="card-text text-center">Nos Of Retailer Registred under saarthi</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title text-center nosOfRetailerRegistredSaarthiPercent">0</h4>
                           <p class="card-text text-center">Nos Of Retailer Registred under saarthi %</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title text-center orderTarget">0</h4>
                           <p class="card-text text-center">Order Target</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center orderAchievement">0</h4>
                           <p class="card-text text-center">Order Achievement</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title text-center achievementPercent">0</h4>
                           <p class="card-text text-center">Achievement %</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title text-center perDayAverageSales">0</h4>
                           <p class="card-text text-center">Per Day Avg Sales</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="card bg-color  ">
                        <div class="card-body">
                           <h4 class="card-title text-center perDayAverageVisit">0</h4>
                           <p class="card-text text-center">Per Day average Visit</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="secondarysalesreport">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Secondary Sales Summary</h4>
            </div>
            <div class="col-md-12">
            <form method="GET" action="{{ URL::to('customers-download') }}">
               <div class="d-flex flex-wrap flex-row">
                  <!-- division filter -->
                  <div class="p-2" style="width:200px;">
                     <select class="select2" name="division" id="ss_division_id" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.division') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.division') !!}</option>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division->id !!}">{!! $division->division_name !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  <!-- branch filter -->
                  <div class="p-2" style="width:180px;">
                     <select class="select2" name="branch_id" id="ss_branch_id" data-style="select-with-transition" title="panel.sales_users.branch">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.branch') !!}</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  <!-- financial year filter -->
                  <div class="p-2" style="width:200px;">
                     <select class="select2" name="financial_year" id="ss_financial_year" required data-style="select-with-transition" title="Year">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.year') !!}</option>
                        @foreach($years as $year)
                        @php
                        $startYear = $year - 1;
                        $endYear = $year;
                        @endphp
                        <option value="{!!$startYear!!}-{!!$endYear!!}">{!! $startYear!!} - {!! $endYear !!}</option>
                        @endforeach
                     </select>
                  </div>
                  <!-- month filter-->
                  <div class="p-2" style="width:200px;">
                     <select class="selectpicker" name="ss_month" disabled id="ss_month" data-style="select-with-transition" title="Month">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.month') !!}</option>
                        @for ($month = 4; $month <= 12; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                           @endfor
                           @for ($month = 1; $month <= 3; $month++) <option value="{!! date('M', mktime(0, 0, 0, $month, 1)) !!}">{!! date('M', mktime(0, 0, 0, $month, 1)) !!}</option>
                              @endfor
                     </select>
                  </div>
                  <!-- retailer filter -->
                  <div class="p-2" style="width:200px;">
                     <select class="select2" name="user" id="ss_retailer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.retailer_name') !!}</option>
                        @if(@isset($retailers ))
                        @foreach($retailers as $retailer)
                        <option value="{!! $retailer->id !!}">{!! $retailer->name !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  <!-- dealer/distributors filter -->
                  <div class="p-2" style="width:200px;">
                     <select class="select2" name="dealer" id="ss_dealer_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.dealers_and_distibutors') !!}</option>
                        @if(@isset($dealers_and_distibutors ))
                        @foreach($dealers_and_distibutors as $dealer)
                        <option value="{!! $dealer->id !!}">{!! $dealer->first_name !!} {!! $dealer->last_name !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  <!-- sales persons filter -->
                  <div class="p-2" style="width:200px;">
                     <select class="select2" name="sales_person" id="ss_executive_id" data-style="select-with-transition" title="{!! trans('panel.sales_users.user_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.sales_person') !!}</option>
                        @if(@isset($sales_persons ))
                        @foreach($sales_persons as $sales_person)
                        <option value="{!! $sales_person->id !!}">{!! $sales_person->name !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  <!-- product models filter -->
                  <div class="p-2" style="width:200px;">
                     <select class="select2" name="product_model" id="ss_product_model" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.product_model') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.product_model') !!}</option>
                        @if(@isset($products ))
                        @foreach($products as $product)
                        <option value="{!! $product->id !!}">{!! $product->model_no !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  <!-- new group name filter -->
                  <div class="p-2" style="width:200px;">
                     <select class="select2" name="new_group" id="ss_new_group" data-style="select-with-transition" title="{!! trans('panel.secondary_dashboard.new_group_name') !!}">
                        <option value="" disabled selected>{!! trans('panel.secondary_dashboard.new_group_name') !!}</option>
                        @if(@isset($uniqueProductsNewGroup ))
                        @foreach($uniqueProductsNewGroup as $product)
                        <option value="{!! $product->id !!}">{!! $product->new_group !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
               </div>
            </form>
         </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color text-center">
                        <div class="card-body">
                           <h4 class="card-title" id="total_order_value">0</h4>
                           <p class="card-text">Total Order Value</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color text-center">
                        <div class="card-body">
                           <h4 class="card-title" id="total_order_qty">0</h4>
                           <p class="card-text text-center">Total Order Quantity</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title" id="total_order">0</h4>
                           <p class="card-text text-center">Total Order</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="table-responsive">
                        <table id="getsecondarysales" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
                           <thead class=" text-primary">
                              <th>S. No</th>
                              <th>{!! trans('panel.secondary_dashboard.invoice_no') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.invoice_date') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.month') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.division') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.party_name') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.city') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.state') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.distributor_dealer_name') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.distributor_dealer_city') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.final_branch') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.sales_person') !!}</th>
                              <th>Emp Code</th>
                              <th>{!! trans('panel.secondary_dashboard.product_name') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.quantity') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.rate') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.net_amount') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.tax_percentage') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.gst_amount') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.total') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.store_name') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.branch') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.new_group_name') !!}</th>
                              <th>{!! trans('panel.secondary_dashboard.product_id') !!}</th>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="saarthi">
         <div class="row mt-4" style="height: 300px !important;">
            <div class="col-md-6">
               <canvas id="userRegistrationChart" width="200" height="100"></canvas>
            </div>
            <div class="col-md-6">
               <canvas id="PointChart" width="200" height="100"></canvas>
            </div>
         </div>
         <hr>
         <div class="row mt-4" style="height: 300px !important;">
            <div class="col-md-6">
               <canvas id="RedemptionChart" width="200" height="100"></canvas>
            </div>
            <div class="col-md-6">
               <canvas id="RedemptionChart" width="200" height="100"></canvas>
            </div>
         </div>
         <hr>
         <div class="row mt-4" style="height: 400px !important;">
            <div class="col-md-6">
               <canvas id="pieChart" width="400" height="400"></canvas>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="visitsummarytab">
         <div class="row text-center">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Visit Summary</h4>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color  text-center">
                  <div class="card-body">
                     <h4 class="card-title newSTUsregisteredcount">0</h4>
                     <p class="card-text">New STUs Registered</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center newFleetOwnercount">0</h4>
                     <p class="card-text text-center">New Fleet Owner Registered</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center newMechanicregisteredcount">0</h4>
                     <p class="card-text text-center">New Mechanincs Registered</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center newDealerRegisteredcount">0</h4>
                     <p class="card-text text-center">New Dealers Registered</p>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color  text-center">
                  <div class="card-body">
                     <h4 class="card-title visitedstuscount">0</h4>
                     <p class="card-text text-center">STUs Visited</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center visitedFleetOwnercount">0</h4>
                     <p class="card-text text-center">Fleet Owner Visited</p>
                  </div>
               </div>
            </div>

            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center visitedMechanicCount">0</h4>
                     <p class="card-text text-center">Mechanincs Visited</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center visitedDealerCount">0</h4>
                     <p class="card-text text-center">Dealers Visited</p>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title">Customer Registered in {!! date('F') !!}</h4>
                     <div id="MonthNewCustomerRegisteredBar" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Dealers Registered
                           <i class="fa fa-circle text-danger pr-6"></i> STUs Registered
                           <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Registered
                           <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Registered
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color">
                  <div class="card-body">
                     <h4 class="card-title">Customer Visited in {!! date('F') !!}</h4>
                     <div id="MonthCustomerVisitedBar" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Dealers Visited
                           <i class="fa fa-circle text-danger pr-6"></i> STUs Visited
                           <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Visited
                           <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Visited
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title">Customer Registered in {!! date('Y') !!}</h4>
                     <div id="YearNewCustomerRegisteredBar" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Dealers Registered
                           <i class="fa fa-circle text-danger pr-6"></i> STUs Registered
                           <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Registered
                           <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Registered
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title">Customer Visited in {!! date('Y') !!}</h4>
                     <div id="YearCustomerVisitedBar" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Dealers Visited
                           <i class="fa fa-circle text-danger pr-6"></i> STUs Visited
                           <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Visited
                           <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Visited
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="couponsummarytab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Coupons Summary</h4>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color  text-center">
                  <div class="card-body">
                     <h4 class="card-title couponsCollectedValue">0</h4>
                     <p class="card-text">Coupons collected under value scheme</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color text-center">
                  <div class="card-body">
                     <h4 class="card-title mrpCollectedValue">0</h4>
                     <p class="card-text text-center">Total Value under value scheme</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center couponsCollectedPoints">0</h4>
                     <p class="card-text text-center">Coupon collected under MRP scheme</p>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title text-center mrpCollectedPoints">0</h4>
                     <p class="card-text text-center">Total Value under MRP Scheme</p>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title">Coupon Vs Mrp Scheme in {!! date('F') !!}</h4>
                     <div id="ValueVsMrpSchemePai" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                           <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title">Monthly Coupon Value vs MRP Scheme in {!! date('F') !!}</h4>
                     <div id="ValueVsMrpSchemeQtyPai" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                           <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title">Coupon Vs Mrp Scheme in {!! date('Y') !!}</h4>
                     <div id="YearValueVsMrpSchemePai" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                           <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm">
               <div class="card bg-color ">
                  <div class="card-body">
                     <h4 class="card-title">Monthly Coupon Value vs MRP Scheme in {!! date('Y') !!}</h4>
                     <div id="YearValueVsMrpSchemeQtyPai" class="ct-chart"></div>
                  </div>
                  <div class="card-footer">
                     <div class="row">
                        <div class="col-md-12 pr-6">
                           <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                           <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="ordersummarytab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Order Summary</h4>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title orderCollectedCount">0</h4>
                           <p class="card-text">Order Collected</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title orderCollectedSum">0</h4>
                           <p class="card-text text-center">Total Orders</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center GGPLorderCollectedCount">0</h4>
                           <p class="card-text text-center">Total orders GGPL</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center GGPLorderCollectedSum">0</h4>
                           <p class="card-text text-center">Total order value GGPL</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title text-center GPDorderCollectedCount">0</h4>
                           <p class="card-text text-center">Total orders GPD</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center GPDorderCollectedSum">0</h4>
                           <p class="card-text text-center">Total order value GPD</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center GDGLorderCollectedCount">0</h4>
                           <p class="card-text text-center">Total orders GDGL</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center GDGLorderCollectedSum">0</h4>
                           <p class="card-text text-center">Total order value GDGL</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Top 10 Product</h4>
                           <div id="Top10ProductBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Target Vs Achievement</h4>
                           <div id="TargetVsAchievementBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">State Wise Target Vs Achievement</h4>
                           <div id="StateWiseTargetVsAchievementBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Top 10 Sales Representatives</h4>
                           <div id="Top10OrderRepresentativesBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Orders by Zone </h4>
                           <div id="OrdersbyZoneBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="salessummarytab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Sales Summary</h4>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title salestargetamount">0</h4>
                           <p class="card-text">Sales Target</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color  text-center">
                        <div class="card-body">
                           <h4 class="card-title salesachivmentamount">0</h4>
                           <p class="card-text text-center">Achivement </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center salesachivmentpercent">0</h4>
                           <p class="card-text text-center">Achivement %</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title text-center salesvalues">0</h4>
                           <p class="card-text text-center">Sales in Value</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12">
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Sales Target Vs Achievement</h4>
                           <div id="SalesTargetAchievementBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color">
                        <div class="card-body">
                           <h4 class="card-title">State Wise Target Vs Achievement</h4>
                           <div id="StateWiseSalesTargetVsAchievementBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Top 10 Sales Representatives</h4>
                           <div id="Top10SalesRepresentativesBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-sm">
                     <div class="card bg-color ">
                        <div class="card-body">
                           <h4 class="card-title">Sales (Amount in RS ) by Zone </h4>
                           <div id="SalesbyZoneBar" class="ct-chart"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="tab-pane" id="activitytab">
         <div class="row">
            <div class="col-md-12 text-center">
               <h4 class="section-heading mb-3 h4 mt-0">Activity</h4>
            </div>
            <div class="col-md-12">
               <ul class="timeline timeline-simple usertodayActivity">

               </ul>
            </div>
         </div>
      </div>
   </div>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

   <script type="text/javascript">
      $(document).ready(function() {
         getDashboardData();
      });

      $(document).ready(function() {
         $('.nav-link[href="#secondarysaleskpi"]').on('click', function(event) {
            event.preventDefault();
            getSecondarySalesKpi();
         });
      });

      $(document).ready(function() {
         $('.nav-link[href="#secondarysalesreport"]').on('click', function(event) {
            event.preventDefault();
            getTotalOrderValueQty();
         });
      });


      $('#fromdate').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         switch (activetabs) {
            case '#kpistab':
               getDashboardData();
               break;
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            case '#secondarysalesreport':
               getTotalOrderValueQty()
               break;
            case '#primarysalestab':
               getPrimarySalesDashboard()
               break;
            case '#primarysaleskpitab':
               getPrimarySalesKpi()
               break;
            case '#travelsummarytab':
               travelSummaryDashboard()
               break;
            case '#visitsummarytab':
               visitSummaryDashboard()
               break;
            case '#couponsummarytab':
               couponSummaryDashboard()
               break;
            case '#ordersummarytab':
               orderSummaryDashboard()
               break;
            case '#salessummarytab':
               saleSummaryDashboard()
               break;
            case '#activitytab':
               activityDashboard()
            default:
               getDashboardData();
         }
      });
      $('#todate').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         switch (activetabs) {
            case '#kpistab':
               getDashboardData();
               break;
            case '#primarysalestab':
               getPrimarySalesDashboard()
               break;
            case '#primarysaleskpitab':
               getPrimarySalesKpi()
               break;
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            case '#secondarysalesreport':

               getTotalOrderValueQty();
               break;
            case '#travelsummarytab':
               travelSummaryDashboard()
               break;
            case '#visitsummarytab':
               visitSummaryDashboard()
               break;
            case '#couponsummarytab':
               couponSummaryDashboard()
               break;
            case '#ordersummarytab':
               orderSummaryDashboard()
               break;
            case '#salessummarytab':
               saleSummaryDashboard()
               break;
            case '#activitytab':
               activityDashboard()
            default:
               getDashboardData();
         }
      });

      $('#user_id').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         switch (activetabs) {
            case '#kpistab':
               getDashboardData();
               break;
            case '#primarysalestab':
               getPrimarySalesDashboard()
               break;
            case '#primarysaleskpitab':
               getPrimarySalesKpi()
               break;
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            case '#secondaryordersreport':
               break;
            case '#travelsummarytab':
               travelSummaryDashboard()
               break;
            case '#visitsummarytab':
               visitSummaryDashboard()
               break;
            case '#couponsummarytab':
               couponSummaryDashboard()
               break;
            case '#ordersummarytab':
               orderSummaryDashboard()
               break;
            case '#salessummarytab':
               saleSummaryDashboard()
               break;
            case '#activitytab':
               activityDashboard()
            default:
               getDashboardData();
         }
      });

      $('#division_id').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         $('#user_id').val('');
         $('#user_id').trigger('change');
         switch (activetabs) {
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            default:
               getSecondarySalesKpi();
         }
      });

      $('#branch_id').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         switch (activetabs) {
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            default:
               getSecondarySalesKpi();
         }
      });

      $('#sales_id').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         switch (activetabs) {
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            default:
               getSecondarySalesKpi();
         }
      });

      $('#financial_year').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         switch (activetabs) {
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            default:
               getSecondarySalesKpi();
         }
      });

      $('#month').change(function() {
         var activetabs = $("#tabs .active").attr("href");
         switch (activetabs) {
            case '#secondarysaleskpi':
               getSecondarySalesKpi();
               break;
            default:
               getSecondarySalesKpi();
         }
      });

      function travelSummaryDashboard() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         $.ajax({
            url: "{{ url('travelSummaryData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id
            },
            success: function(res) {
               $(".daystouredcount").empty();
               $('.citiescoveredcount').empty();
               $(".daysOfficeWorkCount").empty()
               $(".daysCentralMarketcount").empty();
               $(".daysSuburbancount").empty();
               $(".daystouredcount").append(res.daystouredcount);
               $(".citiescoveredcount").append(res.citiescoveredcount);
               $(".daysCentralMarketcount").append(res.daysCentralMarketcount);
               $(".daysOfficeWorkCount").append(res.daysOfficeWorkCount);
               $(".daysSuburbancount").append(res.daysSuburbancount);
               monthlyTourBar(res)
               YearCitiesTours(res.yeartours)
            }
         })
      }

      function getSecondarySalesKpi() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         var branch_id = $("select[name=branch_id]").val();
         var division_id = $("select[name=division_id]").val();
         var sales_id = $("select[name=sales_id]").val();
         var financial_year = $("select[name=financial_year]").val();
         var month = $("select[name=month]").val();

         $.ajax({
            url: "{{ url('secondarySalesKpiData') }}",
            dataType: "json",
            type: 'POST',
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id,
               branch_id: branch_id,
               division_id: division_id,
               sales_id: sales_id,
               financial_year: financial_year,
               month: month
            },
            success: function(response) {
               $('.registredretailercount').text(response.registeredRetailerCount);
               $('.activeretailercount').text(response.activeRetailerCount);
               $('.activeRetailerPercent').text(response.activeRetailerPercent);
               $('.nosOfRetailerRegistredSaarthi').text(response.nosOfRetailerRegistredSaarthi);
               $('.nosOfRetailerRegistredSaarthiPercent').text(response.nosOfRetailerRegistredSaarthiPercent);
               $('.orderTarget').text(response.orderTarget);
               $('.orderAchievement').text(response.orderAchievement);
               $('.achievementPercent').text(response.achievementPercent);
               $('.perDayAverageSales').text(response.perDayAverageSales);
               $('.perDayAverageVisit').text(response.perDayAverageVisit);
            },
            error: function(xhr, status, error) {
               console.error(xhr.responseText);
            }
         });
      }

      function getPrimarySalesKpi() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         var branch_id = $("select[name=branch_id]").val();
         var division_id = $("select[name=division_id]").val();
         var sales_id = $("select[name=sales_id]").val();
         var financial_year = $("select[name=financial_year]").val();
         var month = $("select[name=month]").val();

         $.ajax({
            url: "{{ url('primarySalesKpiData') }}",
            dataType: "json",
            type: 'POST',
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id,
               branch_id: branch_id,
               division_id: division_id,
               sales_id: sales_id,
               financial_year: financial_year,
               month: month
            },
            success: function(response) {
               $('.registredretailercount').text(response.registeredRetailerCount);
               $('.activeretailercount').text(response.activeRetailerCount);
               $('.activeRetailerPercent').text(response.activeRetailerPercent);
               $('.nosOfRetailerRegistredSaarthi').text(response.nosOfRetailerRegistredSaarthi);
               $('.nosOfRetailerRegistredSaarthiPercent').text(response.nosOfRetailerRegistredSaarthiPercent);
               $('.orderTarget').text(response.orderTarget);
               $('.orderAchievement').text(response.orderAchievement);
               $('.achievementPercent').text(response.achievementPercent);
               $('.perDayAverageSales').text(response.perDayAverageSales);
               $('.perDayAverageVisit').text(response.perDayAverageVisit);
            },
            error: function(xhr, status, error) {
               console.error(xhr.responseText);
            }
         });
      }

      function visitSummaryDashboard() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         $.ajax({
            url: "{{ url('visitSummaryData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id
            },
            success: function(res) {
               $(".newSTUsregisteredcount").empty();
               $(".newFleetOwnercount").empty();
               $(".newMechanicregisteredcount").empty();
               $(".newDealerRegisteredcount").empty();
               $(".visitedstuscount").empty();
               $(".visitedFleetOwnercount").empty();
               $(".visitedMechanicCount").empty();
               $(".visitedDealerCount").empty();
               $(".newSTUsregisteredcount").append(res.newSTUsregisteredcount);
               $(".newFleetOwnercount").append(res.newFleetOwnercount);
               $(".newMechanicregisteredcount").append(res.newMechanicregisteredcount);
               $(".newDealerRegisteredcount").append(res.newDealerRegisteredcount);
               $(".visitedstuscount").append(res.visitedstuscount);
               $(".visitedFleetOwnercount").append(res.visitedFleetOwnercount);
               $(".visitedMechanicCount").append(res.visitedMechanicCount);
               $(".visitedDealerCount").append(res.visitedDealerCount);
               monthCustomerData(res.month_created_data)
               yearCustomerData(res.year_created_data)
               monthlyBeatAdheranceBar(res.monthbeatAdherence)
               yearlyBeatAdheranceBar(res.yearbeatAdherence)
            }
         })
      }

      function couponSummaryDashboard() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         $.ajax({
            url: "{{ url('couponSummaryData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id
            },
            success: function(res) {
               $(".couponsCollectedValue").empty();
               $(".mrpCollectedValue").empty();
               $(".couponsCollectedPoints").empty();
               $(".mrpCollectedPoints").empty();
               $(".couponsCollectedValue").append(res.couponsCollectedValue);
               $(".mrpCollectedValue").append(res.mrpCollectedValue);
               $(".couponsCollectedPoints").append(res.couponsCollectedPoints);
               $(".mrpCollectedPoints").append(res.mrpCollectedPoints);
               couponsSummaryPaiChart(res.coupon_summary_chart)
            }
         })

      }

      function orderSummaryDashboard() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         $.ajax({
            url: "{{ url('orderSummaryData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id
            },
            success: function(res) {
               $(".orderCollectedCount").empty();
               $(".orderCollectedSum").empty();
               $(".GGPLorderCollectedCount").empty();
               $(".GGPLorderCollectedSum").empty();
               $(".GPDorderCollectedCount").empty();
               $(".GPDorderCollectedSum").empty();
               $(".GDGLorderCollectedCount").empty();
               $(".GDGLorderCollectedSum").empty();
               $(".orderCollectedCount").append(res.orderCollectedCount);
               $(".orderCollectedSum").append(res.orderCollectedSum.toFixed(1));
               $(".GGPLorderCollectedCount").append(res.GGPLorderCollectedCount);
               $(".GGPLorderCollectedSum").append(res.GGPLorderCollectedSum.toFixed(1));
               $(".GPDorderCollectedCount").append(res.GPDorderCollectedCount);
               $(".GPDorderCollectedSum").append(res.GPDorderCollectedSum.toFixed(1));
               $(".GDGLorderCollectedCount").append(res.GDGLorderCollectedCount);
               $(".GDGLorderCollectedSum").append(res.GDGLorderCollectedSum.toFixed(1));
               getTop10Products(res.top_products)
               YearTargetVsAchievement(res.yeartargetachievement)
               getTop10Representatives(res.top_representatives)
               getOrderByZone(res.orders_by_zone)
               getOrderByState(res.orders_by_state)
            }
         })

      }

      function saleSummaryDashboard() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         $.ajax({
            url: "{{ url('salesSummaryData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id
            },
            success: function(res) {
               getTop10SalesRepresentatives(res.top_sales_representatives)
            }
         })

      }

      function activityDashboard() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         $.ajax({
            url: "{{ url('activityDashboardCount') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id
            },
            success: function(res) {
               $.each(res.activities, function(index, item) {
                  $(".usertodayActivity").append('<li class="timeline-inverted">' +
                     '<div class="timeline-badge danger">' +
                     '<i class="material-icons">card_travel</i>' +
                     '</div>' +
                     '<div class="timeline-panel">' +
                     '<div class="timeline-heading">' +
                     '<span class="badge badge-pill badge-danger">' + item.users.name + '</span>' +
                     '</div>' +
                     '<div class="timeline-body">' +
                     '<p>' + item.description + '</p>' +
                     '</div>' +
                     '<h6>' +
                     '<i class="ti-time"></i> ' + item.time +
                     '</h6>' +
                     '</div>' +
                     '</li>');
               });
            }
         })
      }

      function couponsSummaryPaiChart(data) {
         var labels = ['Mrp', 'Coupon'];
         var monthpointdata = [data.month_mrp_point, data.month_coupon_point];
         var monthquantitydata = [data.month_coupon_quantity, data.month_mrp_quantity];
         var yearpointdata = [data.year_mrp_point, data.year_coupon_point];
         var yearquantitydata = [data.year_coupon_quantity, data.year_mrp_quantity];
         var options = {
            labelInterpolationFnc: function(value) {
               return value[0]
            }
         };

         var responsiveOptions = [
            ['screen and (min-width: 640px)', {
               chartPadding: 10,
               labelOffset: 10,
               labelDirection: 'explode',
               labelInterpolationFnc: function(value) {
                  return value;
               }
            }],
            ['screen and (min-width: 1024px)', {
               labelOffset: 0,
               chartPadding: 0
            }]
         ];
         new Chartist.Pie('#ValueVsMrpSchemePai', {
            labels: labels,
            series: monthpointdata
         }, options, responsiveOptions);
         new Chartist.Pie('#ValueVsMrpSchemeQtyPai', {
            labels: labels,
            series: monthquantitydata
         }, options, responsiveOptions);

         new Chartist.Pie('#YearValueVsMrpSchemePai', {
            labels: labels,
            series: yearpointdata
         }, options, responsiveOptions);
         new Chartist.Pie('#YearValueVsMrpSchemeQtyPai', {
            labels: labels,
            series: yearquantitydata
         }, options, responsiveOptions);
      }

      function thisMonthlyBeatAdheranceBar(data) {

         var labels = [];
         var targets = [];
         var visited = [];
         var productive = [];
         data.forEach(function(item) {
            labels.push(item.date);
            targets.push(item.visit_target)
            visited.push(item.counter_visited)
            productive.push(item.productive_counter)
         });
         new Chartist.Bar('#BeatProductivityBar', {
            labels: labels,
            series: [
               visited,
               productive
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });

         new Chartist.Bar('#BeatAdherenceBar', {
            labels: labels,
            series: [
               targets,
               visited
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });
      }

      function thisYearBeatAdheranceBar(data) {

         var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
         var targets = [];
         var visited = [];
         var productive = [];
         data.forEach(function(item) {
            targets.push(item.visit_target)
            visited.push(item.counter_visited)
            productive.push(item.productive_counter)
         });

         new Chartist.Bar('#YearBeatProductivityBar', {
            labels: labels,
            series: [
               visited,
               productive
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });

         new Chartist.Bar('#YearBeatAdherenceBar', {
            labels: labels,
            series: [
               targets,
               visited
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });
      }

      function monthlyBeatAdheranceBar(data) {

         var labels = [];
         var targets = [];
         var visited = [];
         var productive = [];
         var dealer_visited = [];
         var stus_visited = [];
         var mechanic_visited = [];
         var fleet_owner_visited = [];
         data.forEach(function(item) {
            labels.push(item.date);
            targets.push(item.visit_target)
            visited.push(item.counter_visited)
            productive.push(item.productive_counter)
            dealer_visited.push(item.dealer_visited)
            stus_visited.push(item.stus_visited)
            mechanic_visited.push(item.mechanic_visited)
            fleet_owner_visited.push(item.fleet_owner_visited)
         });
         new Chartist.Bar('#BeatProductivityBar', {
            labels: labels,
            series: [
               visited,
               productive
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });

         new Chartist.Bar('#BeatAdherenceBar', {
            labels: labels,
            series: [
               targets,
               visited
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });

         new Chartist.Bar('#MonthCustomerVisitedBar', {
            labels: labels,
            series: [
               dealer_visited,
               stus_visited,
               mechanic_visited,
               fleet_owner_visited
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });
      }

      function yearlyBeatAdheranceBar(data) {

         var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
         var targets = [];
         var visited = [];
         var productive = [];
         var dealer_visited = [];
         var stus_visited = [];
         var mechanic_visited = [];
         var fleet_owner_visited = [];
         data.forEach(function(item) {
            targets.push(item.visit_target)
            visited.push(item.counter_visited)
            productive.push(item.productive_counter)
            dealer_visited.push(item.dealer_visited)
            stus_visited.push(item.stus_visited)
            mechanic_visited.push(item.mechanic_visited)
            fleet_owner_visited.push(item.fleet_owner_visited)
         });

         new Chartist.Bar('#YearBeatProductivityBar', {
            labels: labels,
            series: [
               visited,
               productive
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });

         new Chartist.Bar('#YearBeatAdherenceBar', {
            labels: labels,
            series: [
               targets,
               visited
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });

         new Chartist.Bar('#YearCustomerVisitedBar', {
            labels: labels,
            series: [
               dealer_visited,
               stus_visited,
               mechanic_visited,
               fleet_owner_visited
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });
      }

      function monthlyTourBar(data) {
         new Chartist.Bar('#MonthlyCitiesTours', {
            labels: ['Toured', 'Central Market', 'Suburban', 'Office Work'],
            series: [
               [data.daystouredcount, data.daysCentralMarketcount, data.daysSuburbancount, data.daysOfficeWorkCount],
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         }).on('draw', function(data) {
            if (data.type === 'bar') {
               data.element.attr({
                  style: 'stroke-width: 30px'
               });
            }
         });
      }

      function YearCitiesTours(data) {

         var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
         var toured = [];
         var centralMarket = [];
         var suburban = [];
         var officeWork = [];
         data.forEach(function(item) {
            toured.push(item.daystouredcount)
            centralMarket.push(item.daysCentralMarketcount)
            suburban.push(item.daysSuburbancount)
            officeWork.push(item.daysOfficeWorkCount)
         });
         new Chartist.Bar('#YearCitiesTours', {
            labels: labels,
            series: [
               toured,
               centralMarket,
               suburban,
               // officeWork
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            }
         });
      }

      function monthCustomerData(data) {
         var labels = [];
         var dealer = [];
         var stus = [];
         var mechanic = [];
         var fleet_owner = [];
         data.forEach(function(item) {
            labels.push(item.label);
            dealer.push(item.dealer_registered)
            stus.push(item.stus_registered)
            mechanic.push(item.mechanic_registered)
            fleet_owner.push(item.fleet_owner_registered)
         });
         new Chartist.Bar('#MonthNewCustomerRegisteredBar', {
            labels: labels,
            series: [
               dealer,
               stus,
               mechanic,
               fleet_owner
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            }
         });
      }

      function yearCustomerData(data) {
         var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
         var dealer = [];
         var stus = [];
         var mechanic = [];
         var fleet_owner = [];
         data.forEach(function(item) {
            labels.push(item.label);
            dealer.push(item.dealer_registered)
            stus.push(item.stus_registered)
            mechanic.push(item.mechanic_registered)
            fleet_owner.push(item.fleet_owner_registered)
         });
         new Chartist.Bar('#YearNewCustomerRegisteredBar', {
            labels: labels,
            series: [
               dealer,
               stus,
               mechanic,
               fleet_owner
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            resize: true
         });
      }

      function getTop10Products(data) {
         var labels = [];
         var series = [];
         data.forEach(function(item) {
            labels.push(item.products.product_no);
            series.push(item.total_price)
         });

         new Chartist.Bar('#Top10ProductBar', {
            labels: labels,
            series: series
         }, {
            distributeSeries: true,
            resize: true
         });
      }

      function YearTargetVsAchievement(data) {

         var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
         var achievement = [];
         var salesachievement = [];
         var target = [];
         data.forEach(function(item) {
            achievement.push(item.achievement)
            salesachievement.push(item.salesachievement)
            target.push(item.target)
         });
         new Chartist.Bar('#TargetVsAchievementBar', {
            labels: labels,
            series: [
               target,
               achievement
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            }
         });

         new Chartist.Bar('#SalesTargetAchievementBar', {
            labels: labels,
            series: [
               target,
               salesachievement
            ]
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            }
         });
      }

      function getTop10Representatives(data) {
         var labels = [];
         var series = [];
         data.forEach(function(item) {
            labels.push(item.createdbyname.name);
            series.push(item.total_amount)
         });

         new Chartist.Bar('#Top10OrderRepresentativesBar', {
            labels: labels,
            series: series
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            distributeSeries: true,
            resize: true
         });
      }

      function getTop10SalesRepresentatives(data) {
         var labels = [];
         var series = [];
         data.forEach(function(item) {
            labels.push(item.createdbyname.name);
            series.push(item.total_amount)
         });

         new Chartist.Bar('#Top10SalesRepresentativesBar', {
            labels: labels,
            series: series
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            distributeSeries: true,
            resize: true
         });
      }

      function getOrderByZone(data) {
         var labels = [];
         var orderamount = [];
         var salesamount = [];

         var sortedOrderData = data.sort((a, b) => (a.order_amount > b.order_amount ? -1 : 1))
         sortedOrderData.forEach(function(item) {
            if (item.order_amount >= 100) {
               labels.push(item.zone_name);
               orderamount.push(item.order_amount)
            }
         });

         var sortedSaleData = data.sort((a, b) => (a.sales_amount > b.sales_amount ? -1 : 1))
         sortedSaleData.forEach(function(item2) {
            if (item2.sales_amount >= 1000) {
               labels.push(item2.zone_name);
               salesamount.push(item2.sales_amount)
            }
         });
         new Chartist.Bar('#OrdersbyZoneBar', {
            labels: labels,
            series: orderamount
         }, {
            axisX: {
               scaleMinSpace: 15,
               offset: 20
            },
            axisY: {
               offset: 30,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            distributeSeries: true
         });

         new Chartist.Bar('#SalesbyZoneBar', {
            labels: labels,
            series: salesamount
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            distributeSeries: true,
            resize: true
         });
      }

      function getOrderByState(data) {
         var labels = [];
         var orderamount = [];
         var salesamount = [];
         var sortedOrderData = data.sort((a, b) => (a.order_amount > b.order_amount ? -1 : 1))
         sortedOrderData.forEach(function(item) {
            if (item.order_amount >= 10000) {
               labels.push(item.state_name.substring(0, 3));
               orderamount.push(item.order_amount)
            }
         });

         var sortedSaleData = data.sort((a, b) => (a.sales_amount > b.sales_amount ? -1 : 1))
         sortedSaleData.forEach(function(item2) {
            if (item2.sales_amount >= 10000) {
               labels.push(item2.state_name.substring(0, 3));
               salesamount.push(item2.sales_amount)
            }
         });
         new Chartist.Bar('#StateWiseTargetVsAchievementBar', {
            labels: labels,
            series: orderamount
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            distributeSeries: true,
            resize: true
         });

         new Chartist.Bar('#StateWiseSalesTargetVsAchievementBar', {
            labels: labels,
            series: salesamount
         }, {
            seriesBarDistance: 10,
            axisX: {
               offset: 20
            },
            axisY: {
               offset: 30,
               scaleMinSpace: 15,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            distributeSeries: true,
            resize: true
         });
      }

      function getDashboardData() {
         var fromdate = $("input[name=fromdate]").val();
         var todate = $("input[name=todate]").val();
         var user_id = $("select[name=user_id]").val();
         $.ajax({
            url: "{{ url('dashboardData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               fromdate: fromdate,
               todate: todate,
               user_id: user_id
            },
            success: function(res) {
               $(".visittargetcount").empty();
               $(".visitedcount").empty();
               $(".beatadherancecount").empty();
               $(".beatproductivitycount").empty();
               $(".activedealerscount").empty();
               $(".activeStockistcount").empty();
               $(".activeFleetOwnercount").empty();
               $(".activeMechanicscount").empty();
               $(".totaldealerscount").empty();
               $(".totalStockistcount").empty();
               $(".totalFleetOwnercount").empty();
               $(".totalMechanicscount").empty();
               $(".visittargetcount").append(res.visittarget);
               $(".visitedcount").append(res.visitedcounter);
               $(".beatadherancecount").append(res.beatadherance);
               $(".beatproductivitycount").append(res.beatproductivity);
               $(".activedealerscount").append(res.activedealerscount);
               $(".activeStockistcount").append(res.activeStockistcount);
               $(".activeFleetOwnercount").append(res.activeFleetOwnercount);
               $(".activeMechanicscount").append(res.activeMechanicscount);
               $(".totaldealerscount").append(res.totaldealerscount);
               $(".totalStockistcount").append(res.totalStockistcount);
               $(".totalFleetOwnercount").append(res.totalFleetOwnercount);
               $(".totalMechanicscount").append(res.totalMechanicscount);
               thisMonthlyBeatAdheranceBar(res.monthbeatAdherence)
               thisYearBeatAdheranceBar(res.yearbeatAdherence)
            }
         })
      }

      function getPrimarySalesDashboard() {
         $.ajax({
            url: "{{ url('secondary_dashboard/sales') }}",
            dataType: "json",
            type: "GET",
            data: {
               _token: "{{csrf_token()}}"
            },
            success: function(res) {
               console.log(res);
            }
         })
      }
   </script>
   <script type="text/javascript">
      $(function() {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         var table = $('#getsecondarysales').DataTable({
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
               url: "{{ route('secondary_dashboard.sales.list') }}",
               data: function(d) {
                  d.executive_id = $('#ss_executive_id').val(),
                     d.division_id = $('#ss_division_id').val(),
                     d.branch_id = $('#ss_branch_id').val(),
                     d.financial_year = $('#ss_financial_year').val(),
                     d.month = $('#ss_month').val(),
                     d.retailer_id = $('#ss_retailer_id').val(),
                     d.dealer_id = $('#ss_dealer_id').val(),
                     d.product_model = $('#ss_product_model').val(),
                     d.new_group = $('#ss_new_group').val(),
                     d.search = $('input[type="search"]').val()
               }
            },
            columns: [
               // {
               //   data: 'action',
               //   name: 'action',
               //   "defaultContent": ''
               // },
               {
                  data: 'id',
                  name: 'id',
                  orderable: true,
                  searchable: true,
                  "defaultContent": 'orderno'
               },
               {
                  data: 'orders.orderno',
                  name: 'orders.orderno',
                  orderable: false,
                  searchable: false,
                  "defaultContent": 'orderno'
               },
               {
                  data: 'order_date',
                  name: 'order_date',
                  orderable: false,
                  searchable: false,
                  "defaultContent": 'test'
               },
               {
                  data: 'month',
                  name: 'month',
                  orderable: false,
                  searchable: false,
                  "defaultContent": 'month'
               },
               {
                  data: 'orders.getuserdetails.getdivision.division_name',
                  name: 'orders.getuserdetails.getdivision.division_name',
                  orderable: false,
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'orders.buyers.name',
                  name: 'orders.buyers.name',
                  orderable: false,
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'orders.buyers.customeraddress.cityname.city_name',
                  name: 'orders.buyers.customeraddress.cityname.city_name',
                  orderable: false,
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'orders.buyers.customeraddress.statename.state_name',
                  name: 'orders.buyers.customeraddress.statename.state_name',
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'orders.sellers.name',
                  name: 'orders.sellers.name',
                  "defaultContent": ''
               },
               {
                  data: 'orders.sellers.customeraddress.cityname.city_name',
                  name: 'orders.sellers.customeraddress.cityname.city_name',
                  "defaultContent": ''
               },
               {
                  data: 'orders.getuserdetails.getbranch.branch_name',
                  name: 'orders.getuserdetails.getbranch.branch_name',
                  "defaultContent": ''
               },
               {
                  data: 'orders.createdbyname.name',
                  name: 'orders.createdbyname.name',
                  "defaultContent": ''
               },
               {
                  data: 'products.model_no',
                  name: 'products.model_no',
                  "defaultContent": 'product name'
               },
               {
                  data: 'quantity',
                  name: 'quantity',
                  "defaultContent": 'total_qty'
               },
               {
                  data: 'products.productpriceinfo.mrp',
                  name: 'products.productpriceinfo.mrp',
                  "defaultContent": 'mrpsss'
               },
               {
                  data: 'line_total',
                  name: 'line_total',
                  "defaultContent": ''
               },
               {
                  data: 'products.productpriceinfo.gst',
                  name: 'products.productpriceinfo.gst',
                  "defaultContent": 'tax amount'
               },
               {
                  data: 'gst_amount',
                  name: 'gst_amount',
                  "defaultContent": 'gst_amount'
               },
               {
                  data: 'total_amount',
                  name: 'total_amount',
                  "defaultContent": 'total_amount'
               },
               {
                  data: '',
                  name: '',
                  "defaultContent": ''
               },
               {
                  data: 'orders.getuserdetails.getbranch.branch_code',
                  name: 'orders.getuserdetails.getbranch.branch_code',
                  "defaultContent": ''
               },
               {
                  data: 'products.new_group',
                  name: 'products.new_group',
                  "defaultContent": ''
               },
               {
                  data: 'products.id',
                  name: 'products.id',
                  "defaultContent": ''
               },
            ]
         });

         $('#ss_executive_id').change(function() {
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_division_id').change(function() {
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_branch_id').change(function() {
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_financial_year').change(function() {
            $('#ss_month').prop('disabled', false);
            $('#ss_month').selectpicker('refresh');
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_retailer_id').change(function() {
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_dealer_id').change(function() {
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_product_model').change(function() {
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_new_group').change(function() {
            table.draw();
            getTotalOrderValueQty();
         });
         $('#ss_month').change(function() {
            table.draw();
            getTotalOrderValueQty();
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
            var $customerSelect = $('#dealer_id').select2({
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

      function getTotalOrderValueQty() {

         var executive_id = $('#executive_id').val();
         var division_id = $('#division_id').val();
         var branch_id = $('#branch_id').val();
         var financial_year = $('#financial_year').val();
         var month = $('#month').val();
         var retailer_id = $('#retailer_id').val();
         var dealer_id = $('#dealer_id').val();
         var product_model = $('#product_model').val();
         var new_group = $('#new_group').val();

         var token = $("meta[name='csrf-token']").attr("content");
         $.ajax({
            url: "{{ url('secondary_dashboard/total_order_value') }}",
            type: 'POST',
            data: {
               _token: token,
               executive_id: executive_id,
               division_id: division_id,
               branch_id: branch_id,
               financial_year: financial_year,
               month: month,
               retailer_id: retailer_id,
               dealer_id: dealer_id,
               product_model: product_model,
               new_group: new_group,
            },
            success: function(response) {
               var totalOrderValue = parseFloat(response.total_order_value);
               var totalOrderValueInLacs = (totalOrderValue / 100000).toFixed(2);

               $('#total_order_value').text(totalOrderValueInLacs + ' Lacs');
               $('#total_order_qty').text(response.total_order_qty);
               $('#total_order').text(response.total_order);
            },
         });
      }
   </script>
   <script type="text/javascript">
      $(function() {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         var table = $('#getprimarysales').DataTable({
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
               url: "{{ route('primary_dashboard.sales.list') }}",
               data: function(d) {
                  d.executive_id = $('#ps_executive_id').val(),
                     d.division_id = $('#ps_division_id').val(),
                     d.branch_id = $('#ps_branch_id').val(),
                     d.financial_year = $('#ps_financial_year').val(),
                     d.month = $('#ps_month').val(),
                     d.retailer_id = $('#ps_retailer_id').val(),
                     d.dealer_id = $('#ps_dealer_id').val(),
                     d.product_model = $('#ps_product_model').val(),
                     d.new_group = $('#ps_new_group').val(),
                     d.search = $('input[type="search"]').val()
               }
            },
            columns: [
               // {
               //   data: 'action',
               //   name: 'action',
               //   "defaultContent": ''
               // },
               {
                  data: 'id',
                  name: 'id',
                  orderable: true,
                  searchable: true,
                  "defaultContent": 'orderno'
               },
               {
                  data: 'invoiceno',
                  name: 'invoiceno',
                  orderable: false,
                  searchable: false,
                  "defaultContent": 'orderno'
               },
               {
                  data: 'invoice_date',
                  name: 'invoice_date',
                  orderable: false,
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'month',
                  name: 'month',
                  orderable: false,
                  searchable: false,
                  "defaultContent": 'month'
               },
               {
                  data: 'division',
                  name: 'division',
                  orderable: false,
                  searchable: false,
                  "defaultContent": 'division'
               },
               {
                  data: 'dealer',
                  name: 'dealer',
                  orderable: false,
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'city',
                  name: 'city',
                  orderable: false,
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'state',
                  name: 'state',
                  searchable: false,
                  "defaultContent": ''
               },
               {
                  data: 'final_branch',
                  name: 'final_branch',
                  "defaultContent": ''
               },
               {
                  data: 'sales_person',
                  name: 'sales_person',
                  "defaultContent": ''
               },
               {
                  data: 'emp_code',
                  name: 'emp_code',
                  "defaultContent": ''
               },
               {
                  data: 'model_name',
                  name: 'model_name',
                  "defaultContent": 'final branch'
               },
               {
                  data: 'product_name',
                  name: 'product_name',
                  "defaultContent": 'final branch'
               },
               {
                  data: 'quantity',
                  name: 'quantity',
                  "defaultContent": ''
               },
               {
                  data: 'rate',
                  name: 'rate',
                  "defaultContent": 'product name'
               },
               {
                  data: 'net_amount',
                  name: 'net_amount',
                  "defaultContent": 'total_qty'
               },
               {
                  data: 'cgst_amount',
                  name: 'cgst_amount',
                  "defaultContent": ''
               },
               {
                  data: 'sgst_amount',
                  name: 'sgst_amount',
                  "defaultContent": 'tax amount'
               },
               {
                  data: 'igst_amount',
                  name: 'igst_amount',
                  "defaultContent": ''
               },
               {
                  data: 'total_amount',
                  name: 'total_amount',
                  "defaultContent": 'total'
               },
               {
                  data: 'store_name',
                  name: 'store_name',
                  "defaultContent": ''
               },
               {
                  data: 'new_group',
                  name: 'new_group',
                  "defaultContent": ''
               },
               {
                  data: 'branch',
                  name: 'branch',
                  "defaultContent": 'branch'
               },
               {
                  data: 'new_group_name',
                  name: 'new_group_name',
                  "defaultContent": ''
               },
               {
                  data: 'product_id',
                  name: 'product_id',
                  "defaultContent": ''
               },
            ]
         });
         $('#ps_executive_id').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_division_id').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_branch_id').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_financial_year').change(function() {
            $('#ps_month').prop('disabled', false);
            $('#ps_month').selectpicker('refresh');
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_retailer_id').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_dealer_id').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_product_model').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_new_group').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
         $('#ps_month').change(function() {
            table.draw();
            $.ajax({
               url: "{{ route('getPrimaryTotal') }}",
               data: {
                  executive_id: $('#ps_executive_id').val(),
                  division_id: $('#ps_division_id').val(),
                  branch_id: $('#ps_branch_id').val(),
                  financial_year: $('#ps_financial_year').val(),
                  month: $('#ps_month').val(),
                  retailer_id: $('#ps_retailer_id').val(),
                  dealer_id: $('#ps_dealer_id').val(),
                  product_model: $('#ps_product_model').val(),
                  new_group: $('#ps_new_group').val(),
                  search: $('input[type="search"]').val()
               },
               method: "GET",
               success: function(data) {
                  $("#total_primary_sale_value").html(data.total_sale);
                  $("#total_primary_qty").html(data.total_qty);
               }
            });
         });
      });

      ////////////////// sarthi graph scripts start ///////////////////

      var labels = @json($labels);
      var data = @json($data);
      var labels2 = @json($labels2);
      var data2 = @json($data2);
      var labels3 = @json($labels3);
      var data3 = @json($data3);

      // Create Chart.js instance
      var ctx = document.getElementById('userRegistrationChart').getContext('2d');
      var userRegistrationChart = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: labels,
            datasets: [{
               label: 'Monthly Retailer Activation',
               data: data,
               backgroundColor: 'rgba(54, 162, 235, 0.5)',
               borderColor: '#1c81c5',
               borderWidth: 1,
               datalabels: {
                  color: 'black',
                  font: {
                     weight: 'bold'
                  },
                  formatter: function(value, context) {
                     return 'Value: ' + value;
                  }
               }
            }]
         },
         options: {
            scales: {
               y: {
                  beginAtZero: true
               }
            },
            plugins: {
               datalabels: {
                  display: true,
                  color: 'black',
               }
            }
         }
      });

      var ctx = document.getElementById('PointChart').getContext('2d');
      var PointChart = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: labels2,
            datasets: [{
               label: 'Monthly Retailer Points',
               data: data2,
               backgroundColor: 'rgba(54, 162, 235, 0.5)',
               borderColor: 'rgba(54, 162, 235, 1)',
               borderWidth: 1
            }]
         },
         options: {
            scales: {
               y: {
                  beginAtZero: true
               }
            }
         }
      });

      var ctx = document.getElementById('RedemptionChart').getContext('2d');
      var PointChart = new Chart(ctx, {
         type: 'bar',
         data: {
            labels: labels3,
            datasets: [{
               label: 'Monthly Retailer Redemption ',
               data: data3,
               backgroundColor: 'rgba(54, 162, 235, 0.5)',
               borderColor: 'rgba(54, 162, 235, 1)',
               borderWidth: 1
            }]
         },
         options: {
            scales: {
               y: {
                  beginAtZero: true
               }
            }
         }
      });

      $(document).ready(function() {
         // Fetch data from your Laravel backend
         $.ajax({
            url: "{{ route('fetchPieChartData') }}",
            method: "GET",
            success: function(data) {
               var labels = data.labels;
               var values = data.values;

               // Create the pie chart
               var ctx = document.getElementById('pieChart').getContext('2d');
               var myPieChart = new Chart(ctx, {
                  type: 'doughnut',
                  data: {
                     labels: labels,
                     datasets: [{
                        label: 'Point Distribution',
                        data: values,
                        backgroundColor: [
                           '#59dd59',
                           '#cbcb5e',
                           '#5858f1'
                        ],
                        borderColor: [
                           '#59dd59',
                           '#cbcb5e',
                           '#5858f1'
                        ],
                        borderWidth: 1
                     }]
                  },
                  options: {
                     cutoutPercentage: 80
                  }
               });
            }
         });
      });


      ////////////////// sarthi graph scripts end ///////////////////
   </script>
   @endif
</x-app-layout>