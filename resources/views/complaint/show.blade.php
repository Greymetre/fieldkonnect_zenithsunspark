<x-app-layout>
  <style>
    .table.new-table th,
    .table.new-table td {
      border-top: 0px !important;
    }

    b {
      font-weight: 600;
    }

    .all-attach {
      align-items: center;
      border: 1px solid lightgrey;
      border-radius: 5px;
      padding: 5px 10px;
      width: 90%;
    }

    .card.blck-clr p,
    .card.blck-clr i {
      color: #4a4a4a !important;
    }

    .swal2-container.swal2-center.swal2-fade.swal2-shown {
      z-index: 999999 !important;
    }

  .invoice-info {
      margin-top: 20px;
  }
  .invoice-info .col-md-6 {
      background: #f8f9fa;
      border-radius: 11px;
      padding: 4px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  }
  .invoice-info h6 {
      font-weight: bold;
      color: #6e7c85; /* Bootstrap primary color */
      margin-bottom: 8px;
  }
  .invoice-info p {
      font-size: 14px;
      color: #333;
      background: #ffffff; /* White background for text */
      padding: 10px;
      border-radius: 5px;
      display: inline-block;
      box-shadow: inset 0px 1px 3px rgba(0, 0, 0, 0.1);
  }

  .open_status{
    background: #fbfb7a !important;
    color: black;
  }
  .open_status:hover {
      background: #f5f500 !important; /* Darker yellow on hover */
      color: #000000; /* Keep text black */
      cursor: pointer;
  }
  .done_status {
      background: #95badb !important;
      color: black;
      transition: background 0.3s, color 0.3s;
  }

  .done_status:hover {
      background: #3e6b91 !important; /* Darker shade of blue on hover */
      color: black; /* Change text color for better contrast */
      cursor: pointer;
  }
  .back_button {
      background: #29bad5 !important;
      color: black;
      transition: background 0.3s, color 0.3s;
  }

  .back_button:hover {
      background: #1f94a8 !important; /* Darker shade of cyan on hover */
      color: black; /* Change text color for better contrast */
      cursor: pointer;
  }
  .open_complaint{
      background: #f5f500 !important; /* Darker yellow on hover */
      color: #000000; /* Keep text black */
  }
  .done_complaints{
     background: #95badb !important;
      color: black;
  }

  section.content {
      position: relative;
  }

  button#toggle-btn {
      position: fixed;
      right: 10px;
      z-index: 19;
      width: 40px;
      height: 40px;
      padding: 0;
      top: 25%;
      font-size: 41px;
      border-radius: 50px;
      line-height: 20px;
  }

  </style>
  <!-- Main content -->
  <section class="content">
    <button id="toggle-btn" class="btn btn-primary mb-2"><i class="bx bx-chevron-right toggle"></i></button>
    <div class="row">
      <div class="col-9 main-content1">
        @if(Session::has('success'))
        <div class="alert alert-success" id="hide_div">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('success') !!}</strong>
        </div>
        @endif

        @if(Session::has('danger'))
        <div class="alert alert-danger" id="hide_danger">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('danger') !!}</strong>
        </div>
        @endif


        <div class="row">
          <div class="col-md-12">
              <div class="card mt-0 p-0">
                  <div class="card-header m-0 card-header-tabs card-header-warning">
                      <div class="d-flex justify-content-between align-items-center">
                          <h4 class="card-title m-0">ViewKonnect</h4>
                          <div>
                              @if(auth()->user()->hasRole(['superadmin']) || auth()->user()->hasRole(['Sub_Admin']) || auth()->user()->hasRole(['Service Admin']))
                                  @if($complaint->complaint_status == '0')
                                      @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan

                                      @can('work_done_complaint')
                                          <a href="{{ route('complaint_work_done', $complaint) }}" class="btn btn-sm done_status"><b>Work Done</b></a>
                                      @endcan

                                  @elseif($complaint->complaint_status == '1')
                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan

                                      @can('cancel_complaint')
                                          <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                                      @endcan

                                  @elseif($complaint->complaint_status == '2')
                                      @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan

                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan

                                      @can('complete_complaint')
                                          <button type="button" class="btn btn-sm btn-primary complete_status"><b>Complete</b></button>
                                      @endcan

                                  @elseif($complaint->complaint_status == '3')
                                      @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan

                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan

                                      @can('close_complaint')
                                          <button type="button" class="btn btn-sm btn-success close_status"><b>Close</b></button>
                                      @endcan

                                  @elseif($complaint->complaint_status == '5')
                                      @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan

                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan
                                  @endif

                              @else
                                  @if($complaint->complaint_status == '0')
                                     <!--  @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan -->

                                      @can('work_done_complaint')
                                          <a href="{{ route('complaint_work_done', $complaint) }}" class="btn btn-sm done_status"><b>Work Done</b></a>
                                      @endcan

                                  @elseif($complaint->complaint_status == '1')
                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan

                                  @elseif($complaint->complaint_status == '2')
                                     {{--  @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan 

                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan --}}

                                      @can('complete_complaint')
                                          <button type="button" class="btn btn-sm btn-primary complete_status"><b>Complete</b></button>
                                      @endcan

                                  @elseif($complaint->complaint_status == '3')
                                     <!--  @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan

                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan -->

                                      @can('cancel_complaint')
                                          <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                                      @endcan

                                      @can('close_complaint')
                                          <button type="button" class="btn btn-sm btn-success close_status"><b>Close</b></button>
                                      @endcan

                                  @elseif($complaint->complaint_status == '5')
                                     <!--  @can('pending_complaint')
                                          <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending</b></button>
                                      @endcan

                                      @can('open_complaint')
                                          <button type="button" class="btn btn-sm open_status"><b>Open</b></button>
                                      @endcan -->
                                  @endif
                              @endif
                              @if($complaint->complaint_status < '4' || auth()->user()->hasRole(['superadmin']) || auth()->user()->hasRole(['Sub_Admin']) || auth()->user()->hasRole(['Service Admin']))
                                @can(['complaint_edit'])
                                    <a class="btn btn-warning btn-sm" href="{{ route('complaints.edit', $complaint->id) }}"><b>Edit</b></a>
                                @endcan
                              @endif

                              <a class="btn btn-primary btn-sm back_button" href="{{ route('complaints.index') }}"><b>Back</b></a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
        <div class="alert" style="display: none;" id="hide_check">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <strong class="message"></strong>
        </div>
       <div class="card" style="color:black;">
          <div class="card-body">
              <div class="row">
                  <div class="col-md-3">
                      <label>Complaint No.</label>
                      <p class="border p-2 bg-light">#{!! $complaint['complaint_number'] !!}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Complaint Date.</label>
                      <p class="border p-2 bg-light">{!! $complaint['complaint_date'] !!}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Assign User</label>
                      <select name="assign_user" id="assign_user" {{ auth()->user()->hasRole(['Service Eng']) ? 'disabled' : ''}} class="select2 form-control">
                          <option value="">Select User</option>
                          @if($assign_users)
                              @foreach($assign_users as $assign_user)
                                  <option value="{{$assign_user->id}}" {!! old('assign_user', $complaint['assign_user'])==$assign_user->id ? 'selected':'' !!} >
                                      [{{$assign_user->employee_codes}}] {{$assign_user->name}}
                                  </option>
                              @endforeach
                          @endif
                      </select>
                      @if ($errors->has('assign_user'))
                          <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('assign_user') }}</p>
                          </div>
                      @endif
                  </div>
                  <div class="col-md-3">
                      <label>Service Center</label>
                      <select name="service_center" id="service_center" class="select2 form-control">
                          <option value="">Select Service Center</option>
                          @if($service_centers)
                              @foreach($service_centers as $service_center)
                                  <option value="{{$service_center->id}}" {!! old('service_center', $complaint['service_center'])==$service_center->id ? 'selected':'' !!} >
                                      [{{$service_center->customer_code}}] {{$service_center->name}}
                                  </option>
                              @endforeach
                          @endif
                      </select>
                      @if ($errors->has('service_center'))
                          <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('service_center') }}</p>
                          </div>
                      @endif
                  </div>
                  <div class="col-md-3">
                      <label>Complaint Status</label>
                      @if( $complaint['complaint_status'] == 0)
                         <p class="border p-2 open_complaint">OPEN</p>
                      @elseif($complaint['complaint_status'] == 1)
                         <p class="border p-2 badge-warning">PENDING</p>
                      @elseif($complaint['complaint_status'] == 2)
                         <p class="border p-2 done_complaints">WORK DONE</p>
                      @elseif($complaint['complaint_status'] == 3)
                         <p class="border p-2 badge-info">COMPLETE</p>
                      @elseif($complaint['complaint_status'] == 4)
                         <p class="border p-2 badge-success">Closed</p>
                      @elseif($complaint['complaint_status'] == 5)
                         <p class="border p-2 badge-danger">Cancelled</p>
                      @else
                        <p class="border p-2 bg-light">{!! $complaint['complaint_status'] !!}</p>
                      @endif
                  </div>
                  <div class="col-md-3">
                      <label>Created By</label>
                      <p class="border p-2 bg-light">{{$complaint->createdbyname ? $complaint->createdbyname->name : '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Close Date & Time</label>
                      <p class="border p-2 bg-light">{{$complaint['complaint_status'] == 4 ?  complaintClose($complaint->id) : "Not Closed Yet"}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Total Time Hrs Close</label>
                      <p class="border p-2 bg-light">{{$complaint['complaint_status'] == 4 ? complaintCloseIn($complaint->created_at,$complaint->id) : 'Not Closed Yet'}}</p>
                  </div>
                 <input type="hidden" name="complaint_id" id="complaint_id" value="{{$complaint->id}}">
                 <!--  Customer Details -->
                  <div class="col-md-12">
                     <div class="p-1 mb-2 text-white text-center" style="background-color: #3972af;">
                        <h4 class="m-0"><strong>Customer Details</strong></h4>
                     </div>
                  </div>
                  <div class="col-md-3">
                    <label>Customer Name</label>
                    <p class="border p-2 bg-light">{{$complaint->customer->customer_name ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Customer Number</label>
                      <p class="border p-2 bg-light">{{$complaint->customer->customer_number ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Customer Email</label>
                      <p class="border p-2 bg-light">{{$complaint->customer->customer_email != '' ? $complaint->customer->customer_email : '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>State</label>
                      <p class="border p-2 bg-light">{{$complaint->customer->customer_state ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>District</label>
                      <p class="border p-2 bg-light">{{$complaint->customer->customer_district ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>City</label>
                      <p class="border p-2 bg-light">{{$complaint->customer->customer_city ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Place</label>
                      <p class="border p-2 bg-light">{{$complaint->customer->customer_place ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Pincode</label>
                      <p class="border p-2 bg-light">{{getPincode($complaint->customer->customer_pindcode) ?? '-'}}</p>
                  </div>
                  <div class="col-md-12">
                      <label>Address</label>
                      <p class="border p-2 bg-light">{{$complaint->customer->customer_address ?? '-'}}</p>
                  </div>
                  <!--  Complaint-Type -->
                   <div class="col-md-12">
                     <div class="p-1 mb-2 text-white text-center" style="background-color: #3972af;">
                      <h4 class="m-0"><strong>Product Details</strong></h4>
                        <!-- <h4 class="m-0"><strong>Complaint-Type : {{$complaint->complaint_type_details->name ?? '-'}}</strong></h4> -->
                     </div>
                  </div>
                  <div class="col-md-3">
                    <label>Complaint-Type </label>
                    <p class="border p-2 bg-light">{{$complaint->complaint_type_details->name  ?? '-'}}</p>
                  </div>
                   <div class="col-md-3">
                    <label>Serial Number </label>
                    <p class="border p-2 bg-light">{{$complaint->product_serail_number ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Division</label>
                      <p class="border p-2 bg-light">{{$complaint->product_details->categories->category_name ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Product Group</label>
                      <p class="border p-2 bg-light">{{$complaint->product_group ??  '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Product SAP Code</label>
                      <p class="border p-2 bg-light">{{$complaint->product_details->sap_code ?? '-'}}</p>
                  </div>
                   <div class="col-md-3">
                    <label>Product Name</label>
                    <p class="border p-2 bg-light">{{$complaint->product_name ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>HP</label>
                      <p class="border p-2 bg-light">{{$complaint->specification ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Stage</label>
                      <p class="border p-2 bg-light">{{$complaint->product_no ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Phase</label>
                      <p class="border p-2 bg-light">{{$complaint->phase ?? '-'}}</p>
                  </div>
                   <div class="col-md-3">
                    <label>Model & Code </label>
                    <p class="border p-2 bg-light">{{$complaint->product_code ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Bill By Company (Party Name)</label>
                      <p class="border p-2 bg-light">{{$complaint->seller ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Company Sale Bill Date</label>
                      <p class="border p-2 bg-light">{{getDateInIndFomate($complaint->company_sale_bill_date) ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Company Sale Bill NO</label>
                      <p class="border p-2 bg-light">{{ $complaint->company_sale_bill_no ?? '-'}}</p>
                  </div>
                   <div class="col-md-3">
                    <label>Bill To Customer (Party Name)</label>
                    <p class="border p-2 bg-light">{{$complaint->party->customer_name ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Warranty / Customer Bill Date</label>
                      <p class="border p-2 bg-light">{{getDateInIndFomate($complaint->customer_bill_date) ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Customer Bill No.</label>
                      <p class="border p-2 bg-light">{{$complaint->customer_bill_no ?? '-'}}</p>
                  </div>
                   <div class="col-md-3">
                      <label>Warranty Upto</label>
                      <p class="border p-2 bg-light">{{$response['warrenty_expire_date'] ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Warranty Status</label>
                      <p class="border p-2 {{$complaint->under_warranty == 'Yes' ? 'badge-success' : 'badge-danger'}}">
                        {{$complaint->under_warranty ?? '' }}
                      </p>
                  </div>
                   <div class="col-md-3">
                    <label>Warranty/Bill</label>
                    <p class="border p-2 bg-light">{{$complaint->warranty_bill ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Service Paid/Free</label>
                      <p class="border p-2 bg-light">{{$complaint->service_type ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Total Warranty Product In Month</label>
                      <p class="border p-2 bg-light">{{$complaint->product_details->expiry_interval_preiod  ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Service Branch</label>
                      <p class="border p-2 bg-light">{{$complaint->purchased_branch_details->branch_code ?? '-'}} {{$complaint->purchased_branch_details->branch_name ?? '-'}}</p>
                  </div>
                   <div class="col-md-3">
                    <label>Complaint Register By</label>
                    <p class="border p-2 bg-light">{{$complaint->register_by ?? '-'}}</p>
                  </div>
                  <div class="col-md-3">
                      <label>Product Laying at</label>
                      <p class="border p-2 bg-light">{{$complaint->product_laying ?? '-'}}</p>
                  </div>
                   <div class="col-md-4">
                      <label>CRM Remark</label>
                      <p class="border p-2 bg-light">{{$complaint->description ?? '-'}}</p>
                  </div>
                  <div class="col-md-4">
                    <label>Warranty Card Invoice</label>
                    <div class="d-flex flex-wrap align-items-center all-attach">
                        @if($complaint->warranty_details)
                            @php
                                $mediaItems = $complaint->warranty_details->getMedia('warranty_activation_attach');
                            @endphp

                            @if($mediaItems->count() > 0)
                                @foreach($mediaItems as $media)
                                    @php $mediaUrl = $media->getFullUrl(); @endphp
                                    
                                    <a href="{{ $mediaUrl }}" target="_blank" class="m-1">
                                        <img width="80" height="80" class="img-fluid rounded border" 
                                             src="{{ $media->mime_type == 'application/pdf' ? asset('assets/img/pdf-icon.jpg') : $mediaUrl }}">
                                    </a>
                                @endforeach
                            @else
                                <img width="80" height="80" class="img-fluid rounded border" src="{{ asset('assets/img/placeholder.jpg') }}">
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Product Photo -->
                <div class="col-md-4">
                    <label>Product Photo</label>
                    <div class="d-flex flex-wrap align-items-center all-attach">
                        @if($complaint->exists && $complaint->getMedia('complaint_attach')->count() > 0)
                            @foreach($complaint->getMedia('complaint_attach') as $media)
                                <a href="{{ $media->getFullUrl() }}" target="_blank" class="m-1">
                                    <img width="80" height="80" class="img-fluid rounded border" 
                                         src="{{ $media->mime_type == 'application/pdf' ? asset('assets/img/pdf-icon.jpg') : $media->getFullUrl() }}">
                                </a>
                            @endforeach
                        @else
                            <p class="border p-2 bg-light">No Image Found</p>
                        @endif
                    </div>
                </div>

                <!-- Work Done Status -->
                <div class="col-md-12 mt-2">
                   <div class="p-1 mb-2 text-white text-center" style="background-color: #3972af;">
                      <h4 class="m-0"><strong>Work Done Details</strong></h4>
                   </div>
                </div>
                <div class="col-md-3">
                    <label>Action Done by ASC</label>
                    <p class="border p-2 bg-light">{{$work_done?$work_done->done_by:'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Service Center Remark</label>
                    <p class="border p-2 bg-light">{{$work_done?$work_done->remark:'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Work Done Date</label>
                    <p class="border p-2 bg-light">{{isset($work_done->created_at) ? getDateInIndFomate($work_done->created_at) :   'Not Done Yet'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Attachment</label>
                     @if($work_done && $work_done->getMedia('complaint_work_done_attach')->count() > 0)
                        @php
                            $mediaItems = $work_done->getMedia('complaint_work_done_attach');
                        @endphp

                        <div class="d-flex flex-wrap align-items-center all-attach">
                            @foreach($mediaItems as $media)
                                <a href="{{ $media->getFullUrl() }}" target="_blank" class="m-1">
                                    <img width="80" height="80" class="img-fluid rounded border"
                                         src="{{ $media->mime_type == 'application/pdf' ? asset('assets/img/pdf-icon.jpg') : $media->getFullUrl() }}">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="border p-2 bg-light">No Attachments Found</p>
                    @endif
                </div>

                <!-- Compleate and Close Details -->
                <div class="col-md-12 mt-2">
                   <div class="p-1 mb-2 text-white text-center" style="background-color: #3972af;">
                      <h4 class="m-0"><strong>Complete & Close Details</strong></h4>
                   </div>
                </div>
                <div class="col-md-3">
                    <label>Replacement Tag</label>
                    <p class="border p-2 bg-light">{{isset($service_bill->replacement_tag) ?$service_bill->replacement_tag:'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Replacement Tag No</label>
                    <p class="border p-2 bg-light">{{isset($service_bill->replacement_tag_number)?$service_bill->replacement_tag_number:'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Complete Remark</label>
                    <p class="border p-2 bg-light">{{$complete_complaint?$complete_complaint->remark:'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Close Remark</label>
                    <p class="border p-2 bg-light">{{$close_complaint?$close_complaint->remark:'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Category Of Complaint</label>
                    <p class="border p-2 bg-light">{{$service_bill->category  ??'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Complaint Type</label>
                     <p class="border p-2 bg-light">{{$service_bill->complaint_type ??'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Complaint Reason</label>
                     <p class="border p-2 bg-light">{{$service_bill->complaint_reason ?? '-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Condition Of Service</label>
                     <p class="border p-2 bg-light">{{$service_bill->condition_of_service??'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Received Product</label>
                    <p class="border p-2 bg-light">{{$service_bill->received_product??'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Nature Of Fault</label>
                     <p class="border p-2 bg-light">{{$service_bill->nature_of_fault??'-'}}</p>
                </div>
                <div class="col-md-3">
                    <label>Service Location</label>
                     <p class="border p-2 bg-light">{{$service_bill->service_location??'-'}}</p>
                </div>
                 <!-- Service Bill Details -->
                <div class="col-md-12 mt-2">
                   <div class="p-1 mb-2 text-white text-center" style="background-color: #3972af;">
                      <h4 class="m-0"><strong>Service Bill Details</strong></h4>
                   </div>
                </div>
                <div class="col-md-12">
                    <table class="table table-striped responsive">
                      <tr>
                        <th>Serial No.</th>
                        <th>Total Amount</th>
                        <th>Service Bill Date</th>
                        <th>Service Bill Status</th>
                      </tr>
                      <tr>
                        <td>{{strtoupper($complaint->product_serail_number)}}</td>
                        <td>{{$service_bill?$service_bill->service_bill_products->sum('subtotal'):'-'}}</td>
                        <td>{{$service_bill? getDateInIndFomate($service_bill->created_at):'-'}}</td>
                        @if($service_bill && !empty($service_bill))
                        @if($service_bill->status == '0')
                        <td><a href="{{route('service_bills.show', $service_bill->id)}}" title="Show Service Bill"><span class="badge badge-secondary">Draft</span></td>
                        @elseif($service_bill->status == '1')
                        <td><a href="{{route('service_bills.show', $service_bill->id)}}" title="Show Service Bill"><span class="badge badge-warning">Claimed</span></a></td>
                        @elseif($service_bill->status == '2')
                        <td><a href="{{route('service_bills.show', $service_bill->id)}}" title="Show Service Bill"><span class="badge badge-info">Customer payble</span></a></td>
                        @elseif($service_bill->status == '3')
                        <td><a href="{{route('service_bills.show', $service_bill->id)}}" title="Show Service Bill"><span class="badge badge-success">Approve</span></a></td>
                        @elseif($service_bill->status == '4')
                        <td><a href="{{route('service_bills.show', $service_bill->id)}}" title="Show Service Bill"><span class="badge badge-danger">Cancel</span></a></td>
                        @endif
                        @else
                        <td>-</td>
                        @endif
                      </tr>

                    </table>
                </div>
              </div>
          </div>
      </div>
      </div>



      <div class="col-3 sidebar1">

        <div class="card blck-clr">
          <div class="card-body">

            <div class="row">

              <div class="col-12">

                <h3 class="card-title pb-3">Time Line</h3>
                @if(auth()->user()->can(['add_complaint_notes']))
                <button class="btn btn-sm btn-primary w-50 mb-3" id="openModalButton">
                   + Add Notes
                </button>
                @endif
                <hr>
                <p class="lead"></p>
                @if(count($timelines) > 0)
                @foreach($timelines as $timeline)
                @if($timeline->status == '100')
                @php $assign_user = App\Models\User::find($timeline->remark); @endphp
                @elseif($timeline->status == '101')
                @php $assign_customer = App\Models\Customers::find($timeline->remark); @endphp
                @elseif($timeline->status == '0')
                @php $status_is = 'Open'; @endphp
                @elseif($timeline->status == '1')
                @php $status_is = 'Pending'; @endphp
                @elseif($timeline->status == '2')
                @php $status_is = 'Work Done'; @endphp
                @elseif($timeline->status == '3')
                @php $status_is = 'Completed'; @endphp
                @elseif($timeline->status == '4')
                @php $status_is = 'Closed'; @endphp
                @elseif($timeline->status == '5')
                @php $status_is = 'Canceled'; @endphp
                @elseif($timeline->status == '587')
                @php $status_is = 'Message'; @endphp
                @else
                @php $status_is = '-'; @endphp
                @endif


                @if($timeline->status == '102')
                <div class="d-flex">
                  <i class="material-icons">double_arrow</i>
                  <p>
                    Complaint <b> {!! $complaint['complaint_number'] !!} </b> {{$timeline->remark}} by <b>{{$timeline->created_by_details->name ?? ''}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}}.</b>
                  </p>
                </div>
                @elseif($timeline->status == 'Note')
                <div class="d-flex">
                  <i class="material-icons">double_arrow</i>
                  <p>
                    Complaint <b> {!! $complaint['complaint_number'] !!} </b> {{$timeline->remark}} by <b>{{$timeline->created_by_details->name ?? ''}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}}.</b>
                  </p>
                </div>
                @elseif($timeline->status == '587')
                <div class="d-flex">
                  <i class="material-icons">double_arrow</i>
                  <p>
                    Sent Message  <b> {!! $complaint['complaint_number'] !!} </b> {{$timeline->remark}} by <b>{{$timeline->created_by_details->name ?? ''}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}} to Customer.</b>
                  </p>
                </div>

                @else
                @if($timeline->status == '100' || $timeline->status == '101')
                @if($timeline->status == '100')
                <div class="d-flex">
                  <i class="material-icons">double_arrow</i>
                  <p>
                    Complaint <b> {!! $complaint['complaint_number'] !!} </b> assign to <b>{{$assign_user->name}}</b> by <b>{{$timeline->created_by_details->name ?? ''}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}}.</b>
                  </p>
                </div>
                @else
                <div class="d-flex">
                  <i class="material-icons">double_arrow</i>
                  <p>
                    @if($assign_customer == NULL)
                    Complaint <b> {!! $complaint['complaint_number'] !!} </b> Unassign Service Center by <b>{{$timeline->created_by_details->name}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}}.</b>
                    @else
                    Complaint <b> {!! $complaint['complaint_number'] !!} </b> assign to <b>{{$assign_customer->name}}</b> Service Center by <b>{{$timeline->created_by_details->name ?? ''}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}}.</b>
                    @endif
                  </p>
                </div>
                @endif
                @else
                <div class="d-flex">
                  <i class="material-icons">double_arrow</i>
                  <p>
                    Complaint <b> {!! $complaint['complaint_number'] !!} </b> moved to <b>{{$status_is}}</b> by <b>{{$timeline->created_by_details->name ?? ''}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}}.</b>
                  </p>
                </div>
                @endif
                @endif
                @endforeach
                @endif

                <p>
                  <b> #{!! $complaint['complaint_number'] !!} Created by {{ $complaint->createdbyname->name?? '' }} At {{date("d M Y, h:i a", strtotime($complaint->created_at));}} 
                </p>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- new model for reject status -->

      <div class="modal fade bd-example-modal-lg" id="reject_expense" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content card">
            <div class="card-header card-header-icon card-header-theme">
              <div class="card-icon">
                <i class="material-icons">perm_identity</i>
              </div>
              <h4 class="card-title">
                <span class="modal-title">Submit </span> Reject <span class="pull-right">
                  <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                    <i class="material-icons">clear</i>
                  </a>
                </span>
              </h4>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ route('cancelComplaint') }}" enctype="multipart/form-data" id="createleadstagesForm_new"> @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Reason</label>
                      <input type="text" name="reason" id="reason" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                      <input type="text" name="cancel_complaint_id" id="cancel_complaint_id" class="form-control" hidden>
                    </div>
                  </div>
                </div>
                <button class="btn btn-info save">Reject</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- end model for status -->


      <!-- new model for approve status -->

      <div class="modal fade bd-example-modal-lg" id="approve_expense" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content card">
            <div class="card-header card-header-icon card-header-theme">
              <div class="card-icon">
                <i class="material-icons">perm_identity</i>
              </div>
              <h4 class="card-title">
                <span class="modal-title">Submit </span> Approve <span class="pull-right">
                  <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                    <i class="material-icons">clear</i>
                  </a>
                </span>
              </h4>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ route('approveExpense') }}" enctype="multipart/form-data" id="createleadstagesForms"> @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Approve Amount</label>
                      <input type="text" name="approve_amnt" id="approve_amnt" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                      <input type="text" name="expense_new_id" id="expense_new_id" class="form-control" hidden>
                    </div>

                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Reason</label>
                      <input type="text" name="reasons" id="reasons" class="form-control" value="{!! old( 'reasons') !!}"> <br><br>
                    </div>


                  </div>
                </div>
                <button class="btn btn-info save">Approve</button>
              </form>
            </div>
          </div>
        </div>
      </div>
     <div class="modal fade bd-example-modal-lg" id="addTask" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card">
          <div class="card-header card-header-icon card-header-theme">
            <h4 class="card-title">
              <span class="modal-title">{!! trans('panel.global.add') !!}</span> Notes
              <span class="pull-right">
                <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" id="closeModalButton">
                  <i class="material-icons">clear</i>
                </a>
              </span>
            </h4>
          </div>
          <div class="modal-body">
            <form method="POST" action="{{route('complaint_add_notes')}}" enctype="multipart/form-data" id="createWarehouseForm">
              @csrf
              <div class="row">
                <div class="col-md-12">
                  <div class="input_section">
                    <label class="col-form-label">Notes <span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="hidden" name="complaint_id" id="complaint_id" class="form-control" value="{{$complaint->id}}">
                      <input type="text" name="complaint_notes" id="complaint_notes" class="form-control" value="{!! old('complaint_task') !!}" maxlength="200" required>
                      @if ($errors->has('complaint_task'))
                        <div class="error"><p class="text-danger">{{ $errors->first('complaint_notes') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="pull-right">
                <input type="hidden" name="id" id="warehouse_id" />
                <button class="btn btn-info save"> Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
      <!-- end model for status -->

      <!-- Custom styles for this page -->
      <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

      <script src="{{ url('/').'/'.asset('lightboxx/js/lightbox-plus-jquery.min.js') }}"></script>

      <!-- for checked -->
      <script>
        $(document).ready(function() {
            $("#toggle-btn").click(function() {
                $(".sidebar1").toggle();
                if ($(".sidebar1").is(":visible")) {
                    $(".main-content1").removeClass("col-md-12").addClass("col-md-9");
                } else {
                    $(".main-content1").removeClass("col-md-9").addClass("col-md-12");
                }
                let icon = this.querySelector(".toggle");
                if (icon.classList.contains("bx-chevron-right")) {
                    icon.classList.replace("bx-chevron-right", "bx-chevron-left");
                } else {
                    icon.classList.replace("bx-chevron-left", "bx-chevron-right");
                }
            });

              $("#openModalButton").click(function () {
                $("#addTask").modal("show");
              });
               $("#closeModalButton").click(function () {
                $("#addTask").modal("hide");
              });

              // Close Modal when clicking outside (optional)
              $("#addTask").on("click", function (e) {
                if ($(e.target).hasClass("modal")) {
                  $("#addTask").modal("hide");
                }
              });
        });
      </script>
      <script type="text/javascript">
        var token = $("meta[name='csrf-token']").attr("content");
        $('body').on('click', '.open_status', function() {
          var id = $('#complaint_id').val();
          $.ajax({
            url: "{{ url('complaint-open') }}",
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
                setTimeout(function() {
                  location.reload();
                }, 500);

              } else {
                $('.alert').addClass("alert-danger");
                // setTimeout(function() {
                //   location.reload();
                // }, 3000);
              }
              $('.message').append(data.message);
            },
          });
        });

        $('body').on('click', '.pending_status', function() {
          var id = $('#complaint_id').val();
          $.ajax({
            url: "{{ url('complaint-pending') }}",
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
                setTimeout(function() {
                  location.reload();
                }, 500);

              } else {
                $('.alert').addClass("alert-danger");
                // setTimeout(function() {
                //   location.reload();
                // }, 3000);
              }
              $('.message').append(data.message);
            },
          });
        });

        $('body').on('click', '.cancel_status', function() {
          var id = $('#complaint_id').val();
          $.ajax({
            url: "{{ url('complaint-cancel') }}",
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
                setTimeout(function() {
                  location.reload();
                }, 500);

              } else {
                $('.alert').addClass("alert-danger");
                // setTimeout(function() {
                //   location.reload();
                // }, 3000);
              }
              $('.message').append(data.message);
            },
          });
        });

        $('body').on('click', '.close_status', function() {
          var id = $('#complaint_id').val();

          Swal.fire({
            title: 'Enter your remark',
            input: 'text',
            inputPlaceholder: 'Remark',
            showCancelButton: true,
            inputValidator: (value) => {
              if (!value) {
                return 'You need to write something!';
              }
            }
          }).then((result) => {
            console.log(result);
            if (result.value) {
              $.ajax({
                url: "{{ url('complaint-close') }}",
                type: 'POST',
                data: {
                  _token: token,
                  id: id,
                  remark: result.value
                },
                success: function(data) {
                  if (data.status == 'success') {
                    $('.message').empty();
                    $('.alert').show();
                    $('.alert').addClass("alert-success");
                    $('.message').append(data.message);
                    setTimeout(function() {
                      location.reload();
                    }, 700);
                  } else {
                    $('.message').empty();
                    $('.alert').show();
                    $('.alert').addClass("alert-danger");
                    $('.message').append(data.message);
                  }
                }
              })
            }
          });
        });

        $('body').on('click', '.complete_status', function() {
          var id = $('#complaint_id').val();
          $.ajax({
            url: "{{ url('check-complaint-complete') }}",
            type: 'POST',
            data: {
              _token: token,
              id: id
            },
            success: function(data) {
              if (data.status == 'success') {
                Swal.fire({
                  title: 'Enter your remark',
                  input: 'text',
                  inputPlaceholder: 'Remark',
                  showCancelButton: true,
                  inputValidator: (value) => {
                    if (!value) {
                      return 'You need to write something!';
                    }
                  }
                }).then((result) => {
                  console.log(result);
                  if (result.value) {
                    $.ajax({
                      url: "{{ url('complaint-complete') }}",
                      type: 'POST',
                      data: {
                        _token: token,
                        id: id,
                        remark: result.value
                      },
                      success: function(data) {
                        if (data.status == 'success') {
                          $('.message').empty();
                          $('.alert').show();
                          $('.alert').addClass("alert-success");
                          $('.message').append(data.message);
                          setTimeout(function() {
                            location.reload();
                          }, 700);
                        } else {
                          $('.message').empty();
                          $('.alert').show();
                          $('.alert').addClass("alert-danger");
                          $('.message').append(data.message);
                        }
                      }
                    })
                  }
                });
              } else {
                $('.message').empty();
                $('.alert').show();
                $('.alert').addClass("alert-danger");
                $('.message').append(data.message);
              }
            },
          });
        });

        $(document).on('change', '#assign_user', function() {
          var user_id = $(this).val();
          var complaint_id = $('#complaint_id').val();
          $.ajax({
            url: "{{ url('complaint-assign-user') }}",
            type: 'POST',
            data: {
              _token: token,
              user_id: user_id,
              complaint_id: complaint_id
            },
            success: function(data) {
              $('.message').empty();
              $('.alert').show();
              if (data.status == 'success') {
                $('.alert').addClass("alert-success");
                setTimeout(function() {
                  location.reload();
                }, 1000);

              } else {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
            },
          });
        })

        $(document).on('change', '#service_center', function() {
          var service_center_id = $(this).val();
          var complaint_id = $('#complaint_id').val();
          $.ajax({
            url: "{{ url('complaint-assign-service-center') }}",
            type: 'POST',
            data: {
              _token: token,
              service_center_id: service_center_id,
              complaint_id: complaint_id
            },
            success: function(data) {
              $('.message').empty();
              $('.alert').show();
              if (data.status == 'success') {
                $('.alert').addClass("alert-success");
                setTimeout(function() {
                  location.reload();
                }, 1000);

              } else {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
            },
          });
        })
      </script>
  </section>
  <!-- /.content -->
</x-app-layout>