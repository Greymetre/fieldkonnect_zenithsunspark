<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.distributors.create_title') !!}
              <span class="pull-right">
                <div class="btn-group">
                  @if(auth()->user()->can(['customer_access']))
                  <a href="{{ url('customers') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.lead.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                  @endif
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
            {!! Form::model($customers,[
            'route' => $customers->exists ? ['customers.update', $customers->id] : 'customers.store',
            'method' => $customers->exists ? 'PUT' : 'POST',
            'id' => 'storeCustomerData',
            'files'=>true
            ]) !!}
            <input type="hidden" name="id" value="{!! $customers['id'] !!}">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.name') !!}<span class="text-danger"> *</span></label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="name" class="form-control" value="{!! old( 'name', $customers['name']) !!}" maxlength="200" required>
                      @if ($errors->has('name'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('name') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.customer_code') !!} </label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="customer_code" class="form-control" value="{!! old( 'customer_code', $customers['customer_code']) !!}" maxlength="200">
                      @if ($errors->has('customer_code'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('customer_code') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.first_name') !!} <span class="text-danger"> *</span></label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="first_name" class="form-control" value="{!! old( 'first_name', $customers['first_name']) !!}" maxlength="200" required>
                      @if ($errors->has('first_name'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('first_name') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.last_name') !!}<span class="text-danger"> *</span></label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="last_name" class="form-control" value="{!! old( 'last_name', $customers['last_name']) !!}" maxlength="200" required>
                      @if ($errors->has('last_name'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('last_name') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">{!! trans('panel.global.email') !!}<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="email" name="email" class="form-control" value="{!! old( 'email', $customers['email']) !!}" maxlength="200">
                    @if ($errors->has('email'))
                      <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('email') }}</p></div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">{!! trans('panel.global.mobile') !!}<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="mobile" class="form-control" value="{!! old( 'mobile', $customers['mobile']) !!}" maxlength="13" minlength="10" required>
                  </div>
                  @if ($errors->has('mobile'))
                    <label class="error">{{ $errors->first('mobile') }}</label>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.customertype') !!}<span class="text-danger"> *</span></label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="customertype" style="width: 100%;" required>
                        <option value="">Select {!! trans('panel.customers.fields.customertype') !!}</option>
                        @if(@isset($customertype ))
                        @foreach($customertype as $type)
                        <option value="{!! $type['id'] !!}" {{ old( 'customertype' , (!empty($customers->customertype))?($customers->customertype):('') ) == $type['id'] ? 'selected' : '' }}>{!! $type['customertype_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  @if ($errors->has('customertype'))
                   <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('customertype') }}</p>
                   </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.firmtype') !!}</label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="firmtype" style="width: 100%;">
                        <option value="">Select {!! trans('panel.customers.fields.firmtype') !!}</option>
                        @if(@isset($firmtype ))
                        @foreach($firmtype as $firm)
                        <option value="{!! $firm['id'] !!}" {{ old( 'firmtype' , (!empty($customers->firmtype))?($customers->firmtype):('') ) == $firm['id'] ? 'selected' : '' }}>{!! $firm['firmtype_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  @if ($errors->has('firmtype'))
                   <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('firmtype') }}</p>
                   </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
          <hr class="my-3">
          <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">{!! trans('panel.customers.title_kyc') !!}</h4> 
          <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.gstin_no') !!} </label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="gstin_no" class="form-control" value="{!! old( 'gstin_no', isset($customers['customerdetails']['gstin_no']) ? $customers['customerdetails']['gstin_no'] :'' ) !!}" maxlength="200">
                      @if ($errors->has('gstin_no'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('gstin_no') }}</p></div>
                      @endif
                    </div>
                  </div>
                  
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.pan_no') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="pan_no" class="form-control" value="{!! old( 'pan_no', isset($customers['customerdetails']['pan_no']) ? $customers['customerdetails']['pan_no'] :'' ) !!}" maxlength="200">
                      @if ($errors->has('pan_no'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('pan_no') }}</p></div>
                      @endif
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.aadhar_no') !!} </label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="aadhar_no" class="form-control" value="{!! old( 'aadhar_no', isset($customers['customerdetails']['aadhar_no']) ? $customers['customerdetails']['aadhar_no'] :'' ) !!}" maxlength="200">
                      @if ($errors->has('aadhar_no'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('aadhar_no') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.customers.fields.otherid_no') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="otherid_no" class="form-control" value="{!! old( 'otherid_no', isset($customers['customerdetails']['otherid_no']) ? $customers['customerdetails']['otherid_no'] :'' ) !!}" maxlength="200">
                      @if ($errors->has('otherid_no'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('otherid_no') }}</p></div>
                      @endif
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 col-sm-3">
                 <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                   <div class="selectThumbnail">
                     <span class="btn btn-just-icon btn-round btn-file">
                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                       <span class="fileinput-exists">Change</span>
                       <input type="file" name="imggstin" class="getimage1">
                     </span>
                     <br>
                     <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                   </div>
                   <div class="fileinput-new thumbnail">
                     <img src="{!! ($customers['gstin_image']) ? asset($customers['gstin_image']) : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                   </div>
                   <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                   <label class="bmd-label-floating">{!! trans('panel.customers.fields.gstin_image') !!}</label>
                 </div>
               </div>
              <div class="col-md-3 col-sm-3">
                <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                  <div class="selectThumbnail">
                   <span class="btn btn-just-icon btn-round btn-file">
                     <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                     <span class="fileinput-exists">Change</span>
                     <input type="file" name="imgpan" class="getimage2">
                   </span>
                   <br>
                   <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                 </div>
                 <div class="fileinput-new thumbnail">
                   <img src="{!! ($customers['pan_image']) ? asset($customers['pan_image']) : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview2">
                 </div>
                 <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                 <label class="bmd-label-floating">{!! trans('panel.customers.fields.pan_image') !!}</label>
               </div>
             </div>
              <div class="col-md-3 col-sm-3">
                <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                  <div class="selectThumbnail">
                   <span class="btn btn-just-icon btn-round btn-file">
                     <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                     <span class="fileinput-exists">Change</span>
                     <input type="file" name="imgaadhar" class="getimage3">
                   </span>
                   <br>
                   <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                 </div>
                 <div class="fileinput-new thumbnail">
                   <img src="{!! ($customers['aadhar_image']) ? asset($customers['aadhar_image']) :url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview3">
                 </div>
                 <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                 <label class="bmd-label-floating">{!! trans('panel.customers.fields.aadhar_image') !!}</label>
               </div>
             </div>
              <div class="col-md-3 col-sm-3">
                <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                 <div class="selectThumbnail">
                   <span class="btn btn-just-icon btn-round btn-file">
                     <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                     <span class="fileinput-exists">Change</span>
                     <input type="file" name="imgother" class="getimage4">
                   </span>
                   <br>
                   <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                 </div>
                 <div class="fileinput-new thumbnail">
                   <img src="{!! ($customers['other_image']) ? asset($customers['other_image']) : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview4">
                 </div>
                 <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                 <label class="bmd-label-floating">{!! trans('panel.customers.fields.otherid_image') !!}</label>
                </div>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">{!! trans('panel.customers.title_address') !!}</h4> 
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.address.address1') !!} <span class="text-danger"> *</span></label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="address1" class="form-control" value="{!! old( 'address1', isset($customers['customeraddress']['address1']) ? $customers['customeraddress']['address1'] :'' ) !!}" maxlength="200" required>
                      @if ($errors->has('address1'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('address1') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.address.address2') !!} </label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="address2" class="form-control" value="{!! old( 'address2', isset($customers['customeraddress']['address2']) ? $customers['customeraddress']['address2'] :'' ) !!}" maxlength="200">
                      @if ($errors->has('address2'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('address2') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.address.landmark') !!} </label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="landmark" class="form-control" value="{!! old( 'address1', isset($customers['customeraddress']['landmark']) ? $customers['customeraddress']['landmark'] :'' ) !!}" maxlength="200">
                      @if ($errors->has('landmark'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('landmark') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.address.locality') !!} </label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="locality" class="form-control" value="{!! old( 'locality', isset($customers['customeraddress']['locality']) ? $customers['customeraddress']['locality'] :'' ) !!}" maxlength="200">
                      @if ($errors->has('aadhar_no'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('aadhar_no') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.global.pincode') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2" name="pincode_id" onchange="getAddressData()" style="width: 100%;">
                          <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                          @if(@isset($pincodes ))
                            @foreach($pincodes as $pincode)
                            <option value="{!! $pincode['id'] !!}" {{ old( 'pincode_id' , (!empty($customers['customeraddress']['pincode_id']))?($customers['customeraddress']['pincode_id']):('') ) == $pincode['id'] ? 'selected' : '' }}>{!! $pincode['pincode'] !!}</option>
                            @endforeach
                          @endif
                       </select>
                    </div>
                    @if ($errors->has('pincode_id'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('pincode_id') }}</p>
                     </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.global.country') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2 country" name="country_id" onchange="getStateData()" style="width: 100%;">
                          <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                          @if(@isset($countries ))
                            @foreach($countries as $country)
                            <option value="{!! $country['id'] !!}" {{ old( 'pincode_id' , (!empty($customers['customeraddress']['pincode_id']))?($customers['customeraddress']['pincode_id']):('') ) == $country['id'] ? 'selected' : '' }}>{!! $country['country_name'] !!}</option>
                            @endforeach
                          @endif
                       </select>
                    </div>
                    @if ($errors->has('country_id'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('country_id') }}</p>
                     </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.global.state') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2 state" name="state_id" onchange="getDistrictData()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customeraddress']['state_id']))
                          <option value="{!! $customers['customeraddress']['state_id'] !!}">{!! $customers['customeraddress']['statename']['state_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.state') !!}</option>
                          @endif
                       </select>
                    </div>
                    @if ($errors->has('state_id'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('state_id') }}</p>
                     </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.global.district') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2 district" name="district_id" onchange="getCityData()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customeraddress']['district_id']))
                          <option value="{!! $customers['customeraddress']['district_id'] !!}">{!! $customers['customeraddress']['districtname']['district_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.district') !!}</option>
                          @endif
                       </select>
                    </div>
                    @if ($errors->has('country_id'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('country_id') }}</p>
                     </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.global.city') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2 city" name="city_id" onchange="getPincodeData()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customeraddress']['city_id']))
                          <option value="{!! $customers['customeraddress']['city_id'] !!}">{!! $customers['customeraddress']['cityname']['city_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.city') !!}</option>
                          @endif
                       </select>
                    </div>
                    @if ($errors->has('city_id'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('city_id') }}</p>
                     </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer pull-right">
               {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
            </div>
            {{ Form::close() }} 
         </div>
      </div>
   </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/validation_customers.js') }}"></script>
<script type="text/javascript">
   $(document).ready(function () {

   });
   
   $(function () {
      //Initialize Select2 Elements
      $('.select2').select2()
   
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
    })

</script>
</x-app-layout>