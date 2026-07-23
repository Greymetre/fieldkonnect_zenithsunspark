<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        input.form-check-input {
            border: 2px solid;
            width: 20px;
            height: 20px;
            float: right;
        }

        td,
        th {
            padding: 15px 10px !important;
            border: 1px solid #000;
        }
    </style>
</head>

<body>
    @if(session()->has('message_success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
        </button>
        <span>
            {{ session()->get('message_success') }}
        </span>
    </div>
    @endif
    <div class="container text-center bg-success mt-4">
        <h6>KYC FORM - CHECK LIST</h6>
    </div>

    <div class="container mt-3">
        <form action="{{route('dealer-appointment-kyc-form.store')}}" method="post">
            @csrf
            <input type="hidden" name="appointment_id" id="appointment_id" value="{{$dealer_appointment->id}}">
            <div class="form-group">
                <div class="row mt-3">
                    <div class="col-md-2">
                        <label for="channel_partner">Name of Channel Partner :</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="channel_partner" id="channel_partner" class="form-control">
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-1">
                        <label for="place">Place :</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="place" id="place" class="form-control">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-2">
                        <label for="concerned_branch">Concerned Branch :</label>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="concerned_branch" id="concerned_branch" class="form-control">
                    </div>
                </div>
                <div class="row mt-4 bg-warning p-2">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="dealer_code">DEALER CODE</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="dealer_code" id="dealer_code" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <p>DIVISION</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <label class="form-check-label" for="PUMPMOTORS"> PUMP & MOTORS </label>
                            <input class="form-check-input" type="radio" value="PUMP&MOTORS" name="division" id="PUMPMOTORS">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <label class="form-check-label" for="FAN&APP"> FAN & APP </label>
                            <input class="form-check-input" type="radio" name="division" value="FAN&APP" id="FAN&APP">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <label class="form-check-label" for="AGRI"> AGRI </label>
                            <input class="form-check-input" type="radio" name="division" value="AGRI" id="AGRI">
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>PROPRIETARY CONCERN</th>
                                <th>PARTNERSHIP FIRM</th>
                                <th>LTD. / PVT LTD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kyc_ckeckbox as $key => $val)
                            @if ($key < 60 && ($key==0 || $key % 3==0)) <tr>
                                <td>{{($key / 3)+1}}</td>
                                <td>
                                    <div class="form-group">
                                        <label for="proprietary_concern_{{($key / 3)+1}}"> {{$kyc_ckeckbox[$key]}} </label>
                                        <input class="form-check-input" type="checkbox" value="{{($key / 3)+1}}" name="proprietary_concern[]" id="proprietary_concern_{{($key / 3)+1}}">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="partnership_firm_{{($key / 3)+1}}"> {{$kyc_ckeckbox[$key+1]}} </label>
                                        <input class="form-check-input" type="checkbox" value="{{($key / 3)+1}}" name="partnership_firm[]" id="partnership_firm_{{($key / 3)+1}}">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="ltd_pvt_{{($key / 3)+1}}"> {{$kyc_ckeckbox[$key+2]}} </label>
                                        <input class="form-check-input" type="checkbox" value="{{($key / 3)+1}}" name="ltd_pvt[]" id="ltd_pvt_{{($key / 3)+1}}">
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
                                                <label for="Dealer"> Dealer </label>
                                                <input class="form-check-input" type="radio" value="Dealer" id="Dealer" name="distribution_channel">
                                            </div>
                                            <div class="form-check col-md-3">
                                                <label for="Distributor"> Distributor </label>
                                                <input class="form-check-input" type="radio" value="Distributor" id="Distributor" name="distribution_channel">
                                            </div>
                                            <div class="form-check col-md-3">
                                                <label for="Service Center"> Service Center </label>
                                                <input class="form-check-input" type="radio" value="Service Center" id="service_center" name="distribution_channel">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row mt-3 p-2">
                    <small class="text-danger">Without Security Deposit & MCL Cheques Code will not be Process <br>
                        **Only Advance Payment Dealer will Allow for Without MCL Code Process (GM Approval Require) , Agri Dealer only- advance Payment <br>
                        *** Please check Name should be match with GSTN & Bank Cheques <br>
                        **Require Dealer/Distributor and Service policy duly signed & stamp</small>
                </div>
                <div class="row mt-3 mb-3">
                    <div class="col-md-3">
                        <h5>Documents Checked & Approved by Accountant</h5>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-3">
                        <h5>Name :-</h5>
                        <h5>Signature :-</h5>
                    </div>
                </div>
            </div>
            <input type="submit" value="Submit" class="btn btn-info btn-lg mt-4">
        </form>
    </div>

</body>

</html>