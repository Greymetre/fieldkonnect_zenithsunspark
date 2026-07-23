<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Dealer / Distributor Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://silver.fieldkonnect.io//public/assets/plugins/select2/css/select2.css">
    <style>
        .error {
            font-size: 0.8rem;
            color: #f44336;
        }

        .form-control {
            margin-bottom: 10px;
        }

        input.form-check-input {
            border: 2px solid !important;
            width: 20px !important;
            height: 20px !important;
            opacity: 1 !important;
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
            left: 50px;
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





        @media (max-width: 966px) {
            .profile-pic-container {
                width: 152px;
                height: 152px;
            }


            label {
                font-size: 12px;
            }

            .form-select {
                font-size: 12px;
                padding: 3px 4px;
            }


            .form-control {
                font-size: 12px;
            }


            input.form-check-input {
                width: 15px !important;
                height: 15px !important;

            }

            .mt-5 {
                margin-top: 1rem !important;
                font-size: 14px;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                height: 27px;
                font-size: 12px;
            }

            .form-check {
                padding-left: 0;

            }

            .mt-4 {
                margin-top: 0.5rem !important;
            }

            table.table tr th {
                font-size: 12px;
            }

            p {
                font-size: 10px;
            }

            table.table td {
                font-size: 12px;
            }

            span.text-info {
                font-size: 10px;
            }

            h5 {
                font-size: 14px;
            }

            h6 {
                font-size: 14px;
            }

            ol li {
                font-size: 12px;
            }
        }






        @media (max-width: 767px) {
            .profile-pic-container {
                width: 168px;
                height: 168px;
                margin: 0 auto;
                margin-top: 13px;
                position: relative;
            }


            label {
                font-size: 11px;
            }

            .form-select {
                font-size: 11px;
                padding: 3px 4px;
            }


            .form-control {
                font-size: 10px;
            }


            input.form-check-input {
                width: 15px !important;
                height: 15px !important;

            }

            .mt-5 {
                margin-top: 1rem !important;
                font-size: 13px;
            }

            table.table.border tr th {
                font-size: 10px;
            }

            table.table th {
                font-size: 10px;
            }

            p {
                font-size: 8px;
                margin-bottom: 0;
            }

            table.table td {
                font-size: 10px;
            }



            span.text-info {
                font-size: 8px;
            }

            h5 {
                font-size: 12px;
            }

            h6 {
                font-size: 12px;
            }

            ol li {
                font-size: 10px;
            }

            .content img {
                width: 100%;
            }

            form {
                width: 100%;
            }

            .form-check {
                padding-left: 1.5rem;
            }


            input.form-control:read-only {
                margin-bottom: 10px;
            }

            .row.box-inputs.mt-2 {
                margin-top: 0 !important;
            }

            p {
                font-size: 10px !IMPORTANT;
            }

            button.btn {
                width: 100%;
                margin-bottom: 12px;
                margin-top: 12px;
            }

            input#fileInput[type="file"] {
                position: absolute;
                width: 168px;
                height: 168px;
                opacity: 0;
                cursor: pointer;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            .footer-div {
                display: flex;
                gap: 15px;
                flex-flow: row;
            }

            .row.mt-5 {
                margin-top: 0 !important;
            }
        }
    </style>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
</head>

<body>
    <div class="container">
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

        <h1 class="text-center">Dealer/Distributor Data Sheet</h1>
        <p>(All information furnished by you will be treated as strictly confidential)</p>

        <form id="new_dealer_appoint_form" action="{{route('dealer-appointment-form.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row mt-3">
                <div class="col-md-3 ">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="branch">Branch </label>
                        </div>
                        <div class="col-md-9">
                            <div class="input_section">
                                <select class="form-select" name="branch" id="branch" required>
                                    <option value="" disabled selected>Your answer</option>
                                    @if($branchs && count($branchs) > 0)
                                    @foreach($branchs as $branch)
                                    <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if ($errors->has('branch'))
                                <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('branch') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 ">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="branch">User(Created By) </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input_section">
                                <select class="select2" name="created_by" id="created_by" data-style="select-with-transition" title="Select User">
                                    <option value="">Select User</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="profile-pic-container">
                        <div class="profile-pic">
                            <img id="profileImage" src="default-profile.png" alt="Passport Size Profile Picture">
                        </div>
                        <input type="file" id="fileInput" name="profile_picture" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3 ">
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
                                    <option value="{{$district->id}}">{{$district->district_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 ">
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
                <div class="col-md-3 ">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="place">Place </label>
                        </div>
                        <div class="col-md-8">
                            <div class="input_section">
                                <input type="text" name="place" id="place" class="form-control uppercase">
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
                    <div class="input_section">
                        <input type="date" name="appointment_date" id="appointment_date" class="form-control uppercase" required>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="PUMPMOTORS"> PUMP & MOTORS </label>
                        <input required class="form-check-input" type="radio" value="PUMP&MOTORS" name="division" id="PUMPMOTORS">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="FAN&APP"> FAN & APP </label>
                        <input required class="form-check-input" type="radio" name="division" value="FAN&APP" id="FAN&APP">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="AGRID"> AGRI </label>
                        <input required class="form-check-input" type="radio" name="division" value="AGRI" id="AGRID">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="SOLAR"> SOLAR </label>
                        <input required class="form-check-input" type="radio" name="division" value="SOLAR" id="SOLAR">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="LIGHTING"> LIGHTING </label>
                        <input required class="form-check-input" type="radio" name="division" id="LIGHTING" value="LIGHTING">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="SERVECE"> ASC </label>
                        <input required class="form-check-input" type="radio" name="division" id="SERVECE" value="SERVICE">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Others"> Others </label>
                        <input required class="form-check-input" type="radio" name="division" id="Others" value="Others">
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
                            <select class="form-select" name="old_user" id="old_user" required>
                                <option value="" disabled selected>Your Answer</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
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
                            <select class="form-select select2" name="asc_divi[]" multiple id="asc_divi" required>
                                <option value="PUMP&MOTORS">PUMP & MOTORS</option>
                                <option value="FAN&APP">FAN & APP</option>
                                <option value="AGRI">AGRI</option>
                                <option value="SOLAR">SOLAR</option>
                                <option value="LIGHTING">LIGHTING</option>
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
                            <select class="form-select" name="old_division" id="old_division">
                                <option value="" disabled selected>Your Answer</option>
                                <option value="PUMP&MOTORS">PUMP & MOTORS</option>
                                <option value="FAN&APP">FAN & APP</option>
                                <option value="AGRI">AGRI</option>
                                <option value="SOLAR">SOLAR</option>
                                <option value="LIGHTING">LIGHTING</option>
                                <option value="Others">Others</option>
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
                            <input type="text" name="old_firm_name" id="old_firm_name" class="form-control uppercase">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="old_gst">GST Number </label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="old_gst" id="old_gst" class="form-control uppercase">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="form-check">
                        <label class="form-check-label" for="distributor"> Distributor </label>
                        <input required class="form-check-input mr-3" type="radio" name="customertype" value="distributor" id="distributor">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <label class="form-check-label" for="dealer"> Dealer </label>
                        <input required class="form-check-input mr-3" type="radio" name="customertype" value="dealer" id="dealer">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <label class="form-check-label" for="shopee"> Shopee </label>
                        <input required class="form-check-input mr-3" type="radio" name="customertype" value="shopee" id="shopee">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check">
                        <label class="form-check-label" for="service center"> Service Center </label>
                        <input required class="form-check-input mr-3" type="radio" name="customertype" value="Service Center" id="servicecenter">
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
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label" for="pumo"> PUMP & MOTORS </label>
                                <input required class="form-check-input" type="radio" name="security_deposit" id="pumo" value="10000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mr-3" type="text" oninput="this.value = this.value.toUpperCase()" name="SDPUMPMOTORS" value="10000" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label" for="F&A"> F&A </label>
                                <input required class="form-check-input" type="radio" name="security_deposit" id="F&A" value="5000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mr-3" type="text" oninput="this.value = this.value.toUpperCase()" name="SDF&A" value="5000" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label" for="agri"> AGRI </label>
                                <input required class="form-check-input" type="radio" name="security_deposit" id="agri" value="100000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mr-3" id="asda" type="text" name="SDPUMPMOTORS" value="100000" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label" for="service_centerd"> Service Center </label>
                                <input required class="form-check-input" type="radio" name="security_deposit" id="service_centerd" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mr-3" id="asda" type="text" name="SDservicecenterd" value="0">
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">GST DETAILS:</h5>

            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="REGULAR"> REGULAR </label>
                        <input class="form-check-input mr-3" type="radio" name="gst_type" value="REGULAR" id="REGULAR">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Composition"> COMPOSITION </label>
                        <input class="form-check-input mr-3" type="radio" name="gst_type" value="Composition" id="Composition">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="UNREGD"> UNREGD </label>
                        <input class="form-check-input mr-3" type="radio" name="gst_type" value="UNREGD" id="UNREGD">
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
                            <input class="form-control mr-3" type="text" oninput="this.value = this.value.toUpperCase()" name="gst_no" value="">
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">Firm:</h5>

            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Prop"> Proprietorship </label>
                        <input class="form-check-input mr-3" type="radio" name="firm_type" value="Prop" id="Prop">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Partnership"> Partnership Firm </label>
                        <input class="form-check-input mr-3" type="radio" name="firm_type" value="Partnership" id="Partnership">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="LTD"> (P) LTD </label>
                        <input class="form-check-input mr-3" type="radio" name="firm_type" value="LTD" id="LTD">
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
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="firm_name" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> CIN No in case of Company </label>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="cin_no" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Name of Related Firm in which presently dealing </label>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="related_firm_name" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Line of Business </label>
                            </div>
                            <div class="col-md-8">
                                <textarea name="line_business" class="form-control uppercase" id="line_business"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Office Address: </label>
                            </div>
                            <div class="col-md-12">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_address" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Pin: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_pincode" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Mobile No.: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_mobile" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Email: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_email" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <label> Showroom Address / GODOWN: </label>
                            </div>
                            <div class="col-md-12">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_address" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Pin: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_pincode" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Mobile No.: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_mobile" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Email: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_email" value="">
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
                                <select name="status" id="status" class="form-control uppercase">
                                    <option value="" disabled selected>Please Select Status</option>
                                    <option value="Proprietor">Proprietor</option>
                                    <option value="Partnership">Partnership</option>
                                    <option value="Private">Private LTD</option>
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
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_2" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_3" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_3" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_3" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_4" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_4" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_4" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Contact Person / Name</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="contact_person_name" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Mobile No./ E-Mail</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="mobile_email" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Name of your Bankers</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="bank_name" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Address of the Banker</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="bank_address" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Account Type</th>
                                    <td colspan="3">
                                        <select name="account_type" id="account_type" class="form-control uppercase">
                                            <option value="" disabled selected>Please Select Account Type</option>
                                            <option value="Current Account">Current Account</option>
                                            <option value="CC Account">CC Account</option>
                                            <option value="OD Account">OD Account</option>
                                            <option value="Saving Accounts">Saving Accounts</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Account No.</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="account_number" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>IFSC CODE</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="ifsc_code" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Payment terms</th>
                                    <td colspan="3">
                                        <select name="payment_term" id="payment_term" class="form-control uppercase">
                                            <option value="" disabled selected>Please Select Payment Term</option>
                                            <option value="Direct">Direct</option>
                                            <option value="against">against</option>
                                            <option value="Advance">Advance</option>
                                            <option value="PDC">PDC</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Maximum Credit period</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="credit_period" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th colspan="3">Whether two (2) Cheque (s) have been collected – MCL CHEQUES (Nationalize) <span class="text-info">*(To Be filled at HO)</span></th>
                                    <td>
                                        <select name="payment_term_bm" disabled id="payment_term_bm" class="form-control uppercase">
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
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_no_1" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_account_number_1" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_bank_1" class="form-control uppercase"></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_no_2" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_account_number_2" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_bank_2" class="form-control uppercase"></td>
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
                                    <th>PRODUCT</th>
                                    <th>Nature Of Business (Dealer/Distributor/Stockiest)</th>
                                    <th>Annual Turn Over</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_company_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_product_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_turn_over_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_company_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_product_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_business_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_turn_over_2" class="form-control uppercase"></td>
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
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="present_annual_turnover" value="">
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
                                    <td>MOTORS</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="motor_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="motor_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>PUMP</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="pump_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="pump_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>FAN & APP</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="F&A_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="F&A_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>LIGHTING</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="lighting_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="lighting_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>AGRI</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="agri_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="agri_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>SOLAR – PUMP</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="solar_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="solar_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td colspan="2"><input type="text" oninput="this.value = this.value.toUpperCase()" name="anticipated_business_total" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th colspan="3"><b>Note: Please sign Target sheets for TOD incentives</b></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">Attachments:</h5>
            <div class="border border-dark rounded p-4">
                <div class="row mt-2 mb-2">
                    <div class="col-md-3 mb-3">
                        <label for="service_policy">Service Policy(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="service_policy" id="service_policy" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="dealer_policy">Dealer Policy(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="dealer_policy" id="dealer_policy" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="mou_sheet">MOU Sheet(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="mou_sheet" id="mou_sheet" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="mcl_cheque_1">MCL(cheque) 1(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="mcl_cheque_1" id="mcl_cheque_1" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="mcl_cheque_2">MCL(cheque) 2(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="mcl_cheque_2" id="mcl_cheque_2" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="gst_certificate">GST Certificate(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="gst_certificate" id="gst_certificate" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="adhar_card">Adhar Card(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" multiple name="adhar_card" id="adhar_card" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="pan_card">PAN Card(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" multiple name="pan_card" id="pan_card" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bank_statement">6 Month Bank Statement(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="bank_statement" id="bank_statement" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="application_form">Application Form(PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="application_form" id="application_form" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="cancel_cheque">Cancel Cheque / Passbook (PDF Only)</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="cancel_cheque" id="cancel_cheque" class="form-control file-input" accept="application/pdf">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="shop_image">Shop Image</label>
                        <div class="inp-div">
                            <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                            <p class="m-0">Attach a File</p>
                            <input type="file" name="shop_image" id="shop_image" class="form-control file-input" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>


            <h5 class="mt-5">Signatures of Dealer:</h5>

            <div class="main-details">
                <div class="row box-inputs mt-2">
                    <div class="col-md-12 mt-4">
                        <div class="row">
                            <div class="col-md-4">
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
                                <input readonly type="text" oninput="this.value = this.value.toUpperCase()" name="credit_limit" class="form-control uppercase">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Credit Rating (in grade)</label>
                            </div>
                            <div class="col-md-8">
                                <input readonly type="text" oninput="this.value = this.value.toUpperCase()" name="credit_rating" class="form-control uppercase">
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
                <br>
                <br>
                <br>
                <h6><b>Channel Partners</b></h6>
                <br>
                <br>
                <br>
                <br>
                <h5><b>(Sign With Stamp)</b></h5>
                <br>
                <br>
                <br>
                <div class="footer-div">
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
            </div>

            <div class="row mt-5">
                <div class="col-md-4">
                    <button class="btn btn-success">Submit</button>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4" style="text-align: right;">
                    <button type="button" id="printButton" class="btn btn-info">Print Form</button>
                </div>
            </div>


            <div class="row mt-5"></div>

        </form>
        <div class="row mt-3"></div>
    </div>
    <div class="baseurl" data-baseurl="{{ url('/')}}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://silver.fieldkonnect.io//public/assets/plugins/select2/js/select2.full.min.js"></script>
        <script src="{{ url('/').'/'.asset('assets/js/core/jquery.validate.js') }}"></script>
        <script src="{{ url('/').'/'.asset('assets/js/plugins/jquery.validate.min.js') }}"></script>
        <script src="{{ url('/').'/'.asset('assets/js/validation_dealer_appointment.js?') }}"></script>
        <script>
            $(document).ready(function() {
                $('#printButton').click(function() {
                    window.print();
                });
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

                    }
                });
            })

            $('.select2').select2()

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
            })

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
                    url: "{{ url('getUserListAppoint') }}",
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
            }).trigger("chnage");

            $(document).ready(function() {
                $('.file-input').on('change', function() {
                    var inpDiv = $(this).closest('.inp-div');
                    if (this.files && this.files.length > 0) {
                        inpDiv.css('background-color', '#80ec759e');
                    } else {
                        inpDiv.css('background-color', '');
                    }
                });
                $('input[name="customertype"]').change(function() {
                    var selectedType = $(this).val();
                    var selectedDivision = $('input[name="division"]:checked').val();
                    if (selectedType == 'dealer') {
                        document.getElementById("asda").value = '25000';
                    } else {
                        document.getElementById("asda").value = '100000';
                    }

                    if (selectedDivision == 'AGRI' && selectedType == 'dealer') {
                        $("#parent-div").removeClass('d-none');
                    } else {
                        $("#parent-div").addClass('d-none');
                    }
                });
                $('input[name="division"]').change(function() {
                    var selectedType = $('input[name="customertype"]:checked').val();
                    var selectedDivision = $(this).val();

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
                });
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
        </script>
</body>

</html>