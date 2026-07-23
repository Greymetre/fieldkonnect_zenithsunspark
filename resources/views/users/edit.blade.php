<x-app-layout>
   <div class="row">
      <div class="col-md-12">
         <div class="card card-profile">
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
               <form method="POST" action="{{ route('users.update', [$user->id]) }}" enctype="multipart/form-data" id="storeUserData">
                  @method('PUT')
                  @csrf
                  <div class="card-avatar">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <div class="selectThumbnail">
                           <span class="btn btn-just-icon btn-round btn-file">
                              <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="file" name="image" class="getimage1">
                           </span>
                           <br>
                           <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                        </div>
                        <div class="fileinput-new thumbnail">
                           <img src="{!! ($user['profile_image']) ? asset($user['profile_image']) : asset('public/assets/img/placeholder.jpg') !!}" class="imagepreview1">
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                        <label class="bmd-label-floating">{!! trans('panel.user.profile_image') !!}</label>
                     </div>
                  </div>
                  <div class="row">
                     <input type="hidden" name="id" id="user_id" value="{!! $user->id !!}">
                     <input type="hidden" name="name" value="{!! $user->name !!}">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.first_name') }}sss</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="first_name" class="form-control" value="{!! old( 'first_name', !empty($user['first_name']) ? $user['first_name']:'') !!}">
                                 @if ($errors->has('first_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('first_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.last_name') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_name" class="form-control" value="{!! old( 'last_name', !empty($user['last_name']) ? $user['last_name']:'') !!}">
                                 @if ($errors->has('last_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.mobile') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="mobile" class="form-control" value="{!! old( 'mobile', !empty($user['mobile']) ? $user['mobile']:'') !!}">
                                 @if ($errors->has('mobile'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('mobile') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.email') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="email" name="email" class="form-control" value="{!! old( 'email', !empty($user['email']) ? $user['email']:'') !!}">
                                 @if ($errors->has('email'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.password') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="password" name="password" class="form-control">
                                 @if ($errors->has('password'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('password') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.roles') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2 {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple required style="height: 300px;">
                                    @foreach($roles as $id => $roles)
                                    <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || $user->roles->contains($id)) ? 'selected' : '' }}>{{ $roles }}</option>
                                    @endforeach
                                 </select>
                                 @if ($errors->has('roles'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('roles') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.date_of_joining') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="date_of_joining" class="form-control datepicker" value="{!! old( 'date_of_joining', !empty($user['userinfo']['date_of_joining']) ? $user['userinfo']['date_of_joining']:'') !!}" autocomplete="off" readonly>
                                 @if ($errors->has('date_of_joining'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('date_of_joining') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.salary') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="salary" class="form-control" value="{!! old( 'salary', !empty($user['userinfo']['salary']) ? $user['userinfo']['salary']:'') !!}">
                                 @if ($errors->has('salary'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('salary') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>


                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.gender') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="selectpicker" data-style="select-with-transition" name="gender">
                                    <option value="Male" {{ (old('gender') == 'Male') ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (old('gender') == 'Female') ? 'selected' : '' }}>Female</option>
                                 </select>
                                 @if($errors->has('roles'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('roles') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Location</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="location" class="form-control" value="{!! old( 'location', $user['location']) !!}">
                                 @if ($errors->has('location'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('location') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.marital_status') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="marital_status" required>
                                    <option value="" disabled selected>Marital Status</option>
                                    <option value="Single" {{ old( 'marital_status' , (!empty($user['userinfo']['marital_status']))?($user['userinfo']['marital_status']):('') ) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old( 'marital_status' , (!empty($user['userinfo']['marital_status']))?($user['userinfo']['marital_status']):('') ) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Separated" {{ old( 'marital_status' , (!empty($user['userinfo']['marital_status']))?($user['userinfo']['marital_status']):('') ) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                    <option value="Widowed" {{ old( 'marital_status' , (!empty($user['userinfo']['marital_status']))?($user['userinfo']['marital_status']):('') ) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old( 'marital_status' , (!empty($user['userinfo']['marital_status']))?($user['userinfo']['marital_status']):('') ) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                 </select>
                                 @if($errors->has('marital_status'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('marital_status') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="date_of_birth" class="form-control datepicker" value="{!! old( 'date_of_birth', !empty($user['userinfo']['date_of_birth']) ? $user['userinfo']['date_of_birth']:null) !!}">
                                 @if ($errors->has('date_of_birth'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('date_of_birth') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Assign Cities</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2 {{ $errors->has('cities') ? 'is-invalid' : '' }}" name="cities[]" id="cities" multiple required>
                                    @foreach($cities as $id => $cities)
                                    <option value="{{ $id }}" {{ (in_array($id, old('cities', [])) || $user->cities->contains($id)) ? 'selected' : '' }}>{{ $cities }}</option>
                                    @endforeach
                                 </select>
                                 @if ($errors->has('cities'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('cities') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Reporting To</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2" name="reportingid" required>
                                    <option value="" disabled selected>Select Reporting</option>
                                    @if(@isset($reportings ))
                                    @foreach($reportings as $reporting)
                                    <option value="{!! $reporting['id'] !!}" {{ old( 'reportingid' , (!empty($user->reportingid)) ? ($user->reportingid) :('') ) == $reporting['id'] ? 'selected' : '' }}>{!! $reporting['name'] !!}</option>
                                    @endforeach
                                    @endif
                                 </select>
                                 @if($errors->has('reportingid'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('reportingid') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>


                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.employee_codes') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="employee_codes" class="form-control" value="{!! old( 'employee_codes', !empty($user['employee_codes']) ? $user['employee_codes']:'') !!}">
                                 @if ($errors->has('employee_codes'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('employee_codes') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.branch_name') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="branch_id" required>
                                    <option value="" disabled selected>Select Branch</option>
                                    @foreach($branches as $branche)
                                    <option value="{{$branche->id}}" <?php if ($user->branch_id == $branche->id) {
                                                                        echo "selected";
                                                                     } ?>>{{$branche->branch_name}}</option>
                                    @endforeach
                                 </select>
                                 @if ($errors->has('branch_id'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.designation') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="designation_id">
                                    <option value="" disabled selected>Select {!! trans('panel.user.designation') !!}</option>
                                    @foreach($designations as $designation)
                                    <option value="{{$designation->id}}" <?php if ($user->designation_id == $designation->id) {
                                                                              echo "selected";
                                                                           } ?>> {{$designation->designation_name}}</option>
                                    @endforeach
                                 </select>
                                 @if ($errors->has('designation_id'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('designation_id') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <br><br>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.division') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="division_id">
                                    <option value="" disabled selected>Select {!! trans('panel.user.division') !!}</option>
                                    @foreach($divisions as $division)
                                    <option value="{{$division->id}}" <?php if ($user->division_id == $division->id) {
                                                                           echo "selected";
                                                                        } ?>>{{$division->division_name}}</option>
                                    @endforeach
                                 </select>
                                 @if ($errors->has('division_id'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('division_id') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>


                  <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.department') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="department_id">
                                    <option value="" disabled selected>Select {!! trans('panel.user.department') !!}</option>
                                    @foreach($departments as $department)
                                    <option value="{{$department->id}}" <?php if ($user->department_id == $department->id) {
                                                                           echo "selected";
                                                                        } ?>  >{{$department->name}}</option>
                                    @endforeach
                                 </select>
                                 @if($errors->has('department_id'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('department_id') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>




                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.last_year_increments') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_year_increments" class="form-control" value="{!! old( 'last_year_increments', !empty($user['userinfo']['last_year_increments']) ? $user['userinfo']['last_year_increments']:'') !!}">
                                 @if ($errors->has('last_year_increments'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_year_increments') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.last_promotion') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_promotion" class="form-control" value="{!! old( 'last_promotion', !empty($user['userinfo']['last_promotion']) ? $user['userinfo']['last_promotion']:'') !!}">
                                 @if ($errors->has('last_promotion'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_promotion') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.order_mail') !!}<span class="text-danger"> *</span></label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="order_mails" id="order_mails" class="form-control" value="{{$user['userinfo']?$user['userinfo']['order_mails']:'' }}">
                                 @if ($errors->has('order_mails'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('order_mails') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Order Mails Type<span class="text-danger"> *</span></label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2" multiple name="order_mails_type[]" style="width: 100%;" required id="type">
                                    <option value="">Select {!! trans('panel.customers.fields.customertype') !!}</option>
                                    @if(@isset($customertype ))
                                    @foreach($customertype as $type)
                                    @if(in_array($type['id'],explode(',', $user['userinfo']?$user['userinfo']['order_mails_type']:'')))
                                    <option value="{!! $type['id'] !!}" selected>{!! $type['customertype_name'] !!}</option>
                                    @else
                                    <option value="{!! $type['id'] !!}" >{!! $type['customertype_name'] !!}</option>
                                    @endif
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




                  </div>
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right submituser')) }}
                  {{ Form::close() }}
            </div>
         </div>
      </div>
      <script src="{{ url('/').'/'.asset('assets/js/validation_users.js') }}"></script>
      <script type="text/javascript">
         $('.copyaddreess').click(function() {
            var checked = $(this).is(':checked');
            if (checked == true) {
               var currentAddress = $("input[name=current_address]").val();
               $('.permanentAddress').val(currentAddress);
            } else {
               $('.permanentAddress').val('');
            }
         });

         function getfullName() {
            var first_name = $("input[name=first_name]").val();
            var last_name = $("input[name=last_name]").val();
            $('#name').val(first_name + ' ' + last_name);
         }
      </script>
</x-app-layout>