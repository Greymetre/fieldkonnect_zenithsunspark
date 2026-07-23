<x-app-layout>
  <style>
    input.form-check-input {
      border: 2px solid !important;
      width: 20px !important;
      height: 20px !important;
      opacity: 1 !important;
      z-index: 1 !important;
    }

    /* Landscape print layout */
    @media print {
      @page {
        size: landscape;
      }
    }

    /* input.form-control:read-only {
      border-bottom: 1px solid #000 !important;
    }*/

    p.attach-p:before {
      content: " ";
      position: absolute;
      width: 100px;
      height: 5px;
      top: 24px;
      background: radial-gradient(#00aadb, transparent);
      left: 74px;
    }

    p.attach-p {
      font-weight: 900;
      font-family: cursive;
      position: relative;
    }

    .col-md-3.mb-3.ml-5.text-center.border.rounded {
      transition: transform 0.5s, box-shadow 0.5s;
    }

    .col-md-3.mb-3.ml-5.text-center.border.rounded:hover {
      transform: scale(1.2);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .inp-div {
      position: relative;
      display: flex;
      align-items: center;
      background: #ebe7e7;
      border-radius: 5px;
      padding: 15px;
    }

    .inp-div input {
      position: absolute;
      top: 0;
      width: 100%;
      height: 100%;
      opacity: 0 !important;
      cursor: pointer !important;
    }

    .inp-div p {
      font-size: 14px;
    }

    .inp-div i:first-child {
      font-size: 35px !important;
    }

    .profile-pic-container {
      position: absolute;
      width: 200px;
      height: 200px;
    }

    .profile-pic {
      width: 100%;
      height: 100%;
      border-radius: 5%;
      overflow: hidden;
      border: 2px solid #ccc;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .profile-pic img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    input[type="file"] {
      position: absolute;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
      top: 0;
      left: 0;
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

        <div id="print-section">

          <h2 class="text-center">Dealer/Distributor Data Sheet</h2>
          <p>(All information furnished by you will be treated as strictly confidential)</p>
          <form action="{{route('dealer-appointment-form.update', $dealerAppointment)}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row mt-3">
              <div class="col-md-3 content-frm bg-light">
                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="branch">Branch </label>
                  </div>
                  <div class="col-md-9">
                    <div class="input_section">
                      <select class="form-select select2" name="branch" id="branch" required>
                        <option value="" disabled selected>Your answer</option>
                        @if($branchs && count($branchs) > 0)
                        @foreach($branchs as $branch)
                        <option value="{{$branch->id}}" {{($dealerAppointment->branch == $branch->id)?'selected':''}}>{{$branch->branch_name}}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 content-frm bg-light">
                <div class="form-group row">
                  <div class="col-md-4">
                    <label for="branch">User(Created By) </label>
                  </div>
                  <div class="col-md-8">
                    <div class="input_section">
                      <select class="select2" name="created_by" id="created_by" data-style="select-with-transition" title="Select User">
                        <option value="">Select User</option>
                        @if(count($users) > 0)
                        @foreach($users as $user)
                        <option value="{{$user->id}}" {{($dealerAppointment->created_by == $user->id)?'selected':''}}>{{$user->name}}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="profile-pic-container">
                  <div class="profile-pic">
                    @if($dealerAppointment->exists && $dealerAppointment->getMedia('profile_picture')->count() > 0 && Storage::disk('s3')->exists($dealerAppointment->getMedia('profile_picture')[0]->getPath()))
                    <img id="profileImage" src="{{$dealerAppointment->getMedia('profile_picture')[0]->getFullUrl()}}" alt="Passport Size Profile Picture">
                    @else
                    <img id="profileImage" src="default-profile.png" alt="Passport Size Profile Picture">
                    @endif
                  </div>
                  <input type="file" id="fileInput" name="profile_picture" accept="image/*">
                </div>
              </div>
            </div>
            <div class="row mt-3">
              <div class="col-md-3 content-frm bg-light">
                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="district">District </label>
                  </div>
                  <div class="col-md-9">
                    <div class="input_section">
                      <select class="form-select select2" name="district" id="district" required>
                        <option value="" disabled selected>Your answer</option>
                        @if($districts && count($districts) > 0)
                        @foreach($districts as $district)
                        <option value="{{$district->id}}" {{($dealerAppointment->district == $district->id)?'selected':''}}>{{$district->district_name}}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3 content-frm bg-light">
                <div class="form-group row">
                  <div class="col-md-4">
                    <label for="city">Town / City </label>
                  </div>
                  <div class="col-md-8">
                    <div class="input_section">
                      <select class="form-select select2" name="city" id="city" required>
                        <option value="" disabled selected>Select City</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3 content-frm bg-light">
                <div class="form-group row">
                  <div class="col-md-4">
                    <label for="place">Place </label>
                  </div>
                  <div class="col-md-8">
                    <div class="input_section">
                      <input type="text" name="place" id="place" class="form-control uppercase" value="{{$dealerAppointment->place}}">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-5">
              <div class="col-md-2">
                <label for="appointment_date">Date of Appointment </label>
              </div>
              <div class="col-md-4">
                <input type="date" value="{{$dealerAppointment->appointment_date}}" name="appointment_date" id="appointment_date" class="form-control">
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="PUMPMOTORS"> PUMP & MOTORS </label>
                  <input class="form-check-input" type="radio" value="PUMP&MOTORS" name="division" id="PUMPMOTORS" {{($dealerAppointment->division == 'PUMP&MOTORS')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="FAN&APP"> FAN & APP </label>
                  <input class="form-check-input" type="radio" name="division" value="FAN&APP" id="FAN&APP" {{($dealerAppointment->division == 'FAN&APP')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="AGRI"> AGRI </label>
                  <input class="form-check-input" type="radio" name="division" value="AGRI" id="AGRI" {{($dealerAppointment->division == 'AGRI')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="SOLAR"> SOLAR </label>
                  <input class="form-check-input" type="radio" name="division" value="SOLAR" id="SOLAR" {{($dealerAppointment->division == 'SOLAR')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="LIGHTING"> LIGHTING </label>
                  <input class="form-check-input" type="radio" name="division" id="LIGHTING" value="LIGHTING" {{($dealerAppointment->division == 'LIGHTING')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="SERVECE"> ASC </label>
                  <input required class="form-check-input" type="radio" name="division" id="SERVECE" value="SERVICE" {{($dealerAppointment->division == 'SERVICE')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="Others"> Others </label>
                  <input class="form-check-input" type="radio" name="division" id="Others" value="Others" {{($dealerAppointment->division == 'Others')?'checked':''}}>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-md-10">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="old_user">Are you already working on another division of <b>Silver/Bediya</b>? </label>
                  </div>
                  <div class="col-md-6">
                    <select class="form-select select2" name="old_user" id="old_user" required>
                      <option value="" disabled selected>Your Answer</option>
                      <option value="Yes" {{$dealerAppointment->old_user == 'Yes' ? 'selected' : ''}}>Yes</option>
                      <option value="No" {{$dealerAppointment->old_user == 'No' ? 'selected' : ''}}>No</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-4 d-none" id="asc-div">
              <div class="col-md-10">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label for="asc_divi">Please select divisions </label>
                  </div>
                  <div class="col-md-6">
                    <select class="form-select select2" name="asc_divi[]" multiple id="asc_divi">
                      <option value="PUMP&MOTORS" {{in_array('PUMP&MOTORS', explode(',',$dealerAppointment->asc_divi)) ? 'selected' : ''}}>PUMP & MOTORS</option>
                      <option value="FAN&APP" {{in_array('FAN&APP', explode(',',$dealerAppointment->asc_divi)) ? 'selected' : ''}}>FAN & APP</option>
                      <option value="AGRI" {{in_array('AGRI', explode(',',$dealerAppointment->asc_divi)) ? 'selected' : ''}}>AGRI</option>
                      <option value="SOLAR" {{in_array('SOLAR', explode(',',$dealerAppointment->asc_divi)) ? 'selected' : ''}}>SOLAR</option>
                      <option value="LIGHTING" {{in_array('LIGHTING', explode(',',$dealerAppointment->asc_divi)) ? 'selected' : ''}}>LIGHTING</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-4 if_old d-none">
              <div class="col-md-3">
                <div class="form-group row">
                  <div class="col-md-5">
                    <label for="old_division">Select devision </label>
                  </div>
                  <div class="col-md-7">
                    <select class="form-select select2" name="old_division" id="old_division">
                      <option value="" disabled selected>Your Answer</option>
                      <option value="PUMP&MOTORS" {{$dealerAppointment->old_division == 'PUMP&MOTORS' ? 'selected' : ''}}>PUMP & MOTORS</option>
                      <option value="FAN&APP" {{$dealerAppointment->old_division == 'FAN&APP' ? 'selected' : ''}}>FAN & APP</option>
                      <option value="AGRI" {{$dealerAppointment->old_division == 'AGRI' ? 'selected' : ''}}>AGRI</option>
                      <option value="SOLAR" {{$dealerAppointment->old_division == 'SOLAR' ? 'selected' : ''}}>SOLAR</option>
                      <option value="LIGHTING" {{$dealerAppointment->old_division == 'LIGHTING' ? 'selected' : ''}}>LIGHTING</option>
                      <option value="Others" {{$dealerAppointment->old_division == 'Others' ? 'selected' : ''}}>Others</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group row">
                  <div class="col-md-4">
                    <label for="old_firm_name">Firm Name / Sister Concern </label>
                  </div>
                  <div class="col-md-8">
                    <input type="text" name="old_firm_name" id="old_firm_name" value="{{old('old_firm_name', $dealerAppointment->old_firm_name)}}" class="form-control uppercase">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group row">
                  <div class="col-md-3">
                    <label for="old_gst">GST Number </label>
                  </div>
                  <div class="col-md-9">
                    <input type="text" name="old_gst" id="old_gst" value="{{old('old_gst', $dealerAppointment->old_gst)}}" class="form-control uppercase">
                  </div>
                </div>
              </div>
            </div>

            <div class="row mt-4">
              <div class="col-md-3">
                <div class="form-check">
                  <label class="form-check-label" for="distributor"> Distributor </label>
                  <input class="form-check-input mr-3" type="radio" name="customertype" value="distributor" id="distributor" {{($dealerAppointment->customertype == 'distributor')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-check">
                  <label class="form-check-label" for="dealer"> Dealer </label>
                  <input class="form-check-input mr-3" type="radio" name="customertype" value="dealer" id="dealer" {{($dealerAppointment->customertype == 'dealer')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-check">
                  <label class="form-check-label" for="shopee"> Shopee </label>
                  <input class="form-check-input mr-3" type="radio" name="customertype" value="shopee" id="shopee" {{($dealerAppointment->customertype == 'shopee')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-check">
                  <label class="form-check-label" for="server center"> Service Center </label>
                  <input class="form-check-input mr-3" type="radio" name="customertype" value="server center" id="servercenter" {{($dealerAppointment->customertype == 'server center')?'checked':''}}>
                </div>
              </div>
            </div>

            <div class="row mt-4 d-none" id="parent-div">
              <div class="col-md-4">
                <select name="parent_id" id="parent_id" class="select2 form-control"></select>
              </div>
            </div>

            <h5 class="mt-5">SECURITY DEPOSIT:</h5>

            <div class="row mt-4">
              <div class="col-md-4">
                <div class="form-group row">
                  <div class="col-md-6">
                    <label> PUMP & MOTORS </label>
                  </div>
                  <div class="col-md-6">
                    <input class="form-check-input ml-3" type="radio" name="security_deposit" value="10000" {{($dealerAppointment->security_deposit == '10000')?'checked':''}}>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group row">
                  <div class="col-md-2">
                    <label> F&A </label>
                  </div>
                  <div class="col-md-6">
                    <input class="form-check-input ml-3" type="radio" name="security_deposit" value="5000" {{($dealerAppointment->security_deposit == '5000')?'checked':''}}>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group row">
                  <div class="col-md-2">
                    <label> AGRI </label>
                  </div>
                  <div class="col-md-6">
                    <input class="form-check-input ml-3" type="radio" name="security_deposit" value="100000" {{($dealerAppointment->security_deposit == '100000')?'checked':''}}>
                  </div>
                </div>
              </div>
            </div>

            <h5 class="mt-5">GST DETAILS:</h5>

            <div class="row mt-2">
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="REGULAR"> REGULAR </label>
                  <input class="form-check-input mr-3" type="radio" name="gst_type" value="REGULAR" id="REGULAR" {{($dealerAppointment->gst_type == 'REGULAR')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="Composition"> COMPOSITION </label>
                  <input class="form-check-input mr-3" type="radio" name="gst_type" value="Composition" id="Composition" {{($dealerAppointment->gst_type == 'Composition')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="UNREGD"> UNREGD </label>
                  <input class="form-check-input mr-3" type="radio" name="gst_type" value="UNREGD" id="UNREGD" {{($dealerAppointment->gst_type == 'UNREGD')?'checked':''}}>
                </div>
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-6">
                <div class="form-group row">
                  <div class="col-md-3">
                    <label> GST No. </label>
                  </div>
                  <div class="col-md-8">
                    <input class="form-control mr-3" type="text" name="gst_no" value="{{$dealerAppointment->gst_no}}">
                  </div>
                </div>
              </div>
            </div>

            <h5 class="mt-5">Firm:</h5>

            <div class="row mt-2">
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="Prop"> Proprietorship </label>
                  <input class="form-check-input mr-3" type="radio" name="firm_type" value="Prop" id="Prop" {{($dealerAppointment->firm_type == 'Prop')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="Partnership"> Partnership Firm </label>
                  <input class="form-check-input mr-3" type="radio" name="firm_type" value="Partnership" id="Partnership" {{($dealerAppointment->firm_type == 'Partnership')?'checked':''}}>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-check">
                  <label class="form-check-label" for="LTD"> (P) LTD </label>
                  <input class="form-check-input mr-3" type="radio" name="firm_type" value="LTD" id="LTD" {{($dealerAppointment->firm_type == 'LTD')?'checked':''}}>
                </div>
              </div>
            </div>

            <h5 class="mt-5">GENERAL:</h5>

            <div class="main-details">
              <div class="row box-inputs mt-2">
                <div class="col-md-12 mt-4">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Name of the Company/Firm </label>
                    </div>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="firm_name" value="{{$dealerAppointment->firm_name}}">
                    </div>
                  </div>
                </div>
                <div class="col-md-12 mt-2">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> CIN No in case of Company </label>
                    </div>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="cin_no" value="{{$dealerAppointment->cin_no}}">
                    </div>
                  </div>
                </div>
                <div class="col-md-12 mt-2">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Name of Related Firm in which presently dealing </label>
                    </div>
                    <div class="col-md-8">
                      <input class="form-control" type="text" name="related_firm_name" value="{{$dealerAppointment->related_firm_name}}">
                    </div>
                  </div>
                </div>
                <div class="col-md-12 mt-2">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Line of Business </label>
                    </div>
                    <div class="col-md-8">
                      <textarea name="line_business" class="form-control" id="line_business">{{$dealerAppointment->line_business}}</textarea>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mt-2">
                  <div class="form-group row">
                    <div class="col-md-6">
                      <label> Office Address: </label>
                    </div>
                    <div class="col-md-12">
                      <textarea class="form-control" name="office_address">{{$dealerAppointment->office_address}}</textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Pin: </label>
                    </div>
                    <div class="col-md-9">
                      <input class="form-control" type="text" name="office_pincode" value="{{$dealerAppointment->office_pincode}}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Mobile No.: </label>
                    </div>
                    <div class="col-md-9">
                      <input class="form-control" type="text" name="office_mobile" value="{{$dealerAppointment->office_mobile}}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Email: </label>
                    </div>
                    <div class="col-md-9">
                      <input class="form-control" type="text" name="office_email" value="{{$dealerAppointment->office_email}}">
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mt-2">
                  <div class="form-group row">
                    <div class="col-md-8">
                      <label> Showroom Address / GODOWN: </label>
                    </div>
                    <div class="col-md-12">
                      <textarea class="form-control" name="godown_address">{{$dealerAppointment->godown_address}}</textarea>
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Pin: </label>
                    </div>
                    <div class="col-md-9">
                      <input class="form-control" type="text" name="godown_pincode" value="{{$dealerAppointment->godown_pincode}}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Mobile No.: </label>
                    </div>
                    <div class="col-md-9">
                      <input class="form-control" type="text" name="godown_mobile" value="{{$dealerAppointment->godown_mobile}}">
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Email: </label>
                    </div>
                    <div class="col-md-9">
                      <input class="form-control" type="text" name="godown_email" value="{{$dealerAppointment->godown_email}}">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <h5 class="mt-5">ORGANISATION:</h5>

            <div class="main-details">
              <div class="row box-inputs mt-2">
                <div class="col-md-12 mt-4">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Status </label>
                    </div>
                    <div class="col-md-8">
                      <select name="status" id="status" class="form-control">
                        <option value="" selected>Please Select Status</option>
                        <option value="Proprietor" {{($dealerAppointment->status == 'Proprietor')?'selected':''}}>Proprietor</option>
                        <option value="Partnership" {{($dealerAppointment->status == 'Partnership')?'selected':''}}>Partnership</option>
                        <option value="Private" {{($dealerAppointment->status == 'Private')?'selected':''}}>Private LTD</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-12 mt-2">
                  <table class="table border">
                    <tbody>
                      <tr>
                        <th>#</th>
                        <th>NAME</th>
                        <th>AADHAR No</th>
                        <th>PAN NO</th>
                      </tr>
                      <tr>
                        <th rowspan="4" style="width: 15%;">Name of the Proprietor/Partners/Direct ors (Self attested copy Of AADHAR Card and PAN No to be attached)</th>
                        <td><input value="{{$dealerAppointment->ppd_name_1}}" type="text" name="ppd_name_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_adhar_1}}" type="text" name="ppd_adhar_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_pan_1}}" type="text" name="ppd_pan_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td><input value="{{$dealerAppointment->ppd_name_2}}" type="text" name="ppd_name_2" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_adhar_2}}" type="text" name="ppd_adhar_2" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_pan_2}}" type="text" name="ppd_pan_2" class="form-control"></td>
                      </tr>
                      <tr>
                        <td><input value="{{$dealerAppointment->ppd_name_3}}" type="text" name="ppd_name_3" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_adhar_3}}" type="text" name="ppd_adhar_3" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_pan_3}}" type="text" name="ppd_pan_3" class="form-control"></td>
                      </tr>
                      <tr>
                        <td><input value="{{$dealerAppointment->ppd_name_4}}" type="text" name="ppd_name_4" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_adhar_4}}" type="text" name="ppd_adhar_4" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->ppd_pan_4}}" type="text" name="ppd_pan_4" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>Contact Person / Name</th>
                        <td colspan="3"><input value="{{$dealerAppointment->contact_person_name}}" type="text" name="contact_person_name" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>Mobile No./ E-Mail</th>
                        <td colspan="3"><input value="{{$dealerAppointment->mobile_email}}" type="text" name="mobile_email" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>Name of your Bankers</th>
                        <td colspan="3"><input value="{{$dealerAppointment->bank_name}}" type="text" name="bank_name" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>Address of the Banker</th>
                        <td colspan="3"><input value="{{$dealerAppointment->bank_address}}" type="text" name="bank_address" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>Account Type</th>
                        <td colspan="3">
                          <select name="account_type" id="account_type" class="form-control uppercase">
                            <option value="" disabled selected>Please Select Account Type</option>
                            <option value="Current Account" {{$dealerAppointment->account_type == 'Current Account'?'selected':''}}>Current Account</option>
                            <option value="CC Account" {{$dealerAppointment->account_type == 'CC Account'?'selected':''}}>CC Account</option>
                            <option value="OD Account" {{$dealerAppointment->account_type == 'OD Account'?'selected':''}}>OD Account</option>
                            <option value="Saving Accounts" {{$dealerAppointment->account_type == 'Saving Accounts'?'selected':''}}>Saving Accounts</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <th>Account No.</th>
                        <td colspan="3"><input value="{{$dealerAppointment->account_number}}" type="text" name="account_number" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>IFSC CODE</th>
                        <td colspan="3"><input value="{{$dealerAppointment->ifsc_code}}" type="text" name="ifsc_code" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>Payment terms</th>
                        <td colspan="3">
                          <select name="payment_term" id="payment_term" class="form-control uppercase">
                            <option value="">Please Select Payment Term</option>
                            <option value="Direct" {{$dealerAppointment->payment_term == 'Direct' ? 'selected' : ''}}>Direct</option>
                            <option value="against" {{$dealerAppointment->payment_term == 'against' ? 'selected' : ''}}>against</option>
                            <option value="Advance" {{$dealerAppointment->payment_term == 'Advance' ? 'selected' : ''}}>Advance</option>
                            <option value="PDC" {{$dealerAppointment->payment_term == 'PDC' ? 'selected' : ''}}>PDC</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <th>Maximum Credit period</th>
                        <td colspan="3"><input value="{{$dealerAppointment->credit_period}}" type="text" name="credit_period" class="form-control"></td>
                      </tr>
                      <tr>
                        <th colspan="3">Whether two (2) Cheque (s) have been collected – MCL CHEQUES (Nationalize) <span class="text-info">*(To Be filled at HO)</span></th>
                        <td>
                          <select name="payment_term_bm" id="payment_term_bm" class="form-control">
                            <option value="" selected>Please Select</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <th class="text-center" colspan="4">Give the Cheque details- (PLS SEND THE TWO CHEQUE (S) TO HO)</th>
                      </tr>
                      <tr>
                        <table class="table">
                          <thead>
                            <tr>
                              <th>S.No.</th>
                              <th>Cheque No.</th>
                              <th>Account Number</th>
                              <th>Banker’s Name & Address</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td><input value="{{$dealerAppointment->cheque_no_1}}" type="text" name="cheque_no_1" class="form-control"></td>
                              <td><input value="{{$dealerAppointment->cheque_account_number_1}}" type="text" name="cheque_account_number_1" class="form-control"></td>
                              <td><input value="{{$dealerAppointment->cheque_bank_1}}" type="text" name="cheque_bank_1" class="form-control"></td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td><input value="{{$dealerAppointment->cheque_no_2}}" type="text" name="cheque_no_2" class="form-control"></td>
                              <td><input value="{{$dealerAppointment->cheque_account_number_2}}" type="text" name="cheque_account_number_2" class="form-control"></td>
                              <td><input value="{{$dealerAppointment->cheque_bank_2}}" type="text" name="cheque_bank_2" class="form-control"></td>
                            </tr>
                          </tbody>
                        </table>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <h5 class="mt-5">ACTIVITES:</h5>

            <div class="main-details">
              <div class="row box-inputs mt-2">
                <div class="col-md-12 mt-4">
                  <p class="text-center">Please give details of your present business (mention manufacturer’s name)</p>
                </div>
                <div class="col-md-12 mt-4">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Company Name</th>
                        <th style="width: 300px;">PRODUCT</th>
                        <th>Nature Of Business (Dealer/Distributor/Stockiest)</th>
                        <th>Annual Turn Over</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><input value="{{$dealerAppointment->manufacture_company_1}}" type="text" name="manufacture_company_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->manufacture_product_1}}" type="text" name="manufacture_product_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->manufacture_business_1}}" type="text" name="manufacture_business_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->manufacture_turn_over_1}}" type="text" name="manufacture_turn_over_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td><input value="{{$dealerAppointment->manufacture_company_2}}" type="text" name="manufacture_company_2" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->manufacture_product_2}}" type="text" name="manufacture_product_2" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->manufacture_business_2}}" type="text" name="manufacture_business_2" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->manufacture_turn_over_2}}" type="text" name="manufacture_turn_over_2" class="form-control"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-12 mt-2">
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label> Present Annual Turnover </label>
                    </div>
                    <div class="col-md-8">
                      <input value="{{$dealerAppointment->present_annual_turnover}}" class="form-control" type="text" name="present_annual_turnover" value="">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <h5 class="mt-5">ANTICIPATED BUSINESS:</h5>

            <div class="main-details">
              <div class="row box-inputs mt-2">
                <div class="col-md-12 mt-4">
                  <p class="text-center">Division for which you are interested & Anticipated Turnover for Ensuing FY (All Figures in Lacs)</p>
                </div>
                <div class="col-md-12 mt-4">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Products</th>
                        <th>Anticipated Business in Ensuing Full Year</th>
                        <th>Next Year</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>MOTROS</td>
                        <td><input value="{{$dealerAppointment->motor_anticipated_business_1}}" type="text" name="motor_anticipated_business_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->motor_next_year_business_1}}" type="text" name="motor_next_year_business_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td>PUMP</td>
                        <td><input value="{{$dealerAppointment->motor_next_year_business_1}}" type="text" name="pump_anticipated_business_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->motor_next_year_business_1}}" type="text" name="pump_next_year_business_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td>FAN & APP</td>
                        <td><input value="{{$dealerAppointment['F&A_anticipated_business_1']}}" type="text" name="F&A_anticipated_business_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment['F&A_next_year_business_1']}}" type="text" name="F&A_next_year_business_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td>LIGHTING</td>
                        <td><input value="{{$dealerAppointment->lighting_anticipated_business_1}}" type="text" name="lighting_anticipated_business_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->lighting_next_year_business_1}}" type="text" name="lighting_next_year_business_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td>AGRI</td>
                        <td><input value="{{$dealerAppointment->agri_anticipated_business_1}}" type="text" name="agri_anticipated_business_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->agri_next_year_business_1}}" type="text" name="agri_next_year_business_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td>SOLAR – PUMP</td>
                        <td><input value="{{$dealerAppointment->solar_anticipated_business_1}}" type="text" name="solar_anticipated_business_1" class="form-control"></td>
                        <td><input value="{{$dealerAppointment->solar_next_year_business_1}}" type="text" name="solar_next_year_business_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <th>Total</th>
                        <td colspan="2"><input value="{{$dealerAppointment->anticipated_business_total}}" type="text" name="anticipated_business_total" class="form-control"></td>
                      </tr>
                      <tr>
                        <th colspan="3"><b>Note: Please sign Target sheets for TOD incentives</b></th>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <h5 class="mt-5 all-attachments">Attachments:</h5>
            <div class="border border-dark rounded p-4">
              <div class="row mt-2 mb-2">
                <div class="col-md-4 mb-3">
                  <label for="service_policy">Service Policy</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="service_policy" id="service_policy" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="dealer_policy">Dealer Policy</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="dealer_policy" id="dealer_policy" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="mou_sheet">MOU Sheet</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="mou_sheet" id="mou_sheet" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="mcl_cheque_1">MCL(cheque) 1</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="mcl_cheque_1" id="mcl_cheque_1" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="mcl_cheque_2">MCL(cheque) 2</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="mcl_cheque_2" id="mcl_cheque_2" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="gst_certificate">GST Certificate</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="gst_certificate" id="gst_certificate" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="adhar_card">Adhar Card</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="adhar_card" id="adhar_card" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="pan_card">PAN Card</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="pan_card" id="pan_card" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="bank_statement">6 Month Bank Statement</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="bank_statement" id="bank_statement" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="application_form">Application Form(PDF Only)</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="application_form" id="application_form" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="cancel_cheque">Cancel Cheque / Passbook (PDF Only)</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="cancel_cheque" id="cancel_cheque" class="form-control file-input" accept="application/pdf">
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="shop_image">Shop Image</label>
                  <div class="inp-div">
                    <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                    <p class="m-0">Attach a File</p>
                    <input type="file" name="shop_image" id="shop_image" class="form-control file-input" accept="image/*">
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-5">
              <div class="row all-attachments-div">
                @if($dealerAppointment->exists)
                @php
                $allMediaExceptProfilePictures = $dealerAppointment->getMedia('*')
                ->filter(function ($mediaItem) {
                return $mediaItem->collection_name !== 'profile_picture';
                });
                @endphp
                @if($allMediaExceptProfilePictures->count() > 0)
                @foreach($allMediaExceptProfilePictures as $k=>$media)
                <div class="col-md-3 mb-3 ml-5 text-center border rounded">
                  <p class="attach-p">{{ucfirst(str_replace('_',' ',$media->collection_name))}}</p>
                  <a href="{{$media->getFullUrl()}}" download="" target="_blank">
                    @if(str_contains($media->mime_type, 'image'))
                    <img class="m-2 rounded img-fluid" src="{!! $media->getFullUrl() !!}" style="width: 170px;height:170px;">
                    @else
                    {{ucfirst(str_replace('_','',$media->collection_name))}}.pdf
                    @endif
                  </a>
                </div>
                @endforeach
                @else
                <h6>No Attachment</h6>
                @endif
                @else
                <h6>No Attachment</h6>
                @endif
              </div>
            </div>

            <h5 class="mt-5">Signatures of Dealer:</h5>

            <div class="main-details">
              <div class="row box-inputs mt-2">
                <div class="col-md-12 mt-4">
                  <div class="row">
                    <div class="col-md-6">
                      <p>Dealer Name and Rubber Stamp </p>
                    </div>
                    <div class="col-md-6 text-center">
                      <p>.............................................................. <br> (With Signature) </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="main-details">
              <div class="row box-inputs mt-2">
                <div class="col-md-6 mt-4">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Credit limit (Lacs)</label>
                    </div>
                    <div class="col-md-8">
                      <input readonly type="text" name="credit_limit" disabled class="form-control">
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mt-4">
                  <div class="row">
                    <div class="col-md-4">
                      <label>Credit Rating (in grade)</label>
                    </div>
                    <div class="col-md-8">
                      <input readonly type="text" name="credit_rating" disabled class="form-control">
                    </div>
                  </div>
                </div>
                <span class="text-info">*(To be Filled in by Branch manager)</span>
              </div>
            </div>

            <div class=" mt-5">
              <h5 style="text-decoration: underline;line-height: 5px;">Signatures and Approvals</h5>
              <h6 style="text-decoration: underline;">Important Note.</h6>
              <ol>
                <li>Dealer/Customer has no Financial Interest in the Company apart from the making the purchases of the products of the Company.</li>
                <li>Dealer to submit Order copy on letter head, Recent Photograph along with one Visiting card and photograph of counter</li>
                <li>Any discount / offer / commercial terms etc. shall not be applicable unless communicated to dealer in writing, jointly, at least by the concerned Branch Head and National Head.</li>
                <li>The Company shall not be responsible for any kind of loss or damage suffered by any person resulting out of any unethical / unwarranted acts or omissions etc. of any individual associated with the Company, whether deliberate or otherwise.</li>
                <li>In Case of any dispute the courts at Rajkot along shall have the sole and exclusive jurisdiction.</li>
              </ol>
            </div>

            <div class="mt-5">
              <h6 class="text-center" style="text-decoration: underline;">Declaration: Payment Instructions - Company Bank Account Only</h6>
              <p style="font-size: 12px;">I hope this letter finds you well. I am writing to formally communicate our company's payment policy regarding transactions. We kindly request that all payments to Silver Consumer Electricals Ltd be made exclusively through our designated company bank account. <br><br> In line with our commitment to ensuring transparency, security, and accountability in financial transactions, this policy to safeguard both our organization and our clients. Utilizing only our official company bank account for payments will help us better track and manage transactions, minimize errors, and prevent potential risks associated with cash transactions. <br><br> Kindly ensure that all future payments, including invoices and any other financial transactions, are processed using the provided bank account information. We kindly request your full cooperation in adhering to this payment policy to ensure a smooth and efficient business relationship.</p>
              <br>
              <p style="font-size: 15px;">Company does not entertain any type of cash transactions with any of the Company Representatives. Company is totally against CASH DEALING. If Dealer deals in cash with any Company representatives than he is personally liable for that.</p>
              <br>
              <p style="font-size: 15px;">Thank you for your understanding and cooperation in this matter. We look forward to continuing our positive business association.</p>
            </div>
            <div class="mb-5">
              <h6><b>Channel Partners</b></h6>
            </div>
            <div class="mb-5 mt-5">
              <h5><b>(Sign With Stamp)</b></h5>
            </div>
            <div class="row">
              <div class="col-md-3 text-center"><b>(TM-ASM) </b></div>
              <div class="col-md-3 text-center"><b>Branch Manager </b></div>
              <div class="col-md-3 text-center"><b>Cluster/State Head </b></div>
              <div class="col-md-3 text-center"><b>National Head (HO) </b></div>
            </div>
            <div class="row mt-5">
              <div class="col-md-3 text-center"><b>(SIGN) </b></div>
              <div class="col-md-3 text-center"><b>(SIGN) </b></div>
              <div class="col-md-3 text-center"><b>(SIGN) </b></div>
              <div class="col-md-3 text-center"><b>(SIGN) </b></div>
            </div>



            <div class="row mt-5">
              <div class="col-md-4">
                <button class="btn btn-success"><b>Update</b></button>
              </div>
              <div class="col-md-4"></div>
              <div class="col-md-4" style="text-align: right;">
                <!-- <button type="button" id="printButton" class="btn btn-info">Print Form</button> -->
              </div>
            </div>

            <div class="row mt-5"></div>
          </form>
        </div>

        <div class="col-1"></div>

        <div class="baseurl" data-baseurl="{{ url('/')}}">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
          <script src="https://silver.fieldkonnect.io//public/assets/plugins/select2/js/select2.full.min.js"></script>
          <script>
            $(document).ready(function() {
              $('#district').trigger('change');
            });
            $(document).on('change', '#district', function() {
              var district_id = $(this).val();
              var base_url = $('.baseurl').data('baseurl');
              $.ajax({
                url: base_url + '/getCity',
                dataType: "json",
                type: "GET",
                data: {
                  _token: "{{csrf_token()}}",
                  district_id: district_id
                },
                success: function(res) {
                  var html = '<option value="">Select City</option>';
                  $.each(res, function(index, value) {
                    html += '<option value="' + value.id + '">' + value.city_name + '</option>';
                  });
                  $("#city").html(html);
                  var city_is = '{{$dealerAppointment->city}}';
                  if (city_is != '') {
                    $("#city").val(city_is);
                    $("#city").change();
                  }
                }
              });
            }).trigger('change');
            $('.select2').select2()

            document.getElementById('fileInput').addEventListener('change', function(event) {
              const file = event.target.files[0];
              if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                  document.getElementById('profileImage').src = e.target.result;
                }
                reader.readAsDataURL(file);
              }
            });

            $("#branch").on("change", function() {
              localStorage.removeItem('executive_id');
              var branch_id = $(this).val();
              $.ajax({
                url: "{{ url('getUserList') }}",
                dataType: "json",
                type: "POST",
                data: {
                  _token: "{{csrf_token()}}",
                  branch_id: branch_id
                },
                success: function(res) {
                  var html = '<option value="">Select User</option>';
                  $.each(res, function(k, v) {
                    html += '<option value="' + v.id + '"> (' + v.employee_codes + ') ' + v.name + '</option>';
                  });
                  $("#created_by").html(html);
                }
              });
            });

            $(document).ready(function() {
              $('.file-input').on('change', function() {
                var inpDiv = $(this).closest('.inp-div');
                if (this.files && this.files.length > 0) {
                  inpDiv.css('background-color', '#80ec759e'); // Change to your desired color
                } else {
                  inpDiv.css('background-color', ''); // Reset the background color if no file is selected
                }
              });

              $('input[name="division"]').change(function() {
                var selectedType = $('input[name="customertype"]:checked').val();
                var selectedDivision = $('input[name="division"]:checked').val();
                console.log(selectedType, selectedDivision);

                if (selectedDivision == 'SERVICE') {
                  $('#asc-div').removeClass('d-none');
                } else {
                  $('#asc-div').addClass('d-none');
                }

                if (selectedDivision == 'AGRI' && selectedType == 'dealer') {
                  $("#parent-div").removeClass('d-none');
                } else {
                  $("#parent-div").addClass('d-none');
                }
              }).trigger('change');
            });

            setTimeout(() => {
              var $customerSelect = $('#parent_id').select2({
                placeholder: 'Select Parent',
                allowClear: true,
                ajax: {
                  url: "{{ route('getDealerDisDataSelect') }}",
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
            }, 1500);

            $("#old_user").on("change", function() {
              var if_user = $(this).val();
              console.log(if_user);
              if (if_user == 'Yes') {
                $(".if_old").removeClass('d-none');
                $("#old_division").attr('required', true);
                $("#old_firm_name").attr('required', true);
                $("#old_gst").attr('required', true);
              } else {
                $(".if_old").addClass('d-none');
                $("#old_division").attr('required', false);
                $("#old_firm_name").attr('required', false);
                $("#old_gst").attr('required', false);
              }
            }).trigger("change");
          </script>
</x-app-layout>