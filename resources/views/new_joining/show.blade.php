<x-app-layout>
  <style>
    img.card-img-top {
      width: 340px;
      height: 300px;
    }

    .row.head {
      color: #fff;
      text-shadow: 0px 1px 4px #000;
    }

    .card-body.main-card:before {
      content: ' ';
      width: 105%;
      height: 134px;
      background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      top: -24px;
      position: absolute;
      left: -24px;
      border-radius: 10px;
      box-shadow: 0px 8px 12px 0px grey;
    }

    .row.head.mb-4:after {
      content: ' ';
      width: 20px;
      height: 20px;
     background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);
      top: 98px;
      position: absolute;
      left: 0px;
      transform: rotate(-45deg);
    }

    .fr-hr {
      background: #000;
      width: 102%;
      margin-left: -15px;
      height: 2px;
      border-radius: 10px;
    }

    .card.new_text p {
    color: #000;
}
  </style>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
      </div>
    </div>
  </div>

  <section class="content">
    <div class="row">
      <div class="col-1"></div>
      <div class="col-10">

        @if(Session::has('success'))
        <div class="alert alert-success" id="hide_div">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('success') !!}</strong>
        </div>
        @endif

        @if(Session::has('danger'))
        <div class="alert alert-danger" id="hide_danger">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('danger') !!}</strong>
        </div>
        @endif


        <div class="alert" style="display: none;" id="hide_check">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <strong class="message"></strong>
        </div>



        <div class="card new_text">
          <div class="card-body main-card">
            <div class="row head mb-4">
              <div class="col-md-4 col-sm-6">
                <h6>Email : {{$newJoining->email}}</h6>
                <h6>Mobile Number : {{$newJoining->mobile_number}}</h6>
                <h6>Emergency Contact Number : {{$newJoining->contact_number}}</h6>
              </div>
              <div class="col-md-4 col-sm-6 text-center">
                <h4 style="font-weight: 800; font-size:25px;font-family: cursive;">{{$newJoining->first_name}} {{$newJoining->middle_name}} {{$newJoining->last_name}}</h4>
              </div>
              <div class="col-md-4 col-sm-6 text-right">
                <h6>Branch : {{$newJoining->branch_details->branch_name}}</h6>
                <h6>Department : {{$newJoining->department_details->name}}</h6>
                <h6>Designation : {{$newJoining->designation_details->designation_name}}</h6>
              </div>
            </div>

            <h6 style="text-decoration: underline;">Personal Details :</h6>
            <div class="row">
              <div class="col-md-4">
                <p><b>Date of Birth</b> : {{$newJoining->dob?date('d M Y', strtotime($newJoining->dob)):''}}</p>
                <p><b>Gender</b> : {{$newJoining->gender}}</p>
                <p><b>Blood Group</b> : {{$newJoining->blood_group}}</p>
                <p><b>Marital Status</b> : {{$newJoining->marital_status}}</p>
              </div>
              <div class="col-md-4">
                <p><b>Father Name</b> : {{$newJoining->father_name}}</p>
                <p><b>Father Occupation</b> : {{$newJoining->father_occupation}}</p>
                <p><b>Mother Name</b> : {{$newJoining->mother_name}}</p>
                <p><b>Mother Occupation</b> : {{$newJoining->mother_occupation}}</p>
              </div>
              <div class="col-md-4">
                <p><b>Spouse's Name</b> : {{$newJoining->spouse_name}}</p>
                <p><b>Spouse's DOB</b> : {{$newJoining->spouse_dob?date('d M Y', strtotime($newJoining->spouse_dob)):''}}</p>
                <p><b>Spouse's Education</b> : {{$newJoining->spouse_education}}</p>
                <p><b>Spouse's Occupation</b> : {{$newJoining->spouse_occupation}}</p>
              </div>
            </div>

            <hr>

            <h6 style="text-decoration: underline;">Address Details :</h6>
            <div class="row">
              <div class="col-md-6" style="border-right: 1px solid;">
                <p><b>Present Address</b></p>
                <div class="row">
                  <div class="col-md-6">
                    <p><b>Address</b> : {{$newJoining->present_address}}</p>
                    <p><b>City</b> : {{$newJoining->present_city}}</p>
                  </div>
                  <div class="col-md-6">
                    <p><b>State</b> : {{$newJoining->present_state}}</p>
                    <p><b>Pincode</b> : {{$newJoining->present_pincode}}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <p><b>Permanent Address</b></p>
                <div class="row">
                  <div class="col-md-6">
                    <p><b>Address</b> : {{$newJoining->permanent_address}}</p>
                    <p><b>City</b> : {{$newJoining->permanent_city}}</p>
                  </div>
                  <div class="col-md-6">
                    <p><b>State</b> : {{$newJoining->permanent_state}}</p>
                    <p><b>Pincode</b> : {{$newJoining->permanent_pincode}}</p>
                  </div>
                </div>
              </div>
            </div>

            <hr>

            <h6 style="text-decoration: underline;">Other Details :</h6>
            <div class="row">
              <div class="col-md-6">
                <p><b>Pan Number</b> : {{$newJoining->pan}}</p>
                <p><b>Adhar Number</b> : {{$newJoining->aadhar}}</p>
                <p><b>Driving Licence Number</b> : {{$newJoining->driving_licence}}</p>
                <p><b>qualification</b> : {{$newJoining->qualification}}</p>
              </div>
              <div class="col-md-6">
                @php $langs = json_decode($newJoining->language) @endphp
                
                @foreach($langs as $k=>$lang)
                @php
                if (is_array($lang)) {
                $mappedArray = array_map(function($item) {
                if ($item == 's') {
                return 'Speak';
                } elseif ($item == 'w') {
                return 'Write';
                } elseif ($item == 'r') {
                return 'Read';
                } else {
                return '';
                }
                }, $lang);
                }else{
                  $mappedArray = [];
                }
                @endphp
                <p><b>{{ucfirst($k)}}</b> : {{count($mappedArray) > 0?implode(', ', $mappedArray):' Nothing'}} </p>
                @endforeach
              </div>
            </div>

            <hr>
            <h6 style="text-decoration: underline;">Attachments :</h6>
            <div class="row">
              @if($newJoining->exists && $newJoining->getMedia('adhar_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('adhar_images')->getPath()))
              @foreach($newJoining->getMedia('adhar_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img class="card-img-top" src="{{$media->getFullUrl()}}" alt="Adhar Card">
                <div class="card-body">
                  <h5 class="card-title">Adhar Card</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" data-types="Adhar" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('pan_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('pan_images')->getPath()))
              @foreach($newJoining->getMedia('pan_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="PAN Card">
                <div class="card-body">
                  <h5 class="card-title">PAN Card</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('passport_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('passport_images')->getPath()))
              @foreach($newJoining->getMedia('passport_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="Passport">
                <div class="card-body">
                  <h5 class="card-title">Passport</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('ssc_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('ssc_images')->getPath()))
              @foreach($newJoining->getMedia('ssc_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="SSC Image">
                <div class="card-body">
                  <h5 class="card-title">SSC Image</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('hsc_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('hsc_images')->getPath()))
              @foreach($newJoining->getMedia('hsc_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="HSC Image">
                <div class="card-body">
                  <h5 class="card-title">HSC Image</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('graduation_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('graduation_images')->getPath()))
              @foreach($newJoining->getMedia('graduation_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="Graduation">
                <div class="card-body">
                  <h5 class="card-title">Graduation</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('birth_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('birth_images')->getPath()))
              @foreach($newJoining->getMedia('birth_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="50" class="card-img-top" src="{{$media->getFullUrl()}}" alt="Birth Certificate">
                <div class="card-body">
                  <h5 class="card-title">Birth Certificate</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('relieving_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('relieving_images')->getPath()))
              @foreach($newJoining->getMedia('relieving_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="Experience Certificate cum Relieving Latter">
                <div class="card-body">
                  <h5 class="card-title">Experience Certificate cum Relieving Latter</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('last_salray_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('last_salray_images')->getPath()))
              @foreach($newJoining->getMedia('last_salray_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="Last Salary Slip">
                <div class="card-body">
                  <h5 class="card-title">Last Salary Slip</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('bank_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('bank_images')->getPath()))
              @foreach($newJoining->getMedia('bank_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="Bank Details">
                <div class="card-body">
                  <h5 class="card-title">Bank Details</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif

              @if($newJoining->exists && $newJoining->getMedia('offer_images')->count() > 0 && Storage::disk('s3')->exists($newJoining->getFirstMedia('offer_images')->getPath()))
              @foreach($newJoining->getMedia('offer_images') as $k=>$media)
              <div class="card col-md-5 ml-5">
                <img width="150" class="card-img-top" src="{{$media->getFullUrl()}}" alt="Silver Offer">
                <div class="card-body">
                  <h5 class="card-title">Silver Offer</h5>
                  <a href="{{$media->getFullUrl()}}" download class="btn btn-info">Download</a>
                  <button  data-imgurl="{{$media->getFullUrl()}}" class="btn btn-dark prnt-btns">Print</button>
                </div>
              </div>
              @endforeach
              @endif
            </div>

          </div>

        </div>
        <div class="col-1"></div>


        <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

        <script src="{{ url('/').'/'.asset('lightboxx/js/lightbox-plus-jquery.min.js') }}"></script>

        <script>
          $(document).on('click', '.prnt-btns', function(){
            var image = $(this).data('imgurl');
            var imageWindow = window.open('', '_blank');
            imageWindow.document.write('<html><head><title>Print</title></head><body><img src="' + image + '"></body></html>');
            imageWindow.document.close();
            imageWindow.print();
          });
        </script>

</x-app-layout>