<x-app-layout>
   <style>
      .row.image_preview {
         border: 1px solid lightgrey;
         border-radius: 10px;
      }

      .img-div {
         position: relative;
      }

      span.delete-img {
         position: absolute;
         top: -8px;
         right: -14px;
         background: red;
         color: #fff;
         border-radius: 50%;
         width: 20px;
         height: 20px;
         text-align: center;
         font-size: 14px;
         line-height: 18px;
         font-weight: 900;
         cursor: pointer;
         display: flex;
         align-items: center;
         justify-content: center;
      }

      .select2-container--default .select2-selection--multiple .select2-selection__choice {
         background-color: #0080b8;

      }

      .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
         color: red;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card p-0 mt-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Loyalty App Setting
                        @if(auth()->user()->can(['district_access']))
                        <!-- <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('schemes') }}">
                                 <i class="material-icons">next_plan</i> {!! trans('panel.scheme.title') !!}
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                        </ul> -->
                        @endif
                     </h4>
                  </div>
               </div>
            </div>
            @if (session('success'))
            <div class="alert alert-success mt-3">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               {{ session('success') }}
            </div>
            @endif
            <div class="alert mt-3" style="display: none;">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               <span class="message"></span>
            </div>
            @if (session('error'))
            <div class="alert alert-danger mt-3">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               {{ session('error') }}
            </div>
            @endif
            @if(isset($loyalty_app_setting) && $loyalty_app_setting->getMedia('slider_image')->count() > 0 && Storage::disk('s3')->exists($loyalty_app_setting->getMedia('slider_image')[0]->getPath()))
            <div class="image_preview p-4">
               <div class="row">

                  <div class="col-md-12">
                     <label class="bmd-label-floating mt-2">Main Slider Images</label>
                  </div>
                  @foreach($loyalty_app_setting->getMedia('slider_image') as $loyalty_app_setting_image)
                  <div class="col-md-3">
                     <div class="img-div">
                        <img style="box-shadow: 0 0 15px #000;" class="rounded" width="100%" src="{{$loyalty_app_setting_image->getFullUrl()}}" alt=""><span title="Delete Image" class="delete-img" data-id="{{$loyalty_app_setting_image->id}}">X</span>
                     </div>
                  </div>

                  @endforeach
               </div>

               @endif
               <div class="card-body p-0 mt-5">
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
                  {!! Form::model($loyalty_app_setting,[
                  'route' => 'loyalty-app-setting.store',
                  'method' => 'POST',
                  'id' => 'storeLoyaltyAppSetting',
                  'files'=>true
                  ]) !!}
                  <div class="row">
                     <div class="col-md-3">
                        <div class="input_section">

                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                                 <div class="selectThumbnail">
                                    <span class="btn btn-just-icon btn-round btn-file">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <span class="fileinput-exists">Change</span>
                                       <input type="file" name="images[]" multiple id="images" class="form-control" accept="image/png, image/gif, image/jpeg">
                                    </span>
                                    <br>
                                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                                 </div>
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>

                           </div>
                           <label for="warranty_activation_attach" class="bmd-label-floating">Upload Main Silder Images</label>
                        </div>



                        @if ($errors->has('images'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('images') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                   @if(isset($loyalty_app_setting) && $loyalty_app_setting->getMedia('gift_slider_image')->count() > 0 && Storage::disk('s3')->exists($loyalty_app_setting->getMedia('gift_slider_image')[0]->getPath()))

                  <div class="row mt-4">loyalty_side_menu_image
                     <div class="col-md-12">
                        <label class="bmd-label-floating mt-2">Loyalty Side Menu Image</label>
                     </div>

                     @if ($loyalty_app_setting->hasMedia('loyalty_side_menu_image'))
                         @php
                             $image = $loyalty_app_setting->getFirstMedia('loyalty_side_menu_image');
                         @endphp
                         <div class="col-md-3">
                           <div class="img-div">
                              <img style="box-shadow: 0 0 15px #000;" class="rounded" width="100%" src="{{ $image->getFullUrl() }}" alt=""><span title="Delete Image" class="delete-img" data-id="{{$loyalty_app_setting_image->id}}">X</span>
                           </div>
                        </div>
                     @endif

                  </div>
                  @endif
                  <div class="row mt-3">
                     <div class="col-md-3">
                        <div class="input_section">

                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                                 <div class="selectThumbnail">
                                    <span class="btn btn-just-icon btn-round btn-file">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <span class="fileinput-exists">Change</span>
                                       <input type="file" name="loyalty_side_menu_image" id="loyalty_side_menu_image" class="form-control" accept="image/png, image/gif, image/jpeg">
                                    </span>
                                    <br>
                                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                                 </div>
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>

                           </div>
                           <label for="warranty_activation_attach" class="bmd-label-floating">Upload Loyalty Side Menu Image</label>
                        </div>






                        @if ($errors->has('loyalty_side_menu_image'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('loyalty_side_menu_image') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  @if(isset($loyalty_app_setting) && $loyalty_app_setting->getMedia('gift_slider_image')->count() > 0 && Storage::disk('s3')->exists($loyalty_app_setting->getMedia('gift_slider_image')[0]->getPath()))

                  <div class="row mt-4">
                     <div class="col-md-12">
                        <label class="bmd-label-floating mt-2">Gift Slider Images</label>
                     </div>

                     @foreach($loyalty_app_setting->getMedia('gift_slider_image') as $loyalty_app_setting_image)
                     <div class="col-md-3">
                        <div class="img-div">
                           <img style="box-shadow: 0 0 15px #000;" class="rounded" width="100%" src="{{$loyalty_app_setting_image->getFullUrl()}}" alt=""><span title="Delete Image" class="delete-img" data-id="{{$loyalty_app_setting_image->id}}">X</span>
                        </div>
                     </div>
                     @endforeach
                  </div>
                  @endif
                  <div class="row mt-3">
                     <div class="col-md-3">
                        <div class="input_section">

                           <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                              <div class="fileinput-new thumbnail">
                                 <img src="{!! asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                                 <div class="selectThumbnail">
                                    <span class="btn btn-just-icon btn-round btn-file">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <span class="fileinput-exists">Change</span>
                                       <input type="file" name="gift_images[]" multiple id="gift_images" class="form-control" accept="image/png, image/gif, image/jpeg">
                                    </span>
                                    <br>
                                    <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                                 </div>
                              </div>
                              <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>

                           </div>
                           <label for="warranty_activation_attach" class="bmd-label-floating">Upload Gift Silder Images</label>
                        </div>






                        @if ($errors->has('gift_images'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('gift_images') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="input_section">
                           <label class="col-form-label">Select Customer Type </label>
                           <select name="customer_types[]" multiple id="customer_types" class="select2 form-control" required>
                              <option value="">Select Customer Type</option>
                              @if($customer_types)
                              @foreach($customer_types as $customer_type)
                              <option value="{{$customer_type->id}}" {{ in_array($customer_type->id, old('customer_types', explode(',', $loyalty_app_setting->customer_types))) ? 'selected' : '' }}> {{$customer_type->customertype_name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('customer_types'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('customer_types') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="input_section">

                           <label class="col-form-label">App Version </label>

                           <input type="number" name="app_version" placeholder="1.01" step="0.01" class="form-control" id="app_version" value="{{old('app_version', $loyalty_app_setting['app_version'])}}" required>
                           @if ($errors->has('app_version'))
                           <div class="error ">
                              <p class="text-danger">{{ $errors->first('app_version') }}</p>
                           </div>
                           @endif

                        </div>
                     </div>

                  </div>
                  <div class="row mt-5">
                     <div class="col-md-6">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="bmd-label-floating">Upload Product Catalogue </label>
                           </div>
                           <div class="col-md-8">
                              <input type="file" name="product_catalogue" accept="application/pdf" id="product_catalogue" class="form-control">
                              <input type="hidden" name="id" id="id" class="form-control" value="{!! old( 'id', $loyalty_app_setting?$loyalty_app_setting['id']:'') !!}">
                              @if ($errors->has('product_catalogue'))
                              <div class="error ">
                                 <p class="text-danger">{{ $errors->first('product_catalogue') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        @if(isset($loyalty_app_setting) && $loyalty_app_setting->getMedia('product_catalogue')->count() > 0 && Storage::disk('s3')->exists($loyalty_app_setting->getMedia('product_catalogue')[0]->getPath()))

                        <div class="row image_preview p-4 mt-2 m-2">
                           <label class="bmd-label-floating">Product Catalogue</label>
                           <iframe src="{{ $loyalty_app_setting->getMedia('product_catalogue')[0]->getFullUrl() }}" width="100%" height="400px" frameborder="0"></iframe>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="bmd-label-floating">Upload Scheme Catalogue </label>
                           </div>
                           <div class="col-md-8">
                              <input type="file" name="scheme_catalogue" accept="application/pdf" id="scheme_catalogue" class="form-control">
                              @if ($errors->has('scheme_catalogue'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('scheme_catalogue') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        @if(isset($loyalty_app_setting) && $loyalty_app_setting->getMedia('scheme_catalogue')->count() > 0 && Storage::disk('s3')->exists($loyalty_app_setting->getMedia('scheme_catalogue')[0]->getPath()))

                        <div class="row image_preview p-4 mt-2 m-2">
                           <label class="bmd-label-floating">Scheme Catalogue</label>
                           <iframe src="{{ $loyalty_app_setting->getMedia('scheme_catalogue')[0]->getFullUrl() }}" width="100%" height="400px" frameborder="0"></iframe>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 mt-4">
                        <div class="row">
                           <div class="col-md-4">
                              <label class="bmd-label-floating">Upload Terms & Condition </label>
                           </div>
                           <div class="col-md-8">
                              <input type="file" name="terms_condition" accept="application/pdf" id="terms_condition" class="form-control">
                              @if ($errors->has('terms_condition'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('terms_condition') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                        @if(isset($loyalty_app_setting) && $loyalty_app_setting->getMedia('terms_condition')->count() > 0 && Storage::disk('s3')->exists($loyalty_app_setting->getMedia('terms_condition')[0]->getPath()))
                        <h3>Terms & Condition</h3>
                        <div class="row image_preview p-4 m-4">
                           <iframe src="{{ $loyalty_app_setting->getMedia('terms_condition')[0]->getFullUrl() }}" width="100%" height="400px" frameborder="0"></iframe>
                        </div>
                        @endif
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
      <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
      <script>
         $('body').on('click', '.delete-img', function() {
            var deleteButton = $(this);
            var id = $(this).data("id");
            var token = $("meta[name='csrf-token']").attr("content");
            if (!confirm("Are You sure want to delete ?")) {
               return false;
            }
            $.ajax({
               url: "{{ url('loyalty-app-setting') }}" + '/' + id,
               type: 'DELETE',
               data: {
                  _token: token,
                  id: id
               },
               success: function(data) {
                  $('.message').empty();
                  $('.alert').show();
                  if (data.status == 'success') {
                     deleteButton.parent('div').addClass('d-none');
                     $('.alert').addClass("alert-success");
                  } else {
                     $('.alert').addClass("alert-danger");
                  }
                  $('.message').append(data.message);
               },
            });
         });
      </script>
</x-app-layout>