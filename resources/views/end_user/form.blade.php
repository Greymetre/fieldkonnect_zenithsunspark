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

        .select2-container {
            border-bottom: 1px solid lightgray;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-0 p-0">
                <div class="card-header  m-0 card-header-tabs card-header-warning">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper new_id">
                            <h4 class="card-title ">
                                {{$endUser->exists ? 'Edit':'Create'}} End User
                            </h4>
                            @if(auth()->user()->can(['district_access']))
                            <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                                <li class="nav-item">
                                    <a class="nav-link" href="javascript:void(0);" onclick="window.history.back();">
                                        <i class="material-icons">next_plan</i> Back
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
                    <div class="container">
                        {!! Form::model($endUser,[
                        'route' => $endUser->exists ? ['end_user.update', $endUser->id ] : 'end_user.store',
                        'method' => $endUser->exists ? 'PUT' : 'POST',
                        'id' => 'storeTransactionHistoryData',
                        'files'=>true
                        ]) !!}
                        <div class="pt-4">
                            <h5>Contact Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_number" class="col-form-label">Customer Number Search</label>

                                        <input type="number" name="customer_number" id="customer_number" class="form-control" value="{!! old( 'customer_number' , $endUser['customer_number']?$endUser['customer_number']:'') !!}" required>
                                        <input type="hidden" name="end_user_id" id="end_user_id" value="{!! old( 'end_user_id' , $endUser['id']) !!}">
                                        @if ($errors->has('customer_number'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_number') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_name" class="col-form-label">Customer Name</label>

                                        <input type="text" name="customer_name" id="customer_name" class="form-control" value="{!! old( 'customer_name' , $endUser['customer']?$endUser['customer']['customer_name']:'') !!}" required>
                                        @if ($errors->has('customer_name'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_email" class="col-form-label">Email</label>

                                        <input type="text" name="customer_email" id="customer_email" class="form-control" value="{!! old( 'customer_email' , $endUser['customer']?$endUser['customer']['customer_email']:'') !!}">
                                        @if ($errors->has('customer_email'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_email') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_address" class="col-form-label">Address</label>

                                        <input type="text" name="customer_address" id="customer_address" class="form-control" value="{!! old( 'customer_address' , $endUser['customer']?$endUser['customer']['customer_address']:'') !!}" required>
                                        @if ($errors->has('customer_address'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_state" class="col-form-label">State</label>

                                        <select name="customer_state" id="customer_state" placeholder="Select State" class="select2 form-control" required>
                                            <option value="" disabled selected>Select State</option>
                                            @if($states && count($states) > 0)
                                            @foreach($states as $state)
                                            <option value="{{$state->id}}" {!! old( 'customer_state' , $endUser['customer']?$endUser['customer']['customer_state']:'')==$state->id?'selected':'' !!}>{{$state->state_name}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('customer_state'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_state') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_district" class="col-form-label">District</label>

                                        <select name="customer_district" id="customer_district" class="select2 form-control" required></select>
                                        @if ($errors->has('customer_district'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_district') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_city" class="col-form-label">City</label>

                                        <select name="customer_city" id="customer_city" class="select2 form-control" required></select>
                                        @if ($errors->has('customer_city'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_city') }}</p>
                                        </div>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_place" class="col-form-label">Place</label>

                                        <input type="text" name="customer_place" id="customer_place" class="form-control" value="{!! old( 'customer_place' , $endUser?$endUser['customer_place']:'') !!}">
                                        @if ($errors->has('customer_place'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_place') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input_section">
                                        <label for="customer_pindcode" class="col-form-label">Pincode</label>

                                        <select name="customer_pindcode" id="customer_pindcode" placeholder="Select Pincode" class="select2 form-control">
                                            <option value="" disabled selected>Select Pincode</option>
                                            @if($pincodes && count($pincodes) > 0)
                                            @foreach($pincodes as $pincode)
                                            <option value="{{$pincode->id}}" {!! old( 'customer_pindcode' , $endUser['customer']?$endUser['customer']['customer_pindcode']:'')==$pincode->id?'selected':'' !!}>{{$pincode->pincode}}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @if ($errors->has('customer_pindcode'))
                                        <div class="error">
                                            <p class="text-danger">{{ $errors->first('customer_pindcode') }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="input_section">
                                        <label for="customer_status" class="col-form-label">Customer status</label>
                                        <input type="radio" name="customer_status" id="inactive" value="0" {{ old( 'customer_status' , $endUser?$endUser['status']:'')=='0'?'checked':''}}><span class="yes_no"> Inactive</span>
                                        <input type="radio" name="customer_status" id="active" value="1" {{ old( 'customer_status' , $endUser?$endUser['status']:'')=='1'?'checked':''}}> <span class="yes_no">active</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer pull-right">
                            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
            <script>
                $("#customer_number").on("keyup", function() {
                    var customer_number = $(this).val();
                    $.ajax({
                        url: "{{ url('getEndUserData') }}",
                        dataType: "json",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            customer_number: customer_number
                        },
                        success: function(res) {
                            if (res.status === true) {
                                $("#customer_name").val(res.data.customer_name);
                                $("#end_user_id").val(res.data.id);
                                $("#customer_email").val(res.data.customer_email);
                                $("#customer_address").val(res.data.customer_address);
                                $("#customer_place").val(res.data.customer_place);
                                $("#customer_state").val(res.data.state_id).trigger("change");
                                if(res.data.status == '1') {
                                    $('#active').prop('checked', true);
                                } else if (res.data.status == '0') {
                                    $('#inactive').prop('checked', true);
                                }
                                setTimeout(() => {
                                    $("#customer_district").val(res.data.district_id).trigger("change");
                                }, 1000);
                                setTimeout(() => {
                                    $("#customer_city").val(res.data.city_id).trigger("change");
                                }, 1500);
                                setTimeout(() => {
                                    $("#customer_pindcode").val(res.data.customer_pindcode).trigger("change");
                                }, 2000);

                            } else {
                                $("#customer_name").val("");
                                $("#end_user_id").val("");
                                $("#customer_email").val("");
                                $("#customer_address").val("");
                                $("#customer_place").val("");
                                $("#customer_pindcode").val("").trigger("change");;
                                $("#customer_state").val("");
                                $("#customer_district").val("");
                                $("#customer_city").val("");

                                $("#customer_name").prop('readonly', false);
                                $("#customer_email").prop('readonly', false);
                                $("#customer_address").prop('readonly', false);
                                $("#customer_place").prop('readonly', false);
                                $("#customer_pindcode").prop('disabled', false);
                                $("#customer_state").prop('readonly', false);
                                $("#customer_district").prop('readonly', false);
                                $("#customer_city").prop('readonly', false);
                                $("#customer_name").prop('readonly', false);
                                $("#customer_email").prop('readonly', false);
                            }
                        }
                    });
                }).trigger('keyup');

                $("#customer_state").on("change", function() {
                    var state_id = $(this).val();
                    if (state_id != null && state_id != '') {
                        $.ajax({
                            url: "{{ url('getDistrict') }}",
                            dataType: "json",
                            type: "POST",
                            data: {
                                _token: "{{csrf_token()}}",
                                state_id: state_id
                            },
                            success: function(res) {
                                var options = '<option value="">Select District</option>';
                                $.each(res, function(key, val) {
                                    options += '<option value="' + val.id + '">' + val.district_name + '</option>';
                                })
                                $("#customer_district").html(options);
                            }
                        });
                    }
                });

                $("#customer_district").on("change", function() {
                    var district_id = $(this).val();
                    $.ajax({
                        url: "{{ url('getCity') }}",
                        dataType: "json",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            district_id: district_id
                        },
                        success: function(res) {
                            var options = '<option value="">Select City</option>';
                            $.each(res, function(key, val) {
                                options += '<option value="' + val.id + '">' + val.city_name + '</option>';
                            })
                            $("#customer_city").html(options);
                        }
                    });
                });

                $("#customer_city").on("change", function() {
                    var city_id = $(this).val();
                    $.ajax({
                        url: "{{ url('getPincode') }}",
                        dataType: "json",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            city_id: city_id
                        },
                        success: function(res) {
                            var options = '<option value="">Select Pincode</option>';
                            $.each(res, function(key, val) {
                                options += '<option value="' + val.id + '">' + val.pincode + '</option>';
                            })
                            $("#customer_pindcode").html(options);
                        }
                    });
                });
            </script>
</x-app-layout>