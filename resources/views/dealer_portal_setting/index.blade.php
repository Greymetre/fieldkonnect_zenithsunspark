<x-app-layout>
   <style>
      .image_preview {
         border: 1px solid lightgrey;
         border-radius: 10px;
         width: 100%;
         height: 350px;
         justify-content: center;
         position: relative;
      }

      .img-div {
         position: relative;
      }

      span.delete-img {
         position: absolute;
         top: 0px;
         right: 12px;
         background: #f73232;
         color: #fff;
         border-radius: 50%;
         width: 16px;
         height: 16px;
         text-align: center;
         font-size: 14px;
         line-height: 17px;
         font-weight: 900;
         cursor: pointer;
      }

      i.fa.fa-window-close {
         position: absolute;
         top: -1px;
         right: 2px;
         font-size: 22px;
         color: red;
         cursor: pointer;
      }

      iframe {
         position: relative;
      }

      img.img_view {
    object-fit: contain;
}
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card p-0 m-0">
            <div class="card-header  m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Dealer Portal Setting
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
               {!! Form::model($dealer_portal_setting,[
               'route' => 'delar-portal-setting.store',
               'method' => 'POST',
               'id' => 'storeLoyaltyAppSetting',
               'files'=>true
               ]) !!}


<!-- <div class="card-body">
               <div class="row"> -->

             
                
                  <!-- <div class="col-md-6">
                     <div class="form-group">
                        <div class="row">
                           <div class="col-md-3">
                              <label class="form-control">Slider Heading</label>
                           </div>
                           <div class="col-md-9">
                              <input type="text" class="form-control" name="slider_heading" value="{{old('slider_heading', $dealer_portal_setting->slider_heading)}}">
                           </div>
                        </div>
                     </div>
                  </div> -->
               <!-- </div> -->
               <!-- <hr> -->
               <div class="row mt-4">
     <div class="col-md-6">
                     <div class="input_section">
                              <label class="bmd-label-floating">Show Slider </label>
                              <select name="slider" id="slider" class="select2">
                                 <option value="Y" {{$dealer_portal_setting->slider == 'Y'?'selected':''}}>Yes</option>
                                 <option value="N" {{$dealer_portal_setting->slider == 'N'?'selected':''}}>No</option>
                              </select>
                           </div>
                        </div>
              

                  <div class="col-md-6">
                     <div class="input_section">
               
                           <label class="bmd-label-floating">Upload Slider Images </label>
                    
                           <input type="file" multiple name="dealer_portal_slider_image[]" accept="images/*" id="dealer_portal_slider_image" class="form-control">
                           <input type="hidden" name="id" id="id" class="form-control" value="{!! old( 'id', $dealer_portal_setting?$dealer_portal_setting['id']:'') !!}">
                           @if ($errors->has('dealer_portal_slider_image'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('dealer_portal_slider_image') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     @if(isset($dealer_portal_setting) && $dealer_portal_setting->getMedia('dealer_portal_slider_image')->count() > 0 && Storage::disk('s3')->exists($dealer_portal_setting->getMedia('dealer_portal_slider_image')[0]->getPath()))

               
                                          <div class="col-md-12">

                          <h4 class="bmd-label-floating mt-2">Product Catalogue </h4>
                                          </div>
                        @foreach($dealer_portal_setting->getMedia('dealer_portal_slider_image') as $k=>$media)
                        <div class="col-md-6">
                           
                           <div class="image_preview p-4">
                              <label class="bmd-label-floating">{{$media->name}}</label>
                              <img class="img_view" src="{{ $media->getFullUrl() }}" alt="{{$media->name}}" width="100%" height="100%">
                              <i class="fa fa-window-close close-btn" data-id="{{$media->id}}" title="Delete" aria-hidden="true"></i>
                           </div>
                        </div>
                        @endforeach
                     
    <div class="col-md-12 pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
                     </div>
                     @endif


                  </div>   
              

            
               {{ Form::close() }}
            </div>
               </div>
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
         $('body').on('click', '.close-btn', function() {
            var deleteButton = $(this);
            var id = $(this).data("id");
            var token = $("meta[name='csrf-token']").attr("content");
            if (!confirm("Are You sure want to delete ?")) {
               return false;
            }
            $.ajax({
               url: "{{ url('field-konnect-app-setting') }}" + '/' + id,
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
      // 
   </script>
</x-app-layout>