<x-app-layout>
   <style>
      #pass-div {
         position: relative;
      }

      #pass-seen {
         position: absolute;
         top: 7px;
         right: 10px;
         cursor: pointer;
      }
   </style>
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
               <form method="POST" action="{{($user->id)?route('users.update', [$user->id]): route('users.store') }}" enctype="multipart/form-data" id="{{($user->id)?'updateCustomerUserData':'storeCustomerUserData'}}">
                  @csrf
                  @if($user->id)
                  @method('PUT')
                  <div class="card-avatar mb-3">
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
                           <img src="{{ ($user ? (count($user->getMedia('profile_image')) > 0 ? $user->getMedia('profile_image')[0]->getFullUrl() : asset('assets/img/placeholder.jpg')) : asset('assets/img/placeholder.jpg')) }}" class="imagepreview1">
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                        <label class="bmd-label-floating">{!! trans('panel.user.profile_image') !!}</label>
                     </div>
                  </div>
                  @endif
                  <input type="hidden" name="name" id="name" required>
                  <input type="hidden" name="reportingid" id="reportingid" required>
                  <div class="first-box">
                     <div class="row">
                        <input type="hidden" name="id" id="user_id" value="{!! $user?$user->id:'' !!}">

                        <div class="col-md-12">
                           <div class="input_section">
                              <label class="col-form-label">Customer To</label>
                           
                                 <div class="form-group has-default bmd-form-group">
                                    <select class="form-control select2" name="customerid" id="customerid" required>
                                       <option value="" disabled>Select Customer</option>
                                       @if($user->id)
                                       <option value="{{$user->customerid}}" selected>{{ $user->user_customer->name}}</option>
                                       @endif
                                    </select>
                                    @if($errors->has('customerid'))
                                    <div class="invalid-feedback">
                                       {{ $errors->first('customerid') }}
                                    </div>
                                    @endif
                                 </div>
                              </div>
                       
                        </div>
                        <div class="col-md-6">
                           <div class="input_section">
                              <label class=" col-form-label">{{ trans('panel.user.fields.first_name') }} <span class="text-danger"> *</span></label>
                            
                                 <div class="form-group has-default bmd-form-group">
                                    <input type="text" name="first_name" id="first_name" class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" value="{!! old( 'first_name'), $user->first_name !!}" maxlength="200" required>
                                    @if ($errors->has('first_name'))
                                    <div class="error col-lg-12">
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
                                    <input type="text" name="last_name" id="last_name" class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" value="{!! old( 'last_name'), $user->last_name !!}" maxlength="200" required>
                                    @if ($errors->has('last_name'))
                                    <div class="error col-lg-12">
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
                              <label class="ccol-form-label">{!! trans('panel.global.email') !!}<span class="text-danger"> *</span></label>
                            
                                 <div class="form-group has-default bmd-form-group">
                                    <input type="email" name="email" id="email" class="form-control" value="{!! old( 'email', $user->email) !!}" maxlength="200">
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
                                    <input type="number" name="mobile" id="mobile" class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}" value="{!!  $user->mobile, old( 'mobile') !!}" maxlength="10" minlength="10" required>
                                 </div>
                                 @if ($errors->has('mobile'))
                                 <label class="error">{{ $errors->first('mobile') }}</label>
                                 @endif
                              </div>
                        
                        </div>
                        <div class="col-md-6">
                           <div class="input_section">
                              <label class="col-form-label">{{ trans('panel.user.fields.password') }}<span class="text-danger"> *</span></label>
                          
                                 <div class="form-group has-default bmd-form-group" id="pass-div">
                                    <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="text" name="password" id="password" value="{{ old('password', $user->password_string) }}" minlength="6" maxlength="200" required>
                                    <span class="material-icons" title="Show" id="pass-seen">visibility</span>
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
                        {{-- <div class="col-md-6 d-none" id="branch-data">
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
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('branch_show') }}</p>
                     </div>
                     @endif
                  </div>
        
         </div>
      </div> --}}
   </div>
   </div>
   <div class="row pull-right">
      {{ Form::submit('Submit', array('class' => 'btn btn-info submituser')) }}
   </div>
   </form>
   </div>
   </div>
   </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
   <script src="{{ url('/').'/'.asset('assets/js/validation_users.js') }}"></script>
   <script type="text/javascript">
      $(document).ready(function() {
         setTimeout(() => {
            var $customerSelect = $('#customerid').select2({
               placeholder: 'Customer Select...',
               multiple: false,
               allowClear: true,
               ajax: {
                  url: "{{ route('getCustomerDataSelect') }}",
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
      })
      $(document).on('change', '#customerid', function() {
         var customID = $(this).val();
         $.ajax({
            url: "{{ url('getCustomerData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               customer_id: customID
            },
            success: function(res) {
               let mobile = res.mobile;
               if (mobile.length === 12 && mobile.startsWith('91')) {
                  mobile = mobile.substring(2);
               }
               if (res.created_by != '' && res.created_by != null) {
                  $("#reportingid").val(res.created_by);
               } else if (res.executive_id != '' && res.executive_id != null) {
                  $("#reportingid").val(res.executive_id);
               } else {
                  $("#reportingid").val('1');
               }
               $("#first_name").val(res.first_name);
               $("#last_name").val(res.last_name);
               $("#email").val(res.email);
               $("#mobile").val(mobile);
               $("#password").val('');
               $("#name").val(res.name);
            }
         });
      })
      $("#pass-seen").on('click', function() {
         let currentText = $(this).text();
         let passwordField = $("#password");

         if (currentText === 'visibility') {
            $(this).text('visibility_off');
            $(this).attr('title', 'Show');
            passwordField.attr("type", "password");
         } else {
            $(this).text('visibility');
            $(this).attr('title', 'Hide');
            passwordField.attr("type", "text");
         }
      });
   </script>
</x-app-layout>