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

      i.fa.fa-window-close {
         position: absolute;
         top: 11px;
         right: 26px;
         font-size: 22px;
         color: red;
         cursor: pointer;
         background: #fff;
      }

      iframe {
         position: relative;
      }

      #badge-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px; /* Adds space between badges */
    }

    /* Style for the badges */
    .badge {
         background-color: #17a2b8;
         color: white;
         padding: 6px 12px;
         border-radius: 20px;
         font-size: 10px;
         display: flex;
         align-items: center;
         justify-content: space-between;
      }

    /* Add hover effect on badges */
    .badge:hover {
        background-color: #138496; /* Slightly darker shade of blue */
        cursor: pointer;
    }

    /* Style for the close button */
    .badge .close {
        color: white;
        background: transparent;
        border: none;
        font-size: 16px;
        margin-left: 10px;
        cursor: pointer;
    }

    /* Close button hover effect */
    .badge .close:hover {
        color: #ff4d4f; /* Red color when hovering over the cross button */
    }

    /* Style for the input field */
    .input-group input {
        border-radius: 20px 0 0 20px;
        padding: 10px;
        font-size: 14px;
    }

    /* Style for the "Add" button */
    .input-group-prepend .btn {
        border-radius: 0 20px 20px 0;
        background-color: #28a745;
        color: white;
        font-size: 14px;
    }

    /* Hover effect for Add button */
    .input-group-prepend .btn:hover {
        background-color: #218838;
    }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card p-0 m-0">
            <div class="card-header card-header-tabs m-0 card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        FieldKonnect App Setting
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
            </div>
            @endif
            {!! Form::model($field_konnect_app_setting,[
            'route' => 'field-konnect-app-setting.store',
            'method' => 'POST',
            'id' => 'storeLoyaltyAppSetting',
            'files'=>true
            ]) !!}



            <div class="row">


            </div>
            <hr>

            <div class="card-body">
               <div class="row">

                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">App Version </label>
                        <input type="number" name="app_version" placeholder="1.01" step="0.01" class="form-control" id="app_version" value="{{old('app_version', $field_konnect_app_setting['app_version'])}}" required>
                        @if ($errors->has('app_version'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('app_version') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Order Discount Limit </label>
                        <input type="number" name="order_discount_limit" placeholder="1" step="1" class="form-control" id="order_discount_limit" value="{{old('order_discount_limit', $field_konnect_app_setting['order_discount_limit'])}}" required>
                        @if ($errors->has('order_discount_limit'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('order_discount_limit') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="bmd-label-floating">Upload Product Catalogue </label>
                        <input type="file" name="product_catalogue" accept="application/pdf" id="product_catalogue" class="form-control">
                        <input type="hidden" multiple name="id" id="id" class="form-control" value="{!! old( 'id', $field_konnect_app_setting?$field_konnect_app_setting['id']:'') !!}">
                        @if ($errors->has('product_catalogue'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('product_catalogue') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  @if(isset($field_konnect_app_setting) && $field_konnect_app_setting->getMedia('product_catalogue')->count() > 0 && Storage::disk('s3')->exists($field_konnect_app_setting->getMedia('product_catalogue')[0]->getPath()))

                  <div class="col-md-12">
                     <div class="row mt-3">
                        <div class="col-md-12">
                           <label class="bmd-label-floating">Product Catalogue</label>
                        </div>
                        @foreach($field_konnect_app_setting->getMedia('product_catalogue') as $k=>$media)
                        <div class="col-md-6">

                           <div class="row image_preview p-4 m-2">
                              <iframe src="{{ $media->getFullUrl() }}" width="100%" height="400px" frameborder="0"></iframe>
                              <i class="fa fa-window-close close-btn" data-id="{{$media->id}}" aria-hidden="true"></i>
                           </div>
                        </div>
                        @endforeach
                     </div>
                  </div>
                  @endif
               </div>
               <div class="pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
            </div>
            {{ Form::close() }}
 
            <hr class="mt-5">
            <div class="card-body">
               <div id="success-message"></div>
               <div class="row">
                  <!-- Form Section -->
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">Division </label>
                        <select name="activity_division" id="activity_division" class="form-control selec2">
                           <option value="">Select Division</option>
                           @foreach($divisions as $division)
                           <option value="{{$division->id}}">{{$division->division_name}}</option>
                           @endforeach
                        </select>
                        @if ($errors->has('activity_type'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('activity_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label"style="color: #53697c">Marketing Activity </label>
                         <div class="input-group">
                             <input type="text" name="activity_type" placeholder="Activity Type"  class="form-control" id="activity_type" >
                             <div class="input-group-prepend ml-2">
                                    <button class="btn btn-primary" type="button" id="marketing_type">Add</button>
                              </div>
                         </div>
                         <!-- Static Badges -->
                        </div>
                     </div>
                     <div id="badge-container" class="mt-2">
                 </div>                 
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
      $(document).ready(function(){
         getupdateMarketingType();
      })
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

     function getupdateMarketingType() {
          $.ajax({
              url: "{{ url('getMarketingType') }}",
              method: "GET",
              success: function (res) {
                  if (res.status) {
                      $("#badge-container").html(res.html); // Inject HTML from the response
                  }
              },
              error: function () {
                  alert("Error fetching marketing types.");
              }
          });
      }

      function removeBadge(id) {
         if (!confirm("Are you sure you want to delete this marketing type?")) {
              return;
          }
          $.ajax({
              url: "{{ url('deleteMarketingType') }}", // Make sure this route is defined in Laravel
              method: "POST",
              data: {
                  "id": id,
                  "_token": "{{ csrf_token() }}" // CSRF token for security
              },
              success: function (res) {
                  if (res.status === true) {
                      $('#badge_' + id).remove(); // Remove badge from the UI
                      $('#success-message').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<strong>Success!</strong> Marketing activity removed successfully.' +
                            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                '<span aria-hidden="true">&times;</span>' +
                            '</button>' +
                        '</div>'
                     );
                  }
              },
              error: function () {
                  alert("Something went wrong!");
              }
          });
      }


      // function removeBadge(element, id) {
      //    console.log(element, id);
      //     if (!confirm("Are you sure you want to delete this marketing type?")) {
      //         return;
      //     }

      //     $.ajax({
      //         url: "{{ url('deleteMarketingType') }}",
      //         method: "POST",
      //         data: {
      //             "id": id,
      //             "_token": "{{ csrf_token() }}" // CSRF token for security
      //         },
      //         success: function (res) {
      //             if (res.status) {
      //                 $(element).closest("span").remove(); // Remove badge from UI
      //                 // Show success message
      //                  $('#success-message').html(
      //                      '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
      //                          '<strong>Success!</strong> Marketing activity removed successfully.' +
      //                          '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      //                              '<span aria-hidden="true">&times;</span>' +
      //                          '</button>' +
      //                      '</div>'
      //                  );
      //             } else {
      //                 alert(res.message);
      //             }
      //         },
      //         error: function () {
      //             alert("Error deleting marketing type.");
      //         }
      //     });
      // }



      $('#marketing_type').on('click', function () {
          var type = $('#activity_type').val().trim(); // Trim to remove extra spaces
          var division = $('#activity_division').val();

          if (type !== '' && division !== '') {
              $.ajax({
                  url: "{{ url('addMarketingType') }}",
                  method: "POST", // Assuming it's a POST request
                  data: {
                      "type": type,
                      "activity_division": division,
                      "_token": "{{ csrf_token() }}" // Add CSRF token for security
                  },
                  success: function (res) {
                      if (res.status === true) {
                          getupdateMarketingType(); // Refresh the list
                          
                          // Show success message
                          $('#success-message').html(
                              '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                  '<strong>Success!</strong> Marketing activity added successfully.' +
                                  '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                      '<span aria-hidden="true">&times;</span>' +
                                  '</button>' +
                              '</div>'
                          );

                          // Clear input field
                          $('#activity_type').val('');
                      }
                  },
                  error: function () {
                      $('#success-message').html(
                          '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                              '<strong>Error!</strong> Something went wrong. Please try again.' +
                              '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                                  '<span aria-hidden="true">&times;</span>' +
                              '</button>' +
                          '</div>'
                      );
                  }
              });
          } else {
              alert('Please enter an activity type and division.');
          }
      });

      // 
   </script>
</x-app-layout>