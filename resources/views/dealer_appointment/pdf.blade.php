<html>

<head>
  <title>Appointment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    p.attach-p {
      font-weight: 900;
      font-family: cursive;
      position: relative;
    }

    p.attach-p:before {
      content: " ";
      position: absolute;
      width: 100px;
      height: 5px;
      top: 24px;
      background: radial-gradient(#00aadb, transparent);
      left: 105px;
    }

    input[type="checkbox"].form-check-input {
      border: 2px solid !important;
      width: 20px !important;
      height: 20px !important;
      opacity: 1 !important;
      float: right !important;
      position: relative !important;
      z-index: 99999 !important;
    }

    .all-attachments-div {
      display: none;
    }

    .all-attachments {
      display: none;
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
  </style>
</head>

<body>
  <div id="print-section">

    <div class="container-fluid">
      <div class="outer-border">
        <div class="middle-border">
          <div class="inner-border">
            <div class="text-center mt-3 content">
              <img src="{{asset('assets/img/dealer_appointment_logo.png')}}" alt="">
              <p style="font-weight: 900;font-family: revert;">SILVER CONSUMER ELECTRICALS LIMITED</p>
            </div>
          </div>
        </div>
      </div>

      <h2 class="text-center">Dealer/Distributor Data Sheet</h2>
      <p>(All information furnished by you will be treated as strictly confidential)</p>
      <div class="row mt-3">
        <div class="col-md-3 content-frm bg-light">
          <div class="form-group row">
            <div class="col-md-3">
              <label for="branch">Branch </label>
            </div>
            <div class="col-md-9">
              <input type="text" class="form-control" disabled value="{{$dealerAppointment->branch_details->branch_name}}">
            </div>
          </div>
        </div>
        <div class="col-md-6 content-frm bg-light">
          <div class="form-group row">
            <div class="col-md-4">
              <label for="branch">User(Created By) </label>
            </div>
            <div class="col-md-6">
              {{$dealerAppointment->createdbyname?$dealerAppointment->createdbyname->name:''}}
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
              <input type="text" class="form-control" disabled value="{{$dealerAppointment->district_details->district_name}}">
            </div>
          </div>
        </div>
        <div class="col-md-3 content-frm bg-light">
          <div class="form-group row">
            <div class="col-md-4">
              <label for="city">Town / City </label>
            </div>
            <div class="col-md-8">
              <input type="text" disabled class="form-control" value="{{$dealerAppointment->city_details->city_name}}">
            </div>
          </div>
        </div>
        <div class="col-md-3 content-frm bg-light">
          <div class="form-group row">
            <div class="col-md-4">
              <label for="place">Place </label>
            </div>
            <div class="col-md-8">
              <input type="text" readonly name="place" id="place" class="form-control uppercase" value="{{$dealerAppointment->place}}">
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-md-2">
          <label for="appointment_date">Date of Appointment </label>
        </div>
        <div class="col-md-4">
          <input type="date" disabled value="{{$dealerAppointment->appointment_date}}" name="appointment_date" id="appointment_date" class="form-control">
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-3">
          <div class="form-check">
            <label class="form-check-label" for="distributor"> Distributor </label>
            <input disabled class="form-check-input mr-3" type="radio" name="customertype" value="distributor" id="distributor" {{($dealerAppointment->customertype == 'distributor')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-check">
            <label class="form-check-label" for="dealer"> Dealer </label>
            <input disabled class="form-check-input mr-3" type="radio" name="customertype" value="dealer" id="dealer" {{($dealerAppointment->customertype == 'dealer')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-check">
            <label class="form-check-label" for="shopee"> Shopee </label>
            <input disabled class="form-check-input mr-3" type="radio" name="customertype" value="shopee" id="shopee" {{($dealerAppointment->customertype == 'shopee')?'checked':''}}>
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
              <select class="form-select select2" disabled name="old_user" id="old_user" required>
                <option value="" disabled selected>Your Answer</option>
                <option value="Yes" {{($dealerAppointment->old_user == 'Yes')?'selected':''}}>Yes</option>
                <option value="No" {{($dealerAppointment->old_user == 'No')?'selected':''}}>No</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      @if($dealerAppointment->old_user == 'Yes')
      <div class="row mt-4 if_old">
        <div class="col-md-3">
          <div class="form-group row">
            <div class="col-md-5">
              <label for="old_division">Select devision </label>
            </div>
            <div class="col-md-7">
              <select class="form-select" disabled name="old_division" id="old_division" required>
                <option value="" disabled selected>Your Answer</option>
                <option value="PUMP&MOTORS" {{($dealerAppointment->old_division == 'PUMPMOTORS')?'selected':''}}>PUMP & MOTORS</option>
                <option value="FAN&APP" {{($dealerAppointment->old_division == 'FAN&APP')?'selected':''}}>FAN & APP</option>
                <option value="AGRI" {{($dealerAppointment->old_division == 'AGRI')?'selected':''}}>AGRI</option>
                <option value="SOLAR" {{($dealerAppointment->old_division == 'SOLAR')?'selected':''}}>SOLAR</option>
                <option value="LIGHTING" {{($dealerAppointment->old_division == 'LIGHTING')?'selected':''}}>LIGHTING</option>
                <option value="Others" {{($dealerAppointment->old_division == 'Others')?'selected':''}}>Others</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="form-group row">
            <div class="col-md-4">
              <label for="old_firm_name">Frim Name / Sister Concern </label>
            </div>
            <div class="col-md-8">
              <input type="text" readonly name="old_firm_name" value="{{old('old_firm_name', $dealerAppointment->old_firm_name)}}" id="old_firm_name" class="form-control">
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group row">
            <div class="col-md-3">
              <label for="old_gst">GST Number </label>
            </div>
            <div class="col-md-9">
              <input type="text" readonly name="old_gst" id="old_gst" value="{{old('old_gst', $dealerAppointment->old_gst)}}" class="form-control">
            </div>
          </div>
        </div>
      </div>
      @endif

      <div class="row mt-4">
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="PUMPMOTORS"> PUMP & MOTORS </label>
            <input disabled class="form-check-input" type="radio" value="PUMP&MOTORS" name="division" id="PUMPMOTORS" {{($dealerAppointment->division == 'PUMP&MOTORS')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="FAN&APP"> FAN & APP </label>
            <input disabled class="form-check-input" type="radio" name="division" value="FAN&APP" id="FAN&APP" {{($dealerAppointment->division == 'FAN&APP')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="AGRI"> AGRI </label>
            <input disabled class="form-check-input" type="radio" name="division" value="AGRI" id="AGRI" {{($dealerAppointment->division == 'AGRI')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="SOLAR"> SOLAR </label>
            <input disabled class="form-check-input" type="radio" name="division" value="SOLAR" id="SOLAR" {{($dealerAppointment->division == 'SOLAR')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="LIGHTING"> LIGHTING </label>
            <input disabled class="form-check-input" type="radio" name="division" id="LIGHTING" value="LIGHTING" {{($dealerAppointment->division == 'LIGHTING')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="Others"> Others </label>
            <input disabled class="form-check-input" type="radio" name="division" id="Others" value="Others" {{($dealerAppointment->division == 'Others')?'checked':''}}>
          </div>
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
              <input class="form-check-input ml-3" type="radio" name="security_deposit" value="10000" disabled {{($dealerAppointment->security_deposit == '10000')?'checked':''}}>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group row">
            <div class="col-md-2">
              <label> F&A </label>
            </div>
            <div class="col-md-6">
              <input class="form-check-input ml-3" type="radio" name="security_deposit&A" value="5000" disabled {{($dealerAppointment->security_deposit == '5000')?'checked':''}}>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group row">
            <div class="col-md-2">
              <label> AGRI </label>
            </div>
            <div class="col-md-6">
              <input class="form-check-input ml-3" type="radio" name="security_deposit" value="100000" disabled {{($dealerAppointment->security_deposit == '100000')?'checked':''}}>
            </div>
          </div>
        </div>
      </div>

      <h5 class="mt-5">GST DETAILS:</h5>

      <div class="row mt-2">
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="REGULAR"> REGULAR </label>
            <input disabled class="form-check-input mr-3" type="radio" name="gst_type" value="REGULAR" id="REGULAR" {{($dealerAppointment->gst_type == 'REGULAR')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="Composition"> COMPOSITION </label>
            <input disabled class="form-check-input mr-3" type="radio" name="gst_type" value="Composition" id="Composition" {{($dealerAppointment->gst_type == 'Composition')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="UNREGD"> UNREGD </label>
            <input disabled class="form-check-input mr-3" type="radio" name="gst_type" value="UNREGD" id="UNREGD" {{($dealerAppointment->gst_type == 'UNREGD')?'checked':''}}>
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
              <input disabled class="form-control mr-3" type="text" name="gst_no" value="{{$dealerAppointment->gst_no}}">
            </div>
          </div>
        </div>
      </div>

      <h5 class="mt-5">Firm:</h5>

      <div class="row mt-2">
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="Prop"> Proprietorship </label>
            <input disabled class="form-check-input mr-3" type="radio" name="firm_type" value="Prop" id="Prop" {{($dealerAppointment->firm_type == 'Prop')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="Partnership"> Partnership Firm </label>
            <input disabled class="form-check-input mr-3" type="radio" name="firm_type" value="Partnership" id="Partnership" {{($dealerAppointment->firm_type == 'Partnership')?'checked':''}}>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-check">
            <label class="form-check-label" for="LTD"> (P) LTD </label>
            <input disabled class="form-check-input mr-3" type="radio" name="firm_type" value="LTD" id="LTD" {{($dealerAppointment->firm_type == 'LTD')?'checked':''}}>
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
                <input disabled class="form-control" type="text" name="firm_name" value="{{$dealerAppointment->firm_name}}">
              </div>
            </div>
          </div>
          <div class="col-md-12 mt-2">
            <div class="form-group row">
              <div class="col-md-3">
                <label> CIN No in case of Company </label>
              </div>
              <div class="col-md-8">
                <input disabled class="form-control" type="text" name="cin_no" value="{{$dealerAppointment->cin_no}}">
              </div>
            </div>
          </div>
          <div class="col-md-12 mt-2">
            <div class="form-group row">
              <div class="col-md-3">
                <label> Name of Related Firm in which presently dealing </label>
              </div>
              <div class="col-md-8">
                <input disabled class="form-control" type="text" name="related_firm_name" value="{{$dealerAppointment->related_firm_name}}">
              </div>
            </div>
          </div>
          <div class="col-md-12 mt-2">
            <div class="form-group row">
              <div class="col-md-3">
                <label> Line of Business </label>
              </div>
              <div class="col-md-8">
                <textarea disabled name="line_business" class="form-control" id="line_business">{{$dealerAppointment->line_business}}</textarea>
              </div>
            </div>
          </div>
          <div class="col-md-6 mt-2">
            <div class="form-group row">
              <div class="col-md-6">
                <label> Office Address: </label>
              </div>
              <div class="col-md-12">
                <textarea class="form-control">{{$dealerAppointment->office_address}}</textarea>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-3">
                <label> Pin: </label>
              </div>
              <div class="col-md-9">
                <input disabled class="form-control" type="text" name="office_pincode" value="{{$dealerAppointment->office_pincode}}">
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-3">
                <label> Mobile No.: </label>
              </div>
              <div class="col-md-9">
                <input disabled class="form-control" type="text" name="office_mobile" value="{{$dealerAppointment->office_mobile}}">
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-3">
                <label> Email: </label>
              </div>
              <div class="col-md-9">
                <input disabled class="form-control" type="text" name="office_email" value="{{$dealerAppointment->office_email}}">
              </div>
            </div>
          </div>
          <div class="col-md-6 mt-2">
            <div class="form-group row">
              <div class="col-md-8">
                <label> Showroom Address / GODOWN: </label>
              </div>
              <div class="col-md-12">
                <textarea class="form-control">{{$dealerAppointment->godown_address}}</textarea>
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-3">
                <label> Pin: </label>
              </div>
              <div class="col-md-9">
                <input disabled class="form-control" type="text" name="godown_pincode" value="{{$dealerAppointment->godown_pincode}}">
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-3">
                <label> Mobile No.: </label>
              </div>
              <div class="col-md-9">
                <input disabled class="form-control" type="text" name="godown_mobile" value="{{$dealerAppointment->godown_mobile}}">
              </div>
            </div>
            <div class="form-group row">
              <div class="col-md-3">
                <label> Email: </label>
              </div>
              <div class="col-md-9">
                <input disabled class="form-control" type="text" name="godown_email" value="{{$dealerAppointment->godown_email}}">
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
                <select disabled name="status" id="status" class="form-control">
                  <option value="" disabled selected>Please Select Status</option>
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
                  <td>{{$dealerAppointment->ppd_name_1}}</td>
                  <td>{{$dealerAppointment->ppd_adhar_1}}</td>
                  <td>{{$dealerAppointment->ppd_pan_1}}</td>
                </tr>
                <tr>
                  <td>{{$dealerAppointment->ppd_name_2}}</td>
                  <td>{{$dealerAppointment->ppd_adhar_2}}</td>
                  <td>{{$dealerAppointment->ppd_pan_2}}</td>
                </tr>
                <tr>
                  <td>{{$dealerAppointment->ppd_name_3}}</td>
                  <td>{{$dealerAppointment->ppd_adhar_3}}</td>
                  <td>{{$dealerAppointment->ppd_pan_3}}</td>
                </tr>
                <tr>
                  <td>{{$dealerAppointment->ppd_name_4}}</td>
                  <td>{{$dealerAppointment->ppd_adhar_4}}</td>
                  <td>{{$dealerAppointment->ppd_pan_4}}</td>
                </tr>
                <tr>
                  <th>Contact Person / Name</th>
                  <td colspan="3"><input disabled value="{{$dealerAppointment->contact_person_name}}" type="text" name="contact_person_name" class="form-control"></td>
                </tr>
                <tr>
                  <th>Mobile No./ E-Mail</th>
                  <td colspan="3"><input disabled value="{{$dealerAppointment->mobile_email}}" type="text" name="mobile_email" class="form-control"></td>
                </tr>
                <tr>
                  <th>Name of your Bankers</th>
                  <td colspan="3"><input disabled value="{{$dealerAppointment->bank_name}}" type="text" name="bank_name" class="form-control"></td>
                </tr>
                <tr>
                  <th>Address of the Banker</th>
                  <td colspan="3"><input disabled value="{{$dealerAppointment->bank_address}}" type="text" name="bank_address" class="form-control"></td>
                </tr>
                <tr>
                  <th>Account Type</th>
                  <td colspan="3">
                    <input disabled value="{{$dealerAppointment->account_type}}" type="text" class="form-control">
                  </td>
                </tr>
                <tr>
                  <th>Account No.</th>
                  <td colspan="3"><input disabled value="{{$dealerAppointment->account_number}}" type="text" name="account_number" class="form-control"></td>
                </tr>
                <tr>
                  <th>IFSC CODE</th>
                  <td colspan="3"><input disabled value="{{$dealerAppointment->ifsc_code}}" type="text" name="ifsc_code" class="form-control"></td>
                </tr>
                <tr>
                  <th>Payment terms</th>
                  <td colspan="3">
                    <input type="text" value="{{$dealerAppointment->payment_term}}" disabled class="form-control">
                  </td>
                </tr>
                <tr>
                  <th>Maximum Credit period</th>
                  <td colspan="3"><input disabled value="{{$dealerAppointment->credit_period}}" type="text" name="credit_period" class="form-control"></td>
                </tr>
                <tr>
                  <th colspan="3">Whether two (2) Cheque (s) have been collected – MCL CHEQUES (Nationalize) <span class="text-info">*(To Be filled at HO)</span></th>
                  <td>
                    <select name="payment_term" disabled id="payment_term" class="form-control">
                      <option value="" disabled selected>Please Select</option>
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
                        <td><input disabled value="{{$dealerAppointment->cheque_no_1}}" type="text" name="cheque_no_1" class="form-control"></td>
                        <td><input disabled value="{{$dealerAppointment->cheque_account_number_1}}" type="text" name="cheque_account_number_1" class="form-control"></td>
                        <td><input disabled value="{{$dealerAppointment->cheque_bank_1}}" type="text" name="cheque_bank_1" class="form-control"></td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td><input disabled value="{{$dealerAppointment->cheque_no_2}}" type="text" name="cheque_no_2" class="form-control"></td>
                        <td><input disabled value="{{$dealerAppointment->cheque_account_number_2}}" type="text" name="cheque_account_number_2" class="form-control"></td>
                        <td><input disabled value="{{$dealerAppointment->cheque_bank_2}}" type="text" name="cheque_bank_2" class="form-control"></td>
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
                  <td><input disabled value="{{$dealerAppointment->manufacture_company_1}}" type="text" name="manufacture_company_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->manufacture_product_1}}" type="text" name="manufacture_product_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->manufacture_business_1}}" type="text" name="manufacture_business_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->manufacture_turn_over_1}}" type="text" name="manufacture_turn_over_1" class="form-control"></td>
                </tr>
                <tr>
                  <td><input disabled value="{{$dealerAppointment->manufacture_company_2}}" type="text" name="manufacture_company_2" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->manufacture_product_2}}" type="text" name="manufacture_product_2" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->manufacture_business_2}}" type="text" name="manufacture_business_2" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->manufacture_turn_over_2}}" type="text" name="manufacture_turn_over_2" class="form-control"></td>
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
                <input disabled value="{{$dealerAppointment->present_annual_turnover}}" class="form-control" type="text" name="present_annual_turnover" value="">
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
                  <td><input disabled value="{{$dealerAppointment->motor_anticipated_business_1}}" type="text" name="motor_anticipated_business_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->motor_next_year_business_1}}" type="text" name="motor_next_year_business_1" class="form-control"></td>
                </tr>
                <tr>
                  <td>PUMP</td>
                  <td><input disabled value="{{$dealerAppointment->motor_next_year_business_1}}" type="text" name="pump_anticipated_business_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->motor_next_year_business_1}}" type="text" name="pump_next_year_business_1" class="form-control"></td>
                </tr>
                <tr>
                  <td>FAN & APP</td>
                  <td><input disabled value="{{$dealerAppointment['F&A_anticipated_business_1']}}" type="text" name="F&A_anticipated_business_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment['F&A_next_year_business_1']}}" type="text" name="F&A_next_year_business_1" class="form-control"></td>
                </tr>
                <tr>
                  <td>LIGHTING</td>
                  <td><input disabled value="{{$dealerAppointment->lighting_anticipated_business_1}}" type="text" name="lighting_anticipated_business_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->lighting_next_year_business_1}}" type="text" name="lighting_next_year_business_1" class="form-control"></td>
                </tr>
                <tr>
                  <td>AGRI</td>
                  <td><input disabled value="{{$dealerAppointment->agri_anticipated_business_1}}" type="text" name="agri_anticipated_business_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->agri_next_year_business_1}}" type="text" name="agri_next_year_business_1" class="form-control"></td>
                </tr>
                <tr>
                  <td>SOLAR – PUMP</td>
                  <td><input disabled value="{{$dealerAppointment->solar_anticipated_business_1}}" type="text" name="solar_anticipated_business_1" class="form-control"></td>
                  <td><input disabled value="{{$dealerAppointment->solar_next_year_business_1}}" type="text" name="solar_next_year_business_1" class="form-control"></td>
                </tr>
                <tr>
                  <th>Total</th>
                  <td colspan="2"><input disabled value="{{$dealerAppointment->anticipated_business_total}}" type="text" name="anticipated_business_total" class="form-control"></td>
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
            @if($media->collection_name == 'shop_image')
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
                <input readonly type="text" name="credit_limit" class="form-control">
              </div>
            </div>
          </div>
          <div class="col-md-6 mt-4">
            <div class="row">
              <div class="col-md-4">
                <label>Credit Rating (in grade)</label>
              </div>
              <div class="col-md-8">
                <input readonly type="text" name="credit_rating" class="form-control">
              </div>
            </div>
          </div>
          <span class="text-info">*(To be Filled in by Branch manager)</span>
        </div>
      </div>

      <div class="row mt-5">
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

      <div class="row mt-5">
        <h6 class="text-center" style="text-decoration: underline;">Declaration: Payment Instructions - Company Bank Account Only</h6>
        <p style="font-size: 12px;">I hope this letter finds you well. I am writing to formally communicate our company's payment policy regarding transactions. We kindly request that all payments to Silver Consumer Electricals Ltd be made exclusively through our designated company bank account. <br><br> In line with our commitment to ensuring transparency, security, and accountability in financial transactions, this policy to safeguard both our organization and our clients. Utilizing only our official company bank account for payments will help us better track and manage transactions, minimize errors, and prevent potential risks associated with cash transactions. <br><br> Kindly ensure that all future payments, including invoices and any other financial transactions, are processed using the provided bank account information. We kindly request your full cooperation in adhering to this payment policy to ensure a smooth and efficient business relationship.</p>
        <br>
        <p style="font-size: 15px;">Company does not entertain any type of cash transactions with any of the Company Representatives. Company is totally against CASH DEALING. If Dealer deals in cash with any Company representatives than he is personally liable for that.</p>
        <br>
        <p style="font-size: 15px;">Thank you for your understanding and cooperation in this matter. We look forward to continuing our positive business association.</p>
      </div>
      <div class="row mb-5">
        <h6><b>Channel Partners</b></h6>
      </div>
      <div class=" row mb-5 mt-5">
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
    </div>
    <div class="row mt-5 border border-dark rounded p-4">
      <table class="table">
        <thead>
          <tr>
            <th>Sr No</th>
            <th>PROPRIETARY CONCERN</th>
            <th>PARTNERSHIP FIRM</th>
            <th>LTD. / PVT LTD</th>
          </tr>
        </thead>
        <tbody>
          @if($dealerAppointment->appointment_kyc_detail && !empty($dealerAppointment->appointment_kyc_detail))


          <div class="row">
            <div class="col-md-6">
              Dealer Code -
            </div>
            <div class="col-md-6">
              {{$dealerAppointment->appointment_kyc_detail->dealer_code??'-'}}
            </div>
          </div>

          @php
          $proprietary_concern_array = ($dealerAppointment->appointment_kyc_detail->proprietary_concern && $dealerAppointment->appointment_kyc_detail->proprietary_concern!='null' && !empty($dealerAppointment->appointment_kyc_detail->proprietary_concern))?json_decode($dealerAppointment->appointment_kyc_detail->proprietary_concern):array();

          $partnership_firm_array = ($dealerAppointment->appointment_kyc_detail->partnership_firm && $dealerAppointment->appointment_kyc_detail->partnership_firm!='null' && !empty($dealerAppointment->appointment_kyc_detail->partnership_firm))?json_decode($dealerAppointment->appointment_kyc_detail->partnership_firm):array();

          $ltd_pvt_array = ($dealerAppointment->appointment_kyc_detail->ltd_pvt && $dealerAppointment->appointment_kyc_detail->ltd_pvt!='null' && !empty($dealerAppointment->appointment_kyc_detail->ltd_pvt))?json_decode($dealerAppointment->appointment_kyc_detail->ltd_pvt):array();
          @endphp
          @else
          @php
          $proprietary_concern_array = [];
          $partnership_firm_array = [];
          $ltd_pvt_array = [];
          @endphp
          @endif

          @foreach($kyc_ckeckbox as $key => $val)
          @if ($key < 60 && ($key==0 || $key % 3==0)) <tr>
            <td>{{($key / 3)+1}}</td>
            <td>
              <div class="form-group">
                <label for="proprietary_concern"> {{$kyc_ckeckbox[$key]}} </label>
                <input disabled class="form-check-input" type="checkbox" value="{{($key / 3)+1}}" {{in_array(($key / 3)+1, $proprietary_concern_array)?'checked':''}}>
              </div>
            </td>
            <td>
              <div class="form-group">
                <label for="partnership_firm"> {{$kyc_ckeckbox[$key+1]}} </label>
                <input disabled class="form-check-input" type="checkbox" value="{{($key / 3)+1}}" {{in_array(($key / 3)+1, $partnership_firm_array)?'checked':''}}>
              </div>
            </td>
            <td>
              <div class="form-group">
                <label for="ltd_pvt"> {{$kyc_ckeckbox[$key+2]}} </label>
                <input disabled class="form-check-input" type="checkbox" value="{{($key / 3)+1}}" {{in_array(($key / 3)+1, $ltd_pvt_array)?'checked':''}}>
              </div>
            </td>
            </tr>
            @endif
            @endforeach
            <tr>
              <td colspan="2">* TYPE OF DISTRIBUTION CHANNEL</td>
              <td colspan="2">
                <div class="row" style="margin-left: 10px;">
                  <div class="form-check col-md-3">
                    <label for="partnership_firm"> Dealer </label>
                    <input class="form-check-input" type="radio" value="Dealer" {{($dealerAppointment->appointment_kyc_detail&&$dealerAppointment->appointment_kyc_detail->distribution_channel=='Dealer')?'checked':''}}>
                  </div>
                  <div class="form-check col-md-3">
                    <label for="partnership_firm"> Distributor </label>
                    <input class="form-check-input" type="radio" value="Distributor" {{($dealerAppointment->appointment_kyc_detail&&$dealerAppointment->appointment_kyc_detail->distribution_channel=='Distributor')?'checked':''}}>
                  </div>
                </div>
              </td>
            </tr>
        </tbody>
      </table>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>