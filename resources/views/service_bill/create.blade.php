<x-app-layout>
   <style>
      .select2-results__options {
         overflow: auto;
         max-height: 200px !important;
      }
body table td {
    color: #363954;}
    /*  .select2-container {
         border-bottom: 1px solid lightgray;
      }*/

      button.delete-img-btn {
         position: absolute;
         top: 7px;
         right: 8px;
      }

      .row {
         align-items: end !important;
      }

      .inp-div {
         position: relative;
         display: flex;
         align-items: center;
         background: #ebe7e7;
         border-radius: 5px;
      }

      .inp-div input {
         position: absolute;
         top: 0;
         width: 100%;
         height: 100%;
         opacity: 0;
         cursor: pointer;
      }

      .inp-div i:first-child {
         font-size: 40px !important;
      }

      .remove-tr {
         font-size: 15px !important;
         font-weight: 900 !important;
         padding: 4px 10px 4px 10px !important;
      }

      .imgdiv img {
         border-radius: 10px;
         width: 100%;
         height: 120px !important;
      }

      .imgdiv {
         margin: 2px;
         padding: 5px;
         border: 1px dashed;
         border-radius: 10px;
         width: 100%;
         height: 135px !important;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card m-0 p-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        {{$service_bill->exists?'Edit':'Create'}} Service Bill   </h4>
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="javascript:void(0);" onclick="window.history.back();">
                                 <i class="material-icons">next_plan</i> Service Bills
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
               {!! Form::model($service_bill,[
               'route' => 'service_bills.store',
               'method' => 'POST',
               'id' => 'storeServiceBillData',
               'files'=>true
               ]) !!}
               <div class="row mt-2 mb-2">
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label" for="service_bill_no">Service Bill No.</label>
                        <input class="form-control" type="text" readonly name="service_bill_no" id="service_bill_no" value="{{$serviceBillNo}}">
                        @if($service_bill->exists)
                        <input type="hidden" name="complaint_number" id="complaint_number" value="{{$complaint->complaint_number}}">
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label class="col-form-label" for="complaint_number">Complaint Number</label>
                        <select disabled {{$service_bill->exists?'disabled':''}} name="complaint_number" id="complaint_number" class="select2" required>
                           <option value="">Select Complaint Number</option>
                           @if(count($all_complaint_number) > 0)
                           @foreach($all_complaint_number as $val)
                           <option value="{{$val->complaint_number}}" {{($complaint && $complaint->complaint_number == $val->complaint_number)?'selected':''}}>{{$val->complaint_number}}</option>
                           @endforeach
                           @endif
                        </select>
                        <input type="hidden" readonly name="product_division" id="product_division" value="{{($complaint?($complaint->product_details?$complaint->product_details->categories->id:''):'')}}">
                        <input type="hidden" readonly name="product_group" id="product_group" value="{{($complaint?($complaint->product_details?$complaint->product_details->subcategories->id:''):'')}}">
                        <input type="hidden" readonly name="complaint_details" id="complaint_details" value="{{($complaint?$complaint:'')}}">
                        <input type="hidden" readonly name="complaint_id" id="complaint_id" value="{{($complaint?($complaint->id?$complaint->id:''):'')}}">
                        <input type="hidden" readonly name="service_bill_id" id="service_bill_id" value="{{($service_bill?($service_bill->id?$service_bill->id:''):'')}}">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="input_section">
                        <label for="division" class="col-form-label">Division</label>
                        <select disabled name="division" id="division" class="select2">
                           <option value="">Select Division</option>
                           @if($divisions && count($divisions) > 0)
                           @foreach($divisions as $division)
                           <option value="{{$division->id}}">{{$division->category_name}}</option>
                           @endforeach
                           @endif
                        </select>
                     </div>
                  </div>

                  <div class="col-md-3">
                     <div class="input_section">
                        <label for="division" class="col-form-label">Product Group</label>
                        <select disabled name="subcategory_id" id="subcategory_id" class="select2">
                           <option value="">Select Group</option>
                           @if($groups && count($groups) > 0)
                           @foreach($groups as $group)
                           <option value="{{$group->id}}">{{$group->subcategory_name}}</option>
                           @endforeach
                           @endif
                        </select>
                     </div>
                  </div>
               </div>

               <h3 class="mt-2" style="color: black;"><b>Complaint Details: </b></h3>
               <hr>
               <div class="row mt-2 mb-2">
                  <table class="table" id="complain_details">
                     <thead>
                        <tr>
                           <th>Complaint Number</th>
                           <th>Recived From</th>
                           <th>Date</th>
                           <th>Item</th>
                           <th>Comments</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td>{{$complaint?$complaint->complaint_number:'-'}}</td>
                           <td>{{$complaint?($complaint->createdbyname?$complaint->createdbyname->name:''):'-'}}</td>
                           <td>{{$complaint?date('d M Y' ,strtotime($complaint->complaint_date)):'-'}}</td>
                           <td>{{$complaint?($complaint->product_details?$complaint->product_details->product_name:''):''}}</td>
                           <td>{{$complaint?$complaint->description:'-'}}</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
 
               <h3 class="mt-2" style="color: black;"><b>Warranty Details: </b></h3>
               <hr>
               <div class="row mt-2 mb-2">
                  <table class="table table-striped responsive" id="warranty_details">
                     <thead>
                        <tr>
                           <th>Product Serial No.</th>
                           <th>Item</th>
                           <th>Warranty Start Date</th>
                           <th>Warranty Upto</th>
                           <th>Warranty Status</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td>{{$complaint?strtoupper($complaint->product_serail_number):'-'}}</td>
                           <td>{{$complaint?'['.$complaint->product_code.']':''}} {{$complaint?$complaint->product_name:'-'}}</td>
                           <td>
                              @if($complaint)
                              @if ($complaint->customer_bill_date)
                              {{ date('d-m-Y', strtotime($complaint->customer_bill_date)) }}
                              @else
                              -
                              @endif
                              @else
                              -
                              @endif
                           </td>
                           <td>
                              @if($complaint)
                               @if ($complaint->customer_bill_date)
                                   @php
                                       $today = \Carbon\Carbon::today();

                                       try {
                                           $date = \Carbon\Carbon::parse($complaint->customer_bill_date);
                                           if ($date !== false) {
                                               $date->addMonths(18);
                                           } else {
                                               $date = null;
                                           }
                                       } catch (Exception $e) {
                                           $date = null;
                                       }
                                   @endphp
                                   @if ($date)
                                       {{ $date->format('d-m-Y') }}
                                   @else
                                       Invalid date
                                   @endif
                               @else
                                   -
                               @endif
                           @else
                               -
                           @endif


                           </td>
                           <td>
                              @if($complaint)
                              @if ($complaint->customer_bill_date)
                              @if ($date)
                              @if ($date->gt($today))
                              <span class="badge badge-success">In Warranty</span>
                              @else
                              <span class="badge badge-danger">Out Of Warranty</span>
                              @endif
                              @else
                              Invalid date
                              @endif
                              @else
                              -
                              @endif
                              @else
                              -
                              @endif
                           </td>
                        </tr>
                     </tbody>

                  </table>
               </div>
               <h3 class="mt-2" style="color: black;"><b>Complaint Category: </b></h3>
               <div class="border border-dark rounded p-4">
                  <div class="row mt-2 mb-2">
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="category">Category Of Complaint</label>
                           <select name="category" id="category" class="select2">
                              <option value="">Select Category</option>
                              <option value="Electrical Fault" {{($service_bill && $service_bill->category == 'Electrical Fault')?'selected':''}}>Electrical Fault</option>
                              <option value="Mechanical Fault" {{($service_bill && $service_bill->category == 'Mechanical Fault')?'selected':''}}>Mechanical Fault</option>
                              <option value="Physical Fault" {{($service_bill && $service_bill->category == 'Physical Fault')?'selected':''}}>Physical Fault</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="complaint_type">Complaint Type</label>
                           <select name="complaint_type" id="complaint_type" class="select2">
                              <option value="">Select Complaint Type</option>
                               @if(count($service_bill_complaint) > 0)
                                 @foreach($service_bill_complaint as $complaint_type)
                                       <option value="{{$complaint_type->service_bill_complaint_type->service_bill_complaint_type_name ?? ''}}" 
                                           data-complaint-id="{{ $complaint_type->service_bill_complaint_type->id ?? '' }}" {{($service_bill && $service_bill->complaint_type == $complaint_type->service_bill_complaint_type->service_bill_complaint_type_name)?'selected':''}}>{{$complaint_type->service_bill_complaint_type->service_bill_complaint_type_name ?? ''}}</option>
                                 @endforeach
                              @endif
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label for="complaint_reason">Complaint Reason</label>
                           <select name="complaint_reason" id="complaint_reason" class="select2">
                              <option value="">Select Complaint Reason</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section" >
                           <label for="condition_of_service" class="col-form-label">Condition Of Service</label>
                           <select name="condition_of_service" id="condition_of_service" class="select2">
                              <option value="">Select Condition Of Service</option>
                              <option value="Full Finish" {{($service_bill && $service_bill->condition_of_service == 'Full Finish')?'selected':''}}>Full Finish</option>
                              <option value="Regular Repair" {{($service_bill && $service_bill->condition_of_service == 'Regular Repair')?'selected':''}}>Regular Repair</option>
                              <option value="Field Visit" {{($service_bill && $service_bill->condition_of_service == 'Field Visit')?'selected':''}}>Field Visit</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="received_product">Received Product</label>
                           <select name="received_product" id="received_product" class="select2">
                              <option value="">Select Received Product</option>
                              <option value="Pump" {{($service_bill && $service_bill->received_product == 'Pump')?'selected':''}}>Pump</option>
                              <option value="Motor" {{($service_bill && $service_bill->received_product == 'Motor')?'selected':''}}>Motor</option>
                              <option value="Pump Set" {{($service_bill && $service_bill->received_product == 'Pump Set')?'selected':''}}>Pump Set</option>
                              <option value="Fan" {{($service_bill && $service_bill->received_product == 'Fan')?'selected':''}}>Fan</option>
                              <option value="Heater" {{($service_bill && $service_bill->received_product == 'Heater')?'selected':''}}>Heater</option>
                              <option value="Induction CookTop" {{($service_bill && $service_bill->received_product == 'Induction CookTop')?'selected':''}}>Induction CookTop</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="input_section" for="nature_of_fault">Nature Of Fault</label>
                           <select name="nature_of_fault" id="nature_of_fault" class="select2">
                              <option value="">Select Nature Of Fault</option>
                              <option value="Transit Damage" {{($service_bill && $service_bill->nature_of_fault == 'Transit Damage')?'selected':''}}>Transit Damage</option>
                              <option value="Manufacturing Fault" {{($service_bill && $service_bill->nature_of_fault == 'Manufacturing Fault')?'selected':''}}>Manufacturing Fault</option>
                              <option value="Customer Field Fault" {{($service_bill && $service_bill->nature_of_fault == 'Customer Field Fault')?'selected':''}}>Customer Field Fault</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="service_location">Service Location</label>
                           <select name="service_location" id="service_location" class="select2">
                              <option value="">Select Service Location</option>
                              <option value="Site Visit" {{($service_bill && $service_bill->service_location == 'Site Visit')?'selected':''}}>Site Visit</option>
                              <option value="At ASC" {{($service_bill && $service_bill->service_location == 'At ASC')?'selected':''}}>At ASC</option>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>

                 <h3 class="mt-2" style="color: black;"><b>Field Data: </b></h3>
               <div class="border border-dark rounded p-4">
                  <div class="row mt-2 mb-2">
                      <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="line_voltage">Line Voltage (V)</label>
                           <input type="text" class="form-control"  value="{{$service_bill->line_voltage ?? ''}}" name="line_voltage" id="line_voltage" placeholder="Enter Line Voltage (V)"maxlength="3">
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="load_voltage">Load Voltage (V)</label>
                           <input type="text" class="form-control"  value="{{$service_bill->load_voltage ?? ''}}" name="load_voltage" id="load_voltage" placeholder="Enter Load Voltage (V)"maxlength="3">
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="current">Current (A)</label>
                           <input type="text" class="form-control" value="{{$service_bill->current ?? ''}}" name="current" id="current" placeholder="Enter Current Voltage (V)" maxlength="2">
                        </div>
                     </div>

                      <div class="col-md-3">
                        <div class="input_section">
                           <label class="input_section" for="water_source">Water Source (Product Used)</label>
                           <select name="water_source" id="water_source" class="select2">
                              <option value="">Select Water Source (Product Used)</option>
                              <option value="Munciple Water Supply" {{($service_bill && $service_bill->water_source == 'Munciple Water Supply')?'selected':''}}>Munciple Water Supply</option>
                              <option value="Munciple Water Supply" {{($service_bill && $service_bill->water_source == 'Well')?'selected':''}}>Well</option>
                              <option value="Munciple Water Supply" {{($service_bill && $service_bill->water_source == 'BoreWell')?'selected':''}}>BoreWell</option>
                              <option value="Munciple Water Supply" {{($service_bill && $service_bill->water_source == 'Water Sump')?'selected':''}}>Water Sump</option>
                              <option value="Munciple Water Supply" {{($service_bill && $service_bill->water_source == 'Hand Pump')?'selected':''}}>Hand Pump </option>
                              <option value="Munciple Water Supply" {{($service_bill && $service_bill->water_source == 'Pond / Dam')?'selected':''}}>Pond / Dam</option>
                              <option value="Munciple Water Supply" {{($service_bill && $service_bill->water_source == 'RO Water Plant')?'selected':''}}>RO Water Plant</option>                              
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="panel_rating_running">Panel Rating-Running Capacitor (mfd) </label>
                           <input type="text" class="form-control" name="panel_rating_running" id="panel_rating_running" value="{{$service_bill->panel_rating_running ?? ''}}" placeholder="Enter Panel Rating-Running Capacitor (mfd)" maxlength="3">
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="panel_rating_starting">Panel Rating-Starting Capacitor (mfd)</label>
                           <input type="text" class="form-control" value="{{$service_bill->panel_rating_starting ?? ''}}" name="panel_rating_starting" id="panel_rating_starting" placeholder="Enter Panel Rating-Starting Capacitor (mfd)" maxlength="7">
                        </div>
                     </div>
                  </div>
               </div>

               <h3 class="mt-2" style="color: black;"><b>Service Type: </b></h3>
               <div class="border border-dark rounded p-4">
                  <div class="row mt-2 mb-2">
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="repaired_replacement">Repaired / Replacement</label>
                           <select name="repaired_replacement" id="repaired_replacement" class="select2" required>
                              <option value="">Please Select</option>
                              <option value="Repaired" {{($service_bill && $service_bill->repaired_replacement == 'Repaired')?'selected':''}}>Repaired</option>
                              <option value="Replacement" {{($service_bill && $service_bill->repaired_replacement == 'Replacement')?'selected':''}}>Replacement</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="input_section">
                           <label class="col-form-label" for="replacement_tag">Replacement Tag</label>
                           <select disabled name="replacement_tag" id="replacement_tag" class="select2">
                              <option value="">Select Replacement Tag</option>
                              <option value="Yes">Yes</option>
                              <option value="No">No</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-3 d-none" id="tag-number">
                        <div class="input_section">
                           <input type="text" name="replacement_tag_number" id="replacement_tag_number" placeholder="Replacement Tag Number" value="{{($service_bill?($service_bill->replacement_tag_number?$service_bill->replacement_tag_number:''):'')}}" class="form-control">
                        </div>
                     </div>
                  </div>
               </div>

               <h3 class="mt-2" style="color: black;"><b>Photos: </b></h3>
               <div class="border border-dark rounded p-4">
                  <div class="row mt-2 mb-2">
                     <div class="col-md-3 file-upload-group">
                        <label for="product_sr_no">Product Sr. No.</label>
                        <div class="inp-div">
                           <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                           <p class="m-0">Attach a File</p>
                           <input type="file" name="product_sr_no" id="product_sr_no" class="form-control file-input" accept="image/*">
                        </div>
                        <span class="file-count">0</span> files selected
                         <div class="preview-wrapper" style="margin-top:10px;">
                             <img class="preview-img" src="" style="display:none; max-height: 100px; border-radius: 5px;" />
                             <!-- <p class="file-name" style="display:none; font-weight: bold; margin-top: 5px;"></p> -->
                         </div>
                        @if($service_bill->exists && $service_bill->getMedia('product_sr_no')->count() > 0 && Storage::disk('s3')->exists($service_bill->getMedia('product_sr_no')[0]->getPath()))
                        <div class="imgdiv">
                           <a target="_blank" href="{{ $service_bill->getMedia('product_sr_no')[0]->getFullUrl() }}">
                              <img width="150" src="{{ $service_bill->getMedia('product_sr_no')[0]->getFullUrl() }}" alt="">
                           </a>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-3 file-upload-group">
                        <label for="scr_job_card">SCR-Job Card</label>
                        <div class="inp-div">
                           <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                           <p class="m-0">Attach a File</p>
                           <input type="file" name="scr_job_card" id="scr_job_card" class="form-control file-input" accept="image/*">
                        </div>
                        <span class="file-count">0</span> files selected
                        <div class="preview-wrapper" style="margin-top:10px;">
                             <img class="preview-img" src="" style="display:none; max-height: 100px; border-radius: 5px;" />
                             <!-- <p class="file-name" style="display:none; font-weight: bold; margin-top: 5px;"></p> -->
                         </div>
                        @if($service_bill->exists && $service_bill->getMedia('scr_job_card')->count() > 0 && Storage::disk('s3')->exists($service_bill->getMedia('scr_job_card')[0]->getPath()))
                        <div class="imgdiv">
                           <a target="_blank" href="{{ $service_bill->getMedia('scr_job_card')[0]->getFullUrl() }}">
                              <img width="150" src="{{ $service_bill->getMedia('scr_job_card')[0]->getFullUrl() }}" alt="">
                           </a>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-3 file-upload-group">
                        <label for="photo_3">Complaint Photo 1</label>
                        <div class="inp-div">
                           <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                           <p class="m-0">Attach a File</p>
                           <input type="file" name="photo_3" id="photo_3" class="form-control file-input" accept="image/*">
                        </div>
                        <span class="file-count">0</span> files selected
                        <div class="preview-wrapper" style="margin-top:10px;">
                             <img class="preview-img" src="" style="display:none; max-height: 100px; border-radius: 5px;" />
                             <!-- <p class="file-name" style="display:none; font-weight: bold; margin-top: 5px;"></p> -->
                         </div>
                        @if($service_bill->exists && $service_bill->getMedia('photo_3')->count() > 0 && Storage::disk('s3')->exists($service_bill->getMedia('photo_3')[0]->getPath()))
                        <div class="imgdiv">
                           <a target="_blank" href="{{ $service_bill->getMedia('photo_3')[0]->getFullUrl() }}">
                              <img width="150" src="{{ $service_bill->getMedia('photo_3')[0]->getFullUrl() }}" alt="">
                           </a>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-3 file-upload-group">
                        <label for="photo_4">Complaint Photo 2</label>
                        <div class="inp-div">
                           <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                           <p class="m-0">Attach a File</p>
                           <input type="file" name="photo_4" id="photo_4" class="form-control file-input" accept="image/*">
                        </div>
                        <span class="file-count">0</span> files selected
                        <div class="preview-wrapper" style="margin-top:10px;">
                             <img class="preview-img" src="" style="display:none; max-height: 100px; border-radius: 5px;" />
                             <!-- <p class="file-name" style="display:none; font-weight: bold; margin-top: 5px;"></p> -->
                         </div>
                        @if($service_bill->exists && $service_bill->getMedia('photo_4')->count() > 0 && Storage::disk('s3')->exists($service_bill->getMedia('photo_4')[0]->getPath()))
                        <div class="imgdiv">
                           <a target="_blank" href="{{ $service_bill->getMedia('photo_4')[0]->getFullUrl() }}">
                              <img width="150" src="{{ $service_bill->getMedia('photo_4')[0]->getFullUrl() }}" alt="">
                           </a>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-3 file-upload-group">
                        <label for="photo_5">Complaint/Spare Photo </label>
                        <div class="inp-div">
                           <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                           <p class="m-0">Attach a File</p>
                           <input type="file" name="photo_5" id="photo_5" class="form-control file-input" accept="image/*">
                        </div>
                        <span class="file-count">0</span> files selected
                        <div class="preview-wrapper" style="margin-top:10px;">
                             <img class="preview-img" src="" style="display:none; max-height: 100px; border-radius: 5px;" />
                             <!-- <p class="file-name" style="display:none; font-weight: bold; margin-top: 5px;"></p> -->
                         </div>
                        @if($service_bill->exists && $service_bill->getMedia('photo_5')->count() > 0 && Storage::disk('s3')->exists($service_bill->getMedia('photo_5')[0]->getPath()))
                        <div class="imgdiv">
                           <a target="_blank" href="{{ $service_bill->getMedia('photo_5')[0]->getFullUrl() }}">
                              <img width="150" src="{{ $service_bill->getMedia('photo_5')[0]->getFullUrl() }}" alt="">
                           </a>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-3 file-upload-group">
                        <label for="photo_5">Voltage </label>
                        <div class="inp-div">
                           <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                           <p class="m-0">Attach a File</p>
                           <input type="file" name="voltage_image" id="voltage_image" class="form-control file-input" accept="image/*">
                        </div>
                        <span class="file-count">0</span> files selected
                        <div class="preview-wrapper" style="margin-top:10px;">
                             <img class="preview-img" src="" style="display:none; max-height: 100px; border-radius: 5px;" />
                             <!-- <p class="file-name" style="display:none; font-weight: bold; margin-top: 5px;"></p> -->
                         </div>
                        @if($service_bill->exists && $service_bill->getMedia('voltage_image')->count() > 0 && Storage::disk('s3')->exists($service_bill->getMedia('voltage_image')[0]->getPath()))
                        <div class="imgdiv">
                           <a target="_blank" href="{{ $service_bill->getMedia('voltage_image')[0]->getFullUrl() }}">
                              <img width="150" src="{{ $service_bill->getMedia('voltage_image')[0]->getFullUrl() }}" alt="">
                           </a>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-3 file-upload-group">
                        <label for="photo_5">Current </label>
                        <div class="inp-div">
                           <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                           <p class="m-0">Attach a File</p>
                           <input type="file" name="current_image" id="current_image" class="form-control file-input" accept="image/*">
                        </div>
                        <span class="file-count">0</span> files selected
                        <div class="preview-wrapper" style="margin-top:10px;">
                             <img class="preview-img" src="" style="display:none; max-height: 100px; border-radius: 5px;" />
                             <!-- <p class="file-name" style="display:none; font-weight: bold; margin-top: 5px;"></p> -->
                         </div>
                        @if($service_bill->exists && $service_bill->getMedia('current_image')->count() > 0 && Storage::disk('s3')->exists($service_bill->getMedia('current_image')[0]->getPath()))
                        <div class="imgdiv">
                           <a target="_blank" href="{{ $service_bill->getMedia('current_image')[0]->getFullUrl() }}">
                              <img width="150" src="{{ $service_bill->getMedia('current_image')[0]->getFullUrl() }}" alt="">
                           </a>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               @if(isset($complaint->service_center))
               <h3 class="mt-2" style="color: black;"><b>Service: </b></h3>
               <hr>
               <div class="row mt-2 mb-2">
                  <table class="table table-striped" id="table-service">
                     <thead class="">
                        <tr>
                           <td style="width: 220px !important;">Service Type</td>
                           <td style="width: 320px !important;">Job Type / Description /HP</td>
                           <td>Quantity</td>
                           <td>Distance (KM)</td>
                           <td>Appreciation Charges</td>
                           <td>Charge Value</td>
                           <td>Sub Total</td>
                           <td>#</td>
                        </tr>
                     </thead>
                     @if($service_bill->exists && count($service_bill->service_bill_products) > 0)
                     <tbody>
                        @foreach($service_bill->service_bill_products as $k=>$product)
                        <tr>
                           <div class="input_section">
                           <input type="hidden" name="service[{{$k}}][service_bill_product_id]" id="service_bill_product_id" value="{{$product->id}}"></div>
                           <td>
                              <div class="input_section">
                              <select required name="service[{{$k}}][service_type]" class="form-control select2 chargety1 service_charge_type">
                                 @if(count($charge_type) > 0)
                                 @if($complaint->product_details->category_id == '2')
                                 @php $charge_type = $charge_type->whereIn('id', ['2','3']); @endphp
                                 @endif
                                 @foreach($charge_type as $ch_type)
                                 <option value="{{$ch_type->id}}" {{($product->service_type == $ch_type->id)?'selected':''}}>{{$ch_type->charge_type}}</option>
                                 @endforeach
                                 @endif
                              </select>
                           </div>
                           </td>
                           <td>
                              <div class="input_section">
                              <select required name="service[{{$k}}][product_id]" class="form-control select2 chargeprod">
                                 <option value="{{$product->product_id}}">{{$product->product?$product->product->product_name:$product->product_id}}</option>
                              </select>
                           </div>
                           </td>
                           <td>
                              <div class="input_section"><input type="number" readonly name="service[{{$k}}][quantity]" value="{{$product->quantity}}" class="form-control quantity" />
                              </div></td>
                           <td>
                              <div class="input_section"><input type="number" readonly name="service[{{$k}}][distance]" value="{{$product->distance}}" class="form-control distance" /></div></td>
                           <td><div class="input_section"><input type="number" readonly name="service[{{$k}}][appreciation]" value="{{$product->appreciation}}" class="form-control appreciation" /></div></td>
                           <td><div class="input_section"><input name="service[{{$k}}][price]" readonly class="form-control sprice" value="{{$product->price}}" />
                           </div></td>
                           <td><div class="input_section"><input name="service[{{$k}}][subtotal]" readonly class="form-control ssubtotal" value="{{$product->subtotal}}" /></div></td>
                           <td><button type="button" class="btn btn-danger btn-sm remove-tr m-0">X</button></td>
                        </tr>
                        @endforeach
                     </tbody>

                     <tbody>

                     </tbody>
                     @endif
                  </table>
                  <button type="button" class="btn btn-info btn-sm" id="add-service">ADD</button>
               </div>
               @endif
               <input type="submit" value="Submit" class="btn btn-success float-right mt-2">
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script>
      var counter = '{{($service_bill->exists && count($service_bill->service_bill_products) > 0)?count($service_bill->service_bill_products):"0"}}';
      $(document).on('change', '#complaint_number', function() {
         var complaint_number = $(this).val();
         $("#table-service").html('');
         $.ajax({
            url: "{{ url('getComplaintsDataProduct') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               complaint_number: complaint_number
            },
            success: function(res) {
               if (res.status == 'success') {
                  if (res.data.service_bill != null) {
                     $('#complaint_number').val('');
                     $('#complaint_number').change();
                     Swal.fire({
                        title: "Already Exists",
                        text: "The service bill with this complaint number is already exists please check service bill list and edit them.",
                        type: "error"
                     });
                     return false;
                  }
                  $("#complaint_id").val(res.data.complaint.id);
                  var html = '<tr><td>';
                  html += complaint_number;
                  html += '</td><td>';
                  if (res.data.complaint.createdbyname != null && res.data.complaint.createdbyname != '') {
                     html += res.data.complaint.createdbyname.name;
                  } else {
                     html += '-';
                  }
                  html += '</td><td>';

                  var date = new Date(res.data.complaint.complaint_date);
                  var options = {
                     day: '2-digit',
                     month: 'short',
                     year: 'numeric'
                  };
                  var formattedDate = date.toLocaleDateString('en-GB', options).replace(/ /g, ' ');

                  html += formattedDate;
                  html += '</td><td>';
                  if (res.data.product != '' && res.data.product != null) {
                     html += res.data.product.product_name;
                  } else {
                     html += '-';
                  }
                  html += '</td><td>';
                  if (res.data.complaint.description && res.data.complaint.description != null) {
                     html += res.data.complaint.description;
                  } else {
                     html += '-';
                  }
                  html += '</td></tr>';

                  $("#complain_details tbody").html(html);

                  var html2 = '<tr><td>';
                  html2 += res.data.complaint.product_serail_number;
                  html2 += '</td><td>';
                  if (res.data.product != '' && res.data.product != null) {
                     html2 += res.data.product.product_name;
                  } else {
                     html2 += '-';
                  }
                  html2 += '</td><td>';
                  if (res.data.complaint.customer_bill_date && res.data.complaint.customer_bill_date != null && res.data.complaint.customer_bill_date != '') {
                     var date = new Date(res.data.complaint.customer_bill_date);
                     var dateupto = moment(res.data.complaint.customer_bill_date);
                     dateupto.add(18, 'months');
                     var options = {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                     };
                     var formattedDate = date.toLocaleDateString('en-GB', options).replace(/ /g, ' ');
                     var formattedDateupto = dateupto.format('DD MMM YYYY');
                  } else {
                     var formattedDate = '-';
                     var formattedDateupto = '-';
                     var dateupto = null;
                  }

                  html2 += formattedDate;
                  html2 += '</td><td>';
                  html2 += formattedDateupto;

                  html2 += '</td><td>';
                  var today = moment();
                  if (dateupto != null) {
                     if (dateupto.isAfter(today)) {
                        html2 += '<span class="badge badge-success">In Warranty</span>';
                     } else {
                        html2 += '<span class="badge badge-danger">Out Of Warranty</span>';
                     }
                  } else {
                     html2 += '<span class="badge badge-danger">Out Of Warranty</span>';
                  }
                  html2 += '</td></tr>';

                  $("#warranty_details tbody").html(html2);

                  if (res.data.product != '' && res.data.product != null) {
                     $('#division').val(res.data.product.category_id);
                     $('#subcategory_id').val(res.data.product.subcategory_id);
                     $('#division').change();
                      $('#subcategory_id').change();
                  } else {
                     $('#division').val('');
                     $('#division').change();
                     $('#subcategory_id').val('');
                     $('#subcategory_id').change();
                  }

               }
            }
         });
        
      });
      $(document).ready(function() {
         var division = $('#product_division').val();
         if (division != '' && division != null) {
            $('#division').val(division);
            $('#division').change();
         }
         var groups = $('#product_group').val();
         if (groups != '' && groups != null) {
            $('#subcategory_id').val(groups);
            $('#subcategory_id').change();
         }

         let complaint_id = "{{$complaint->id ?? ''}}";
         if(complaint_id){
             getServiceChargeType(complaint_id , '.chargety');
         }
      })

       // get service types
      function getServiceChargeType(complaint_id = '', class_name = '') {
          if (!class_name) {
              console.warn("Class name is empty. No elements will be updated.");
              return;
          }

          $.ajax({
              url: "{{ url('getServiceChargeType') }}",
              dataType: "json",
              type: "POST",
              data: {
                  _token: "{{ csrf_token() }}",
                  complaint_id: complaint_id
              },
              success: function(res) {
                  if (res.status === true) {
                      $(class_name).html(res.html);
                  }
              },
              error: function(xhr, status, error) {
                  console.error("Error fetching service charge types:", error);
              }
          });
      }


      $('#repaired_replacement').on('change', function() {
         if ($(this).val() == 'Replacement') {
            $("#replacement_tag").val('Yes');
            $("#replacement_tag").change();
            $("#tag-number").removeClass('d-none');
         } else if ($(this).val() == 'Repaired') {
            $("#replacement_tag").val('No');
            $("#replacement_tag").change();
            $("#tag-number").addClass('d-none');
         } else {
            $("#replacement_tag").val('');
            $("#replacement_tag").change();
            $("#tag-number").addClass('d-none');
         }
      }).trigger('change');
      $(document).on("click", "#add-service", function() {
         var pro_cat = '{{ $complaint?$complaint->product_details?->category_id:"" }}';
         if (pro_cat == '2') {
            var chargeType = @json($charge_type->whereIn('id', ['2', '3']));
         } else {
            var chargeType = @json($charge_type);
         }
         var options = '<option value="">Select Service</option>';
         $.each(chargeType, function(k, v) {
            options += '<option value="' + v.id + '">' + v.charge_type + '</option>';
         });
         var newTR = '<tr><td><div class="input_section"><select required name="service[' + counter + '][service_type]" class="form-control select2 chargety">' + options + '</select></div></td><td class="product_td"><div class="input_section"><select required name="service[' + counter + '][product_id]" class="form-control select2 chargeprod"></select></div></td><td><div class="input_section"><input type="number" readonly name="service[' + counter + '][quantity]" value="1" class="form-control quantity" /></div></td><td><div class="input_section"><input type="number" readonly name="service[' + counter + '][distance]"  class="form-control distance" /></div></td><td><div class="input_section"><input type="number" readonly name="service[' + counter + '][appreciation]" class="form-control appreciation" /></div></td><td><div class="input_section"><input name="service[' + counter + '][price]" readonly class="form-control sprice" /></div></td><td><div class="input_section"><input name="service[' + counter + '][subtotal]" readonly class="form-control ssubtotal" /></div></td><td><button type="button" class="btn btn-danger btn-sm remove-tr m-0">X</button></td></tr>';
         counter++;
         $("#table-service").append(newTR);
         $('.select2').select2();
         // let complaint_id = "{{$complaint->id ?? ''}}";
         // getServiceChargeType(complaint_id, '.chargety')
      });

      $(document).ready(function() {
         var selected = "{{ $service_bill->complaint_reason }}";
         if(selected != null && selected != ''){
            getServiceBillReason();
         }
         // $(".chargety").trigger("change");
         $('#storeServiceBillData').validate({
           rules:{
             line_voltage:{
               maxlength : 3,
               number : true,
             },
             load_voltage:{
               maxlength : 3,
               number : true,
             },
             panel_rating_running:{
               maxlength : 3,
               number : true,
             },
             current:{
               maxlength : 2,
               number : true,
             },
             panel_rating_starting: {
               pattern: /^\d{3}\/\d{3}$/, // Regex to validate "234/120" format
             },
           },
           errorPlacement: function(error, element) {
               error.addClass('text-danger'); // Add Bootstrap error styling
               error.insertAfter(element.closest('.form-control')); // Insert after the select field
           },
           highlight: function(element) {
               $(element).addClass('is-invalid'); // Highlight error
           },
           unhighlight: function(element) {
               $(element).removeClass('is-invalid'); // Remove error highlight
           }
         });

         $('#panel_rating_starting').on('input', function () {
             let value = $(this).val().replace(/\D/g, ''); // Remove non-numeric characters
             if (value.length > 3) {
                 value = value.substring(0, 3) + '/' + value.substring(3, 6);
             }
             $(this).val(value);
         });

         $('#line_voltage , #load_voltage , #panel_rating_running').on('input', function () {
             let value = $(this).val().replace(/\D/g, ''); // Remove non-numeric characters
             if (value.length > 3) {
                 value = value.substring(0, 2);
             }
             $(this).val(value);
         });

         $('#current').on('input', function () {
             let value = $(this).val().replace(/\D/g, ''); // Remove non-numeric characters
             if (value.length > 2) {
                 value = value.substring(0, 1);
             }
             $(this).val(value);
         });
      })

      $(document).on("change", ".chargety , .chargety1", function()  {
         var selectedValues = [];
         var id = $(this).val();
         $('.chargety').each(function() {
            selectedValues.push($(this).val());
         });
         var count3 = 0;
         var count5 = 0;
         $.each(selectedValues, function(index, value) {
            if (value == "3") {
               count3++;
            }
            if (value == "5") {
               count5++;
            }
         });
         if (count3 > 1 || count5 > 1) {
            $(this).val('');
            $(this).change();
            Swal.fire({
               title: "Wroung Selecation",
               text: "You can not select this type multiple time",
               type: "error"
            });
            return false;
         }
         var currentRow = $(this).closest('tr');
         var serviceProductSelecttd = currentRow.find('td.product_td');
         serviceProductSelecttd.html('<select required name="service[' + (counter - 1) + '][product_id]" class="form-control select2 chargeprod"></select>');
         var servicePrice = currentRow.find('input.sprice');
         var serviceDistance = currentRow.find('input.distance');
         var serviceQuantity = currentRow.find('input.quantity');
         var pro_cat = '{{ $complaint?$complaint->product_details?->category_id:"" }}';
         var pro_sub_cat = '{{ $complaint ? $complaint->product_details?->subcategories->service_category_id : "" }}';
         var serviceProductSelect = currentRow.find('select.chargeprod');
         var serviceAppreciation = currentRow.find('input.appreciation');
         if (id == "3") {
            servicePrice.prop('readonly', true);
            serviceQuantity.prop('readonly', true);
            serviceQuantity.val('1');
            if (pro_cat == '2') {
               serviceDistance.prop('readonly', false);
            } else {
               serviceDistance.prop('readonly', true);
            }
            $.ajax({
               url: "{{url('/getServiceProduct')}}",
               data: {
                  'charge_type_id': id,
                  'charge_cat_id': pro_cat,
                  'pro_sub_cat'  : pro_sub_cat,
               },
               success: function(data) {
                  var html = '<option value="">Select Product</option>';
                  $.each(data, function(k, v) {
                     html += '<option value="' + v.id + '">' + v.product_name + '</option>';
                  });
                  serviceProductSelect.html(html);
                  $('.select2').select2();
               }
            });
         } else {
            serviceDistance.prop('readonly', true);
            serviceDistance.val('');
            serviceAppreciation.val('');
            if (id == '4') {
               serviceProductSelecttd.html('<input type="text" name="service[' + (counter - 1) + '][product_id]" class="form-control" />');
               servicePrice.prop('readonly', false);
               serviceQuantity.prop('readonly', false);
            } else {
               // serviceProductSelecttd.html('<select required name="service[' + (counter - 1) + '][product_id]" class="form-control select2 chargeprod"></select>');
               servicePrice.prop('readonly', true);
               serviceQuantity.prop('readonly', true);
            }
            $.ajax({
               url: "{{url('/getServiceProduct')}}",
               data: {
                  'charge_type_id': id,
                  'charge_cat_id': pro_cat,
                  'pro_sub_cat'  : pro_sub_cat,
               },
               success: function(data) {
                  var html = '<option value="">Select Product</option>';
                  $.each(data, function(k, v) {
                     html += '<option value="' + v.id + '">' + v.product_name + '</option>';
                  });
                  serviceProductSelect.html(html);
                  $('.select2').select2();
               }
            });
         }
      }).trigger('change');

      $(document).on("change", ".chargeprod", function() {
         var id = $(this).val();
         var currentRow = $(this).closest('tr');
         var servicePrice = currentRow.find('input.sprice');
         var serviceDistance = currentRow.find('input.distance');
         var serviceSubTotal = currentRow.find('input.ssubtotal');
         $.ajax({
            url: "{{url('/getServiceProductDetails')}}",
            data: {
               'id': id
            },
            success: function(data) {
               servicePrice.val(data.price);
               $.ajax({
                  url: "{{url('/getWorkDoneTime')}}",
                  data: {
                     'complaint_id': '{{$complaint?$complaint->id:"0"}}'
                  },
                  success: function(res) {
                     if(res.hours && res.hours <= 24){
                        serviceDistance.val(data.other_charge);
                        serviceSubTotal.val((parseFloat(data.other_charge) || 0) + parseFloat(data.price));
                     }else{
                        serviceSubTotal.val(data.price);
                     }
                  }
               });
            }
         });
      }).trigger('change');

      $(document).on("click", ".remove-tr", function() {
         if (confirm('Are you sure to remove this?')) {
            var currentRow = $(this).closest('tr');
            var checkOldDataId = currentRow.find('input#service_bill_product_id');
            if (checkOldDataId.length > 0) {
               $.ajax({
                  url: "{{ url('service-bill-product-remove') }}",
                  data: {
                     "id": checkOldDataId.val(),
                  },
                  success: function(res) {

                  }
               });
            }
            currentRow.remove();
         }
      })
      $(document).on('keyup', '.sprice', function() {
         if (!$(this).attr('readonly')) {
            var rate = $(this).val();
            var currentRow = $(this).closest('tr');
            var serviceSubtotal = currentRow.find('input.ssubtotal');
            var serviceQuantity = currentRow.find('input.quantity').val();
            var serviceSubtotalss = parseFloat(rate) * parseFloat(serviceQuantity);
            if (isNaN(serviceSubtotalss)) {
               serviceSubtotalss = 0;
            }
            serviceSubtotal.val(serviceSubtotalss);
         }
      })
      $(document).on('keyup', '.quantity', function() {
         if (!$(this).attr('readonly')) {
            var quantit = $(this).val();
            var currentRow = $(this).closest('tr');
            var serviceSubtotal = currentRow.find('input.ssubtotal');
            var servicePrice = currentRow.find('input.sprice').val();
            var serviceSubtotalss = parseFloat(quantit) * parseFloat(servicePrice);
            if (isNaN(serviceSubtotalss)) {
               serviceSubtotalss = 0;
            }
            serviceSubtotal.val(serviceSubtotalss);
         }
      })
      $(document).on('keyup', '.distance', function() {
         if (!$(this).attr('readonly')) {
            var distance = $(this).val();
            $(this).prop('min', '0');
            var currentRow = $(this).closest('tr');
            var serviceSubtotal = currentRow.find('input.ssubtotal');
            var serviceAppreciation = currentRow.find('input.appreciation');
            var servicePrice = currentRow.find('input.sprice').val();
            var tottal_dis_rate = 0;
            if (distance <= 120) {
               tottal_dis_rate = distance * 3;
            } else {
               tottal_dis_rate = 360 + ((distance - 120) * 1.75);
            }
            serviceAppreciation.val(tottal_dis_rate);
            serviceSubtotal.val(parseFloat(tottal_dis_rate) + parseFloat(servicePrice));
         }
      })

      $(document).ready(function() {
         $('.file-input').change(function() {
            var fileCount = this.files.length;
            $(this).closest('.col-md-2').find('.file-count').text(fileCount);
         });
      });

      // get service bill reasons
      $(document).on('change' , '#complaint_type' , function(){

         getServiceBillReason();
      })

      // code for to show the preview of selected image
      $(document).ready(function () {
        $('.file-input').on('change', function () {
            var fileInput = $(this)[0];
            var file = fileInput.files[0];
            var parent = $(this).closest('.file-upload-group');
            var imageDiv = $(this).closest('.imgdiv');
            var imgPreview = parent.find('.preview-img');
            var fileNameDisplay = parent.find('.file-name');
            var fileCountDisplay = parent.find('.file-count');
            var existingImgDiv = parent.find('.imgdiv'); // ✅ correct selector

             existingImgDiv.hide(); 
            // Update file count
            fileCountDisplay.text(fileInput.files.length);

            // Handle preview and name
            if (file && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    imgPreview.attr('src', e.target.result).show();
                    fileNameDisplay.text("Selected File: " + file.name).show();
                };
                reader.readAsDataURL(file);
            } else {
                imgPreview.hide().attr('src', '');
                fileNameDisplay.hide().text('');
            }
        });
    });

      function getServiceBillReason(){
         var complaint_type = $('#complaint_type').val();
         var complaintId = $('#complaint_type option:selected').data('complaint-id'); // Get the data attribute
         var subcategory_id = $('#subcategory_id').val();
         var selected = "{{ $service_bill->complaint_reason }}";
         if(complaint_type != null && complaint_type != ''){
             $("#complaint_reason").empty();
             $.ajax({
                 url: "{{ url('getServiceBillReason') }}",
                 dataType: "json",
                 type: "POST",
                 data: {
                     _token: "{{ csrf_token() }}",
                     complaint_type: complaint_type,
                     subcategory_id : subcategory_id,
                     complaintId  : complaintId,
                     selected : selected
                 },
                 success: function(res) {
                     if (res.status === true) {
                         $("#complaint_reason").html(res.html);
                     }
                 },
                 error: function(xhr, status, error) {
                     console.error("Error fetching service charge types:", error);
                 }
             });
         }
      }
   </script>
</x-app-layout>