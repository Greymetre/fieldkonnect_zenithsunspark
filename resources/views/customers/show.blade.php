<x-app-layout>
   <style>
      .card.text-center.card-body.p-3 {
         font-size: 16px;
         font-weight: 900;
         line-height: 40px;
         text-shadow: 2px 2px 10px gray;
      }
   </style>
   <div class="row all-points">
      <div class="col-md-2">
         <div class="card card-primary text-center card-body p-3">Total Point Earn <br> <span>{{$total_points}}</span> </div>
      </div>
      <div class="col-md-2">
         <div class="card card-primary text-center card-body p-3">Total Active Point <br> <span>{{$active_points}}</span> </div>
      </div>
      <div class="col-md-2">
         <div class="card card-primary text-center card-body p-3">Total Provision Point <br> <span>{{$provision_points}}</span> </div>
      </div>
      <div class="col-md-2">
         <div class="card card-success text-center card-body p-3">Total Redeem Point <br> <span>{{$total_redemption}}</span> </div>
      </div>
      <div class="col-md-2">
         <div class="card card-danger card-body text-center p-3">Total Rejected Point <br> <span>{{$total_rejected}}</span> </div>
      </div>
      <div class="col-md-2">
         <div class="card card-info card-body text-center p-3">Total balance Point <br> <span>{{$total_balance}}</span> </div>
      </div>
   </div>
   <div class="row">
      <div class="col">
         <div class="card card-body">
            <div class="row">
               <div class="col-md-12">
                  <span class="pull-right">
                     <div class="btn-group">
                        @if(auth()->user()->can(['customer_edit']))
                        <a href="{{ url('customers/'.encrypt($customers->id).'/edit') }}" class="btn btn-just-icon btn-theme"><i class="material-icons">edit</i></a>
                        @endif
                        @if(auth()->user()->can(['transaction_history_download']))
                        <form method="GET" action="{{ route('transaction_history.download') }}" class="form-horizontal">
                           <input type="hidden" name="customer_id" value="{{$customers['id']}}">
                           <div class="pl-1"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Transaction Coupon History"><i class="material-icons">cloud_download</i></button></div>
                        </form>
                        @endif
                        @if(auth()->user()->can(['redemption_download']))
                        <form method="GET" action="{{ route('redemptions.download') }}" class="form-horizontal">
                           <input type="hidden" name="customer_id" value="{{$customers['id']}}">
                           <input type="hidden" name="redeem_mode" value="1">
                           <div class="pl-1"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Redemption"><i class="material-icons">cloud_download</i></button></div>
                     </div>
                     </form>
                     @endif
               </div>
               </span>
            </div>
         </div>
         <div class="row gx-4 mb-2 view-tab">
            <div class="col-auto">
               <div class="avatar avatar-xl position-relative">
                  <img style="border-radius: 10%;" src="{!! !empty($customers['shop_image']) ? $customers['shop_image'] : asset('/assets/img/placeholder.jpg') !!}" alt="profile_image" class="w-100 border-radius-lg shadow-sm imageDisplayModel">
               </div>
            </div>
            <div class="col-auto my-auto">
               <div class="h-100">
                  <h5 class="mb-1">
                     {!! isset($customers['name']) ? $customers['name'] : '' !!}
                  </h5>
                  <p class="font-weight-normal text-sm">
                     {!! isset($customers['customertypes']['customertype_name']) ? $customers['customertypes']['customertype_name'] : '' !!}
                  </p>
               </div>
            </div>
            <div class="col-lg-8 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
               <div class="nav-wrapper position-relative end-0">
                  <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" role="tablist">
                     <li class="nav-item">
                        <a class="nav-link {{$kyc?'':'active show'}}" data-toggle="tab" href="#profile-tabs-detail" role="tablist">
                           <i class="material-icons">preview</i> Details
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link orderinfo" data-toggle="tab" href="#profile-tabs-orders" role="tablist">
                           <i class="material-icons">add_shopping_cart</i> Orders
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link pointsinfo" data-toggle="tab" href="#profile-tabs-sales" role="tablist">
                           <i class="material-icons">shopping_bag</i> Sales
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#profile-tabs-payments" role="tablist">
                           <i class="material-icons">currency_rupee</i> Payments
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#profile-tabs-activity" role="tablist" onclick="getCustomerActivity()">
                           <i class="material-icons">add_task</i> Activity
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link {{$kyc?'active show':''}}" data-toggle="tab" href="#kyc" role="tablist">
                           <i class="material-icons">verified</i> KYC
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#transaction_history" role="tablist">
                           <i class="material-icons">payment</i> Transaction
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#redemption1" role="tablist">
                           <i class="material-icons">G</i> Gift Redemption
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#redemption2" role="tablist">
                           <i class="material-icons">NEFT</i> NEFT Redemption
                        </a>
                     </li>
                  </ul>
               </div>
            </div>
         </div>
         <div class="">
            <div class="tab-content tab-subcategories">

        

               <div class="tab-pane {{$kyc?'':'active show'}}" id="profile-tabs-detail">
                  <div class="row mt-3">
                     <div class="col-md-4 col-xl-4 mt-md-0 mt-4 position-relative">
                        <div class="card card-plain h-100">

                           <div class="card-body p-3">
                              <div class="ctmr-box">
                                 <h6 class="">Personal Information</h6>
                                 <ul class="list-group">
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; {!! isset($customers['first_name']) ? $customers['first_name'] : '' !!} {!! isset($customers['last_name']) ? $customers['last_name'] : '' !!}</li>
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; {!! isset($customers['mobile']) ? $customers['mobile'] : '' !!}</li>
                                    @if(isset($customers['email']))
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; {!! $customers['email'] !!} </li>
                                    @endif
                                    @if(isset($customers['gender']) && $customers['gender'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Gender:</strong> &nbsp; {!! $customers['gender'] !!} </li>
                                    @endif
                                 </ul>
                              </div>
                              <div class="ctmr-box">
                                 <h6 class="">Address Info</h6>
                                 <ul class="list-group">
                                    @if(isset($customers['customeraddress']['address1']) && $customers['customeraddress']['address1'] != '')
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Address:</strong> &nbsp; {!! isset($customers['customeraddress']['address1']) ? $customers['customeraddress']['address1'] : '' !!}
                                       {!! isset($customers['customeraddress']['address2']) ? $customers['customeraddress']['address2'] : '' !!}
                                       {!! isset($customers['customeraddress']['landmark']) ? $customers['customeraddress']['landmark'] : '' !!}
                                       {!! isset($customers['customeraddress']['locality']) ? $customers['customeraddress']['locality'] : '' !!}
                                    </li>
                                    @endif
                                    @if(isset($customers['customeraddress']['cityname']['city_name']) && $customers['customeraddress']['cityname']['city_name'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">City:</strong> &nbsp; {!! $customers['customeraddress']['cityname']['city_name'] !!} </li>
                                    @endif
                                    @if(isset($customers['customeraddress']['districtname']['district_name']) && $customers['customeraddress']['districtname']['district_name'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">District:</strong> &nbsp; {!! $customers['customeraddress']['districtname']['district_name'] !!} </li>
                                    @endif
                                    @if(isset($customers['customeraddress']['statename']['state_name']) && $customers['customeraddress']['statename']['state_name'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">State:</strong> &nbsp; {!! $customers['customeraddress']['statename']['state_name'] !!} </li>
                                    @endif
                                    @if(isset($customers['customeraddress']['pincodename']['pincode']) && $customers['customeraddress']['pincodename']['pincode'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Pincode:</strong> &nbsp; {!! $customers['customeraddress']['pincodename']['pincode'] !!} </li>
                                    @endif
                                 </ul>
                              </div>
                           </div>
                        </div>
                        <hr class="vertical dark">
                     </div>
                     <div class="col-md-4 col-xl-4 mt-md-0 mt-4 position-relative">
                        <div class="card card-plain h-100">

                           <div class="card-body p-3">
                              <div class="ctmr-box">
                                 <h6 class="">Customer Information</h6>

                                 <ul class="list-group">

                                    @if(isset($customers->getemployeedetail) && count($customers->getemployeedetail) > 0)
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Executive:</strong> &nbsp;<?php foreach ($customers->getemployeedetail as $key_new => $customer_detail) {

                                                                                                                                                   echo $customer_detail->employee_detail ? $customer_detail->employee_detail->name : '' . ' ' . ',<br>';
                                                                                                                                                }  ?> </li>
                                    @endif

                                    @if(isset($customers['customer_code']))
                                    <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Customer Code:</strong> &nbsp; {!! $customers['customer_code'] !!} </li>
                                    @endif
                                    @if(isset($customers['manager_name']))
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Manager Name:</strong> &nbsp; {!! $customers['manager_name'] !!} </li>
                                    @endif
                                    @if(isset($customers['manager_phone']))
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Manager Phone:</strong> &nbsp; {!! $customers['manager_phone'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['gstin_no']) && $customers['customerdetails']['gstin_no'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">GSTIN No:</strong> &nbsp; {!! $customers['customerdetails']['gstin_no'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['pan_no']) && $customers['customerdetails']['pan_no'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Pan No:</strong> &nbsp; {!! $customers['customerdetails']['pan_no'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['aadhar_no'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Aadhar No:</strong> &nbsp; {!! $customers['customerdetails']['aadhar_no'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['otherid_no']) && $customers['customerdetails']['otherid_no'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Other No:</strong> &nbsp; {!! $customers['customerdetails']['otherid_no'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['enrollment_date']) && $customers['customerdetails']['enrollment_date'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Enrollment Date:</strong> &nbsp; {!! $customers['customerdetails']['enrollment_date'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['approval_date']) && $customers['customerdetails']['approval_date'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Approval Date:</strong> &nbsp; {!! $customers['customerdetails']['approval_date'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['aadhar_no'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Aadhar No:</strong> &nbsp; {!! $customers['customerdetails']['aadhar_no'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['shop_image']) && $customers['customerdetails']['shop_image'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><img src="{!! !empty($customers['customerdetails']['shop_image']) ? $customers['customerdetails']['shop_image'] : asset('public/assets/img/placeholder.jpg') !!}" alt="profile_image" class="w-100 border-radius-lg shadow-sm"> </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['visiting_card'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><img src="{!! $customers['customerdetails']['visiting_card'] !!}" alt="profile_image" class="w-100 border-radius-lg shadow-sm"> </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['grade'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Grade:</strong> &nbsp; {!! $customers['customerdetails']['grade'] !!} </li>
                                    @endif
                                    @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['visit_status'] != '')
                                    <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Visit Status:</strong> &nbsp; {!! $customers['customerdetails']['visit_status'] !!} </li>
                                    @endif
                                 </ul>
                              </div>
                              <div class="ctmr-box">
                                 <h6 class="">Survey Information</h6>
                                 <div id="accordion" role="tablist">
                                    @if(!empty($customers['surveys']))
                                    @foreach( $customers['surveys'] as $index => $survey )
                                    <div class="card-collapse">
                                       <div class="card-header" role="tab" id="heading{{$index}}">
                                          <h5 class="">
                                             <a data-toggle="collapse" href="#collapse{{$index}}" aria-expanded="false" aria-controls="collapse{{$index}}" class="collapsed">
                                                {!! isset($survey['fields']['label_name']) ? $survey['fields']['label_name'] : '' !!}
                                                <i class="material-icons">keyboard_arrow_down</i>
                                             </a>
                                          </h5>
                                       </div>
                                       <div id="collapse{{$index}}" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                          <div class="card-body">
                                             {!! $survey['value'] !!}
                                          </div>
                                       </div>
                                    </div>
                                    @endforeach
                                    @endif
                                 </div>
                              </div>
                           </div>
                        </div>
                        <hr class="vertical dark">
                     </div>
                     <div class="col-md-4 mt-md-0 mt-4">
                        <div class="card card-plain h-100 activity-conv">
                           <div class="card-header pb-0 p-3">
                              <h6 class="">Activities</h6>
                           </div>
                           <div class="card-body p-3">
                              <ul class="timeline timeline-simple">
                                 @if(isset($customers['visitsinfo']) && count($customers['visitsinfo']) > 0)
                                 @foreach( $customers['visitsinfo'] as $visit )
                                 @if(isset($visit['description']) && $visit['description'] != '')
                                 <li class="timeline-inverted">
                                    <div class="timeline-badge danger">
                                       <i class="material-icons">card_travel</i>
                                    </div>
                                    <div class="timeline-panel">
                                       <div class="timeline-heading">
                                          <span class="badge badge-pill badge-danger">{!! $visit['users']['name'] !!}</span>
                                       </div>
                                       <div class="timeline-body">
                                          <p>{!! $visit['description'] !!}</p>
                                       </div>
                                       <h6>
                                          <i class="ti-time"></i> {!! date("d-m-Y", strtotime($visit['created_at'])) !!}
                                       </h6>
                                    </div>
                                 </li>
                                 @else
                                 <li class="timeline-inverted">
                                    <h6>No Activities</h6>
                                 </li>
                                 @endif
                                 @endforeach
                                 @else
                                 <li class="timeline-inverted">
                                    <h6>No Activities</h6>
                                 </li>
                                 @endif
                              </ul>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="showCustomerLocationonMaps" style="display:none;">
                           <div id="map" style="height: 700px;"></div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <span class="pull-right">
                           <a href="javascript:void(0)" class="btn btn-just-icon btn-theme" onclick="showGoogleMaps()"><i class="material-icons">room</i></a>
                        </span>
                     </div>
                  </div>
               </div>
               <div class="tab-pane " id="profile-tabs-orders">
                  <div class="row">
                     <div class="col-md-12">
                        <h4 class="">Orders List</h4>
                        <div class="table-responsive">
                           <table id="getorder" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                              <thead class="text-primary">
                                 <th>{!! trans('panel.global.action') !!}</th>
                                 <th>{!! trans('panel.global.seller') !!}</th>
                                 <th>{!! trans('panel.global.buyer') !!}</th>
                                 <th>{!! trans('panel.order.orderno') !!}</th>
                                 <th>{!! trans('panel.order.order_date') !!}</th>
                                 <th>{!! trans('panel.order.grand_total') !!}</th>
                              </thead>
                              <tbody>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="tab-pane " id="profile-tabs-sales">
                  <h4 class="">Sales List</h4>
                  <div class="table-responsive">
                     <table id="getsales" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                        <thead class=" text-primary">
                           <th>{!! trans('panel.global.action') !!}</th>
                           <th>{!! trans('panel.global.seller') !!}</th>
                           <th>{!! trans('panel.sale.fields.invoice_no') !!}</th>
                           <th>{!! trans('panel.sale.fields.invoice_date') !!}</th>
                           <th>{!! trans('panel.sale.fields.grand_total') !!}</th>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="tab-pane " id="profile-tabs-payments">
                  <h4 class="">Payments List</h4>
                  <div class="table-responsive">
                     <table id="getPaymentList" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                        <thead class=" text-primary">
                           <th>No</th>
                           <th>Date</th>
                           <th>Type</th>
                           <th>Mode</th>
                           <th>Amount</th>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="tab-pane " id="profile-tabs-activity">
                  <div class="row mt-3">
                     <div class="col-12">
                        <div class="card card-plain h-100">
                           <div class="card-header pb-0 p-3">
                              <h4 class="section-heading mb-3 h4 mt-0">
                                 Activity List
                                 <span class="pull-right">
                                    <div class="btn-group">
                                       <a class="btn btn-just-icon btn-theme create" title="Create Notes" onclick="showCreateNotes()"><i class="material-icons">add_circle</i></a>
                                    </div>
                                 </span>
                              </h4>
                           </div>
                           <div class="card-body p-3">
                              <div class="row">
                                 <div class="col-md-12 createCustomerActivity" style="display:none;">
                                    {!! Form::open(['route' => 'notes.store']) !!}
                                    <input type="hidden" name="customer_id" value="{!! $customers['id'] !!}">
                                    <input type="hidden" name="id" id="note_id">
                                    <div class="row">
                                       <div class="col-md-6">
                                          <div class="input_section">
                                             <label class="col-form-label">Purpose<span class="text-danger"> *</span></label>

                                             <div class="form-group has-default bmd-form-group">
                                                <select class="form-control select2" name="purpose" id="purpose" style="width: 100%;" required>
                                                   <option value="" disabled>Select Purpose</option>
                                                   <option value="Welcome Call">Welcome Call</option>
                                                   <option value="Product Launch">Product Launch</option>
                                                </select>
                                             </div>
                                             @if ($errors->has('purpose'))
                                             <div class="error">
                                                <p class="text-danger">{{ $errors->first('purpose') }}</p>
                                             </div>
                                             @endif

                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="input_section">
                                             <label class="col-form-label">Call Status <span class="text-danger"> *</span></label>

                                             <div class="form-group has-default bmd-form-group">
                                                <select class="form-control select2" name="callstatus" id="callstatus" data-style="select-with-transition" title="Select Call Status" required style="width: 100%;">
                                                   <option value="" disabled>Select Call Status</option>
                                                   <option value="Switched Off">Switched Off</option>
                                                   <option value="Out Of Network">Out Of Network</option>
                                                   <option value="Did Not Pick">Did Not Pick</option>
                                                   <option value="Call Back Later">Call Back Later</option>
                                                   <option value="Call Done">Call Done</option>
                                                   <option value="Not Interested">Not Interested</option>
                                                   <option value="Language Issue">Language Issue</option>
                                                   <option value="Wrong Number">Wrong Number</option>
                                                   <option value="Call Disconnected">Call Disconnected</option>

                                                </select>

                                             </div>
                                          </div>
                                       </div>
                                       <div class="col-md-12">
                                          <div class="input_section">
                                             <label class="col-form-label">Notes<span class="text-danger"> *</span></label>

                                             <div class="form-group bmd-form-group">
                                                <textarea class="form-control" rows="4" name="note" id="note" required>{!! old( 'note') !!}</textarea>
                                             </div>

                                          </div>
                                       </div>
                                       <div class="col-md-12 pull-right">
                                          {{ Form::submit('Submit', array('class' => 'btn btn-info pull-right')) }}
                                       </div>
                                       {{ Form::close() }}
                                    </div>
                                 </div>
                                 <div class="col-md-12">
                                    <ul class="timeline timeline-simple customerActivity">
                                    </ul>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="tab-pane border rounded {{$kyc?'active show':''}}" id="kyc">
                  <div class="row align-items-center">
                     <div class="col-md-6 col-sm-12" style="position: relative;">
                        <div class="img-cvr">
                           <label class="bmd-label-floating">Shop Image dd</label>
                           <div class="empty-div"></div>
                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! !empty($customers['profile_image']) ? $customers['profile_image'] : asset('assets/img/placeholder.jpg') !!}" alt="profile_image" class="w-100 border-radius-lg shadow-sm imageDisplayModel">
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div><br>
                              @if ($errors->has('imggstin'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('imggstin') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-12" style="position: relative;">
                        <div class="img-cvr">
                           <label class="bmd-label-floating">{!! trans('panel.customers.fields.gstin_image') !!}</label>
                           <div class="input-field1">
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.gstin_no') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="gstin_no" id="gstin_no" class="form-control" value="{!! old( 'gstin_no', isset($customers['customerdetails']['gstin_no']) ? $customers['customerdetails']['gstin_no'] :'' ) !!}" maxlength="200">
                                    @if ($errors->has('gstin_no'))
                                    <div class="error">
                                       <p class="text-danger">{{ $errors->first('gstin_no') }}</p>
                                    </div>
                                    @endif
                                 </div>

                              </div>
                           </div>
                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! !empty($customers['customerdocuments']->where('document_name','gstin')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','gstin')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="border-radius-lg shadow-sm imageDisplayModel">
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div><br>
                              @if ($errors->has('imggstin'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('imggstin') }}</p>
                              </div>
                              @endif
                           </div>
                           <div class="act-btn">
                              @if($customers['customerdetails'] && $customers['customerdetails']['gstin_no'] && $customers['customerdetails']['gstin_no_status'] == '0')
                              <button class="btn btn-success veridy_data" data-type="gstin_no_status" data-id="{{$customers['id']}}" data-id="{{$customers['id']}}">Verify</button>
                              <button class="btn btn-danger reject_data" data-type="gstin_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['gstin_no'] && $customers['customerdetails']['gstin_no_status'] == '1')
                              <button disabled class="btn btn-success">Verified</button>
                              <button class="btn btn-danger reject_data" data-type="gstin_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['gstin_no'] && $customers['customerdetails']['gstin_no_status'] == '2')
                              <button class="btn btn-success veridy_data" data-type="gstin_no_status" data-id="{{$customers['id']}}">Verify</button>
                              <button disabled class="btn btn-danger">Rejected</button>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-md-6 col-sm-12" style="position: relative;">
                        <div class="img-cvr">
                           <label class="bmd-label-floating">{!! trans('panel.customers.fields.pan_image') !!}</label>
                           <div class="input-field1">
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.pan_no') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="pan_no" id="pan_no" class="form-control" value="{!! old( 'pan_no', isset($customers['customerdetails']['pan_no']) ? $customers['customerdetails']['pan_no'] :'' ) !!}" maxlength="200">
                                    @if ($errors->has('pan_no'))
                                    <div class="error">
                                       <p class="text-danger">{{ $errors->first('pan_no') }}</p>
                                    </div>
                                    @endif
                                 </div>

                              </div>
                           </div>
                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! !empty($customers['customerdocuments']->where('document_name','pan')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','pan')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="border-radius-lg shadow-sm imageDisplayModel">
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div><br>
                           </div>
                           <div class="act-btn">
                              @if($customers['customerdetails'] && $customers['customerdetails']['pan_no'] && $customers['customerdetails']['pan_no_status'] == '0')
                              <button class="btn btn-success veridy_data" data-type="pan_no_status" data-id="{{$customers['id']}}">Verify</button>
                              <button class="btn btn-danger reject_data" data-type="pan_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['pan_no'] && $customers['customerdetails']['pan_no_status'] == '1')
                              <button disabled class="btn btn-success">Verified</button>
                              <button class="btn btn-danger reject_data" data-type="pan_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['pan_no'] && $customers['customerdetails']['pan_no_status'] == '2')
                              <button class="btn btn-success veridy_data" data-type="pan_no_status" data-id="{{$customers['id']}}">Verify</button>
                              <button disabled class="btn btn-danger">Rejected</button>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-12" style="position: relative;">
                        <div class="img-cvr">
                           <label class="bmd-label-floating">{!! trans('panel.customers.fields.aadhar_image') !!}</label>
                           <div class="input-field1">
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.aadhar_no') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="aadhar_no" id="aadhar_no" class="form-control" value="{!! old( 'aadhar_no', isset($customers['customerdetails']['aadhar_no']) ? $customers['customerdetails']['aadhar_no'] :'' ) !!}" maxlength="200">
                                    @if ($errors->has('aadhar_no'))
                                    <div class="error col-lg-12">
                                       <p class="text-danger">{{ $errors->first('aadhar_no') }}</p>
                                    </div>
                                    @endif

                                 </div>
                              </div>
                           </div>
                           <div class="fileinput fileinput-new text-center two-adhar-img" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! !empty($customers['customerdocuments']->where('document_name','aadhar')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','aadhar')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="border-radius-lg shadow-sm imageDisplayModel">
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                              <div class="fileinput-new thumbnail ml-2">
                                 <img src="{!! !empty($customers['customerdocuments']->where('document_name','aadharback')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','aadharback')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="border-radius-lg shadow-sm imageDisplayModel">
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div><br>
                           </div>

                           <div class="act-btn">
                              @if($customers['customerdetails'] && $customers['customerdetails']['aadhar_no'] && $customers['customerdetails']['aadhar_no_status'] == '0')
                              <button class="btn btn-success veridy_data" data-type="aadhar_no_status" data-id="{{$customers['id']}}">Verify</button>
                              <button class="btn btn-danger reject_data" data-type="aadhar_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['aadhar_no'] && $customers['customerdetails']['aadhar_no_status'] == '1')
                              <button disabled class="btn btn-success">Verified</button>
                              <button class="btn btn-danger reject_data" data-type="aadhar_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['aadhar_no'] && $customers['customerdetails']['aadhar_no_status'] == '2')
                              <button class="btn btn-success veridy_data" data-type="aadhar_no_status" data-id="{{$customers['id']}}">Verify</button>
                              <button disabled class="btn btn-danger">Rejected</button>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="row align-items-center">
                     <div class="col-md-6 col-sm-12" style="position: relative;">
                        <div class="img-cvr">
                           <label class="bmd-label-floating">{!! trans('panel.customers.fields.bank_passbook_image') !!}</label>

                           <div class="input-field1">
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.account_holder') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="account_holder" id="account_holder" class="form-control" value="{!! old( 'account_holder', isset($customers['customerdetails']['account_holder']) ? $customers['customerdetails']['account_holder'] :'' ) !!}" maxlength="200">

                                 </div>
                              </div>
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.account_number') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="account_number" id="account_number" class="form-control" value="{!! old( 'account_number', isset($customers['customerdetails']['account_number']) ? $customers['customerdetails']['account_number'] :'' ) !!}" maxlength="200">
                                 </div>

                              </div>
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.bank_name') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="bank_name" id="bank_name" class="form-control" value="{!! old( 'bank_name', isset($customers['customerdetails']['bank_name']) ? $customers['customerdetails']['bank_name'] :'' ) !!}" maxlength="200">
                                 </div>

                              </div>
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.ifsc_code') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="ifsc_code" id="ifsc_code" class="form-control" value="{!! old( 'ifsc_code', isset($customers['customerdetails']['ifsc_code']) ? $customers['customerdetails']['ifsc_code'] :'' ) !!}" maxlength="200">
                                 </div>

                              </div>
                           </div>
                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! !empty($customers['customerdocuments']->where('document_name','bankpass')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','bankpass')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="border-radius-lg shadow-sm imageDisplayModel">
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div><br>
                           </div>
                           <div class="act-btn">
                              @if($customers['customerdetails'] && $customers['customerdetails']['account_number'] && $customers['customerdetails']['bank_status'] == '0')
                              <button class="btn btn-success veridy_data" data-type="bank_status" data-id="{{$customers['id']}}">Verify</button>
                              <button class="btn btn-danger reject_data" data-type="bank_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['account_number'] && $customers['customerdetails']['bank_status'] == '1')
                              <button disabled class="btn btn-success">Verified</button>
                              <button class="btn btn-danger reject_data" data-type="bank_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['account_number'] && $customers['customerdetails']['bank_status'] == '2')
                              <button class="btn btn-success veridy_data" data-type="bank_status" data-id="{{$customers['id']}}">Verify</button>
                              <button disabled class="btn btn-danger">Rejected</button>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-sm-12" style="position: relative;">
                        <div class="img-cvr">
                           <label class="bmd-label-floating">{!! trans('panel.customers.fields.otherid_image') !!}</label>

                           <div class="input-field1">
                              <div class="input_section">
                                 <label class="col-form-label">{!! trans('panel.customers.fields.otherid_no') !!} :</label>

                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" readonly name="otherid_no" id="otherid_no" class="form-control" value="{!! old( 'otherid_no', isset($customers['customerdetails']['otherid_no']) ? $customers['customerdetails']['otherid_no'] :'' ) !!}" maxlength="200">
                                    @if ($errors->has('otherid_no'))
                                    <div class="error col-lg-12">
                                       <p class="text-danger">{{ $errors->first('otherid_no') }}</p>
                                    </div>
                                    @endif
                                 </div>


                              </div>
                           </div>
                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! !empty($customers['customerdocuments']->where('document_name','other')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','other')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="border-radius-lg shadow-sm imageDisplayModel">
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div><br>
                           </div>
                           <div class="act-btn">
                              @if($customers['customerdetails'] && $customers['customerdetails']['otherid_no'] && $customers['customerdetails']['otherid_no_status'] == '0')
                              <button class="btn btn-success veridy_data" data-type="otherid_no_status" data-id="{{$customers['id']}}">Verify</button>
                              <button class="btn btn-danger reject_data" data-type="otherid_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['otherid_no'] && $customers['customerdetails']['otherid_no_status'] == '1')
                              <button disabled class="btn btn-success">Verified</button>
                              <button class="btn btn-danger reject_data" data-type="otherid_no_status" data-id="{{$customers['id']}}">Reject</button>
                              @elseif($customers['customerdetails'] && $customers['customerdetails']['otherid_no_status'] && $customers['customerdetails']['otherid_no_status'] == '2')
                              <button class="btn btn-success veridy_data" data-type="otherid_no_status" data-id="{{$customers['id']}}">Verify</button>
                              <button disabled class="btn btn-danger">Rejected</button>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="tab-pane " id="transaction_history">
                  <div class="table-responsive">
                     <table id="getTransactionHistory" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
                        <thead class=" text-primary">
                           <th>{!! trans('panel.global.no') !!}</th>
                           <th>{!! trans('panel.expenses.fields.date') !!}</th>
                           <th>Firm Name</th>
                           <th>CONTACT PERSON</th>
                           <th>parent name</th>
                           <th>Mobile Number</th>
                           <th>COUPON Code</th>
                           <th>Sub Category</th>
                           <th>Prodcut Name</th>
                           <th>Point</th>
                           <th>{!! trans('panel.global.action') !!}</th>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="tab-pane " id="redemption1">
                  <div class="table-responsive" id="gift_table">
                     <table id="getGiftTransactionHistory" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
                        <thead class=" text-primary">
                           <!-- <th>{!! trans('panel.global.action') !!}</th> -->
                           <th>{!! trans('panel.global.no') !!}</th>
                           <th>{!! trans('panel.expenses.fields.date') !!}</th>
                           <th>Firm Name</th>
                           <th>Redeem Mode</th>
                           <th>Contact Person</th>
                           <th>Parent Name</th>
                           <th>Mobile Number</th>
                           <th>Category Name</th>
                           <th>Prodcut Name</th>
                           <th>Point</th>
                           <th>Status</th>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="tab-pane " id="redemption2">
                  <div class="table-responsive">
                     <table id="getNeftTransactionHistory" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
                        <thead class=" text-primary">
                           <!-- <th>{!! trans('panel.global.action') !!}</th> -->
                           <th>{!! trans('panel.global.no') !!}</th>
                           <th>{!! trans('panel.expenses.fields.date') !!}</th>
                           <th>Firm Name</th>
                           <th>Redeem Mode</th>
                           <th>Contact Person</th>
                           <th>Parent Name</th>
                           <th>Mobile Number</th>
                           <th>Point</th>
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
   </div>
   <script src="https://maps.google.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18" type="text/javascript"></script>
   <script type="text/javascript">
      $(function() {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         var orderTable = $('#getorder').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 5,
            "searching": false,
            "ordering": false,
            "bLengthChange": false,
            "retrieve": true,
            ajax: {
               url: "{{ route('orders.info') }}",
               data: function(d) {
                  d.buyer_id = "{!! $customers['id'] !!}"
               }
            },
            columns: [{
                  data: 'action',
                  name: 'action',
                  "defaultContent": '',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'sellers.name',
                  name: 'sellers.name',
                  "defaultContent": ''
               },
               {
                  data: 'buyers.name',
                  name: 'buyers.name',
                  "defaultContent": ''
               },
               {
                  data: 'orderno',
                  name: 'orderno',
                  "defaultContent": ''
               },
               {
                  data: 'order_date',
                  name: 'order_date',
                  "defaultContent": ''
               },
               {
                  data: 'grand_total',
                  name: 'grand_total',
                  "defaultContent": ''
               },
            ]
         });
         var salesTable = $('#getsales').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 5,
            "searching": false,
            "ordering": false,
            "bLengthChange": false,
            "retrieve": true,
            ajax: {
               url: "{{ route('sales.info') }}",
               data: function(d) {
                  d.buyer_id = "{!! $customers['id'] !!}"
               }
            },
            columns: [{
                  data: 'action',
                  name: 'action',
                  "defaultContent": '',
                  className: 'td-actions text-center',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'sellers.name',
                  name: 'sellers.name',
                  "defaultContent": ''
               },
               {
                  data: 'invoice_no',
                  name: 'invoice_no',
                  "defaultContent": ''
               },
               {
                  data: 'invoice_date',
                  name: 'invoice_date',
                  "defaultContent": ''
               },
               {
                  data: 'grand_total',
                  name: 'grand_total',
                  "defaultContent": ''
               },
            ]
         });
         var pointsTable = $('#getpoints').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 5,
            "searching": false,
            "ordering": false,
            "bLengthChange": false,
            "retrieve": true,
            ajax: {
               url: "{{ route('wallets.info') }}",
               data: function(d) {
                  d.customer_id = "{!! $customers['id'] !!}"
               }
            },
            columns: [{
                  data: 'DT_RowIndex',
                  name: 'DT_RowIndex',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'transaction_at',
                  name: 'transaction_at',
                  "defaultContent": ''
               },
               {
                  data: 'point_type',
                  name: 'point_type',
                  "defaultContent": ''
               },
               {
                  data: 'points',
                  name: 'points',
                  "defaultContent": ''
               },
            ]
         });
         var pointsTable = $('#getCoupons').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 5,
            "searching": false,
            "ordering": false,
            "bLengthChange": false,
            "retrieve": true,
            ajax: {
               url: "{{ route('wallets.info') }}",
               data: function(d) {
                  d.customer_id = "{!! $customers['id'] !!}",
                     d.coupon = "Yes"
               }
            },
            columns: [{
                  data: 'DT_RowIndex',
                  name: 'DT_RowIndex',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'transaction_at',
                  name: 'transaction_at',
                  "defaultContent": ''
               },
               {
                  data: 'point_type',
                  name: 'point_type',
                  "defaultContent": ''
               },
               {
                  data: 'points',
                  name: 'points',
                  "defaultContent": ''
               },
            ]
         });
         var pointsTable = $('#getRedeemed').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 5,
            "searching": false,
            "ordering": false,
            "bLengthChange": false,
            "retrieve": true,
            ajax: {
               url: "{{ route('wallets.info') }}",
               data: function(d) {
                  d.customer_id = "{!! $customers['id'] !!}",
                     d.redeem = "Yes"
               }
            },
            columns: [{
                  data: 'DT_RowIndex',
                  name: 'DT_RowIndex',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'transaction_at',
                  name: 'transaction_at',
                  "defaultContent": ''
               },
               {
                  data: 'points',
                  name: 'points',
                  "defaultContent": ''
               },
            ]
         });

         var paymentTable = $('#getPaymentList').DataTable({
            "processing": true,
            "serverSide": true,
            "pageLength": 5,
            "searching": false,
            "ordering": false,
            "bLengthChange": false,
            "retrieve": true,
            ajax: {
               url: "{{ route('payments.info') }}",
               data: function(d) {
                  d.customer_id = "{!! $customers['id'] !!}"
               }
            },
            columns: [{
                  data: 'DT_RowIndex',
                  name: 'DT_RowIndex',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'payment_date',
                  name: 'payment_date',
                  "defaultContent": ''
               },
               {
                  data: 'payment_type',
                  name: 'payment_type',
                  "defaultContent": ''
               },
               {
                  data: 'payment_mode',
                  name: 'payment_mode',
                  "defaultContent": ''
               },
               {
                  data: 'amount',
                  name: 'amount',
                  "defaultContent": ''
               },
            ]
         });

         $('.orderinfo').change(function() {
            orderTable.draw();
         });
         $('.salesinfo').change(function() {
            salesTable.draw();
         });
         $('.pointsinfo').change(function() {
            pointsTable.draw();
         });
      });

      function getCustomerActivity() {
         $.ajax({
            url: "{{ url('getCustomerActivityData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               customer_id: "{{ $customers['id'] }}"
            },
            success: function(res) {
               $(".customerActivity").empty();
               $.each(res, function(index, item) {
                  $(".customerActivity").append('<li class="timeline-inverted">' +
                     '<div class="timeline-badge danger">' +
                     '<i class="material-icons">card_travel</i>' +
                     '</div>' +
                     '<div class="timeline-panel">' +
                     '<div class="timeline-heading">' +
                     '<div class="d-flex">' +
                     '<div class="mr-auto p-2">' +
                     '<span class="badge badge-pill badge-danger">' + item.callstatus + '</span>' +
                     '</div>' +
                     '<div class="mr-auto p-2">' +
                     '<span class="badge badge-pill badge-info">' + item.purpose + '</span>' +
                     '</div>' +
                     '<div class="p-2">' +
                     '<a href="javascript:void(0)" class="btn btn-info btn-link editNotesForm" rel="tooltip" onclick="editNotesForm(' + item.id + ')"><i class="material-icons">edit</i></a>' +
                     '</div>' +
                     '</div>' +
                     '</div>' +
                     '<div class="timeline-body">' +
                     '<p>' + item.note + '</p>' +
                     '</div>' +
                     '<h6>' +
                     '<i class="ti-time"></i> ' + new Date(item.created_at).toLocaleDateString() +
                     '</h6>' +
                     '</div>' +
                     '</li>');
               });
            }
         })
      }

      function showCreateNotes() {
         $('.createCustomerActivity').show();
      }

      function showGoogleMaps() {
         var locations = [
            [
               "{{ $customers['name'] }}", "{{ $customers['latitude'] }}", "{{ $customers['longitude'] }}", 1
            ]
         ];
         var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: new google.maps.LatLng("{{ $customers['latitude'] }}", "{{ $customers['longitude'] }}"),
            mapTypeId: google.maps.MapTypeId.ROADMAP
         });
         var infowindow = new google.maps.InfoWindow();
         var marker, i;
         for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
               position: new google.maps.LatLng(locations[i][1], locations[i][2]),
               map: map
            });
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
               return function() {
                  infowindow.setContent(locations[i][0]);
                  infowindow.open(map, marker);
               }
            })(marker, i));
         }
         $('.showCustomerLocationonMaps').show();
      }

      function editNotesForm(id) {
         var base_url = $('.baseurl').data('baseurl');
         $.ajax({
            url: base_url + '/notes/' + id + '/edit',
            dataType: "json",
            success: function(data) {
               $('#purpose').val(data.purpose);
               $('#note').val(data.note);
               $('#callstatus').val(data.callstatus).change();
               $('#status_id').val(data.status_id).change();
               $('#note_id').val(data.id);
               showCreateNotes()
            }
         })
      }

      $(document).on('click', '.veridy_data', function() {
         var type = $(this).data('type');
         var customer_id = $(this).data('id');
         Swal.fire({
            title: "ARE YOU SURE TO VERIFIY THE " + type.replaceAll('_', ' ').toUpperCase() + " ?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            denyButtonText: `Don't save`
         }).then((result) => {
            console.log(result);
            if (result.value) {
               $.ajax({
                  url: "{{ url('changeDocumnetStatus') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     type: type,
                     customer_id: customer_id,
                     status: 1
                  },
                  success: function(res) {
                     if (res.status === true) {
                        Swal.fire("Saved!", res.msg, "success");
                        setTimeout(() => {
                           location.reload();
                        }, 3000);
                     } else {
                        Swal.fire("Somthing went wrong", "", "error");
                     }
                  }
               });
            }
         });
      })

      $(document).on('click', '.reject_data', function() {
         var type = $(this).data('type');
         var customer_id = $(this).data('id');
         Swal.fire({
            title: "ARE YOU SURE TO REJECT THE " + type.replaceAll('_', ' ').toUpperCase() + " ?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "YES",
            denyButtonText: `Don't`
         }).then((result) => {
            if (result.value) {
               $.ajax({
                  url: "{{ url('changeDocumnetStatus') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     type: type,
                     customer_id: customer_id,
                     status: 2
                  },
                  success: function(res) {
                     if (res.status === true) {
                        Swal.fire("Saved!", res.msg, "success");
                        setTimeout(() => {
                           location.reload();
                        }, 3000);
                     } else {
                        Swal.fire("Somthing went wrong", "", "error");
                     }
                  }
               });
            }
         });
      })

      $(function() {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         var table = $('#getTransactionHistory').DataTable({
            processing: true,
            serverSide: true,
            "order": [
               [0, 'desc']
            ],
            "ajax": {
               'url': "{{ route('transaction_history.index') }}",
               'data': function(d) {
                  d.branch_id = $('#branch_id').val(),
                     d.customer_id = '{{$customers["id"]}}'
               }
            },
            columns: [{
                  data: 'DT_RowIndex',
                  name: 'DT_RowIndex',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'created_at',
                  name: 'created_at',
                  "defaultContent": ''
               },
               {
                  data: 'customer.name',
                  name: 'customer.name',
               },
               {
                  data: 'contact_person',
                  name: 'contact_person',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'parent_name',
                  name: 'parent_name',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'customer.mobile',
                  name: 'customer.mobile',
                  "defaultContent": ''
               },
               {
                  data: 'coupon_code',
                  name: 'coupon_code',
                  "defaultContent": ''
               },
               {
                  data: 'subcategory_name',
                  name: 'subcategory_name',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'product_name',
                  name: 'product_name',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'point',
                  name: 'point',
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
            ]
         });
         $('body').on('click', '.delete', function() {
            var id = $(this).attr("value");
            var token = $("meta[name='csrf-token']").attr("content");
            if (!confirm("Are You sure want to delete ?")) {
               return false;
            }
            $.ajax({
               url: "{{ url('transaction_history') }}" + '/' + id,
               type: 'DELETE',
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
      $(function() {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         var table = $('#getNeftTransactionHistory').DataTable({
            processing: true,
            serverSide: true,
            "order": [
               [0, 'desc']
            ],
            "ajax": {
               'url': "{{ route('redemptions.index') }}",
               'data': function(d) {
                  d.customer_id = '{{$customers["id"]}}',
                     d.redeem_mode = 2
               }
            },
            columns: [
               {
                  data: 'id',
                  name: 'id',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'created_at',
                  name: 'created_at',
                  "defaultContent": ''
               },
               {
                  data: 'customer.name',
                  name: 'customer.name',
               },
               {
                  data: 'mode',
                  name: 'mode',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'contact_person',
                  name: 'contact_person',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'parent_name',
                  name: 'parent_name',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'customer.mobile',
                  name: 'customer.mobile',
                  "defaultContent": ''
               },
               {
                  data: 'redeem_amount',
                  name: 'redeem_amount',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'status',
                  name: 'status',
                  "defaultContent": ''
               },
            ]
         });
         // $('body').on('click', '.ChangeStatus', function() {
         //    var id = $(this).attr("id");
         //    var active = $(this).data("status");
         //    var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
         //    Swal.fire({
         //       title: 'Please Select Status',
         //       input: 'select',
         //       inputOptions: {
         //          '0': 'Pendding',
         //          '1': 'Approve',
         //          '2': 'Reject'
         //       },
         //       inputValue: status,
         //       inputPlaceholder: 'Select Status',
         //       showCancelButton: true,
         //       inputValidator: function(value) {
         //          return new Promise(function(resolve, reject) {
         //             if (value !== '') {
         //                resolve();
         //             } else {
         //                resolve('You need to select a Status');
         //             }
         //          });
         //       }
         //    }).then(function(result) {
         //       if (!result.dismiss) {
         //          $.ajax({
         //             url: "{{ url('redemption-change-status') }}",
         //             type: 'GET',
         //             data: {
         //                id: id,
         //                status: result.value
         //             },
         //             success: function(data) {
         //                $('.message').empty();
         //                $('.alert').show();
         //                if (data.status == 'success') {
         //                   $('.alert').addClass("alert-success");
         //                } else {
         //                   $('.alert').addClass("alert-danger");
         //                }
         //                $('.message').append(data.message);
         //                table.draw();
         //             },
         //          });
         //       }
         //    });
         // });

         // $('body').on('click', '.delete', function() {
         //    var id = $(this).attr("value");
         //    var token = $("meta[name='csrf-token']").attr("content");
         //    if (!confirm("Are You sure want to delete ?")) {
         //       return false;
         //    }
         //    $.ajax({
         //       url: "{{ url('redemptions') }}" + '/' + id,
         //       type: 'DELETE',
         //       data: {
         //          _token: token,
         //          id: id
         //       },
         //       success: function(data) {
         //          $('.message').empty();
         //          $('.alert').show();
         //          if (data.status == 'success') {
         //             $('.alert').addClass("alert-success");
         //          } else {
         //             $('.alert').addClass("alert-danger");
         //          }
         //          $('.message').append(data.message);
         //          table.draw();
         //       },
         //    });
         // });

         // $('body').on('click', '.successStatus', function() {
         //    var id = $(this).attr("id");
         //    var active = $(this).data("status");
         //    var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
         //    Swal.fire({
         //       title: 'Transfer details and Status',
         //       html: '<select id="statusSelect" class="swal2-select">' +
         //          '<option value="3">Success</option>' +
         //          '<option value="4">Fail</option>' +
         //          '</select>' +
         //          '<input type="text" id="utr_number" name="utr_number" class="swal2-input" placeholder="UTR Number">' +
         //          '<lable for="tds">TDS</lable> ' +
         //          ' <input type="number" id="tds" name="tds" value="10" class="swal2-input" placeholder="TDS %">' +
         //          '<input id="remark" name="remark" class="swal2-input" placeholder="Remark">',
         //       inputAttributes: {
         //          autocapitalize: 'off'
         //       },
         //       inputValue: status,
         //       inputPlaceholder: 'Select Status',
         //       showCancelButton: true,
         //       inputValidator: function(value) {
         //          return new Promise(function(resolve, reject) {
         //             if (value !== '') {
         //                resolve();
         //             } else {
         //                resolve('You need to select a Status');
         //             }
         //          });
         //       }
         //    }).then(function(result) {
         //       if (!result.dismiss) {
         //          var statusValue = $('#statusSelect').val();
         //          var utr_number = $('#utr_number').val();
         //          var tds = $('#tds').val();
         //          var remark = $('#remark').val();
         //          $.ajax({
         //             url: "{{ url('neft-redemption-change-status') }}",
         //             type: 'GET',
         //             data: {
         //                id: id,
         //                status: statusValue,
         //                utr_number: utr_number,
         //                tds: tds,
         //                remark: remark
         //             },
         //             success: function(data) {
         //                $('.message').empty();
         //                $('.alert').show();
         //                if (data.status == 'success') {
         //                   $('.alert').addClass("alert-success");
         //                } else {
         //                   $('.alert').addClass("alert-danger");
         //                }
         //                $('.message').append(data.message);
         //                table.draw();
         //             },
         //          });
         //       }
         //    });

         // });

      });
      $(function() {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         var table2 = $('#getGiftTransactionHistory').DataTable({
            processing: true,
            serverSide: true,
            "order": [
               [0, 'desc']
            ],
            "ajax": {
               'url': "{{ route('redemptions.gifttable') }}",
               'data': function(d) {
                  d.customer_id = '{{$customers["id"]}}',
                     d.redeem_mode = 1
               }
            },
            columns: [
               {
                  data: 'id',
                  name: 'id',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'created_at',
                  name: 'created_at',
                  "defaultContent": ''
               },
               {
                  data: 'customer.name',
                  name: 'customer.name',
               },
               {
                  data: 'mode',
                  name: 'mode',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'contact_person',
                  name: 'contact_person',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'parent_name',
                  name: 'parent_name',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'customer.mobile',
                  name: 'customer.mobile',
                  "defaultContent": ''
               },
               {
                  data: 'product.categories.category_name',
                  name: 'product.categories.category_name',
                  "defaultContent": ''
               },
               {
                  data: 'product.product_name',
                  name: 'product.product_name',
                  "defaultContent": ''
               },
               {
                  data: 'redeem_amount',
                  name: 'redeem_amount',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'status',
                  name: 'status',
                  "defaultContent": ''
               },
            ]
         });
         // $('body').on('click', '.ChangeStatusGift', function() {
         //    var id = $(this).attr("id");
         //    var active = $(this).data("status");
         //    var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
         //    Swal.fire({
         //       title: 'Please Select Status',
         //       input: 'select',
         //       inputOptions: {
         //          '0': 'Pendding',
         //          '1': 'Approve',
         //          '3': 'Dispatch',
         //          '2': 'Reject'
         //       },
         //       inputValue: status,
         //       inputPlaceholder: 'Select Status',
         //       showCancelButton: true,
         //       inputValidator: function(value) {
         //          return new Promise(function(resolve, reject) {
         //             if (value !== '') {
         //                resolve();
         //             } else {
         //                resolve('You need to select a Status');
         //             }
         //          });
         //       },
         //       html: '<div class="input_section"><input id="dispatch_number" name="dispatch_number" class="swal2-input" placeholder="Dispatch Number"></div>',
         //    }).then(function(result) {
         //       if (!result.dismiss) {
         //          var dispatch_number = $('#dispatch_number').val();
         //          $.ajax({
         //             url: "{{ url('redemption-change-status') }}",
         //             type: 'GET',
         //             data: {
         //                id: id,
         //                status: result.value,
         //                dispatch_number: dispatch_number
         //             },
         //             success: function(data) {
         //                $('.message').empty();
         //                $('.alert').show();
         //                if (data.status == 'success') {
         //                   $('.alert').addClass("alert-success");
         //                } else {
         //                   $('.alert').addClass("alert-danger");
         //                }
         //                $('.message').append(data.message);
         //                table2.draw();
         //             },
         //          });
         //       }
         //    });
         // });

         // $('body').on('click', '.delete', function() {
         //    var id = $(this).attr("value");
         //    var token = $("meta[name='csrf-token']").attr("content");
         //    if (!confirm("Are You sure want to delete ?")) {
         //       return false;
         //    }
         //    $.ajax({
         //       url: "{{ url('redemptions') }}" + '/' + id,
         //       type: 'DELETE',
         //       data: {
         //          _token: token,
         //          id: id
         //       },
         //       success: function(data) {
         //          $('.message').empty();
         //          $('.alert').show();
         //          if (data.status == 'success') {
         //             $('.alert').addClass("alert-success");
         //          } else {
         //             $('.alert').addClass("alert-danger");
         //          }
         //          $('.message').append(data.message);
         //          table2.draw();
         //       },
         //    });
         // });

         // $('body').on('click', '.deliveredStatus', function() {
         //    var id = $(this).attr("id");
         //    var active = $(this).data("status");
         //    var status = active == 0 ? '0' : (active == 1 ? '1' : '2');
         //    Swal.fire({
         //       title: 'Transfer details and Status',
         //       html: '<div class="input_section"><select id="statusSelect" class="swal2-select">' +
         //          '<option value="4">Delivered</option>' +
         //          '</select></div>' +
         //          '<div class="input_section"><input id="remark" name="remark" class="swal2-input" placeholder="Remark"></div>',
         //       inputAttributes: {
         //          autocapitalize: 'off'
         //       },
         //       inputPlaceholder: 'Select Status',
         //       showCancelButton: true,
         //       inputValidator: function(value) {
         //          return new Promise(function(resolve, reject) {
         //             if (value !== '') {
         //                resolve();
         //             } else {
         //                resolve('You need to select a Status');
         //             }
         //          });
         //       }
         //    }).then(function(result) {
         //       if (!result.dismiss) {
         //          var statusValue = $('#statusSelect').val();
         //          var remark = $('#remark').val();
         //          $.ajax({
         //             url: "{{ url('redemption-gift-delivered') }}",
         //             type: 'GET',
         //             data: {
         //                id: id,
         //                status: statusValue,
         //                remark: remark
         //             },
         //             success: function(data) {
         //                $('.message').empty();
         //                $('.alert').show();
         //                if (data.status == 'success') {
         //                   $('.alert').addClass("alert-success");
         //                } else {
         //                   $('.alert').addClass("alert-danger");
         //                }
         //                $('.message').append(data.message);
         //                table2.draw();
         //             },
         //          });
         //       }
         //    });

         // });
      });
   </script>
</x-app-layout>