<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-profile">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          @if($user->id)
          <h4 class="card-title ">{{ trans('panel.global.edit') }} {{ trans('panel.user.title_singular') }}</h4>
          @else
          <h4 class="card-title ">{{ trans('panel.global.create') }} {{ trans('panel.user.title_singular') }}</h4>
          @endif
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
          <form method="POST" action="{{($user->id)?route('users.update', [$user->id]): route('users.store') }}" enctype="multipart/form-data" id="{{($user->id)?'updateUserData':'storeUserData'}}">
            @csrf
            @if($user->id)
            @method('PUT')
            <div class="new_css">
              <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                  <img src="{{ ($user ? (count($user->getMedia('profile_image')) > 0 ? $user->getMedia('profile_image')[0]->getFullUrl() : asset('assets/img/placeholder.jpg')) : asset('assets/img/placeholder.jpg')) }}" class="imagepreview1">
                  <div class="selectThumbnail">
                    <span class="btn btn-just-icon btn-round btn-file">
                      <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="file" name="image" class="getimage1">
                    </span>
                    <br>
                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                  </div>
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                <label class="bmd-label-floating">{!! trans('panel.user.profile_image') !!}</label>
              </div>
            </div>
            @endif
            <input type="hidden" name="name" id="name" required>
            <div class="first-box">
              <div class="row">
                <input type="hidden" name="id" id="user_id" value="{!! $user?$user->id:'' !!}">
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Assign Cities</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2 {{ $errors->has('cities') ? 'is-invalid' : '' }}" name="cities[]" id="cities" multiple required>
                        @foreach($cities as $id => $cities)
                        <option value="{{ $id }}" {{ (in_array($id, old('cities', [])) || $user->cities->contains($id)) ? 'selected' : '' }}>{{ $cities }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('cities'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('cities') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Reporting To</label>
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
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{{ trans('panel.user.fields.first_name') }} <span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="first_name" class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" value="{!! old( 'first_name'), $user->first_name !!}" maxlength="200" required onchange="getfullName();">
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
                    <label class="col-form-label">{{ trans('panel.user.fields.last_name') }}<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="last_name" class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" value="{!! old( 'last_name'), $user->last_name !!}" maxlength="200" required>
                      @if ($errors->has('last_name'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('last_name') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.global.email') !!}<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="email" name="email" id="email" class="form-control" value="{!! $user->email,old( 'email') !!}" maxlength="200" required>
                      @if ($errors->has('email'))
                      <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('email') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.global.mobile') !!}<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="number" name="mobile" class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}" value="{!!  $user->mobile, old( 'mobile') !!}" maxlength="10" minlength="10" required>
                    </div>
                    @if ($errors->has('mobile'))
                    <label class="error">{{ $errors->first('mobile') }}</label>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{{ trans('panel.user.fields.password') }}<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" value="{{ old('password') }}" minlength="6" maxlength="200" required>
                      @if ($errors->has('password'))
                      <label class="error">{{ $errors->first('password') }}</label>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{{ trans('panel.user.fields.roles') }}</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2" name="roles[]" id="roles" multiple required>
                        @foreach($roles as $id => $roles)
                        <option value="{{ $id }}" {{ (in_array($id, old('roles', [])) || $user->roles->contains($id)) ? 'selected' : '' }}>{{ $roles }}</option>
                        @endforeach
                      </select>
                      @if ($errors->has('roles'))
                      <label class="error">{{ $errors->first('roles') }}</label>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6 d-none" id="branch-data">
                  <div class="input_section">
                    <label class="col-form-label">Branch data</label>
                    <div class="form-group has-default bmd-form-group">
                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2" name="branch_show[]" multiple required>
                          <option value="" disabled>Select Branch For Data</option>
                          @foreach($branches as $branche)
                          <option value="{{$branche->id}}" {{ (in_array($branche->id, old('branch_show', explode(',', $user->branch_show)))) ? 'selected' : '' }}>{{$branche->branch_name}}</option>
                          @endforeach
                        </select>
                      </div>
                      @if ($errors->has('branch_show'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('branch_show') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <h4 class="section-heading mb-3  h4 mt-0 text-center">Employee Information </h4>
            <hr class="my-3">
            <div class="first-box">
              <div class="row">
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{{ trans('panel.user.fields.gender') }}</label>
                    <!-- <div class="form-group has-default bmd-form-group"> -->
                    <select class="form-control" data-style="select-with-transition" name="gender">
                      <option value="" disabled selected>Gender</option>
                      <option value="Male" {{ (old('gender')??$user->gender == 'Male') ? 'selected' : '' }}>Male</option>
                      <option value="Female" {{ (old('gender')??$user->gender == 'Female') ? 'selected' : '' }}>Female</option>
                    </select>
                    <!-- </div> -->
                    @if ($errors->has('gender'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('gender') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <!--          <div class="col-md-6">
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
                  </div> -->
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.marital_status') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control" name="marital_status">
                        <option value="" disabled selected>Marital Status</option>
                        <option {{ ($user->userinfo?$user->userinfo->marital_status:'' == 'Single') ? 'selected' : '' }} value="Single">Single</option>
                        <option {{ (old('marital_status') ?? ($user->userinfo ? $user->userinfo->marital_status : '')) == 'Married' ? 'selected' : '' }} value="Married">Married</option>
                        <option {{ (old('marital_status') ?? ($user->userinfo ? $user->userinfo->marital_status : '')) == 'Separated' ? 'selected' : '' }} value="Separated">Separated</option>
                        <option {{ (old('marital_status') ?? ($user->userinfo ? $user->userinfo->marital_status : '')) == 'Widowed' ? 'selected' : '' }} value="Widowed">Widowed</option>
                        <option {{ (old('marital_status') ?? ($user->userinfo ? $user->userinfo->marital_status : '')) == 'Divorced' ? 'selected' : '' }} value="Divorced">Divorced</option>
                      </select>
                    </div>
                    @if ($errors->has('marital_status'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('marital_status') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.date_of_birth') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="date_of_birth" id="date_of_birth" class="form-control datepicker" value="{!! $user->userinfo?$user->userinfo->date_of_birth:'', old( 'date_of_birth', $user['date_of_birth']) !!}" autocomplete="off">
                    </div>
                    @if ($errors->has('date_of_birth'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('date_of_birth') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.age') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="age" id="age" readonly class="form-control" value="{!! old( 'age') !!}" autocomplete="off">
                    </div>
                    @if ($errors->has('age'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('age') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.pan_number') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="pan_number" id="pan_number" class="form-control" value="{!! $user->userinfo?$user->userinfo->pan_number:'', old( 'pan_number') !!}">
                      @if ($errors->has('pan_number'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('pan_number') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.pan_card_image') !!}</label>
                    <div class=" has-default bmd-form-group">
                      <input type="file" name="pan_card_image" class="form-control">
                    </div>
                  </div>
                  <div class=" p-0">
                    @if($user->id)
                    @if(count($user->getMedia('pan_image')) > 0)
                    <a class="text-info h3" title="Download Pan" download="download" href="{{$user->getMedia('pan_image')[0]->getFullUrl()}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                    @else
                    <span title="Not Uploaded" class="text-danger h3"><i class="fa fa-exclamation-circle"></i></span>
                    @endif
                    @else
                    <span title="Not Uploaded" class="text-danger h3"><i class="fa fa-exclamation-circle"></i></span>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.aadhar_number') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="aadhar_number" id="aadhar_number" class="form-control" value="{!! $user->userinfo?$user->userinfo->aadhar_number:'', old( 'aadhar_number') !!}">
                      @if ($errors->has('aadhar_number'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('aadhar_number') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.aadhar_card_image') !!}</label>
                    <div class="has-default bmd-form-group">
                      <input type="file" name="aadhar_card_image" class="form-control">
                    </div>
                    <div class="p-0">
                      @if($user->id)
                      @if(count($user->getMedia('aadhar_image')) > 0)
                      <a class="text-info h3" title="Download Aadhar" download="download" href="{{$user->getMedia('aadhar_image')[0]->getFullUrl()}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                      @else
                      <span title="Not Uploaded" class="text-danger h3"><i class="fa fa-exclamation-circle"></i></span>
                      @endif
                      @else
                      <span title="Not Uploaded" class="text-danger h3"><i class="fa fa-exclamation-circle"></i></span>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.emergency_number') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="number" name="emergency_number" class="form-control" value="{!! $user->userinfo?$user->userinfo->emergency_number:'', old( 'emergency_number') !!}">
                      @if ($errors->has('emergency_number'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('emergency_number') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.current_address') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="current_address" class="form-control" value="{!! $user->userinfo?$user->userinfo->current_address:'', old( 'current_address') !!}">
                      @if ($errors->has('current_address'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('current_address') }}</p>
                      </div>
                      @endif
                    </div>

                  </div>
                </div>
                <div class="col-md-2">
                  <label class="form-check-label form-group">
                    <input class="form-check-input copyaddreess" type="checkbox" aria-required="true">&nbsp Copy Address
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>
                  </label>
                </div>
                <div class="col-md-12">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.permanent_address') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="permanent_address" class="form-control permanentAddress" value="{!! $user->userinfo?$user->userinfo->permanent_address:'', old( 'permanent_address') !!}">
                      @if ($errors->has('permanent_address'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('permanent_address') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center">Family Information</h4>
            <hr class="my-3">
            <div class="first-box">
              <div class="row">
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.father_name') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="father_name" class="form-control" value="{!! $user->userinfo?$user->userinfo->father_name:'',old( 'father_name' )!!}">
                      @if ($errors->has('father_name'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('father_name') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.father_date_of_birth') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="father_date_of_birth" class="form-control datepicker" value="{!! $user->userinfo?$user->userinfo->father_date_of_birth:'',old( 'father_date_of_birth') !!}" autocomplete="off">
                    </div>
                    @if ($errors->has('father_date_of_birth'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('father_date_of_birth') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.mother_name') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="mother_name" class="form-control" value="{!!  $user->userinfo?$user->userinfo->mother_name:'',old( 'mother_name') !!}">
                    </div>
                    @if ($errors->has('mother_name'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('mother_name') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.mother_date_of_birth') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="mother_date_of_birth" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->mother_date_of_birth:'',old( 'mother_date_of_birth') !!}" autocomplete="off">
                    </div>
                    @if ($errors->has('mother_date_of_birth'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('mother_date_of_birth') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.marriage_anniversary') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="marriage_anniversary" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->marriage_anniversary:'',old( 'marriage_anniversary') !!}" autocomplete="off">
                    </div>
                    @if ($errors->has('marriage_anniversary'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('marriage_anniversary') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.spouse_name') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="spouse_name" class="form-control" value="{!!  $user->userinfo?$user->userinfo->spouse_name:'',old( 'spouse_name' ) !!}">
                    </div>
                    @if ($errors->has('spouse_name'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('spouse_name') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.spouse_date_of_birth') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="spouse_date_of_birth" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->spouse_date_of_birth:'',old( 'spouse_date_of_birth' ) !!}" autocomplete="off">
                    </div>
                    @if ($errors->has('spouse_date_of_birth'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('spouse_date_of_birth') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.children_one') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="children_one" class="form-control" value="{!!  $user->userinfo?$user->userinfo->children_one:'',old( 'children_one' ) !!}">
                      @if ($errors->has('children_one'))
                    </div>
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('children_one') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_one_date_of_birth') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_one_date_of_birth" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->children_one_date_of_birth:'',old( 'children_one_date_of_birth') !!}" autocomplete="off">
                  </div>
                  @if ($errors->has('children_one_date_of_birth'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('children_one_date_of_birth') }}</p>
                  </div> @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_two') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_two" class="form-control" value="{!!  $user->userinfo?$user->userinfo->children_two:'',old( 'children_two') !!}">
                  </div>
                  @if ($errors->has('children_two'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('children_two') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_two_date_of_birth') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_two_date_of_birth" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->children_two_date_of_birth:'',old( 'children_two_date_of_birth') !!}" autocomplete="off">
                  </div>
                  @if ($errors->has('children_two_date_of_birth'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('children_two_date_of_birth') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_three') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_three" class="form-control" value="{!!  $user->userinfo?$user->userinfo->children_three:'',old( 'children_three') !!}">
                    @if ($errors->has('children_three'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('children_three') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_three_date_of_birth') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_three_date_of_birth" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->children_three_date_of_birth:'',old( 'children_three_date_of_birth') !!}" autocomplete="off">
                  </div>
                  @if ($errors->has('children_three_date_of_birth'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('children_three_date_of_birth') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_four') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_four" class="form-control" value="{!!  $user->userinfo?$user->userinfo->children_four:'',old( 'children_four') !!}">
                  </div>
                  @if ($errors->has('children_four'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('children_four') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_four_date_of_birth') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_four_date_of_birth" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->children_four_date_of_birth:'',old( 'children_four_date_of_birth') !!}" autocomplete="off">
                  </div>
                  @if ($errors->has('children_four_date_of_birth'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('children_four_date_of_birth') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_five') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_five" class="form-control" value="{!!  $user->userinfo?$user->userinfo->children_five:'',old( 'children_five') !!}">
                  </div>
                  @if ($errors->has('children_five'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('children_five') }}</p>
                  </div>
                  @endif
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.user.children_five_date_of_birth') !!}</label>
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="children_five_date_of_birth" class="form-control datepicker" value="{!!  $user->userinfo?$user->userinfo->children_five_date_of_birth:'',old( 'children_five_date_of_birth') !!}" autocomplete="off">
                  </div>
                  @if ($errors->has('children_five_date_of_birth'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('children_five_date_of_birth') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center">Bank Information </h4>
            <hr class="my-3">
            <div class="first-box">
              <div class="row">
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.account_number') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="account_number" id="account_number" class="form-control" value="{!!  $user->userinfo?$user->userinfo->account_number:'',old( 'account_number') !!}">
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
                    <label class="col-form-label">{!! trans('panel.user.bank_name') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="bank_name" class="form-control" value="{!! old( 'bank_name', $user->userinfo?$user->userinfo->bank_name:'') !!}">
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
                    <label class="col-form-label">{!! trans('panel.user.ifsc_code') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="ifsc_code" class="form-control" value="{!! old( 'ifsc_code', $user->userinfo?$user->userinfo->ifsc_code:'') !!}">
                      @if ($errors->has('ifsc_code'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('ifsc_code') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center">HR Information </h4>
            <hr class="my-3">
            <div class="first-box">
              <div class="row">
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.pay_roll') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control" name="payroll">
                          <option value="" disabled selected>Select Pay Roll</option>
                          @foreach($pay_rolls as $key=> $pay_roll)
                          <option {{ (($key == old('payroll', $user->payroll?$user->payroll:''))) ? 'selected' : '' }} value="{{$key}}">{{$pay_roll}}</option>
                          @endforeach
                        </select>
                      </div>
                      @if ($errors->has('payroll'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('payroll') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                @if(Auth::user()->id == '1')
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.salary') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="salary" class="form-control" value="{!! old( 'salary', $user->userinfo?$user->userinfo->salary:'') !!}">
                      @if ($errors->has('salary'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('salary') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.ctc_annual') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="ctc_annual" class="form-control" value="{!! $user->userinfo?$user->userinfo->ctc_annual:'',old( 'ctc_annual') !!}">
                      @if ($errors->has('ctc_annual'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('ctc_annual') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.gross_salary_monthly') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="gross_salary_monthly" class="form-control" value="{!! $user->userinfo?$user->userinfo->gross_salary_monthly:'',old( 'gross_salary_monthly') !!}">
                      @if ($errors->has('gross_salary_monthly'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('gross_salary_monthly') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.last_year_increments') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="last_year_increments" class="form-control" value="{!! $user->userinfo?$user->userinfo->last_year_increments:'',old( 'last_year_increments') !!}">
                      @if ($errors->has('last_year_increments'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('last_year_increments') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.last_year_increment_percent') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="last_year_increment_percent" class="form-control" value="{!! $user->userinfo?$user->userinfo->last_year_increment_percent:'',old( 'last_year_increment_percent') !!}">
                      @if ($errors->has('last_year_increment_percent'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('last_year_increment_percent') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.last_year_increment_value') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="last_year_increment_value" class="form-control" value="{!! $user->userinfo?$user->userinfo->last_year_increment_value:'',old( 'last_year_increment_value') !!}">
                      @if ($errors->has('last_year_increment_value'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('last_year_increment_value') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.last_promotion') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="last_promotion" class="form-control" value="{!! $user->userinfo?$user->userinfo->last_promotion:'',old( 'last_promotion') !!}">
                      @if ($errors->has('last_promotion'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('last_promotion') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                @endif
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.pf_number') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="pf_number" class="form-control" value="{!! old( 'pf_number', $user->userinfo?$user->userinfo->pf_number:'') !!}">
                      @if ($errors->has('pf_number'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('pf_number') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.un_number') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="un_number" class="form-control" value="{!! old( 'un_number', $user->userinfo?$user->userinfo->un_number:'') !!}">
                      @if ($errors->has('un_number'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('un_number') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.esi_number') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="esi_number" class="form-control" value="{!! old( 'esi_number', $user->userinfo?$user->userinfo->esi_number:'') !!}">
                      @if ($errors->has('esi_number'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('esi_number') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.probation_period') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="probation_period" class="form-control datepicker" value="{!! old( 'probation_period', $user->userinfo?$user->userinfo->probation_period:'') !!}">
                      @if ($errors->has('probation_period'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('probation_period') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.date_of_confirmation') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="date_of_confirmation" class="form-control datepicker" value="{!! old( 'date_of_confirmation', $user->userinfo?$user->userinfo->date_of_confirmation:'') !!}" autocomplete="off">
                      @if ($errors->has('date_of_confirmation'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('date_of_confirmation') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.notice_period') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="notice_period" class="form-control" value="{!! old( 'notice_period', $user->userinfo?$user->userinfo->notice_period:'') !!}">
                      @if ($errors->has('notice_period'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('notice_period') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.date_of_leaving') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="date_of_leaving" class="form-control datepicker" value="{!! old( 'date_of_leaving', $user->userinfo?$user->userinfo->date_of_leaving:'') !!}">
                      @if ($errors->has('date_of_leaving'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('date_of_leaving') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{{ trans('panel.user.sales_type') }}</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="selectpicker" data-style="select-with-transition" name="sales_type">
                        <option value="" disabled selected>{{ trans('panel.user.sales_type') }}</option>
                        <option value="Primary" {{ (old('sales_type')??$user->sales_type == 'Primary') ? 'selected' : '' }}>Primary</option>
                        <option value="Secondary" {{ (old('sales_type')??$user->sales_type == 'Secondary') ? 'selected' : '' }}>Secondary</option>
                      </select>
                    </div>
                    @if ($errors->has('sales_type'))
                    <div class="error">
                      <p class="text-danger">{{ $errors->first('sales_type') }}</p>
                    </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Lave Balance</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="leave_balance" class="form-control" value="{!! old( 'leave_balance', $user->leave_balance?$user->leave_balance:'') !!}">
                      @if ($errors->has('leave_balance'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('leave_balance') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Grade</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="grade" class="form-control" value="{!! old( 'grade', $user->grade?$user->grade:'') !!}">
                      @if ($errors->has('grade'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('grade') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Blood Group</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="blood_group" class="form-control" value="{!! old( 'blood_group', $user->blood_group?$user->blood_group:'') !!}">
                      @if ($errors->has('blood_group'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('blood_group') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Personal Number</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="number" name="personal_number" class="form-control" value="{!! old( 'personal_number', $user->personal_number?$user->personal_number:'') !!}">
                      @if ($errors->has('personal_number'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('personal_number') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <!-- user sales type -->
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Attandance Summary Report</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="radio" name="show_attandance_report" class="" value="1" {{old( 'show_attandance_report', $user->show_attandance_report) == '1'?'checked':''}}> <span class="yes_no">Yes</span>
                      <input type="radio" name="show_attandance_report" class="" value="0" {{old( 'show_attandance_report', $user->show_attandance_report) == '0'?'checked':''}}> <span class="yes_no">No</span>
                      @if ($errors->has('show_attandance_report'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('show_attandance_report') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center">Joining Information </h4>
            <hr class="my-3">
            <div class="first-box">
              <div class="row">
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.date_of_joining') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" id="date_of_joining" name="date_of_joining" class="form-control datepicker" value="{!! old( 'date_of_joining', $user->userinfo?$user->userinfo->date_of_joining:'') !!}" autocomplete="off">
                      @if ($errors->has('date_of_joining'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('date_of_joining') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Head Quarter</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="location" class="form-control" value="{!! old( 'location', $user['location']) !!}">
                      @if ($errors->has('location'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('location') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.employee_codes') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="employee_codes" id="employee_codes" class="form-control" value="{!! old( 'employee_codes', $user['employee_codes']) !!}" required>
                      @if ($errors->has('employee_codes'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('employee_codes') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.biometric_code') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="biometric_code" id="biometric_code" class="form-control" value="{!! old( 'biometric_code', isset($user->userinfo->biometric_code)?$user->userinfo->biometric_code:'') !!}">
                      @if ($errors->has('biometric_code'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('biometric_code') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.designation') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control" name="designation_id">
                        <option value="" disabled selected>Select {!! trans('panel.user.designation') !!}</option>
                        @foreach($designations as $designation)
                        <option value="{{$designation->id}}" {{ (($designation->id == old('designation_id', ($user->id && $user->getdesignation)?$user->getdesignation->id:''))) ? 'selected' : '' }}> {{$designation->designation_name}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('designation_id'))
                      <div class="invalid-feedback">
                        {{ $errors->first('designation_id') }}
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.branch_name') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2" name="branch_id[]" multiple required>
                          <option value="" disabled>Select Branch</option>
                          <?php
                          $branch_id = $user->branch_id;
                          $branch_check = App\Models\Branch::where('id', $branch_id)->first();

                          if (!empty($branch_check)) {
                          ?>
                            @foreach($branches as $branche)
                            <option {{ (in_array($branche->id, explode(',', $branch_id))) ? 'selected' : '' }} value="{{$branche->id}}">{{$branche->branch_name}}</option>
                            @endforeach
                          <?php } else { ?>
                            @foreach($branches as $branche)
                            <option value="{{$branche->id}}">{{$branche->branch_name}}</option>
                            @endforeach
                          <?php  } ?>
                        </select>
                      </div>
                      @if ($errors->has('branch_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Primary {!! trans('panel.user.branch_name') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2" name="primary_branch_id">
                          <option value="" disabled selected>Select Branch</option>
                            @foreach($branches as $branche)
                            <option {{ $user->primary_branch_id == $branche->id ? 'selected' : '' }} value="{{$branche->id}}">{{$branche->branch_name}}</option>
                            @endforeach
                        </select>
                      </div>
                      @if ($errors->has('primary_branch_id'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('primary_branch_id') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.division') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control" name="division_id">
                        <option value="" disabled selected>Select {!! trans('panel.user.division') !!}</option>
                        @foreach($divisions as $division)
                        <option value="{{$division->id}}" {{ (($division->id == old('division_id', $user->id?($user->getdivision?$user->getdivision->id:''):''))) ? 'selected' : '' }}>{{$division->division_name}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('division_id'))
                      <div class="invalid-feedback">
                        {{ $errors->first('division_id') }}
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.department') !!}</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control" name="department_id">
                        <option value="" disabled selected>Select {!! trans('panel.user.department') !!}</option>
                        @foreach($departments as $department)
                        <option value="{{$department->id}}" {{ (($department->id == old('department_id', $user->id?($user->getdepartment?$user->getdepartment->id:''):''))) ? 'selected' : '' }}>{{$department->name}}</option>
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
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.order_mail') !!}<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="order_mails" id="order_mails" class="form-control" value="{!! old( 'order_mails', $user->userinfo?$user->userinfo->order_mails:'') !!}">
                      @if ($errors->has('order_mails'))
                      <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('order_mails') }}</p>
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">Order Mails Type<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2" multiple name="order_mails_type[]" style="width: 100%;" id="type">
                        <option value="">Select {!! trans('panel.customers.fields.customertype') !!}</option>
                        @if(@isset($customertype ))
                        @foreach($customertype as $type)
                        <option value="{!! $type['id'] !!}" {{ (in_array($type['id'],($user->userinfo?explode(',',$user->userinfo->order_mails_type):[]))) ? 'selected' : '' }}>{!! $type['customertype_name'] !!}</option>
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
                    <label class="col-form-label">Selecte Ware House</label>
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control" name="warehouse_id">
                        <option value="" disabled selected>Select Ware House</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{$warehouse->id}}" {{ (($warehouse->id == old('warehouse_id', $user->id?($user->warehouse?$user->warehouse->id:''):''))) ? 'selected' : '' }}>{{$warehouse->warehouse_name}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('warehouse_id'))
                      <div class="invalid-feedback">
                        {{ $errors->first('warehouse_id') }}
                      </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center">Educational Information </h4>
            <hr class="my-3">
            <div class="first-box">
              <div class="row">
                <div class="table-responsive">
                  <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                    <thead>
                      <tr class="item-row">
                        <th>Degree</th>
                        <th>University</th>
                        <th>Percentage</th>
                        <th>Grade</th>
                        <th>Upload File</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach(config('constants.education_type') as $k=>$education_type)
                      <tr class="item-row">
                        <input type="hidden" class="form-control" name="education_detail[{{$k}}][education_type_id]" value="{{$k}}">
                        @php
                        $education = $user->geteducation->where('education_type_id', $k)->where('user_id', $user->id)->first();
                        @endphp
                        <td>
                          <input type="text" class="form-control" value="{{ $education ? $education->degree_name : '' }}" name="education_detail[{{$k}}][degree_name]" placeholder="{{ $education_type }}">
                        </td>
                        <td>
                          <input type="text" class="form-control" value="{{ $education ? $education->board_name : '' }}" name="education_detail[{{$k}}][board_name]">
                        </td>
                        <td>
                          <input type="text" class="form-control" value="{{ $education ? $education->percentage : '' }}" name="education_detail[{{$k}}][percentage]">
                        </td>
                        <td>
                          <input type="text" class="form-control" value="{{ $education ? $education->grade : '' }}" name="education_detail[{{$k}}][grade]">
                        </td>
                        <td>
                          <div class="img_loy">
                            <img src="{{ ($education ? (count($education->getMedia('education_image')) > 0 ? $education->getMedia('education_image')[0]->getFullUrl() : asset('assets/img/placeholder.jpg')) : asset('assets/img/placeholder.jpg')) }}" class="imagepreview{{$k}}" width="70px;">
                            <div class="iconpos btn btn-just-icon btn-round btn-file">
                              <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                              <input type="file" name="education_detail[{{$k}}][image]" class="getimage{{$k}}">
                            </div>
                          </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- <div class="col-md-6">
          <div class="row">
             <label class="col-md-3 col-form-label">{!! trans('panel.user.other_education') !!}</label>
             <div class="col-md-9">
                <div class="form-group has-default bmd-form-group">
                   <input type="text" name="other_education" id="other_education" class="form-control" value="{!! old( 'other_education') !!}">
                   @if ($errors->has('other_education'))
                   <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('other_education') }}</p>
                   </div>
                   @endif
                </div>
             </div>
          </div>
          </div> -->
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.current_company_tenture') !!}(in month)</label>

                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="In Year" readonly type="number" name="current_company_tenture" id="current_company_tenture" class="form-control" value="{!! old( 'current_company_tenture', $user->userinfo?$user->userinfo->current_company_tenture:'') !!}">
                      @if ($errors->has('current_company_tenture'))
                      <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('current_company_tenture') }}</p>
                      </div>
                      @endif
                    </div>

                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.previous_exp') !!}(in year)</label>

                    <div class="form-group has-default bmd-form-group">
                      <input type="number" placeholder="In Year" name="previous_exp" id="previous_exp" class="form-control" value="{!! old( 'previous_exp', $user->userinfo?$user->userinfo->previous_exp:'') !!}">
                      @if ($errors->has('previous_exp'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('previous_exp') }}</p>
                      </div>
                      @endif

                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.user.total_exp') !!}(in year)</label>

                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="In Year" type="number" readonly name="total_exp" id="total_exp" class="form-control">
                      @if ($errors->has('total_exp'))
                      <div class="error">
                        <p class="text-danger">{{ $errors->first('total_exp') }}</p>
                      </div>
                      @endif

                    </div>
                  </div>

                </div>

                <div class=" col-md-12 pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-info submituser')) }}
                </div>
              </div>
            </div>
        </div>

        </form>
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_users.js?v='.time()) }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.submituser').click(function() {

      });
    });
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

    $(document).on('change', '#date_of_birth', function() {
      var DOB = $(this).val();
      dob = new Date(DOB);
      var today = new Date();
      var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
      $('#age').val(age);
    })

    $(document).ready(function() {
      var DOB = $('#date_of_birth').val();
      if (DOB != '') {
        dob = new Date(DOB);
        var today = new Date();
        var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
        $('#age').val(age);
      }
      var current_company_tenture = $('#current_company_tenture').val();
      var previous_exp = $('#previous_exp').val();
      $('#total_exp').val(parseInt(current_company_tenture) + parseInt(previous_exp));

      var DOJ = $('#date_of_joining').val();
      var doj = new Date(DOJ);
      var today = new Date();

      var yearDiff = today.getFullYear() - doj.getFullYear();
      var monthDiff = today.getMonth() - doj.getMonth();

      var current_company_tenure = (yearDiff * 12) + monthDiff;
      current_company_tenure = current_company_tenure < 0 ? 0 : current_company_tenure;

      $('#current_company_tenture').val(current_company_tenure);
      var previous_exp = $('#previous_exp').val();

      $('#total_exp').val(parseInt(current_company_tenture) + parseInt(previous_exp));
    })

    $(document).on('change', '#date_of_joining', function() {
      var DOB = $(this).val();
      var doj = new Date(DOJ);
      var today = new Date();

      var yearDiff = today.getFullYear() - doj.getFullYear();
      var monthDiff = today.getMonth() - doj.getMonth();

      var current_company_tenure = (yearDiff * 12) + monthDiff;
      current_company_tenure = current_company_tenure < 0 ? 0 : current_company_tenure;

      $('#current_company_tenture').val(current_company_tenure);
      var previous_exp = $('#previous_exp').val();

      $('#total_exp').val(parseInt(current_company_tenture) + parseInt(previous_exp));
    })

    $(document).on('change', '#current_company_tenture', function() {
      var current_company_tenture = $(this).val();
      var previous_exp = $('#previous_exp').val();
      $('#total_exp').val(parseInt(current_company_tenture) + parseInt(previous_exp));
    })

    $(document).on('keyup', '#previous_exp', function() {
      var previous_exp = $(this).val();
      var current_company_tenture = $('#current_company_tenture').val();
      $('#total_exp').val(parseInt(current_company_tenture) + parseInt(previous_exp));
    })

    $("#roles").on("change", function() {
      if (jQuery.inArray("8", $(this).val()) !== -1 || jQuery.inArray("37", $(this).val()) !== -1) {
        $("#branch-data").removeClass('d-none');
      } else {
        $("#branch-data").addClass('d-none');
      }
    }).trigger('change');
  </script>
</x-app-layout>