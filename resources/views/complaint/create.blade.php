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

      button.delete-img-btn {
         position: absolute;
         cursor: pointer;
         right: 0;
      }

      .img-div {
         width: 25%;
         text-align: center;
         border: 1px solid #ab9a9a;
         margin: 2px 10px;
         border-radius: 5px;
         background: radial-gradient(#c5b0b0, transparent);
      }

      .row {
         align-items: end !important;
      }

      .swal2-container.swal2-center.swal2-fade.swal2-shown {
         z-index: 999999999 !important;
      }

      /*********** For all file preview *****************/

      .preview-container {
         display: flex;
         flex-direction: column;
         gap: 10px;
      }

      .image-preview {
         display: flex;
         flex-wrap: wrap;
         gap: 10px;
      }

      .image-preview img {
         max-width: 150px;
         max-height: 150px;
         border: 1px solid #ddd;
         border-radius: 5px;
      }

      .file-preview {
         display: flex;
         flex-direction: column;
         gap: 5px;
         color: #000 !important;
      }

      .file-item {
         display: flex;
         align-items: center;
         gap: 10px;
         font-size: 14px;
      }

      .file-item i {
         font-size: 20px;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card mt-0 p-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        {!! trans('panel.complaint.new_title') !!} </h4>
                     @if(auth()->user()->can(['district_access']))

                     <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                        <li class="nav-item">
                           <a class="nav-link" href="{{ url('complaints') }}">
                              <i class="material-icons">next_plan</i> {!! trans('panel.complaint.title') !!}
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
               @if(session('message_success'))
               <div class="alert alert-success">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <i class="material-icons">close</i>
                  </button>
                  <span>
                     {{ session('message_success') }}
                  </span>
               </div>
               @endif
               @if(session('message_info'))
               <div class="alert alert-info">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <i class="material-icons">close</i>
                  </button>
                  <span>
                     {{ session('message_info') }}
                  </span>
               </div>
               @endif

               {!! Form::model($complaints,[
               'route' => $complaints->exists ? ['complaints.update', $complaints->id ] : 'complaints.store',
               'method' => $complaints->exists ? 'PUT' : 'POST',
               'id' => 'storeComplaintData',
               'files'=>true
               ]) !!}   
                  <div class="row">
                  <div class="col-12">
                     <div class="p-2 mb-3 text-white text-center" style="background-color: #3972af;">
                        <h4 class="m-0"><strong>Product Details</strong></h4>
                     </div>
                  </div>
                  @if(!$complaints->exists)
                  <div class="col-md-3">
                     <div class="input-group">
                        <input type="text" name="serail_number" id="serail_number" class="form-control" 
                           placeholder="Enter Serial Number"
                           value="{!! old('serail_number', $complaints['serail_number']) !!}" style="
                           height: 54px;">
                        <button type="button" class="btn btn-success w-auto px-3" id="go-search">Go</button>
                     </div>
                     <p class="text-danger d-none" id="search_error"></p>
                     @if ($errors->has('serail_number'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('serail_number') }}</p>
                     </div>
                     @endif
                  </div>                  
                  @endif
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Complaint Number </label>
                        <input type="text" readonly name="complaint_number" id="complaint_number" class="form-control" value="{!! old( 'complaint_number', $complaints['complaint_number']) ?? $newComplaintNumber !!}">
                        @if ($errors->has('complaint_number'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('complaint_number') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <!-- <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Branch </label>
                        <select name="branch_id" id="branch_id" class="select2 form-control">
                           <option value="">Select Branch</option>
                           @if($branchs)
                           @foreach($branchs as $branch)
                           <option value="{{$branch->id}}" {!! old('branch_id') !!}>[{{$branch->branch_code}}] {{$branch->branch_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('branch_id'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                        </div>
                        @endif
                     </div>
                  </div> -->
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Assign Service Engineer</label>
                        <select name="assign_user" id="assign_user" class="select2 form-control" required>
                           <option value="">Select User</option>
                           @if($assign_users)
                              @foreach($assign_users as $assign_user)
                              <option value="{{$assign_user->id}}" {!! old('assign_user', $complaints['assign_user'])==$assign_user->id ? 'selected':'' !!} >[{{$assign_user->employee_codes}}] {{$assign_user->name}}</option>
                              @endforeach
                           @endif
                        </select>
                        @if ($errors->has('assign_user'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('assign_user') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Service Center </label>
                        <select name="service_center" id="service_center" class="select2 form-control">
                           <option value="">Select Service Center</option>
                           @if($service_centers)
                           @foreach($service_centers as $service_center)
                           <option value="{{$service_center->id}}" {!! old('service_center', $complaints['service_center'])==$service_center->id ? 'selected':'' !!} >[{{$service_center->customer_code}}] {{$service_center->name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('service_center'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('service_center') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row d-none  mt-2 mb-2" id="search_data">
                  <h3>Complaint History</h3>
                  <table class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th scope="col">Complaint No</th>
                           <th scope="col">Complaint date</th>
                           <th scope="col">Product Serial No</th>
                           <th scope="col">Claim Amount</th>
                           <th scope="col">Complaint Status</th>
                           <th scope="col">Service Center</th>
                           <th scope="col">Seller (Company billed Party )</th>
                           <th scope="col">Purchased Party Name </th>
                           <th scope="col">Close Remark </th>
                        </tr>
                     </thead>
                     <tbody>

                     </tbody>
                  </table>
               </div>
               <div class="row mt-3">
                   <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Searial Number </label>
                        <input {{($complaints->exists && empty($complaints['product_serail_number']))?'':'readonly'}} type="text" name="product_serail_number" id="product_serail_number" class="form-control" value="{!! old( 'product_serail_number', $complaints['product_serail_number']) !!}" >
                        @if ($errors->has('product_serail_number'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('product_serail_number') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Division </label>
                        <input readonly type="text" name="category" id="category" class="form-control" value="{!! old( 'category', $complaints['category']) !!}">
                        @if ($errors->has('category'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('category') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Product Group </label>
                        <input readonly type="text" name="product_group" id="product_group" class="form-control" value="{!! old( 'product_group', $complaints['product_group']) !!}">
                        @if ($errors->has('product_group'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('product_group') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                   <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Product/Model(Code) </label>
                        <input readonly type="text" name="product_code" id="product_code" class="form-control" value="{!! old( 'product_code', $complaints['product_code']) !!}">
                        @if ($errors->has('product_code'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('product_code') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Product Name </label>
                        <select name="product_id" id="product_id" class="select2">
                           <option value="">Select Product</option>
                           @if(count($products) > 0)
                           @foreach($products as $product)
                           <option value="{{$product->id}}" {{($complaints && $complaints['product_id']==$product->id)?'selected':''}}>{{$product->product_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        <input readonly type="hidden" name="product_name" id="product_name" class="form-control" value="{!! old( 'product_name', $complaints['product_name']) !!}">
                        @if ($errors->has('product_name'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('product_name') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">HP </label>
                        <input readonly type="text" name="specification" id="specification" class="form-control" value="{!! old( 'specification', $complaints['specification']) !!}">
                        @if ($errors->has('specification'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('specification') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Stage </label>
                        <input readonly type="text" name="product_no" id="product_no" class="form-control" value="{!! old( 'product_no', $complaints['product_no']) !!}">
                        @if ($errors->has('product_no'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('product_no') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Phase </label>
                        <input readonly type="text" name="phase" id="phase" class="form-control" value="{!! old( 'phase', $complaints['phase']) !!}">
                        @if ($errors->has('phase'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('phase') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Bill By Company (Party Name)</label>
                        <input type="text" name="seller" id="seller" class="form-control" value="{{old('seller', $complaints['seller'])}}">
                        @if ($errors->has('seller'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('seller') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Company Sale Bill Date </label>
                        <input type="text" name="company_sale_bill_date" id="company_sale_bill_date" class="form-control datepicker" value="{!! old( 'company_sale_bill_date', $complaints['company_sale_bill_date']) !!}">
                        @if ($errors->has('company_sale_bill_date'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('company_sale_bill_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Company Sale Bill NO </label>
                        <input type="text" name="company_sale_bill_no" id="company_sale_bill_no" class="form-control" value="{!! old( 'company_sale_bill_no', $complaints['company_sale_bill_no']) !!}">
                        @if ($errors->has('company_sale_bill_no'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('company_sale_bill_no') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Total Month Bill By Company </label>
                        <input readonly type="text" name="company_bill_date_month" id="company_bill_date_month" class="form-control" value="{!! old( 'company_bill_date_month', $complaints['company_bill_date_month']) !!}">
                        @if ($errors->has('company_bill_date_month'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('company_bill_date_month') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>

                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Bill To Customer (Party Name)</label>
                        <select name="party_name" id="party_name" class="select2 form-control">
                           @if(old('party_name', $complaints['party_name']))
                           <option selected value="{{$complaints['party_name']}}">{{$complaints['party']['name']}}</option>
                           @endif
                        </select>
                        @if ($errors->has('party_name'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('party_name') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                 <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Warranty / Customer Bill Date </label>
                        <input type="text" name="customer_bill_date" id="customer_bill_date" class="form-control datepicker" value="{!! old( 'customer_bill_date', $complaints['customer_bill_date']) !!}">
                        @if ($errors->has('customer_bill_date'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('customer_bill_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Customer Bill No.</label>
                        <input type="text" name="customer_bill_no" id="customer_bill_no" class="form-control" value="{!! old( 'customer_bill_no', $complaints['customer_bill_no']) !!}">
                        @if ($errors->has('customer_bill_no'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('customer_bill_no') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                   <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Total Month Of Customer </label>
                        <input readonly type="text" autocomplete="off" name="customer_bill_date_month" id="customer_bill_date_month" class="form-control" value="{!! old( 'customer_bill_date_month', $complaints['customer_bill_date_month']) !!}">
                        @if ($errors->has('customer_bill_date_month'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('customer_bill_date_month') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Under Warranty </label>
                        <select name="under_warranty" id="under_warranty" class="select2 form-control">
                           <option value="">Under Warranty</option>
                           <option value="Yes" {!! old('under_warranty', $complaints['under_warranty'])=='Yes' ? 'selected' :'' !!}>Yes</option>
                           <option value="No" {!! old('under_warranty', $complaints['under_warranty'])=='No' ? 'selected' :'' !!}>No</option>
                        </select>
                        @if ($errors->has('under_warranty'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('under_warranty') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Service Paid/Free </label>
                        <select name="service_type" id="service_type" class="select2 form-control">
                           <option value="">Service Paid/Free</option>
                           <option value="Paid" {!! old('service_type', $complaints['service_type'])=='Paid' ? 'selected' :'' !!}>Paid</option>
                           <option value="Free" {!! old('service_type', $complaints['service_type'])=='Free' ? 'selected' :'' !!}>Free</option>
                           <option value="later_update" {!! old('service_type', $complaints['service_type'])=='later_update' ? 'selected' :'' !!}>F&A Later Update</option>
                        </select>
                        @if ($errors->has('service_type'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('service_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                 
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Warranty/Bill </label>
                        <select name="warranty_bill" id="warranty_bill" class="select2 form-control">
                           <option value="">Warranty/Bill</option>
                           <option value="Yes" {!! old('warranty_bill', $complaints['warranty_bill'])=='Yes' ? 'selected' :'' !!}>Yes</option>
                           <option value="No" {!! old('warranty_bill', $complaints['warranty_bill'])=='No' ? 'selected' :'' !!}>No</option>
                        </select>
                        @if ($errors->has('warranty_bill'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('warranty_bill') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <input type="hidden" readonly name="complaint_date" id="complaint_date" class="form-control datepicker" value="{!! old('complaint_date', isset($complaints['complaint_date']) ? \Carbon\Carbon::parse($complaints['complaint_date'])->format('d-m-Y') : \Carbon\Carbon::now()->format('d-m-Y')) !!}">
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label">Total Warranty Product In Month</label>
                        <input type="text"  name="warrenty_time" id="warrenty_time" class="form-control datepicker" value="">
                        @if ($errors->has('warrenty_time'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('warrenty_time') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                 
               </div>
               <div class="basic_details mt-1">
                  <div class=" rounded p-2">
                     <div class="row mt-3">
                     </div>
                  </div>
               </div>
               <div class="contact_details mt-2">
                  <div class="col-12">
                     <div class="p-2  text-white text-center" style="background-color: #3972af;">
                        <h4 class="m-0"><strong>Customer Details</strong></h4>
                     </div>
                  </div>
                  <div class=" rounded p-2">
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Customer Number Search</label>
                              <input type="number" name="customer_number" id="customer_number" class="form-control" 
                              value="{!! old('customer_number', $complaints['customer'] ? $complaints['customer']['customer_number'] : '') !!}" 
                              required maxlength="10">
                              <input type="hidden" name="end_user_id" id="end_user_id">
                              <span id="customer_number_error" style="color: red; display: none;"></span>
                              @if ($errors->has('customer_number'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_number') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label"> Customer Name </label>
                              <input type="text" readonly name="customer_name" id="customer_name" class="form-control" value="{!! old( 'customer_name', $complaints['customer_name']) !!}">
                              @if ($errors->has('customer_name'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>

                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Email </label>
                              <input type="text" readonly name="customer_email" id="customer_email" class="form-control" value="{!! old( 'customer_email', $complaints['customer_email']) !!}">
                              @if ($errors->has('customer_email'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_email') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">State </label>
                              <select name="customer_state" id="customer_state" class="select2 form-control" required>
                                 <option value="">Select State</option>
                                 @foreach($states as $state)
                                 <option value="{{$state->id}}">{{$state->state_name}}</option>
                                 @endforeach
                              </select>
                              @if ($errors->has('customer_state'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_state') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                      
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">District </label>
                              <select name="customer_district" id="customer_district" class="select2 form-control" required></select>
                              @if ($errors->has('customer_district'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_district') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">City </label>
                              <select name="customer_city" id="customer_city" class="select2 form-control" required></select>
                              @if ($errors->has('customer_city'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_city') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Place </label>
                              <input type="text" name="customer_place" id="customer_place" class="form-control" value="{!! old( 'customer_place', $complaints['customer_place']) !!}">
                              @if ($errors->has('customer_place'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_place') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Pincode </label>
                              <select name="customer_pindcode" id="customer_pindcode" placeholder="Select Pincode" class="select2 form-control"  onchange="getAddressDataByPincode()">
                                 <option value="" disabled selected >Select Pincode</option>
                                 @if($pincodes && count($pincodes) > 0)
                                 @foreach($pincodes as $pincode)
                                 <option value="{{$pincode->id}}">{{$pincode->pincode}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if ($errors->has('customer_pindcode'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_pindcode') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="input_section">
                              <label class="col-form-label">Address </label>
                              <input type="text" name="customer_address" id="customer_address" class="form-control" value="{!! old( 'customer_address', $complaints['customer_address']) !!}">
                              @if ($errors->has('customer_address'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                    
                     <div class="row mt-3">
                        <div class="col-12">
                           <div class="p-2  text-white text-center" style="background-color: #3972af;">
                              <h4 class="m-0"><strong>Complaints Details</strong></h4>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Service Branch </label>
                              <select name="purchased_branch" id="purchased_branch" class="select2 form-control">
                                  <option value="">Service Branch</option>
                                  @if(!empty($branchs))
                                      @foreach($branchs as $branch)
                                          <option value="{{ $branch->id }}" 
                                              {{ (isset($complaints['purchased_branch']) && $complaints['purchased_branch'] == $branch->id) ? 'selected' : '' }}>
                                              [{{ $branch->branch_code }}] {{ $branch->branch_name }}
                                          </option>
                                      @endforeach
                                  @endif
                              </select>
                              @if ($errors->has('purchased_branch'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('purchased_branch') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Customer Complaint Type </label>
                              <select name="complaint_type" id="complaint_type" placeholder="Select Pincode" class="select2 form-control" required>
                                 <option value="" disabled selected>Complaint Type</option>
                                 @if($complaint_types && count($complaint_types) > 0)
                                 @foreach($complaint_types as $complaint_type)
                                 <option value="{{$complaint_type->id}}" {!! old( 'complaint_type' , $complaints['complaint_type'])==$complaint_type->id?'selected':'' !!}>{{$complaint_type->name}}</option>
                                 @endforeach
                                 @endif
                              </select>
                              @if ($errors->has('complaint_type'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('complaint_type') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                       
                        <!-- <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Pincode </label>
                           <select name="division" id="division" placeholder="Select Division" class="select2 form-control">
                              <option value="" disabled selected>Division</option>
                              @if($divisions && count($divisions) > 0)
                              @foreach($divisions as $division)
                              <option value="{{$division->id}}" {!! old( 'division' , $complaints['division'])==$division->id?'selected':'' !!} >{{$division->division_name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('division'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('division') }}</p>
                           </div>
                           @endif
                        </div>
                     </div> -->
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Complaint Register By </label>
                              <select name="register_by" id="register_by" class="select2 form-control">
                                 <option value="">Select</option>
                                 <option value="Dealer" {!! old( 'register_by' , $complaints['register_by'])=='Dealer' ?'selected':'' !!}>Dealer</option>
                                 <option value="Distributor" {!! old( 'register_by' , $complaints['register_by'])=='Distributor' ?'selected':'' !!}>Distributor</option>
                                 <option value="Retailer" {!! old( 'register_by' , $complaints['register_by'])=='Retailer' ?'selected':'' !!}>Retailer</option>
                                 <option value="Marketing Team" {!! old( 'register_by' , $complaints['register_by'])=='Marketing Team' ?'selected':'' !!}>Marketing Team</option>
                                 <option value="ASC" {!! old( 'register_by' , $complaints['register_by'])=='ASC' ?'selected':'' !!}>ASC</option>
                                 <option value="Service Enginer" {!! old( 'register_by' , $complaints['register_by'])=='Service Enginer' ?'selected':'' !!}>Service Enginer</option>
                                  <option value="Service Enginer" {!! old( 'register_by' , $complaints['register_by'])=='Customer' ?'selected':'' !!}>Customer</option>
                              </select>
                              @if ($errors->has('register_by'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('register_by') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Product Laying at </label>
                              <select name="product_laying" id="product_laying" class="select2 form-control">
                                 <option value="">Select</option>
                                 <option value="Customer" {!! old('product_laying', $complaints['product_laying'])=='Customer' ? 'selected' :'' !!}>Customer</option>
                                 <option value="Dealer" {!! old('product_laying', $complaints['product_laying'])=='Dealer' ? 'selected' :'' !!}>Dealer</option>
                                 <option value="Distributor" {!! old('product_laying', $complaints['product_laying'])=='Distributor' ? 'selected' :'' !!}>Distributor</option>
                                 <option value="Retailer" {!! old('product_laying', $complaints['product_laying'])=='Retailer' ? 'selected' :'' !!}>Retailer</option>
                                 <option value="ASC" {!! old('product_laying', $complaints['product_laying'])=='ASC' ? 'selected' :'' !!}>ASC</option>
                                 <option value="Branch" {!! old('product_laying', $complaints['product_laying'])=='Branch' ? 'selected' :'' !!}>Branch</option>
                              </select>
                              @if ($errors->has('product_laying'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('product_laying') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                         <div class="col-md-3">
                           <div class="input_section">
                              <label class="col-form-label">Select</label>
                              <select name="complaint_recieve_via" id="complaint_recieve_via" class="select2 form-control">
                                 <option value="">Complaint Recieved Via</option>
                                 <option value="WhatsApp" {!! old( 'register_by' , $complaints['complaint_recieve_via'])=='WhatsApp' ?'selected':'' !!}>WhatsApp</option>
                                 <option value="Toll-Free Call" {!! old( 'register_by' , $complaints['complaint_recieve_via'])=='Toll-Free Call' ?'selected':'' !!}>Toll-Free Call</option>
                                 <option value="E-Mail" {!! old( 'register_by' , $complaints['complaint_recieve_via'])=='E-Mail' ?'selected':'' !!}>E-Mail</option>
                              </select>
                              @if ($errors->has('register_by'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('register_by') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-12">
                           <div class="input_section">
                              <label class="col-form-label">Description / CRM Remark </label>
                              <textarea type="text" name="description" id="description" cols="30" rows="7" class="form-control"> {!! old( 'description', $complaints['description']) !!} </textarea>
                              @if ($errors->has('description'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('description') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                       
                     </div>
                     <div class="row mt-3">
                        <div class="col-md-3 d-none" id="invoice-div">
                           <div class="input_section">
                              <h6 class="col-form-label">Invoice </h6>
                              <a href="" download target="_blank"><img class="ml-2 rounded" id="invoice-img" style="width: 200px;height:220px;border: 3px solid #5a5252;" src=""></a>
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="input_section">
                              <label class="col-form-label">Attachments </label>
                              <input type="file" multiple name="files[]" class="form-control" id="fileInput">
                              <div class="preview-container">
                                 <div class="image-preview" id="imagePreview"></div>
                                 <div class="file-preview" id="filePreview"></div>
                              </div>
                              <div class="row mt-3">
                                 @if($complaints->exists && $complaints->getMedia('complaint_attach')->count() > 0 && Storage::disk('s3')->exists($complaints->getMedia('complaint_attach')[0]->getPath()))
                                 @foreach($complaints->getMedia('complaint_attach') as $k=>$media)
                                 <div style="position: relative;" class="img-div">
                                    <button title="Delete Image" type="button" class="badge badge-danger delete-img-btn" data-mediaid="{{$media->id}}">X</button>
                                    <a href="{{$media->getFullUrl()}}" download target="_blank">
                                       @if($media->mime_type == 'application/pdf')
                                       <img class="m-2 rounded img-fluid" src="{{url('/public/assets/img/pdf-icon.jpg')}}" style="width: 170px;height:170px;">
                                       @else
                                       <img class="m-2 rounded img-fluid" src="{!! $media->getFullUrl() !!}" style="width: 170px;height:170px;">
                                       @endif
                                    </a>
                                 </div>
                                 @endforeach
                                 @endif
                              </div>
                              @if ($errors->has('description'))
                              <div class="error">
                                 <p class="text-danger">{{ $errors->first('description') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer pull-right mt-5">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme','id' => 'submit-btn')) }}
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   

   <script>
      setTimeout(() => {
         var $customerSelect = $('#party_name').select2({
            placeholder: 'Purchased Party Name ',
            allowClear: true,
            ajax: {
               url: "{{ route('getRetailerDataSelect') }}",
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
      }, 1000);
      $(document).ready(function() {        
          $('#company_sale_bill_date').datepicker({
            maxDate: 0,
            dateFormat: 'dd-mm-yy',
         });
         $('#customer_bill_date').datepicker({
            maxDate: 0,
            dateFormat: 'dd-mm-yy',
         });
         $('#complaint_date').datepicker({
            minDate: 0,
            maxDate: 0,
            dateFormat: 'dd-mm-yy',
         });


      });
      $("#company_sale_bill_date").on('change', function() {
         var selectedDate = moment($(this).val(), 'DD-MM-YYYY');

         if (!selectedDate.isValid()) {
            // console.error("Invalid date format. Please use DD-MM-YYYY.");
            return;
         
         }
         var selectedStartDate = $('#company_sale_bill_date').datepicker('getDate');
         $('#customer_bill_date').datepicker("option", "minDate", selectedStartDate);
         var today = moment();
         var diffMonths = today.diff(selectedDate, 'months');
         selectedDate.add(diffMonths, 'months');
         var diffDays = today.diff(selectedDate, 'days');
         $('#customer_bill_date').val($('#company_sale_bill_date').val()).trigger('change');
         $("#company_bill_date_month").focus();
         $("#company_bill_date_month").val(diffMonths + " Month " + diffDays + " Day");
      });

      $('#customer_bill_date').on('change', function () {
        if ($(this).val().length >= 10) {
            var selectedDate = moment($(this).val().trim(), 'DD-MM-YYYY', true);
            var billDate = moment($('#company_sale_bill_date').val().trim(), 'DD-MM-YYYY', true);
            if (!selectedDate.isValid() && !billDate.isValid()) {
                alert("Invalid date format. Please use DD-MM-YYYY.");
                $(this).val($('#company_sale_bill_date').val()); // Clear input
                return;
            }
            if (selectedDate.isBefore(billDate)) {
                alert("Selected date must be greater than the bill date.");
                $(this).val($('#company_sale_bill_date').val()); // Clear input
                return;
            }
        }
    }).trigger('change');

      $("#customer_bill_date").on('change', function() {

         var selectedDate = moment($(this).val(), 'DD-MM-YYYY');
         if (!selectedDate.isValid()) {
            console.error("Invalid date format. Please use DD-MM-YYYY.");
            return;
         }

         var today = moment();
         var diffMonths = today.diff(selectedDate, 'months');
         selectedDate.add(diffMonths, 'months');
         var diffDays = today.diff(selectedDate, 'days');

         $("#customer_bill_date_month").focus();
         $("#customer_bill_date_month").val(diffMonths + " Month " + diffDays + " Day");
      });

      let debounceTimer; // Store the debounce timer
      $("#product_serail_number").on("keyup", function() {
          if(!$(this).val()){
            return ''
          }
         clearTimeout(debounceTimer); 
         debounceTimer = setTimeout(() => {
            var product_serail_number = $('#product_serail_number').val();

            if(product_serail_number == ''){
               $("input, select, textarea")
               .not("#serail_number, #complaint_number, #complaint_date, #submit-btn , #company_sale_bill_date , #customer_bill_date , input[name='_token'] , #product_serail_number , #warrenty_time , #description , input[name='_method , #complaint_recieve_via']") // Multiple exclusions in a single string
               .val("")
               .trigger('change');
               $("input, select, textarea, button")
               .not("#serail_number, #complaint_number, #complaint_date, #product_serail_number, #product_group, #company_bill_date_month, #customer_bill_date_month , #warrenty_time , #description , input[name='_method'] , #complaint_recieve_via")
               .prop("readonly", false)
               .prop("disabled", false);
               return ;
            }

            var serial_no = $(this).val();
            if(!serial_no){
               return 0;
            }
            var chekcPro = '{{($complaints && $complaints->product_id)?$complaints->product_id:""}}'
            $.ajax({
               url: "{{ url('getProductInfoBySerialNo') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  serial_no: serial_no
               },
               success: function(res) {
                  $("input, select, textarea")
                  .not("#serail_number, #complaint_number, #complaint_date, #submit-btn , #company_sale_bill_date , #customer_bill_date , input[name='_token'] , #product_serail_number , #warrenty_time , #purchased_branch , #complaint_type , #register_by , #product_laying  , input[name='_method'] , #description , #complaint_recieve_via , #service_center")
                  .val("")
                  .trigger('change');
                  if (res.status === true ) {
                     $("#product_code").val(res.data.product_code);
                     $("#product_name").val(res.data.product_name);
                     $("#product_id").val(res.data.id);
                     $("#product_id").change();
                     $("#category").val(res.data.categories.category_name);
                     $("#product_group").val(res.data.subcategories.subcategory_name);
                     $("#specification").val(res.data.specification);
                     $("#product_no").val(res.data.product_no);
                     $("#phase").val(res.data.phase);
                     $("#product_code").prop('readonly', true);
                     $("#category").prop('readonly', true);
                     $("#product_group").prop('readonly', true);
                     $("#specification").prop('readonly', true);
                     $("#product_no").prop('readonly', true);
                     $("#phase").prop('readonly', true);
                     if(res.data_all.invoice_date != '' && res.data_all.invoice_date != null){
                        $("#company_sale_bill_date").val(res.data_all.invoice_date.split('-').reverse().join('-'));
                        $("#company_sale_bill_date").change();
                     }
                     $("#company_sale_bill_no").val(res.data_all.invoice_no);
                     $("#seller").val(res.data_all.party_name);
                     $("#seller").prop('readonly', true);
                     
                     if (res.check_Warranty != null && res.check_Warranty.status == "1") {
                        $("input, select, textarea, button")
                        .not("#serail_number, #complaint_number, #complaint_date, #product_serail_number, #product_group, #company_bill_date_month, #customer_bill_date_month , #warrenty_time , #purchased_branch , #complaint_type , #register_by , #product_laying , input[name='_method'] , #description , #complaint_recieve_via","#service_center")
                        .prop("readonly", false)
                        .prop("disabled", false);
                        $("#customer_bill_date").val(res.check_Warranty.sale_bill_date.split('-').reverse().join('-'));
                        $("#customer_bill_no").val(res.check_Warranty.sale_bill_no);
                        $("#customer_number").val(res.check_Warranty.customer.customer_number);
                        $("#customer_number").keyup();
                        $("#customer_bill_date").change();
                        var rawDate = res.check_Warranty.warranty_date; // e.g., "24-10-2025"

                        // Split and reformat to "YYYY-MM-DD"
                        var parts = rawDate.split("-");
                        var formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;

                        // Create Date objects
                        var warrantyDate = new Date(formattedDate);
                        warrantyDate.setHours(0, 0, 0, 0);
                        var today = new Date();
                        today.setHours(0, 0, 0, 0);

                        if (res.check_Warranty.seller_details && res.check_Warranty.seller_details != null) {
                           var newOption = new Option(res.check_Warranty.seller_details.name, res.check_Warranty.seller_details.id, false, false);
                           $('#party_name').append(newOption).trigger('change');
                           $("#party_name").val(res.check_Warranty.seller_details.id);
                           $("#party_name").trigger('change');
                        } else {
                           $("#party_name").val('');
                           $("#party_name").trigger('change');
                        }

                        if (warrantyDate > today) {
                           $("#under_warranty").val('Yes').trigger('change');
                           $("#under_warranty").change();
                           $("#service_type").val('Free');
                           $("#service_type").change();
                        } else {
                           $("#under_warranty").val('No');
                           $("#under_warranty").change();
                           $("#service_type").val('Paid');
                           $("#service_type").change();
                        }

                        if (res.check_Warranty.media.length > 0) {
                           $('#warranty_bill').val('Yes').trigger('change');;
                           var attaExt = res.check_Warranty.media[0].original_url.split('.').pop().toLowerCase();
                           $("#invoice-div").removeClass('d-none');
                           $("#invoice-div a").prop('href', res.check_Warranty.media[0].original_url);
                           if (attaExt == 'pdf') {
                              $("#invoice-img").attr('src', '{{url("/public/assets/img/pdf-icon.jpg")}}');
                           } else {
                              $("#invoice-img").attr('src', res.check_Warranty.media[0].original_url);
                           }
                        } else {
                           $('#warranty_bill').val('No').trigger('change');
                           $("#invoice-div").addClass('d-none');
                        }
                        $("#submit-btn").prop("disabled", false);
                     } else {
                        $("input, select, textarea, button").not("#serail_number").prop("readonly", true).prop("disabled", true);
                        $('#submit-btn').prop("disabled", true);
                        if(res.check_Warranty && res.check_Warranty.status == "0"){
                           var active_url = "{{ route('warranty_activation.edit', ['warranty_activation' => '__ID__']) }}";
                           var encrypt_id = res.encrypt_id;  
                           active_url = active_url.replace('__ID__', encrypt_id); 
                           Swal.fire({
                              title: "Warranty is in IN Verification " + serial_no + " serial number. Please activate the warranty first.",
                              icon: "error",
                              showCancelButton: true,
                              confirmButtonText: '<a href="' + active_url + "?back=true" + '" style="color: white; text-decoration: none;">Activate Warranty</a>',
                              cancelButtonText: "Cancel",
                              cancelButtonColor: '#d33',
                              confirmButtonColor: '#3085d6'
                           });
                        }else{
                           var active_url = "{{ route('warranty_activation.create') }}?serial_no=" + serial_no;
                           Swal.fire({
                              title: "Warranty is not active of " + serial_no + " serial number. Please activate the warranty first.",
                              icon: "error",
                              showCancelButton: true,
                              confirmButtonText: '<a href="' + active_url + "&back=true" + '" style="color: white; text-decoration: none;">Activate Warranty</a>',
                              cancelButtonText: "Cancel",
                              cancelButtonColor: '#d33',
                              confirmButtonColor: '#3085d6'
                           });
                        }
                        $("#submit-btn").prop("disabled", true);
                        $("#customer_bill_date").val(" ");
                        $("#customer_number").val(" ");
                     }
                  } else {
                     $("input, select, textarea, button").not("#serail_number").prop("readonly", true).prop("disabled", true);
                     $('#submit-btn').prop("disabled", true);
                   var active_url = "{{ route('warranty_activation.create') }}?serial_no=" + serial_no;
                   Swal.fire({
                       title: "Warranty is not active of " + serial_no + " serial number. Please activate the warranty first.",
                       icon: "error",
                       showCancelButton: true,
                       confirmButtonText: '<a href="' + active_url + "&back=true" + '" style="color: white; text-decoration: none;">Activate Warranty</a>',
                       cancelButtonText: "Cancel",
                       cancelButtonColor: '#d33',
                       confirmButtonColor: '#3085d6'
                   });
                     $("#party_name").val('');
                     $("#party_name").change();
                     $("#company_sale_bill_date").val(" ");
                     $("#company_sale_bill_no").val(" ");
                     $("#product_code").val(" ");
                     $("#product_name").val(" ");
                     $("#product_id").val(" ");
                     $("#product_id").change();
                     $("#category").val(" ");
                     $("#specification").val(" ");
                     $("#product_no").val(" ");
                     $("#phase").val(" ");
                     $("#company_sale_bill_date").val(" ");
                     $("#company_sale_bill_no").val(" ");
                     $("#seller").val(" ");
                     $("#product_code").prop('readonly', false);
                     $("#product_name").prop('readonly', false);
                     $("#category").prop('readonly', false);
                     $("#specification").prop('readonly', false);
                     $("#product_no").prop('readonly', false);
                     $("#phase").prop('readonly', false);
                     $("#seller").prop('readonly', false);
                     $("#company_sale_bill_date").change();
                     if (chekcPro != "") {
                        $("#product_id").val(chekcPro);
                        $("#product_id").change();
                     }
                  }
               }
            });
          }, 500);
      }).trigger('keyup');

      $("#under_warranty").on('change' , function() {
         var under_warrenty = $(this).val();
         if(under_warrenty == 'Yes'){
            $('#service_type').val('Free').trigger('change');
         }
      })

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
                  }, 2000);

               } else {
                  $("#customer_name").val("");

                  $("#end_user_id").val("");
                  $("#customer_email").val("");
                  $("#customer_address").val("");
                  $("#customer_place").val("");
                  $("#customer_pindcode").val("").trigger("change");;
                  $("#customer_state").val("");
                  $("#customer_district").val("");
                  $("#customer_city").val("");
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
      $("#go-search").on('click', function() {
         var search = $("#serail_number").val();
         if (!search || search == '' || search == null) {
            $("#serail_number").focus();
            $("#search_error").html("Please enter Contact Number or Searial Number");
            $("#search_error").removeClass("d-none");
         } else {
            $("#search_error").addClass("d-none");
            $("#search_data").removeClass("d-none");
            $.ajax({
               url: "{{ url('getComplaintsData') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  search: search
               },
               success: function(res) {
                  if (res.status === true) {
                     $("#search_data table tbody").html(res.data);
                  } else {
                     if (res.data) {
                        $("#search_data table tbody").html(res.data);
                     }
                  }
               }
            });
         }
      });
      $(document).on("click", ".delete-img-btn", function() {
         var id = $(this).data('mediaid');
         Swal.fire({
            title: "ARE YOU SURE TO DELETE ATTACHMENT ?",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: "YES",
            denyButtonText: `Don't`
         }).then((result) => {
            if (result.value) {
               $(this).closest('.img-div').remove();
               $.ajax({
                  url: "{{ url('complaint-attach-delete') }}",
                  dataType: "json",
                  type: "POST",
                  data: {
                     _token: "{{csrf_token()}}",
                     id: id
                  },
                  success: function(res) {
                     if (res.status === true) {
                        Swal.fire("Attachment delete successfully !", res.msg, "success");
                     } else {
                        Swal.fire("Somthing went wrong", "", "error");
                     }
                  }
               });
            }
         });
      })

      $("#serail_number").on("keyup", function(event) {
         $("#product_serail_number").val($(this).val());
         var key = event.key || event.which || event.keyCode;

         var ctrlKey = 'Control';
         var shiftKey = 'Shift';
         var altKey = 'Alt';

         if (typeof key === 'string') {
            if (key === ctrlKey || key === shiftKey || key === altKey || key === 'c' || key === 'a') {
               return;
            }
         }
         $("#product_serail_number").keyup();
      });
      
      $("#product_id").on("change", function() {
         var product_id = $(this).val();
         if (product_id != null && product_id != '') {
            $.ajax({
               url: "{{ url('getProductInfo') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  product_id: product_id
               },
               success: function(res) {

                  $("#product_code").val(res.product_code);
                  $("#product_name").val(res.product_name);
                  $("#category").val(res.categories.category_name);
                  $("#specification").val(res.specification);
                  $("#product_group").val(res.subcategories.subcategory_name);
                  $("#product_no").val(res.product_no);
                  $("#warrenty_time").val(res.warrenty_time);
                  $("#phase").val(res.phase);
                  $("#product_code").prop('readonly', true);
                  $("#category").prop('readonly', true);
                  $("#specification").prop('readonly', true);
                  $("#product_no").prop('readonly', true);
                  $("#phase").prop('readonly', true);
                  $("#warrenty_time").prop('readonly', true);
               }
            });
         } else {
            $("#product_code").val("");
            $("#product_name").val("");
            $("#warrenty_time").val("");
            $("#category").val("");
            $("#specification").val("");
            $("#product_no").val("");
            $("#phase").val("");
            $("#product_code").prop('readonly', false);
            $("#category").prop('readonly', false);
            $("#specification").prop('readonly', false);
            $("#product_no").prop('readonly', false);
            $("#phase").prop('readonly', false);
            $("#warrenty_time").prop('readonly', false);
         }
      });
      
      $('#customer_city').on('change' , function(){
           // $('#assign_user').empty();
           var customer_city = $(this).val();
           var selected_user_id = "{{ $complaints['assign_user'] ?? '' }}";
           if(selected_user_id){
                $('#assign_user').val(selected_user_id).trigger('change');
           }else{
               $.ajax({
               url: "{{ url('getUserByBranch') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  customer_city: customer_city,
               },
               success: function(res) {
                  $('#assign_user').val(res.id).trigger('change');
               }
            });
         }

      }); 

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
      //    var sel_id = $('#customer_pindcode').val() ;
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
      //             if(sel_id == val.id){
      //                 options += '<option value="' + val.id + '" ' + (sel_id == val.id ? 'selected' : '') + '>' + val.pincode + '</option>';
      //             }
                 
      //          })
      //          $("#customer_pindcode").append(options);
      //       }
      //    });
      // });

      $(document).ready(function() {
         var serial_number = "{{ $serial_number ?? '' }}";
         if(serial_number && serial_number != ''){
            $('#serail_number').val(serial_number).trigger("keyup");;
         }
         $('#fileInput').on('change', function() {
            const imagePreviewContainer = $('#imagePreview');
            const filePreviewContainer = $('#filePreview');
            imagePreviewContainer.empty(); // Clear previous images
            filePreviewContainer.empty(); // Clear previous file previews

            const files = this.files;
            $.each(files, function(index, file) {
               if (file.type.startsWith('image/')) {
                  // Handle image files
                  const reader = new FileReader();
                  reader.onload = function(e) {
                     const img = $('<img>').attr('src', e.target.result);
                     imagePreviewContainer.append(img);
                  };
                  reader.readAsDataURL(file);
               } else {
                  // Handle non-image files
                  const fileName = file.name;
                  // Create file item
                  const fileItem = $('<div>').addClass('file-item');
                  const name = $('<span>').text(fileName);

                  fileItem.append(name);
                  filePreviewContainer.append(fileItem);
               }
            });
         });

         // jquery validation 

         $("#customer_number").on("input", function () {
            let value = $(this).val();

            // Remove non-numeric characters and ensure max 10 digits
            value = value.replace(/\D/g, '').substring(0, 10);

            // Set the cleaned value back
            $(this).val(value);

            // Check if the length is exactly 10 digits
            if (value.length !== 10) {
               $("#customer_number_error").text("Customer number must be exactly 10 digits.").show();
            } else {
               $("#customer_number_error").hide();
            }
         });

         $('#storeComplaintData').validate({
             rules:{
               customer_number : {
                  required : true,
                  minlength : 10,
                  maxlength: 10,
               }
             },
             messages : {
               customer_number : {
                  minlength : "Customer mobile number must equal to 10 digits only",
                  maxlength: "Customer mobile number must equal to 10 digits only",
               }
             }
         });
      });
   </script>
</x-app-layout>
