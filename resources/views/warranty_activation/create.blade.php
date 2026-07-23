<x-app-layout>
   <style>
      .select2-results__options {
         overflow: auto;
         max-height: 200px !important;
      }

      .select2-results,
      .select2-search--dropdown,
      .select2-dropdown--above {
         min-width: 250px !important;
      }

      .select2-container {
         border-bottom: 1px solid lightgray;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card mt-0 p-0">
            <div class="card-header  m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        Warranty Activation Creation </h4>
                     @if(auth()->user()->can(['district_access']))
                     <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                        <li class="nav-item">
                           <a class="nav-link" href="javascript:void(0);" onclick="window.history.back();">
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
               {!! Form::model($warranty_activation,[
               'route' => $warranty_activation->exists ? ['warranty_activation.update', encrypt($warranty_activation->id) ] : 'warranty_activation.store',
               'method' => $warranty_activation->exists ? 'PUT' : 'POST',
               'id' => 'storeTransactionHistoryData',
               'files'=>true
               ]) !!}
               <div class="form-group">
                  <!-- <div class="row">
                     <div class="col-md-6">
                        <div class="input_section">
                           <label for="status" class="col-form-label">Warranty Activation Status</label>
                           <input type="hidden" name="previous_url" placeholder="Warranty Activation Status" value="{{ strtok(url()->previous(), '?') }}">
                        </div>
                     </div>

                     
                     <div class="col-md-6 reject-remark d-none">
                        <div class="input_section">
                           <label for="remark" class="col-form-label">Reject Remark <span class="text-danger">*</span></label>

                           <textarea name="remark" id="remark" class="form-control">{{$warranty_activation->remark??""}}</textarea>
                        </div>
                     </div>
                  </div> -->
                  <div class="mt-2">
                     <h5 class="newdata">Warranty Details</h5>
                     <input type="hidden" name="back" value="{{ isset($back) ? $back : false }}">
                     <input type="hidden" name="warranty_id" value="{{$warranty_activation->id}}">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="product_serail_number" class="col-form-label">Product Serial Number</label>

                              <input type="text" name="product_serail_number" id="product_serail_number" class="form-control" value="{!! old( 'product_serail_number' , $warranty_activation['product_serail_number'])??(isset($request)?$request->serial_no:'') !!}">
                              @if ($errors->has('product_serail_number'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('product_serail_number') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="product_id" class="col-form-label">Product</label>

                              <select name="select_product_id" id="select_product_id" placeholder="Select Product" class="select2 form-control">
                                 <option value="">Select Product</option>
                                 @foreach($products as $product)
                                     <option value="{{ $product->id }}"
                                         @if(isset($warranty_activation->product_details->id) && $product->id == $warranty_activation->product_details->id) selected @endif>
                                         {{ $product->product_name }}
                                     </option>
                                 @endforeach
                                 <!-- @if($warranty_activation->exists && $warranty_activation->product_details)
                                 <option value="{{$warranty_activation->product_details->id}}" selected>{{$warranty_activation->product_details->product_name}}</option>
                                 @endif -->
                              </select>
                              <input type="hidden" name="product_id" id="product_id">
                              @if ($errors->has('product_id'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('product_id') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>


                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="branch_id"  class="col-form-label">Branch</label>

                              <select name="branch_id" id="branch_id" placeholder="Select Branch" class="select2 form-control" required>
                                 <option value="" disabled selected>Select Branch</option>
                                 @if($branches && count($branches) > 0)
                                 @foreach($branches as $branche)
                                 <option value="{{$branche->id}}" {!! old( 'branch_id' , $warranty_activation['branch_id'])==$branche->id?'selected':'' !!}>{{$branche->branch_name}}({{$branche->branch_code}})</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if ($errors->has('branch_id'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        <input type="hidden" name="status" id="status" value="1">
                      <!--   <div class="col-md-6">
                           <div class="input_section">
                              <label class="col-form-label">Select Status</label>
                              <select name="status" id="status" class="select2" required>
                                 <option value="">Select Status</option>
                                 <option value="0" {{($warranty_activation->exists && $warranty_activation->status == '0')?'selected': (isset($status_flag) && $status_flag == '0' ? 'selected' : '' )}}>In Verification</option>
                                 <option value="1" {{($warranty_activation->exists  && $warranty_activation->status == '1') ? 'selected': (isset($status_flag) && $status_flag == '1' ? 'selected' : '' )}}>Activated</option>
                                 <option value="2" {{($warranty_activation->exists && $warranty_activation->status == '2')?'selected':''}}>Pending Activated</option>
                                 <option value="3" {{($warranty_activation->exists && $warranty_activation->status == '3')?'selected':''}}>Rejected</option>
                              </select>
                           </div>
                        </div> -->
 
                      
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="party_name" class="col-form-label">Bill By Company (Party Name)</label>
                              <input type="hidden" id="party_name_input" name="party_name_input">
                              <select name="party_name" id="party_name" placeholder="Select Customers" class="select2 form-control" required>
                                 <option value="" disabled selected>Select Customer</option>
                                <!--  @if($customers && count($customers) > 0)
                                 @foreach($customers as $customer)
                                 <option value="{{$customer->id}}" {!! old( 'customer_id' , $warranty_activation['customer_id'])==$customer->id?'selected':'' !!}>{{$customer->name}}({{$customer->mobile}})</option>
                                 @endforeach
                                 @endif -->
                              </select>
                              @if ($errors->has('customer_id'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                          <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_id" class="col-form-label">Bill To Customer (Party Name)</label>

                              <select name="customer_id" id="customer_id" placeholder="Select Customers" class="select2 form-control" required>
                                 <option value="" disabled selected>Select Customer</option>
                                <!--  @if($customers && count($customers) > 0)
                                 @foreach($customers as $customer)
                                 <option value="{{$customer->id}}" {!! old( 'customer_id' , $warranty_activation['customer_id'])==$customer->id?'selected':'' !!}>{{$customer->name}}({{$customer->mobile}})</option>
                                 @endforeach
                                 @endif -->
                              </select>
                              @if ($errors->has('customer_id'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                          <!--  <div class="col-md-6">
                            <div class="input_section">
                               <label for="  party_name" class="col-form-label">Seller (Company billed Party )</label>
                               <input type="text" name="party_name" id="party_name" class="form-control" value="{!! old( 'party_name' , isset($service) ? $service->party_name : '') !!}" placeholder="Company billed Party">
                               @if ($errors->has('party_name'))
                               <div class="error">
                                  <p class="text-danger">{{ $errors->first('party_name') }}</p>
                               </div>
                               @endif
                            </div>
                         </div> -->
                       
                         <div class="col-md-6">
                            <div class="input_section">
                               <label for="invoice_date" class="col-form-label">
                                Company Sale Bill Date</label>

                                <input type="text" name="invoice_date" id="invoice_date" class="form-control datepicker" 
                                  value="{{ old('invoice_date', isset($service) && $service->invoice_date ? \Carbon\Carbon::parse($service->invoice_date)->format('d-m-Y') : '') }}" 
                                  placeholder="Company Sale Bill Date" autocomplete="off">
                                @if ($errors->has('invoice_date'))
                                <div class="error">
                                   <p class="text-danger">{{ $errors->first('invoice_date') }}</p>
                                </div>
                                @endif
                            </div>
                         </div>
                           <div class="col-md-6">
                           <div class="input_section">
                              <label for="sale_bill_date" class="col-form-label">Warranty / Customer Bill Date</label>
                             <input type="text" name="sale_bill_date" id="sale_bill_date" 
                               class="datepicker form-control" placeholder="Sale Bill Date" 
                               autocomplete="off"
                               value="{{ old('sale_bill_date', !empty($warranty_activation['sale_bill_date']) ? \Carbon\Carbon::parse($warranty_activation['sale_bill_date'])->format('d-m-Y') : '') }}" 
                               required>
                              @if ($errors->has('sale_bill_date'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('sale_bill_date') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                  
                         <div class="col-md-6">
                            <div class="input_section">
                               <label for="invoice_no" class="col-form-label">Company Sale Bill NO</label>

                               <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="{!! old( 'invoice_no' , isset($service) ? $service->invoice_no : '')  !!}" placeholder="Company Sale Bill NO">
                                @if ($errors->has('invoice_no'))
                                <div class="error">
                                   <p class="text-danger">{{ $errors->first('invoice_no') }}</p>
                                </div>
                                @endif
                            </div>
                         </div>  
                           <div class="col-md-6">
                           <div class="input_section">
                              <label for="sale_bill_no" class="col-form-label">Customer Bill No.</label>
                              <input type="text" name="sale_bill_no" id="sale_bill_no" class="form-control" value="{!! old( 'sale_bill_no' , $warranty_activation['sale_bill_no']) !!}" required>
                              @if ($errors->has('sale_bill_no'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('sale_bill_no') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="warranty_date" class="col-form-label">Warranty Date</label>

                              <input type="text" name="warranty_date" id="warranty_date" class="datepicker form-control" 
                               placeholder="Warranty Date" autocomplete="off"
                               value="{{ old('warranty_date', isset($warranty_activation['warranty_date']) ? \Carbon\Carbon::parse($warranty_activation['warranty_date'])->format('d-m-Y') : '') }}"
                               required disabled>

                              @if ($errors->has('warranty_date'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('warranty_date') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <!-- <div class="col-md-6">
                          
                        </div> -->

                      

                     </div>

                  </div>

                  <div class="pt-4">
                     <h5>Contact Details</h5>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_number" class="col-form-label">Customer Number Search</label>

                              <input type="number" name="customer_number" id="customer_number" class="form-control" value="{!! old( 'customer_number' , $warranty_activation['customer']?$warranty_activation['customer']['customer_number']:'') !!}" required>
                              <input type="hidden" name="end_user_id" id="end_user_id" value="{!! old( 'end_user_id' , $warranty_activation['end_user_id']) !!}">
                              @if ($errors->has('customer_number'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_number') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_name" class="col-form-label">Customer Name</label>

                              <input type="text" name="customer_name" id="customer_name" class="form-control" value="{!! old( 'customer_name' , $warranty_activation['customer']?$warranty_activation['customer']['customer_name']:'') !!}" required>
                              @if ($errors->has('customer_name'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        
                    
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_email" class="col-form-label">Email</label>

                              <input type="text" name="customer_email" id="customer_email" class="form-control" value="{!! old( 'customer_email' , $warranty_activation['customer']?$warranty_activation['customer']['customer_email']:'') !!}">
                              @if ($errors->has('customer_email'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_email') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_pindcode" class="col-form-label">Pincode</label>
                              <input type="hidden" id="customer_pindcode_input" name="customer_pindcode_input" value="" >
                              <select name="customer_pindcode" id="customer_pindcode" placeholder="Select Pincode" class="select2 form-control" onchange="getAddressDataByPincode()">
                                 <option value="" disabled selected>Select Pincode</option>
                                <!--  @if($pincodes && count($pincodes) > 0)
                                 @foreach($pincodes as $pincode)
                                 <option value="{{$pincode->id}}" {!! old( 'customer_pindcode' , $warranty_activation['customer']?$warranty_activation['customer']['customer_pindcode']:'')==$pincode->id?'selected':'' !!}>{{$pincode->pincode}}</option>
                                 @endforeach
                                 @endif -->
                              </select>
                              @if ($errors->has('customer_pindcode'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_pindcode') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>


                       
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_state" class="col-form-label">State</label>

                              <select name="customer_state" id="customer_state" placeholder="Select State" class="select2 form-control" >
                                 <option value="" disabled selected>Select State</option>
                                 @if($states && count($states) > 0)
                                 @foreach($states as $state)
                                 <option value="{{$state->id}}" {!! old( 'customer_state' , $warranty_activation['customer']?$warranty_activation['customer']['customer_state']:'')==$state->state_name?'selected':'' !!}>{{$state->state_name}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if ($errors->has('customer_state'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_state') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_district" class="col-form-label">District</label>

                              <select name="customer_district" id="customer_district" class="select2 form-control" ></select>
                              @if ($errors->has('customer_district'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_district') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_city" class="col-form-label">City</label>

                              <select name="customer_city" id="customer_city" class="select2 form-control" ></select>
                              @if ($errors->has('customer_city'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_city') }}</p>
                              </div>
                              @endif
                           </div>

                        </div>
                         <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_address" class="col-form-label">Address</label>

                              <input type="text" name="customer_address" id="customer_address" class="form-control" value="{!! old( 'customer_address' , $warranty_activation['customer']?$warranty_activation['customer']['customer_address']:'') !!}" required>
                              @if ($errors->has('customer_address'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="input_section">
                              <label for="customer_place" class="col-form-label">Place</label>

                              <input type="text" name="customer_place" id="customer_place" class="form-control" value="{!! old( 'customer_place' , $warranty_activation['customer']?$warranty_activation['customer']['customer_place']:'') !!}">
                              @if ($errors->has('customer_place'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_place') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        
                        <div class="col-md-6 mt-3">
                           <div class="input_section">
                              <label for="customer_status" class="col-form-label">Customer status</label>

                              <input type="radio" name="customer_status" id="inactive" value="0"><span class="yes_no"> Inactive</span>
                              <input type="radio" checked name="customer_status" id="active" value="1"> <span class="yes_no">active</span>
                           </div>
                        </div>

                     </div>
                   
                  </div>
                  <div class="row mt-2">
                     <div class="col-md-6">
                        <div class="input_section">
                           <label for="warranty_activation_attach" class="col-form-label">Attachment</label>

                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">

                              <div class="fileinput-new thumbnail">
                                 @if($warranty_activation->exists && $warranty_activation->getMedia('warranty_activation_attach')->count() > 0)
                                 @php
                                 $media = $warranty_activation->getFirstMedia('warranty_activation_attach');
                                 $filePath = $media->getPath();
                                 @endphp

                                 @if(Storage::disk('s3')->exists($filePath))
                                 @if($media->mime_type == 'application/pdf')
                                 <a href="{!! $media->getFullUrl() !!}" target="_blank">{{ $media->file_name }}</a>
                                 @else
                                 <img src="{!! $media->getFullUrl() !!}" class="imagepreview1">
                                 @endif
                                 @else
                                 <img src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                                 @endif
                                 @else
                                 <img src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                                 @endif
                                 <div class="selectThumbnail">
                                    <span class="btn btn-just-icon btn-round btn-file">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <span class="fileinput-exists">Change</span>
                                       <input type="file" name="warranty_activation_attach" class="getimage1" accept="image/*,application/pdf">
                                    </span>
                                    <br>
                                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                                 </div>
                              </div>

                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                              <!-- <label class="bmd-label-floating">Attachment 1st</label> -->
                              @if ($errors->has('warranty_activation_attach'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('warranty_activation_attach') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="card-footer pull-right">
                           {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
                        </div>
                     </div>



                     {{ Form::close() }}
                  </div>
               </div>
            </div>
         </div>
         <div class="modal fade bd-example-modal-lg" id="createpincode" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
           <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content card">
               <div class="card-header card-header-icon card-header-theme">
                 <div class="card-icon">
                   <i class="material-icons">perm_identity</i>
                 </div>
                 <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.pincode.title_singular') !!}
                   <span class="pull-right" >
                     <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
                   </span>
                 </h4>
               </div>
               <div class="modal-body">
                 <form method="POST" action="{{ route('pincode.store') }}" enctype="multipart/form-data" id="createpincodeForm">
                 @csrf
                 <div class="row">
                     <div class="col-md-6">
                       <div class="input_section">
                         <label class="col-form-label">{!! trans('panel.pincode.pincode') !!} <span class="text-danger"> *</span></label>
                        
                           <div class="form-group has-default bmd-form-group">
                             <input type="text" name="pincode" id="pincode" class="form-control" value="{!! old( 'pincode') !!}" maxlength="200" required>
                             @if ($errors->has('pincode'))
                               <div class="error"><p class="text-danger">{{ $errors->first('pincode') }}</p></div>
                             @endif
                           </div>
                         
                       </div>
                     </div>
                   <div class="col-md-6">
                       <div class="input_section">
                         <label class="col-form-label">{!! trans('panel.pincode.city') !!}<span class="text-danger"> *</span></label>
                        
                           <div class="form-group has-default bmd-form-group">
                           <!-- <input list="browsers" name="city_id" id="browser" class="form-control"> -->
                           <select class="form-control select2" name="city_id" id="browser">
                           
                             @if(@isset($cities ))
                               @foreach($cities as $city)
                               <option value="{!! $city['id'] !!}">{!! $city['city_name'] !!}</option>
                               @endforeach
                             @endif
                           
                             </select>
                             <!-- <select class="form-control select2" name="city_id" id="city_id" style="width: 100%;" required>
                                 <option value="">Select {!! trans('panel.pincode.city') !!}</option>
                                 @if(@isset($cities ))
                                 @foreach($cities as $city)
                                 <option value="{!! $city['id'] !!}" {{ old( 'city_id') == $city['id'] ? 'selected' : '' }}>{!! $city['city_name'] !!}</option>
                                 @endforeach
                                 @endif
                              </select> -->
                           </div>
                           @if ($errors->has('country_id'))
                            <div class="error">
                               <p class="text-danger">{{ $errors->first('country_id') }}</p>
                            </div>
                           @endif
                        
                       </div>
                     </div>
                      </div>
                 <div class="clearfix"></div>
                 <div class="pull-right">
                   <input type="hidden" name="id" id="pincode_id" />
                   <button class="btn btn-info save"> Submit</button>
                 </form>
                 </div>
               </div>
            
           </div>
         <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
         <script>
            $(document).ready(function () {
                $("#createpincodeForm").on("submit", function (e) {
                    e.preventDefault(); // Prevent default form submission (page refresh)

                    let formData = new FormData(this);
                    let submitButton = $(".save");

                    submitButton.prop("disabled", true).text("Submitting...");

                    $.ajax({
                        url: $(this).attr("action"),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                alert("Pincode added successfully!");
                                
                                // Close modal
                                $("#createpincode").modal("hide");

                                // Reset form
                                $("#createpincodeForm")[0].reset();

                                // Optionally refresh a specific section (like a table)
                                // $("#pincodeTable").load(location.href + " #pincodeTable");
                            } else {
                                alert("Error: " + response.message);
                            }
                        },
                        error: function (xhr) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = "";

                            $.each(errors, function (key, value) {
                                errorMessages += value[0] + "\n";
                            });

                            alert("Validation Errors:\n" + errorMessages);
                        },
                        complete: function () {
                            submitButton.prop("disabled", false).text("Submit");
                        }
                    });
                });
            });

            $(document).ready(function() {
               var sr_no = '{{$request->serial_no??""}}';               
               if (sr_no != '') {
                  $("#product_serail_number").keyup();
               }
               $('#storeTransactionHistoryData').validate({
                   rules:{
                     customer_number:
                     {
                       required:true,
                       minlength:10,
                       maxlength: 10,
                     },
                   },
                   highlight: function(element) {
                     $(element).closest('.error').css("display", "none");
                   },
                   unhighlight: function(element) {
                     $(element).closest('.error').css("display", "block");
                   }
               });
            })

            $("#customer_number").on("input", function () {
               let value = $(this).val();

               // Remove non-numeric characters and ensure max 10 digits
               value = value.replace(/\D/g, '').substring(0, 10);

               // Set the cleaned value back
               $(this).val(value);
            });

            $('#sale_bill_date').datepicker({
                maxDate: 0,
                dateFormat: 'dd-mm-yy',
            });

            $('#invoice_date').datepicker({
                maxDate: 0,
                dateFormat: 'dd-mm-yy',
            });
            $("#invoice_date").on('change', function() {
                var selectedStartDate = $('#invoice_date').datepicker('getDate');
                var today = new Date(); // Get today's date

                // Set sale bill date minimum to selected invoice date
                $('#sale_bill_date').datepicker("option", "minDate", selectedStartDate);

                // Validate date format
                var selectedDate = moment($(this).val(), 'DD-MM-YYYY', true);
                if (!selectedDate.isValid()) {
                    alert("Invalid date format. Please use DD-MM-YYYY.");
                    $("#invoice_date").val('');
                    return;
                }

                // Check if selected date is greater than today
                if (selectedDate.isAfter(moment(today))) {
                    alert("Invoice date cannot be greater than today's date.");
                    $("#invoice_date").val(''); // Reset input
                    return;
                }
            });

           $('#sale_bill_date').on('change', function () {
                if ($(this).val().length >= 10 && $('#invoice_date').val().length >= 10) {
                    var selectedDate = moment($(this).val().trim(), 'DD-MM-YYYY', true);
                    var billDate = moment($('#invoice_date').val().trim(), 'DD-MM-YYYY', true);
                    var today = moment(); // Get today's date

                    // Check if dates are valid
                    if (!selectedDate.isValid() || !billDate.isValid()) {
                        alert("Invalid date format. Please use DD-MM-YYYY.");
                        $(this).val($('#invoice_date').val()); // Reset input
                        return;
                    }

                    // Check if sale bill date is before invoice date
                    if (selectedDate.isBefore(billDate)) {
                        alert("Selected date must be greater than or equal to the invoice date.");
                        $(this).val(''); // Reset input
                        return;
                    }

                    // Check if sale bill date is greater than today
                    if (selectedDate.isAfter(today)) {
                        alert("Sale bill date cannot be greater than today's date.");
                        $(this).val(''); // Reset input
                        return;
                    }
                }
            }).trigger('change');

           const product_ser_no = "{{$request->serial_no ?? ''}}"
              if(product_ser_no){
               $('#select_product_id').empty();
               getProductInfoBySerialNo();
              }

            $("#product_serail_number").on("input", function() {
               $('#select_product_id').empty();
               getProductInfoBySerialNo();
            }).trigger('change');

            function getProductInfoBySerialNo(){
               var serial_no = $('#product_serail_number').val();
               $.ajax({
                  url: "{{ url('getProductByCoupon') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     serial_no: serial_no
                  },
                  success: function(res) {
                     if (res.status == true) {
                        $('#select_product_id').html(res.html);
                        $('#product_id').val($('#select_product_id').val());
                        if(res.service){
                            $('#invoice_no').val(res.service.invoice_no);
                            let invoiceDate = res.service.invoice_date;
                            if (invoiceDate) {
                                let dateParts = invoiceDate.split('-'); // Split YYYY-MM-DD
                                let formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`; // Rearrange to DD-MM-YYYY
                                $('#invoice_date').val(formattedDate).trigger('change');
                            }
                            $('#party_name_input').val(res.service.party_name);
                        }
                        if (res.slected === true) {
                           $('#select_product_id').prop('disabled', true);
                        } else {
                           $('#select_product_id').prop('disabled', false);
                        }
                     }
                  }
               });
               $.ajax({
                  url: "{{ url('getProductInfoBySerialNo') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     serial_no: serial_no
                  },
                  success: function(res) {
                     if (res.status === true) {
                        if (res.check_Warranty != null) {
                           $("#customer_id").val(res.check_Warranty.customer_id).trigger('change');
                           let formattedDate = moment(res.check_Warranty.sale_bill_date, 'YYYY-MM-DD').format('DD-MM-YYYY');
                            if ($('#sale_bill_date').length) {
                                $('#sale_bill_date').val(formattedDate).trigger('change');
                            } 
                           $("#customer_number").val(res.check_Warranty.customer.customer_number ?? '');
                           $('#sale_bill_no').val(res.check_Warranty.sale_bill_no);
                           
                           $("#customer_number").keyup();
                        }
                        $('#branch_id').val(res.data_all.branch.id).trigger('change');
                     } else {
                        $("#customer_id").val(" ");
                        $("#customer_id").prop('readonly', false);
                     }
                  }
               });
            }

            $("#select_product_id").on("change", function() {
               $('#product_id').val($(this).val());
               getProductTimeInterval();
            })

            $('#sale_bill_date').on('change' , function(){
                setTimeout(() => {
                   getProductTimeInterval();
                }, 2000);
            });




            function getProductTimeInterval(){
                var product_id = $('#select_product_id').val();
                var sale_bill_date = $('#sale_bill_date').val();
                if(sale_bill_date != "" && product_id != ""){
                    $('#warranty_date').prop('disabled' , false);
                    $.ajax({
                        url: "{{ url('getProductTimeInterval') }}",
                        dataType: "json",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            product_id: product_id,
                            sale_bill_date : sale_bill_date,
                        },
                        success: function(res) {
                            console.log("warrayu" , res);
                            if (res.status == "success") {
                                $('#warranty_date').val(res.warrenty_expire_date).trigger('change');
                            } else {

                            }
                        }
                    });
                }
            }

            function getAddressDataByPincode(){
               var pincode_id = $("select[name=customer_pindcode]").val();
               var token = $("meta[name='csrf-token']").attr("content");
               if(pincode_id){
                  $.ajax({
                     url: "{{ url('/getAddressData') }}" ,
                     dataType: "json",
                     type: "POST",
                     data:{ _token: token, pincode_id:pincode_id},
                     success: function(res){
                        if(res)
                        {
                           $("#customer_state").val(res.state_id).trigger('change');
                           setTimeout(() => {
                              $("#customer_district").val(res.district_id).trigger('change');
                           }, 1000);
                           setTimeout(() => {
                              $("#customer_city").val(res.city_id).trigger('change');
                           }, 2000); 
                        }
                     }
                  });
               } 
            } 
            $("#customer_number").on("keyup", function() {
               var customer_number = $(this).val();
               $.ajax({
                  url: "{{ url('getEndUserData') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     customer_number: customer_number
                  },
                  success: function(res) {
                     if (res.status === true) {
                        $("#customer_name").val(res.data.customer_name);
                        $("#end_user_id").val(res.data.id);
                        $("#customer_email").val(res.data.customer_email);
                        $("#customer_address").val(res.data.customer_address);
                        $("#customer_place").val(res.data.customer_place);
                        $("#customer_state").val(res.data.state_id).trigger("change");
                        setTimeout(() => {
                           $("#customer_district").val(res.data.district_id).trigger("change");
                        }, 1000);
                        setTimeout(() => {
                           $("#customer_city").val(res.data.city_id).trigger("change");
                        }, 1500);
                        setTimeout(() => {
                           $("#customer_pindcode").val(res.data.customer_pindcode).trigger("change");
                           $("#customer_pindcode_input").val(res.data.customer_pindcode).trigger("change");
                        }, 2000);

                     } else {
                        $("#customer_name").val("");
                        $("#end_user_id").val("");
                        $("#customer_email").val("");
                        $("#customer_address").val("");
                        $("#customer_place").val("");
                        $("#customer_pindcode").val("").trigger("change");;
                        $("#customer_state").val("").trigger("change");
                        $("#customer_district").val("").trigger("change");
                        $("#customer_city").val("").trigger("change");

                        $("#customer_name").prop('readonly', false);
                        $("#customer_email").prop('readonly', false);
                        $("#customer_address").prop('readonly', false);
                        $("#customer_place").prop('readonly', false);
                        $("#customer_pindcode").prop('disabled', false);
                        $("#customer_state").prop('readonly', false);
                        $("#customer_district").prop('readonly', false);
                        $("#customer_city").prop('readonly', false);
                        $("#customer_name").prop('readonly', false);
                        $("#customer_email").prop('readonly', false);
                     }
                  }
               });
            }).trigger('keyup');

            $('#status').on('change', function() {
               if ($(this).val() == '3') {
                  $('.reject-remark').removeClass('d-none');
               } else {
                  $('.reject-remark').addClass('d-none');
               }
            }).trigger('change');

            $("#customer_state").on("change", function() {
               var state_id = $(this).val();
               if (state_id != null && state_id != '') {
                  $.ajax({
                     url: "{{ url('getDistrict') }}",
                     dataType: "json",
                     type: "POST",
                     data: {
                        _token: "{{csrf_token()}}",
                        state_id: state_id
                     },
                     success: function(res) {
                        var options = '<option value="">Select District</option>';
                        $.each(res, function(key, val) {
                           options += '<option value="' + val.id + '">' + val.district_name + '</option>';
                        })
                        $("#customer_district").html(options);
                     }
                  });
               }
            });

            $("#customer_district").on("change", function() {
               var district_id = $(this).val();
               $.ajax({
                  url: "{{ url('getCity') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     district_id: district_id
                  },
                  success: function(res) {
                     var options = '<option value="">Select City</option>';
                     $.each(res, function(key, val) {
                        options += '<option value="' + val.id + '">' + val.city_name + '</option>';
                     })
                     $("#customer_city").html(options);
                  }
               });
            });

            // $("#customer_city").on("change", function() {
            //    var city_id = $(this).val();
            //    $.ajax({
            //       url: "{{ url('getPincode') }}",
            //       dataType: "json",
            //       type: "POST",
            //       data: {
            //          _token: "{{csrf_token()}}",
            //          city_id: city_id
            //       },
            //       success: function(res) {
            //          var options = '<option value="">Select Pincode</option>';
            //          $.each(res, function(key, val) {
            //             options += '<option value="' + val.id + '">' + val.pincode + '</option>';
            //          })
            //          $("#customer_pindcode").html(options);
            //       }
            //    });
            // });
         </script>
   @section('script')
   <script type="text/javascript">
      $(document).ready(function () {
         function initializeSelect2(selector, selectedId = null) {
            // if ($(selector).hasClass("select2-initialized")) {
            //    return; // Prevent multiple initializations
            // }
            $(selector).addClass("select2-initialized");

            $(selector).select2({
               ajax: {
                  url: "{{ url('getPincodeSearch') }}",
                  type: "POST",
                  dataType: "json",
                  delay: 250,
                  data: function (params) {
                     return {
                        _token: "{{ csrf_token() }}",
                        q: params.term, 
                        page: params.page || 1,
                        selected: selectedId
                     };
                  },
                  processResults: function (data, params) {
                     if (!data.results.length) {
                         setTimeout(function () {
                             $("#createpincode").modal("show"); // Open modal
                         }, 500);
                     }
                     params.page = params.page || 1;
                     return {
                        results: data.results.map(item => ({ id: item.id, text: item.pincode })),
                        pagination: { more: data.pagination.more }
                     };
                  },
                  cache: true
               },
               placeholder: "Search...",
            });

            // Load pre-selected value if available (only once)
            if (selectedId) {
               $.ajax({
                  url: "{{ url('getPincodeSearch') }}",
                  type: "POST",
                  dataType: "json",
                  data: {
                     _token: "{{ csrf_token() }}",
                     selected: selectedId,
                  },
                  success: function (res) {
                     if (res.results.length) {
                        let selectedItem = res.results.find(item => item.id == selectedId);
                        if (selectedItem) {
                           let newOption = new Option(selectedItem.pincode, selectedItem.id, true, true);
                           $(selector).append(newOption).trigger('change');
                        }
                     }
                  }
               });
            }
         }

         $('#customer_pindcode_input').on('change' , function(){
            var customer_pindcode_input = $(this).val();
            initializeSelect2("#customer_pindcode", customer_pindcode_input);
         })

         // Get selected ID from the backend
         var pincode = "{{ $warranty_activation['customer']['customer_pindcode'] ?? '' }}";
         initializeSelect2("#customer_pindcode", pincode);
      });

      $(document).ready(function () {
         function initializeSelect3(selector, selectedId = null) {
            if ($(selector).hasClass("select2-initialized")) {
               return; // Prevent multiple initializations
            }
            $(selector).addClass("select2-initialized");

            $(selector).select2({
               ajax: {
                  url: "{{ url('getAllCustomer') }}",
                  type: "POST",
                  dataType: "json",
                  delay: 250,
                  data: function (params) {
                     return {
                        _token: "{{ csrf_token() }}",
                        q: params.term, 
                        page: params.page || 1,
                        selected: selectedId
                     };
                  },
                  processResults: function (data, params) {
                     params.page = params.page || 1;
                     return {
                        results: data.results.map(item => ({
                              id: item.id,
                              text: item.name + ' (' + item.mobile + ')' // Correct format
                          })),
                        pagination: { more: data.pagination.more }
                     };
                  },
                  cache: true
               },
               placeholder: "Search...",
            });

            // Load pre-selected value if available (only once)
            if (selectedId) {
               $.ajax({
                  url: "{{ url('getAllCustomer') }}",
                  type: "POST",
                  dataType: "json",
                  data: {
                     _token: "{{ csrf_token() }}",
                     selected: selectedId,
                  },
                  success: function (res) {
                     if (res.results.length) {
                        let selectedItem = res.results.find(item => item.id == selectedId);
                          if (selectedItem) {
                              let full_name = selectedItem.name + ' (' + selectedItem.mobile + ')'; // Correct format
                              let newOption = new Option(full_name, selectedItem.id, true, true);
                              $(selector).append(newOption).trigger('change');
                          }
                     }
                  }
               });
            }
         }

      function initializeSelect4(selector, selectedId = null) {
          // if ($(selector).hasClass("select2-initialized")) {
          //     return; // Prevent multiple initializations
          // }
          $(selector).addClass("select2-initialized");

          $(selector).select2({
              ajax: {
                  url: "{{ url('getAllPartyName') }}",
                  type: "POST",
                  dataType: "json",
                  delay: 250,
                  data: function (params) {
                      return {
                          _token: "{{ csrf_token() }}",
                          q: params.term, 
                          page: params.page || 1,
                          selected: selectedId
                      };
                  },
                  processResults: function (data, params) {
                      params.page = params.page || 1;

                      let results = data.results.map(item => ({
                          id: item.name, // Use name as ID
                          text: item.name + ' (' + item.mobile + ')' // Correct format
                      }));

                      // Always add "Damage Entry" as the first option
                      results.unshift({ id: "Damage Entry", text: "Damage Entry" });

                      return {
                          results: results,
                          pagination: { more: data.pagination.more }
                      };
                  },
                  cache: true
              },
              placeholder: "Search...",
          });

          // Load pre-selected value only if "Damage Entry" is selected
          if (selectedId === "Damage Entry") {
              let newOption = new Option("Damage Entry", "Damage Entry", true, true);
              $(selector).append(newOption).trigger('change');
          } else if (selectedId) {
              $.ajax({
                  url: "{{ url('getAllPartyName') }}",
                  type: "POST",
                  dataType: "json",
                  data: {
                      _token: "{{ csrf_token() }}",
                      selected: selectedId,
                  },
                  success: function (res) {
                      if (res.results.length) {
                          let selectedItem = res.results.find(item => item.name == selectedId);
                          if (selectedItem) {
                              let full_name = selectedItem.name + ' (' + selectedItem.mobile + ')';
                              let newOption = new Option(full_name, selectedItem.name, true, true);
                              $(selector).append(newOption).trigger('change');
                          }
                      }
                  }
              });
          }
      }




         // Get selected ID from the backend
         $('#party_name_input').on('change' , function(){
            setTimeout(function(){
             let party_name = $('#party_name_input').val();
             initializeSelect4("#party_name", party_name);
            },1000)
           
         })
         var customer_id = "{{ $warranty_activation['customer_id'] ?? '' }}";
         var party_name = "{{ $service->party_name ?? '' }}";
         initializeSelect3("#customer_id", customer_id);
         initializeSelect4("#party_name", party_name);
      });
   </script>

   @endsection
</x-app-layout>
