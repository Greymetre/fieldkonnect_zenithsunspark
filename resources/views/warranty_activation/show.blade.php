<x-app-layout>
   <style>
      .theadl {
         font-weight: 900;
      }

      li.nav-item {
         border-left: 1px solid;
      }
   </style>
   <div class="row Warranty Activation ">
      <div class="col-md-12">
         <div class="card card m-0 p-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        Warranty Activation > {{$warrantyactivation->customer->customer_name}}      </h4>
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           @if($warrantyactivation->status == '0')
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="1" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">check_circle</i> Activated
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="2" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">check</i> Pending Activated
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="3" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">cancel</i> Reject
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           @elseif($warrantyactivation->status == '1')
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="0" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">schedule</i> In Verification
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="2" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">check</i> Pending Activated
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="3" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">cancel</i> Reject
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           @elseif($warrantyactivation->status == '2')
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="3" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">cancel</i> Reject
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="0" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">schedule</i> In Verification
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                           <a class="nav-link" onclick="changeStatus(this)" data-status="1" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">check_circle</i> Activated
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           @elseif($warrantyactivation->status == '3')
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="0" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">schedule</i> In Verification
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" data-status="1" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">check_circle</i> Activated
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" onclick="changeStatus(this)" data-status="2" data-waid="{{$warrantyactivation->id}}" href="#">
                                 <i class="material-icons">check</i> Pending Activated
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           @endif
                           <li class="nav-item">
                              <a class="nav-link" href="{{ route('warranty_activation.edit', encrypt($warrantyactivation->id)) }}">
                                 <i class="material-icons">edit</i> Edit
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('warranty_activation'). '?status_is=' . $warrantyactivation->status }}">
                                 <i class="material-icons">next_plan</i> Back
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                        </ul>
                        @endif
                
                  </div>
               </div>
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
               <div class="row">
                  <div class="col-md-12">
                     <h6>Warranty Activation Status</h6>
                     @if($warrantyactivation->status == '0')
                     <p>In Verification</p>
                     @elseif($warrantyactivation->status == '1')
                     <p>Activated</p>
                     @else
                     <p>Pending Activated</p>
                     @endif
                  </div>
                  <div class="col-md-9">
                     <h3>Contact Details</h3>
                     <hr>
                     <div class="row">
                        <div class="col-md-4">
                           <h6>Name</h6>
                           <p>{{$warrantyactivation->customer->customer_name}}</p>
                        </div>
                        <div class="col-md-4">
                           <h6>Email</h6>
                           <p>{{$warrantyactivation->customer->customer_email}}</p>
                        </div>
                        <div class="col-md-4">
                           <h6>Contact</h6>
                           <p>{{$warrantyactivation->customer->customer_number}}</p>
                        </div>
                     </div>
                     <h3 class="mt-4">Warranty Details</h3>
                     <hr>
                     <div class="row">
                        @php
                        $expire_count = $warrantyactivation['product_details']?$warrantyactivation['product_details']['expiry_interval_preiod']:"18";
                        $expire_type = $warrantyactivation['product_details']?strtolower($warrantyactivation['product_details']['expiry_interval'].'s'):"months";
                        @endphp
                        <div class="col-md-3">
                           <h6>Product Serail Number</h6>
                           <p>{{$warrantyactivation->product_serail_number}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Product</h6>
                           <p>{{$warrantyactivation->product_details?$warrantyactivation->product_details->product_name:''}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Start Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation->created_at))}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>End Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation['created_at'] . ' +'.$expire_count.' '.$expire_type))}}</p>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-3">
                           <h6>Seller</h6>
                           @if($warrantyactivation->seller_details)
                           <p><a href="{{route('customers.show', encrypt($warrantyactivation->seller_details->id))}}">[{{$warrantyactivation->seller_details->id}}] {{$warrantyactivation->seller_details->name}}</a></p>
                           <p>{{$warrantyactivation->seller_details->mobile}}</p>
                           @endif
                        </div>
                        <div class="col-md-3">

                        </div>
                     </div>
                     <h3 class="mt-4">Warranty Details</h3>
                     <hr>
                     <div class="row">
                        <div class="col-md-3">
                           <h6>Dealer</h6>
                           @if(count($warrantyactivation->seller_details->getparentdetail) > 0)
                           @foreach($warrantyactivation->seller_details->getparentdetail as $pdetails)
                           <p>{{$pdetails->parent_detail?$pdetails->parent_detail->name:''}}</p>
                           @endforeach
                           @else
                           <p>-</p>
                           @endif
                        </div>
                        <div class="col-md-3">
                           <h6>Sale Bill Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation->sale_bill_date))}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Dealer Waranty Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation->warranty_date))}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Co Sale Bill No.</h6>
                           <p>{{$warrantyactivation->sale_bill_no}}</p>
                        </div>
                     </div>
                     <h3 class="mt-4">Invoice Attachment</h3>
                     <hr>
                     <div class="row text-center">

                        @if($warrantyactivation->exists && $warrantyactivation->getMedia('warranty_activation_attach')->count() > 0 && Storage::disk('s3')->exists($warrantyactivation->getFirstMedia('warranty_activation_attach')->getPath()))
                        <a href="{!! $warrantyactivation->getMedia('warranty_activation_attach')[0]->getFullUrl() !!}" data-lightbox="mygallery" data-title="Invoice">
                           <img width="250" src="{!! $warrantyactivation->getMedia('warranty_activation_attach')[0]->getFullUrl() !!}" class="img-fluid rounded"></a>
                        @else
                        <img width="250" style="border-radius: 5px;" src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3 rounded">
                     <h5 class="theadl">Timeline</h5>
                     <div class="bg-secondar" style="border: 1px dashed #a7a3a3;;padding: 10px; box-shadow: 0px 0px 10px 10px lightgray; border-radius: 5px;">
                        <h5 class="theadl">PLANNED</h5>
                        <p class="text-center text-wrap">
                           You don't have any Schedule activity.
                        </p>
                        <h5 class="theadl mt-5">PAST</h5>
                        @if(count($warranty_timeline) > 0)
                        @foreach($warranty_timeline as $timeline)
                        @if($timeline->status == '0')
                        @php $status = 'In Verification'; @endphp
                        @elseif($timeline->status == '1')
                        @php $status = 'Activated'; @endphp
                        @elseif($timeline->status == '2')
                        @php $status = 'Pending'; @endphp
                        @elseif($timeline->status == '3')
                        @php $status = 'Rejected'; @endphp
                        @endif
                        <div class="d-flex"><i class="material-icons">military_tech</i>
                           @if($timeline->status == '0')
                           <p>Warranty <span class="theadl">{{$warrantyactivation->product_serail_number}}</span> Move to {{$status}}. By <span class="theadl">{{$timeline->createdByName->name}}</span> on {{date('d M Y h:i A', strtotime($timeline->created_at))}}</p>
                           @else
                           <p>Warranty <span class="theadl">{{$warrantyactivation->product_serail_number}}</span> {{$status}}. By <span class="theadl">{{$timeline->createdByName->name}}</span> on {{date('d M Y h:i A', strtotime($timeline->created_at))}}</p>
                           @endif
                        </div>
                        @endforeach
                        @endif
                        <div class="d-flex"><i class="material-icons">military_tech</i>
                           @if($warrantyactivation->created_by)
                           <p>Warranty <span class="theadl">{{$warrantyactivation->product_serail_number}}</span> generated by <span class="theadl">{{$warrantyactivation->createdByName->name}}</span> on {{date('d M Y h:i A', strtotime($warrantyactivation->created_at))}}</p>
                           @else
                           <p>Warranty <span class="theadl">{{$warrantyactivation->product_serail_number}}</span> generated by <span class="theadl">{{$warrantyactivation->seller_details->name}}</span> on {{date('d M Y h:i A', strtotime($warrantyactivation->created_at))}}</p>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
   <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">
   <script src="{{ url('/').'/'.asset('lightboxx/js/lightbox-plus-jquery.min.js') }}"></script>

   <script>
      function changeStatus(element) {
         var id = $(element).data('waid');
         var status = $(element).data('status');
         if (status == '3') {
            Swal.fire({
               title: "ARE YOU SURE TO CHANGE STATUS?",
               input: 'text',
               inputPlaceholder: 'Enter your reason',
               inputAttributes: {
                  maxlength: 100,
                  autocapitalize: 'off',
                  autocorrect: 'off'
               },
               showDenyButton: true,
               showCancelButton: true,
               confirmButtonText: "Yes",
               denyButtonText: `No`,
               preConfirm: (value) => {
                  if (!value) {
                     $('.swal2-input').css('border', '1px solid red');
                     return false;
                  }
                  return value;
               }
            }).then((result) => {
               if (result.value) {
                  $.ajax({
                     url: "{{ url('warranty-status-change') }}",
                     dataType: "json",
                     type: "POST",
                     data: {
                        _token: "{{csrf_token()}}",
                        id: id,
                        status: status,
                        remark: result.value
                     },
                     success: function(res) {
                        if (res.status === true) {
                           Swal.fire("Status Update successfully!", res.msg, "success");
                           setTimeout(() => {
                              location.reload();
                           }, 3000);
                        } else {
                           Swal.fire("Somthing went wrong", "", "error");
                        }
                     }
                  });
               }
            })
         } else {
            Swal.fire({
               title: "ARE YOU SURE TO CHANGE STATUS ?",
               showDenyButton: true,
               showCancelButton: true,
               confirmButtonText: "Yes",
               denyButtonText: `No`
            }).then((result) => {
               console.log(result);
               if (result.value) {
                  $.ajax({
                     url: "{{ url('warranty-status-change') }}",
                     dataType: "json",
                     type: "POST",
                     data: {
                        _token: "{{csrf_token()}}",
                        id: id,
                        status: status
                     },
                     success: function(res) {
                        if (res.status === true) {
                           Swal.fire("Status Update successfully!", res.msg, "success");
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
         }
      }
   </script>

</x-app-layout>