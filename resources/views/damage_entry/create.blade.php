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

   /*   .select2-container {
         border-bottom: 1px solid lightgray;
      }*/

      #help-image {
         position: absolute;
         width: 200px;
         top: -118px;
         right: 0;
      }
      #help-icon{
         cursor: pointer;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card mt-0 pt-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        Add Damage Entry   </h4>
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('transaction_history') }}">
                                 <i class="material-icons">next_plan</i> Damage Entries
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
               {!! Form::model($DamageEntry,[
               'route' => $DamageEntry->exists ? ['damage_entries.update', encrypt($DamageEntry->id) ] : 'damage_entries.store',
               'method' => $DamageEntry->exists ? 'PUT' : 'POST',
               'id' => 'storeDamageEntryData',
               'files'=>true
               ]) !!}
               <div class="row">
                  
                     <div class="col-md-6">
                        <div class="input_section">
                        <label for="customer_id" class="col-form-label">Customer</label>
                   
                        <select name="customer_id" id="customer_id" placeholder="Select Customers" class="select2 form-control">
                           <option value="" disabled selected>Select Customer</option>
                           
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
                        <label for="coupen_code" class="col-form-label">Coupen Code</label>
                   
                        <input type="text" name="coupen_code" id="coupen_code" class="form-control">
                        @if ($errors->has('coupen_code'))
                        <div class="error ">
                           <p class="text-danger">{{ $errors->first('coupen_code') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
              
               <div class="row mt-2">
                  <div class="col-md-12 col-sm-12">
                     <img id="help-image"  src="{{asset('damageimg.jpg')}}" style="display: none;" />
                     <h4 class="card-title ">Attachment <i id="help-icon" class="material-icons">help_outline</i></h4>
                  </div>
                  <div class="col-md-3 col-sm-3">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                     
                        <div class="fileinput-new thumbnail">
                           <img src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                              <div class="selectThumbnail">
                           <span class="btn btn-just-icon btn-round btn-file">
                              <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="file" name="damageattach1" class="getimage1" accept="image/*">
                           </span>
                          
                           <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                        </div>
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                        <label class="bmd-label-floating">Attachment 1st</label>
                        @if ($errors->has('damageattach1'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('damageattach1') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-3">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                     
                        <div class="fileinput-new thumbnail">
                           <img src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview2">
                              <div class="selectThumbnail">
                           <span class="btn btn-just-icon btn-round btn-file">
                              <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="file" name="damageattach2" class="getimage2" accept="image/*">
                           </span>
                           <br>
                           <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                        </div>
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                        <label class="bmd-label-floating">Attachment 2nd</label>
                     </div>
                  </div>
                  <div class="col-md-3 col-sm-3">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                      
                        <div class="fileinput-new thumbnail">
                           <img src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview3">
                             <div class="selectThumbnail">
                           <span class="btn btn-just-icon btn-round btn-file">
                              <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                              <span class="fileinput-exists">Change</span>
                              <input type="file" name="damageattach3" class="getimage3" accept="image/*">
                           </span>
                           <br>
                           <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                        </div>
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                        <label class="bmd-label-floating">Attachment 3rd</label>
                     </div>
                  </div>
               </div>
</div>
               <div class="pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
               {{ Form::close() }}
            
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
   <script>
      $(document).ready(function() {
         // Show image on hover
         $('#help-icon').hover(function() {
            $('#help-image').fadeIn();
         }, function() {
            $('#help-image').fadeOut();
         });
      });
      setTimeout(() => {
         $('#customer_id').select2({
            placeholder: 'Select Customer',
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
         }).trigger('change');
      }, 1000);
   </script>
</x-app-layout>