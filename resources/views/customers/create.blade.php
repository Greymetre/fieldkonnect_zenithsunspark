<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.customers.create_title') !!}
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
          <input type="hidden" name="id" id="customer_id" value="{!! $customers['id'] !!}">
          <div class="first-box">
            <div class="row">
              <div class="col-md-3 ml-auto mr-auto">
                <div class="fileinput fileinput-new" data-provides="fileinput">

                  <div class="fileinput-new thumbnail">
                    <img src="{!! !empty($customers['profile_image']) ? $customers['profile_image'] : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview7">
                    <div class="selectThumbnail">
                      <span class="btn btn-just-icon btn-round btn-file">
                        <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                        <span class="fileinput-exists">Change</span>
                        <input type="file" name="image" class="getimage7" accept="image/*">
                      </span>
                      <br>
                      <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                    </div>
                  </div>
                  <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                  <label class="bmd-label-floating">{!! trans('panel.customers.fields.shop_image') !!}</label>
                  @if ($errors->has('image'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('image') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-3 ml-auto mr-auto">
                <div class="fileinput fileinput-new" data-provides="fileinput">

                  <div class="fileinput-new thumbnail">
                    <img src="{!! !empty($customers['shop_image']) ? $customers['shop_image'] : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview8">
                    <div class="selectThumbnail">
                      <span class="btn btn-just-icon btn-round btn-file">
                        <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                        <span class="fileinput-exists">Change</span>
                        <input type="file" name="profileImage" class="getimage8" accept="image/*">
                      </span>
                      <br>
                      <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                    </div>
                  </div>
                  <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                  <label class="bmd-label-floating">{!! trans('panel.customers.fields.profile_image') !!}</label>
                  @if ($errors->has('profileImage'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('profileImage') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.name') !!}<span class="text-danger"> *</span></label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="name" class="form-control" value="{!! old( 'name', $customers['name']) !!}" maxlength="200" required>
                    @if ($errors->has('name'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('name') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.customer_code') !!} </label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="customer_code" id="customer_code" class="form-control" value="{!! old( 'customer_code', $customers['customer_code']) !!}" maxlength="200">
                    @if ($errors->has('customer_code'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('customer_code') }}</p>
                    </div>
                    @endif
                  </div>

                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.first_name') !!} <span class="text-danger"> *</span></label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="first_name" class="form-control" value="{!! old( 'first_name', $customers['first_name']) !!}" maxlength="200" required>
                    @if ($errors->has('first_name'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('first_name') }}</p>
                    </div>
                    @endif
                  </div>

                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.last_name') !!}</label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="last_name" class="form-control" value="{!! old( 'last_name', $customers['last_name']) !!}" maxlength="200">
                    @if ($errors->has('last_name'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('last_name') }}</p>
                    </div>
                    @endif
                  </div>

                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.customertype') !!}<span class="text-danger"> *</span></label>

                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="customertype" style="width: 100%;" required id="type">
                      <option value="">Select {!! trans('panel.customers.fields.customertype') !!}</option>
                      @if(@isset($customertype ))
                      @foreach($customertype as $type)
                      <option value="{!! $type['id'] !!}" {{ old( 'customertype' , (!empty($customers->customertype))?($customers->customertype):('') ) == $type['id'] ? 'selected' : '' }}>{!! $type['customertype_name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('customertype'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('customertype') }}</p>
                  </div>
                  @endif

                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.global.mobile') !!}<span class="text-danger"> *</span></label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="mobile" pattern="[0-9]{10}" id="mobile" class="form-control" value="{!! old( 'mobile', $customers['mobile']) !!}" required>
                  </div>
                  @if ($errors->has('mobile'))
                  <label class="error">{{ $errors->first('mobile') }}</label>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.global.email') !!}</label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="email" name="email" id="email" class="form-control" value="{!! old( 'email', $customers['email']) !!}" maxlength="200">
                    @if ($errors->has('email'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('email') }}</p>
                    </div>
                    @endif
                  </div>

                </div>
              </div>


              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Employee

                    <?php
                    $userarray = array();
                    ?>
                    @foreach($customers->getemployeedetail as $key_new => $datas)
                    <?php $userarray[] = $datas->user_id; ?>
                    @endforeach

                  </label>

                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="executive_id[]" style="width: 100%;" multiple>
                      <!-- <option value="">Select Employee</option> -->
                      @if(@isset($users ))

                      @foreach($users as $user)
                      <option value="{!! $user['id'] !!}" <?php if (in_array($user->id, $userarray)) {
                                                            echo "selected";
                                                          } ?>>{!! $user['name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  @if ($errors->has('executive_id'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('executive_id') }}</p>
                  </div>
                  @endif

                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.global.contact_number_two') !!}<span class="text-danger"> *</span></label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="contact_number" id="contact_number" class="form-control" value="{!! old( 'contact_number', $customers['contact_number']) !!}" maxlength="13" minlength="10">
                  </div>
                  @if ($errors->has('contact_number'))
                  <label class="error">{{ $errors->first('contact_number') }}</label>
                  @endif

                </div>
              </div>


              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Working Status<span class="text-danger"> *</span></label>

                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="working_status" style="width: 100%;" required id="type">
                      <option value="">Select Working Status</option>
                      <option value="New" {{($customers && $customers['working_status'] == 'New')? 'selected':''}}>New</option>
                      <option value="Existing" {{($customers && $customers['working_status'] == 'Existing')? 'selected':''}}>Existing</option>
                    </select>
                  </div>
                  @if ($errors->has('working_status'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('working_status') }}</p>
                  </div>
                  @endif

                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Creation Date</label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="creation_date" id="creation_date" class="form-control datepicker" value="{!! old( 'contact_number', $customers['creation_date']) !!}" autocomplete="off">
                  </div>
                  @if ($errors->has('creation_date'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('creation_date') }}</p>
                  </div>
                  @endif
                </div>

              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">SAP Code</label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="sap_code" id="sap_code" class="form-control" value="{!! old( 'contact_number', $customers['sap_code']) !!}" autocomplete="off">
                  </div>
                  @if ($errors->has('sap_code'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('sap_code') }}</p>
                  </div>
                  @endif
                </div>

              </div>

              @if(isset($customers['customertype']) && $customers['customertype'])
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Change The Password</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="checkbox" id="change_password" name="pass_check"
                      style="width: 16px; height: 16px; margin-left: 12;" value="" />
                  </div>
                </div>
              </div>

              <div class="col-md-6" style="display: none;" id="password_box">
                <div class="input_section">
                  <label class="col-form-label">Password</label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                  </div>
                  @if ($errors->has('password'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('password') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              @endif


              <div class="col-md-6" id="parentcustomer" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.global.parentcustomer') !!}</label>

                  <?php
                  $parentarray = array();
                  ?>
                  @foreach($customers->getparentdetail as $key => $parentdetail)
                  <?php $parentarray[] = $parentdetail->parent_id;
                  ?>
                  @endforeach



                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2 customer_parent" name="parent_id[]" style="width: 100%;" multiple>
                      <!-- <option value="">Select {!! trans('panel.global.parentcustomer') !!}</option> -->
                      @if(@isset($parentcustomers ))
                      @foreach($parentcustomers as $parentcustomer)
                      <option value="{!! $parentcustomer['id'] !!}" <?php if (in_array($parentcustomer->id, $parentarray)) {
                                                                      echo "selected";
                                                                    } ?>>{!! $parentcustomer['name'] !!}</option>
                      @endforeach
                      @endif

                    </select>
                  </div>
                  @if ($errors->has('parent_id'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('parent_id') }}</p>
                  </div>
                  @endif

                </div>
              </div>
              <!-- row -->
            </div>




            <!-- <div class="col-md-6">
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
            </div> -->
            <!--             <div class="col-md-6">
              <div class="row">
                <label class="col-md-3 col-form-label">Employee</label>
                <div class="col-md-9">
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control select2" name="executive_id" style="width: 100%;">
                        <option value="">Select Employee</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                        <option value="{!! $user['id'] !!}" {{ old( 'executive_id' , (!empty($customers->executive_id))?($customers->executive_id):('') ) == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                  </div>
                  @if ($errors->has('executive_id'))
                   <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('executive_id') }}</p>
                   </div>
                  @endif
                </div>
              </div>
            </div> -->




            <!-- new field -->





            <!--             <div class="col-md-6" id="parentcustomer" style="display:none;">
                <div class="row">
                  <label class="col-md-3 col-form-label">{!! trans('panel.global.parentcustomer') !!}</label>

                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2 customer_parent" name="parent_id"  style="width: 100%;">
                         <option value="">Select {!! trans('panel.global.parentcustomer') !!}</option>
                          @if(@isset($parentcustomers ))
                            @foreach($parentcustomers as $parentcustomer)
                            <option value="{!! $parentcustomer['id'] !!}" {{ old( 'parent_id' , (!empty($customers->parent_id))?($customers->parent_id):('') ) == $parentcustomer['id'] ? 'selected' : '' }}>{!! $parentcustomer['first_name'] !!}{!! $parentcustomer['last_name'] !!}</option>
                            @endforeach
                          @endif

                      </select>
                    </div>
                    @if ($errors->has('parent_id'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('parent_id') }}</p>
                     </div>
                    @endif
                  </div>
                </div>
              </div> -->



            <!-- end new feld -->

          </div>
          <hr class="my-3">
          <div class="row">
            <div class="col-md-6" id="billing_address">
              <h4 class="section-heading mb-3  h4 mt-0 text-center text-theme2">Billing Address</h4>
              <div class="second-box">
                <div class="row">
                  <div class="col-md-12">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.address1') !!} <span class="text-danger"> *</span></label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="address1" class="form-control" value="{!! old( 'address1', isset($customers['customeraddress']['address1']) ? $customers['customeraddress']['address1'] :'' ) !!}" maxlength="200" required>
                        @if ($errors->has('address1'))
                        <div class="error col-lg-12">
                          <p class="text-danger">{{ $errors->first('address1') }}</p>
                        </div>
                        @endif
                      </div>

                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.address2') !!} </label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="address2" class="form-control" value="{!! old( 'address2', isset($customers['customeraddress']['address2']) ? $customers['customeraddress']['address2'] :'' ) !!}" maxlength="200">
                        @if ($errors->has('address2'))
                        <div class="error">
                          <p class="text-danger">{{ $errors->first('address2') }}</p>
                        </div>
                        @endif

                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.landmark') !!} </label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="landmark" class="form-control" value="{!! old( 'address1', isset($customers['customeraddress']['landmark']) ? $customers['customeraddress']['landmark'] :'' ) !!}" maxlength="200">
                        @if ($errors->has('landmark'))
                        <div class="error">
                          <p class="text-danger">{{ $errors->first('landmark') }}</p>
                        </div>
                        @endif

                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.locality') !!} </label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="locality" class="form-control" value="{!! old( 'locality', isset($customers['customeraddress']['locality']) ? $customers['customeraddress']['locality'] :'' ) !!}" maxlength="200">
                        @if ($errors->has('aadhar_no'))
                        <div class="error">
                          <p class="text-danger">{{ $errors->first('aadhar_no') }}</p>
                        </div>
                        @endif
                      </div>

                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="input_section">
                      <label class=" col-form-label">{!! trans('panel.global.country') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 country" name="country_id" onchange="getStateList()" style="width: 100%;">
                          <option value="">Select {!! trans('panel.global.country') !!}</option>
                          @if(@isset($countries ))
                          @foreach($countries as $country)
                          <option value="{!! $country['id'] !!}" {{ old( 'country_id' , (!empty($customers['customeraddress']['country_id']))?($customers['customeraddress']['country_id']):('') ) == $country['id'] ? 'selected' : '' }}>{!! $country['country_name'] !!}</option>
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
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.state') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 state" name="state_id" onchange="getDistrictList()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customeraddress']['state_id']))
                          <option value="{!! $customers['customeraddress']['state_id'] !!}">{!! $customers['customeraddress']['statename']['state_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.state') !!}</option>
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('state_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('state_id') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.district') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 district" name="district_id" onchange="getCityList()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customeraddress']['district_id']))
                          <option value="{!! $customers['customeraddress']['district_id'] !!}">{!! $customers['customeraddress']['districtname']['district_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.district') !!}</option>
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('country_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('country_id') }}</p>
                      </div>
                      @endif

                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.city') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 city" name="city_id" onchange="getPincodeList()" style="width: 100%;">
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
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.pincode') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control pincode select2" name="pincode_id" onchange="getAddressData()" style="width: 100%;">
                          <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                          @if(@isset($pincodes ))
                          @foreach($pincodes as $pincode)
                          <option value="{!! $pincode['id'] !!}" {{ old( 'pincode_id' , (!empty($customers['customeraddress']['pincode_id']))?($customers['customeraddress']['pincode_id']):('') ) == $pincode['id'] ? 'selected' : '' }}>{!! $pincode['pincode'] !!}</option>
                          @endforeach
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('pincode_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('pincode_id') }}</p>
                      </div>
                      @endif

                    </div>
                    <input type="checkbox" name="same_address" id="same_address" {{ old( 'same_address' , (!empty($customers['same_address']))?($customers['same_address']):('') ) == 1 ? 'checked' : '' }}> <span class="text-theme2">Same Shipping Address</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6" id="shipping_address">
              <h4 class="section-heading mb-3  h4 mt-0 text-center text-theme2">Shipping Address</h4>
              <div class="second-box">
                <div class="row">
                  <div class="col-md-12">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.address1') !!} <span class="text-danger"> *</span></label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="shipping_address1" class="form-control" value="{!! old( 'shipping_address1', isset($customers['customershippingaddress']['address1']) ? $customers['customershippingaddress']['address1'] :'' ) !!}" maxlength="200" required>
                        @if ($errors->has('shipping_address1'))
                        <div class="error col-lg-12">
                          <p class="text-danger">{{ $errors->first('shipping_address1') }}</p>
                        </div>
                        @endif
                      </div>

                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.address2') !!} </label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="shipping_address2" class="form-control" value="{!! old( 'shipping_address2', isset($customers['customershippingaddress']['address2']) ? $customers['customershippingaddress']['address2'] :'' ) !!}" maxlength="200">
                        @if ($errors->has('shipping_address2'))
                        <div class="error">
                          <p class="text-danger">{{ $errors->first('shipping_address2') }}</p>
                        </div>
                        @endif

                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.landmark') !!} </label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="shipping_landmark" class="form-control" value="{!! old( 'address1', isset($customers['customershippingaddress']['landmark']) ? $customers['customershippingaddress']['landmark'] :'' ) !!}" maxlength="200">
                        @if ($errors->has('shipping_landmark'))
                        <div class="error">
                          <p class="text-danger">{{ $errors->first('shipping_landmark') }}</p>
                        </div>
                        @endif

                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.address.locality') !!} </label>

                      <div class="form-group has-default bmd-form-group">
                        <input type="text" name="shipping_locality" class="form-control" value="{!! old( 'shipping_locality', isset($customers['customershippingaddress']['locality']) ? $customers['customershippingaddress']['locality'] :'' ) !!}" maxlength="200">
                        @if ($errors->has('aadhar_no'))
                        <div class="error">
                          <p class="text-danger">{{ $errors->first('aadhar_no') }}</p>
                        </div>
                        @endif
                      </div>

                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="input_section">
                      <label class=" col-form-label">{!! trans('panel.global.country') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 shipping_country" name="shipping_country_id" onchange="getShippingStateList()" style="width: 100%;">
                          <option value="">Select {!! trans('panel.global.country') !!}</option>
                          @if(@isset($countries ))
                          @foreach($countries as $country)
                          <option value="{!! $country['id'] !!}" {{ old( 'shipping_country_id' , (!empty($customers['customershippingaddress']['country_id']))?($customers['customershippingaddress']['country_id']):('') ) == $country['id'] ? 'selected' : '' }}>{!! $country['country_name'] !!}</option>
                          @endforeach
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('shipping_country_id'))
                      <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('shipping_country_id') }}</p>
                      </div>
                      @endif

                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.state') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 shipping_state" name="shipping_state_id" onchange="getShippingDistrictList()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customershippingaddress']['state_id']))
                          <option value="{!! $customers['customershippingaddress']['state_id'] !!}">{!! $customers['customershippingaddress']['statename']['state_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.state') !!}</option>
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('shipping_state_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('shipping_state_id') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.district') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 shipping_district" name="shipping_district_id" onchange="getShippingCityList()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customershippingaddress']['district_id']))
                          <option value="{!! $customers['customershippingaddress']['district_id'] !!}">{!! $customers['customershippingaddress']['districtname']['district_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.district') !!}</option>
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('country_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('country_id') }}</p>
                      </div>
                      @endif

                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.city') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2 shipping_city" name="shipping_city_id" onchange="getShippingPincodeList()" style="width: 100%;">
                          @if($customers->exists && isset($customers['customershippingaddress']['city_id']))
                          <option value="{!! $customers['customershippingaddress']['city_id'] !!}">{!! $customers['customershippingaddress']['cityname']['city_name'] !!}</option>
                          @else
                          <option value="">Select {!! trans('panel.global.city') !!}</option>
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('shipping_city_id'))
                      <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('shipping_city_id') }}</p>
                      </div>
                      @endif

                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input_section">
                      <label class="col-form-label">{!! trans('panel.global.pincode') !!}</label>

                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control shipping_pincode select2" name="shipping_pincode_id" onchange="getShippingAddressData()" style="width: 100%;">
                          <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                          @if(@isset($pincodes ))
                          @foreach($pincodes as $pincode)
                          <option value="{!! $pincode['id'] !!}" {{ old( 'shipping_pincode_id' , (!empty($customers['customershippingaddress']['pincode_id']))?($customers['customershippingaddress']['pincode_id']):('') ) == $pincode['id'] ? 'selected' : '' }}>{!! $pincode['pincode'] !!}</option>
                          @endforeach
                          @endif
                        </select>
                      </div>
                      @if ($errors->has('shipping_pincode_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('shipping_pincode_id') }}</p>
                      </div>
                      @endif

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <hr class="my-3">
          <h4 class="section-heading mb-3  h4 mt-0 text-center text-theme2">{!! trans('panel.customers.title_kyc') !!}</h4>
          <div class="last-box">
            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.gstin_no') !!} </label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="gstin_no" id="gstin_no" class="form-control" value="{!! old( 'gstin_no', isset($customers['customerdetails']['gstin_no']) ? $customers['customerdetails']['gstin_no'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('gstin_no'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('gstin_no') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.pan_no') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="pan_no" id="pan_no" class="form-control" value="{!! old( 'pan_no', isset($customers['customerdetails']['pan_no']) ? $customers['customerdetails']['pan_no'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('pan_no'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('pan_no') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.aadhar_no') !!} </label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="aadhar_no" id="aadhar_no" class="form-control" value="{!! old( 'aadhar_no', isset($customers['customerdetails']['aadhar_no']) ? $customers['customerdetails']['aadhar_no'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('aadhar_no'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('aadhar_no') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.account_holder') !!} </label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="account_holder" id="account_holder" class="form-control" value="{!! old( 'account_holder', isset($customers['customerdetails']['account_holder']) ? $customers['customerdetails']['account_holder'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('account_holder'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('account_holder') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="ccol-form-label">{!! trans('panel.customers.fields.account_number') !!} </label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="account_number" id="account_number" class="form-control" value="{!! old( 'account_number', isset($customers['customerdetails']['account_number']) ? $customers['customerdetails']['account_number'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('account_number'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('account_number') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.customers.fields.bank_name') !!} </label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="bank_name" id="bank_name" class="form-control" value="{!! old( 'bank_name', isset($customers['customerdetails']['bank_name']) ? $customers['customerdetails']['bank_name'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('bank_name'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('bank_name') }}</p>
                    </div>
                    @endif

                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class=" col-form-label">{!! trans('panel.customers.fields.ifsc_code') !!} </label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" value="{!! old( 'ifsc_code', isset($customers['customerdetails']['ifsc_code']) ? $customers['customerdetails']['ifsc_code'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('ifsc_code'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('ifsc_code') }}</p>
                    </div>
                    @endif

                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class=" col-form-label">{!! trans('panel.customers.fields.otherid_no') !!}</label>

                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="otherid_no" id="otherid_no" class="form-control" value="{!! old( 'otherid_no', isset($customers['customerdetails']['otherid_no']) ? $customers['customerdetails']['otherid_no'] :'' ) !!}" maxlength="200">
                    @if ($errors->has('otherid_no'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('otherid_no') }}</p>
                    </div>
                    @endif
                  </div>


                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Visit Status</label>
                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control" name="visit_status" id="visit_status" style="width: 100%;" required>
                      <option value="" selected disabled>Select Visit Status</option>
                      <option value="Hot" @if(!empty($customers->customerdetails) && $customers->customerdetails->visit_status == "Hot") selected @endif>Hot</option>
                      <option value="Warm" @if(!empty($customers->customerdetails) && $customers->customerdetails->visit_status == "Warm") selected @endif>Warm</option>
                      <option value="Cold" @if(!empty($customers->customerdetails) && $customers->customerdetails->visit_status == "Cold") selected @endif>Cold</option>
                      <option value="Existing" @if(!empty($customers->customerdetails) && $customers->customerdetails->visit_status == "Existing") selected @endif>Existing</option>
                    </select>
                    @if ($errors->has('visit_status'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('visit_status') }}</p>
                    </div>
                    @endif
                  </div>


                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Grade</label>

                  <div class="form-group has-default bmd-form-group">
                    <select class="form-control" name="grade" id="grade" style="width: 100%;" required>
                      <option value="" selected disabled>Select Grade</option>
                      <option value="Grade A" @if(!empty($customers->customerdetails) && $customers->customerdetails->grade == "Grade A") selected @endif>A</option>
                      <option value="Grade B" @if(!empty($customers->customerdetails) && $customers->customerdetails->grade == "Grade B") selected @endif>B</option>
                      <option value="Grade C" @if(!empty($customers->customerdetails) && $customers->customerdetails->grade == "Grade C") selected @endif>C</option>
                    </select>
                    @if ($errors->has('otherid_no'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('otherid_no') }}</p>
                    </div>
                    @endif
                  </div>


                </div>
              </div>

            </div>
          </div>

          <div class="row mt-5">
            <div class="col-md-2 col-sm-2">
              <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                  <img src="{!! !empty($customers['customerdocuments']->where('document_name','gstin')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','gstin')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                  <div class="selectThumbnail">
                    <span class="btn btn-just-icon btn-round btn-file">
                      <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="imggstin" class="getimage1" accept="image/*">
                    </span>
                    <br>
                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                  </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.customers.fields.gstin_image') !!}</label>
                @if ($errors->has('imggstin'))
                <div class="error col-lg-12">
                  <p class="text-danger">{{ $errors->first('imggstin') }}</p>
                </div>
                @endif
              </div>
            </div>
            <div class="col-md-2 col-sm-2">
              <div class="fileinput fileinput-new" data-provides="fileinput">

                <div class="fileinput-new thumbnail">
                  <img src="{!! !empty($customers['customerdocuments']->where('document_name','pan')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','pan')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview2">
                  <div class="selectThumbnail">
                    <span class="btn btn-just-icon btn-round btn-file">
                      <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="imgpan" class="getimage2" accept="image/*">
                    </span>
                    <br>
                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                  </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.customers.fields.pan_image') !!}</label>
              </div>
            </div>
            <div class="col-md-2 col-sm-2">
              <div class="fileinput fileinput-new" data-provides="fileinput">

                <div class="fileinput-new thumbnail">
                  <img src="{!! !empty($customers['customerdocuments']->where('document_name','aadhar')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','aadhar')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview3">
                  <div class="selectThumbnail">
                    <span class="btn btn-just-icon btn-round btn-file">
                      <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="imgaadhar" class="getimage3" accept="image/*">
                    </span>
                    <br>
                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                  </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.customers.fields.aadhar_front_image') !!}</label>
              </div>
            </div>
            <div class="col-md-2 col-sm-2">
              <div class="fileinput fileinput-new" data-provides="fileinput">

                <div class="fileinput-new thumbnail">
                  <img src="{!! !empty($customers['customerdocuments']->where('document_name','aadharback')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','aadharback')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview4">
                  <div class="selectThumbnail">
                    <span class="btn btn-just-icon btn-round btn-file">
                      <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="imgaadharback" class="getimage4" accept="image/*">
                    </span>
                    <br>
                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                  </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.customers.fields.aadhar_back_image') !!}</label>
              </div>
            </div>
            <div class="col-md-2 col-sm-2">
              <div class="fileinput fileinput-new" data-provides="fileinput">

                <div class="fileinput-new thumbnail">
                  <img src="{!! !empty($customers['customerdocuments']->where('document_name','bankpass')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','bankpass')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview5">
                  <div class="selectThumbnail">
                    <span class="btn btn-just-icon btn-round btn-file">
                      <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="imgbankpass" class="getimage5" accept="image/*">
                    </span>
                    <br>
                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                  </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.customers.fields.bank_passbook_image') !!}</label>
              </div>
            </div>
            <div class="col-md-2 col-sm-2">
              <div class="fileinput fileinput-new" data-provides="fileinput">

                <div class="fileinput-new thumbnail">
                  <img src="{!! !empty($customers['customerdocuments']->where('document_name','other')->pluck('file_path')->first()) ? $customers['customerdocuments']->where('document_name','other')->pluck('file_path')->first() : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview6">
                  <div class="selectThumbnail">
                    <span class="btn btn-just-icon btn-round btn-file">
                      <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="imgother" class="getimage6" accept="image/*">
                    </span>
                    <br>
                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                  </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.customers.fields.otherid_image') !!}</label>
              </div>
            </div>
          </div>
          <hr class="my-3">
          <!-- <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Customer survey</h4>  -->
          <div class="row last-inner-form">
            <div class="col-md-12">
              <div id="accordion" role="tablist">
                <div class="card-collapse">
                  <div class="card-header inner-form-heading" role="tab" id="headingOne">
                    <h4 class="section-heading mb-3  h4 mt-0 text-theme2"><a data-toggle="collapse" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
                        Customer survey
                        <i class="material-icons">keyboard_arrow_down</i>
                      </a></h4>
                  </div>
                  <div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                    <div class="card-body">
                      @if(@isset($fields ))
                      @foreach($fields as $index => $field)
                      <input type="hidden" name="survey[{!! $index !!}][field_id]" value="{!! $field['id']!!}">

                      @if($field['field_type'] == 'Radio')
                      <div class="row">
                        <!-- <label class="col-sm-1 col-form-label label-checkbox"></label> -->
                        <div class="col-sm-12 checkbox-radios">
                          <h4 class="section-heading mb-3  h4 mt-0 text-center text-theme2"> {!! $field['label_name'] !!}</h4>
                          <div class="row">
                            @if(@isset($field['fieldsData'] ))
                            @foreach($field['fieldsData'] as $rows)
                            <div class="col-sm-4">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" type="radio" name="survey[{!! $index !!}][value]" value="{!! $rows['value'] !!}" @if($customers['surveys']->where('field_id', $field['id'])->pluck('value')->first() == $rows['value']) checked @endif> {!! $rows['value'] !!}
                                  <span class="circle">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </div>
                            @endforeach
                            @endif
                          </div>
                        </div>
                      </div>
                      @elseif($field['field_type'] == 'Checkbox')
                      <div class="row">
                        <!-- <label class="col-sm-1 col-form-label label-checkbox"></label> -->
                        <div class="col-sm-12 checkbox-radios">
                          <h4 class="section-heading mb-3  h4 mt-0 text-center text-theme2"> {!! $field['label_name'] !!}</h4>

                          <div class="row">
                            @if(@isset($field['fieldsData'] ))
                            @foreach($field['fieldsData'] as $i => $rows)
                            <div class="col-md-3">
                              <div class="form-check">
                                <label class="form-check-label">
                                  <input class="form-check-input" name="survey[{!! $index !!}][value][]" type="checkbox" value="{!! $rows['value'] !!}" @if(in_array($rows['value'], explode(', ', $customers[' surveys']->where('field_id', $field['id'])->pluck('value')->first()))) checked @endif> {!! $rows['value'] !!}
                                  <span class="form-check-sign">
                                    <span class="check"></span>
                                  </span>
                                </label>
                              </div>
                            </div>
                            @endforeach
                            @endif
                          </div>
                        </div>
                      </div>
                      @elseif($field['field_type'] == 'Select')
                      <div class="row">
                        <label class="col-md-3 col-form-label">{!! $field['label_name'] !!}</label>
                        <div class="col-md-9">
                          <div class="form-group has-default bmd-form-group">
                            <select class="form-control select2" name="survey[{!! $index !!}][value]" style="width: 100%;">
                              <option value="">Select {!! $field['label_name'] !!}</option>
                              @if(@isset($field['fieldsData'] ))
                              @foreach($field['fieldsData'] as $rows)
                              <option value="{!! $rows['value'] !!}">{!! $rows['value'] !!}</option>
                              @endforeach
                              @endif
                            </select>
                          </div>
                        </div>
                      </div>
                      @else
                      <div class="row">
                        <!-- <label class="col-sm-1 col-form-label label-checkbox"></label> -->
                        <div class="col-sm-12 checkbox-radios">
                          <h4 class="section-heading mb-3  h4 mt-0 text-center text-theme2"> {!! $field['label_name'] !!}</h4>
                          <div class="form-group has-default bmd-form-group">
                            <input type="text" name="survey[{!! $index !!}][value]" class="form-control" value="{!! $customers['surveys']->where('field_id', $field['id'])->pluck('value')->first() !!}" maxlength="200">
                          </div>
                        </div>
                      </div>
                      @endif

                      @endforeach
                      @endif
                    </div>
                  </div>
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
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js?v='.time()) }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_customers.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      //Initialize Select2 Elements
      $('.select2').select2()

      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
    })
  </script>

  <script>
    $(document).ready(function() {

      // $(document).on('change','#type',function(e){

      $('#type').change(function() {

        var type = $('#type').val();
        if (type == '3') {
          $('#parentcustomer').hide()
          $('#parentcustomer').prop("disabled", true)
        } else if (type == '1') {
          $('#parentcustomer').hide()
          $('#parentcustomer').prop("disabled", true)
        } else if (type == '2') {
          $('#parentcustomer').show()
          $('#parentcustomer').prop("disabled", false)
        } else {
          $('#parentcustomer').hide()
          $('#parentcustomer').prop("disabled", true)
        }

      }).trigger('change');

      $(document).ready(function() {
        $('#change_password').on('click', function() {
          let isChecked = $(this).is(':checked');
          if (isChecked) {
            $('#password_box').show();
          } else {
            $('#password_box').hide();
          }
        });
      });

    });
    $(document).ready(function() {

      function toggleAddressView() {
        if ($('#same_address').is(':checked')) {
          $('#shipping_address').hide();
          $('#billing_address').removeClass('col-md-6').addClass('col-md-12');
        } else {
          $('#shipping_address').show();
          $('#billing_address').removeClass('col-md-12').addClass('col-md-6');
        }
      }

      // On change
      $(document).on('change', '#same_address', function() {
        toggleAddressView();
      });

      // On page load (for edit form)
      toggleAddressView();
    });
  </script>

</x-app-layout>