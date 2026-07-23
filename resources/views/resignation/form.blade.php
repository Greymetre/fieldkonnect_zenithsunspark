<x-app-layout>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header card-header-icon card-header-theme">
                            <h4 class="card-title">Resignation Application</h3>
                        </div>
                        @if($errors->any())
                        <div>
                            <ul class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="card-body ">
                            <div class="tab-content tab-space">
                                {!! Form::open(['method' => 'POST','files'=>true,'route' => ['resignations.store'],'class' => 'form-horizontal','id' => 'resignation']) !!}


                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="division_id">Division</label>
                                            <select class="form-control select2 {{ $errors->has('division_id') ? 'is-invalid' : '' }}" onchange="getUsers()" name="division_id" id="division_id" required>
                                                <option value="">Select Division</option>
                                                @foreach($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->division_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('division_id'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('division_id') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="branch_id">Branch</label>
                                            <select class="form-control select2 {{ $errors->has('branch_id') ? 'is-invalid' : '' }}" onchange="getUsers()" name="branch_id" id="branch_id" required>
                                                <option value="">Select Branch</option>
                                                @foreach($branches as $branche)
                                                <option value="{{ $branche->id }}">{{ $branche->branch_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('branch_id'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="user_id">Name</label>
                                            <select class="form-control select2 {{ $errors->has('user_id') ? 'is-invalid' : '' }}" onchange="getUserDetails()" name="user_id" id="user_id" required>
                                                <option value="">Select User Name</option>
                                            </select>
                                            @if ($errors->has('user_id'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('user_id') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="employee_code">Employee Code</label>
                                            <input class="form-control {{ $errors->has('employee_code') ? 'is-invalid' : '' }}" type="text" name="employee_code" id="employee_code" readonly required>
                                            @if ($errors->has('employee_code'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('employee_code') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="designation">Designation</label>
                                            <input class="form-control {{ $errors->has('designation') ? 'is-invalid' : '' }}" type="text" name="designation" id="designation" readonly required>
                                            @if ($errors->has('designation'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('designation') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="mobile">Mobile Number</label>
                                            <input class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}" type="text" name="mobile" id="mobile" readonly required>
                                            @if ($errors->has('mobile'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('mobile') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="reporting_manager">Reporting Manager</label>
                                            <input class="form-control {{ $errors->has('reporting_manager') ? 'is-invalid' : '' }}" type="text" name="reporting_manager" id="reporting_manager" readonly required>
                                            @if ($errors->has('reporting_manager'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('reporting_manager') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="date_of_joining">Date Of Joining</label>
                                            <input class="form-control datepicker {{ $errors->has('date_of_joining') ? 'is-invalid' : '' }}" type="text" name="date_of_joining" id="date_of_joining" required>
                                            @if ($errors->has('date_of_joining'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('date_of_joining') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="notice">Notice</label>
                                            <select class="form-control select2 {{ $errors->has('notice') ? 'is-invalid' : '' }}" onchange="calculateRD()" name="notice" id="notice" required>
                                                <option value="">Select Notice Period</option>
                                                <option value="15">15 Days</option>
                                                <option value="1">1 Month</option>
                                                <option value="2">2 Month</option>
                                                <option value="3">3 Month</option>
                                            </select>
                                            @if ($errors->has('notice'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('notice') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="last_working_date">Last Working date</label>
                                            <input class="form-control datepicker {{ $errors->has('last_working_date') ? 'is-invalid' : '' }}" type="text" name="last_working_date" id="last_working_date" readonly required>
                                            @if ($errors->has('last_working_date'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('last_working_date') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="cug_sim_no">CUG SIM no</label>
                                            <input class="form-control {{ $errors->has('cug_sim_no') ? 'is-invalid' : '' }}" type="text" name="cug_sim_no" id="cug_sim_no" required>
                                            @if ($errors->has('cug_sim_no'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('cug_sim_no') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="persoanla_email">Personal Email id</label>
                                            <input class="form-control {{ $errors->has('persoanla_email') ? 'is-invalid' : '' }}" type="text" name="persoanla_email" id="persoanla_email" required>
                                            @if ($errors->has('persoanla_email'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('persoanla_email') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="persoanla_mobile">Personal Mobile Number</label>
                                            <input class="form-control {{ $errors->has('persoanla_mobile') ? 'is-invalid' : '' }}" type="text" name="persoanla_mobile" id="persoanla_mobile" required>
                                            @if ($errors->has('persoanla_mobile'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('persoanla_mobile') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="address">Address</label>
                                            <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address" id="address" required>
                                            @if ($errors->has('address'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('address') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input_section">
                                            <label for="submit_date">Resignation Date</label>
                                            <input class="form-control datepicker {{ $errors->has('submit_date') ? 'is-invalid' : '' }}" type="text" name="submit_date" id="submit_date" value="{{ now()->format('Y-m-d') }}" onchange="calculateRD()" required>
                                            @if ($errors->has('submit_date'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('submit_date') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="input_section">
                                            <label for="reason">Reason</label>
                                            <textarea class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}" name="reason" id="reason" rows="10"></textarea>
                                            @if ($errors->has('reason'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('reason') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{ Form::submit('Save', array('class' => 'btn btn-theme pull-right mt-3')) }}
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <script type="text/javascript">
        function getUsers() {
            var division_id = $('#division_id').val();
            var branch_id = $('#branch_id').val();

            $.ajax({
                url: "{{ url('getUserList') }}",
                dataType: "json",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    branch_id: branch_id,
                    division_id: division_id
                },
                success: function(res) {
                    var html = '<option value="">Select User Name</option>';
                    $.each(res, function(k, v) {
                        html += '<option value="' + v.id + '"> (' + v.employee_codes + ') ' + v.name + '</option>';
                    });
                    $("#user_id").html(html);
                }
            });
        }

        function getUserDetails() {
            var user_id = $('#user_id').val();

            $.ajax({
                url: "{{ url('getUserInfo') }}",
                dataType: "json",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    user_id: user_id
                },
                success: function(res) {
                    $("#employee_code").val(res.employee_codes);
                    $("#designation").val(res.getdesignation?.designation_name || '');
                    $("#reporting_manager").val(res.reportinginfo?.name || '');
                    $("#mobile").val(res.mobile);
                    $("#branch_id").val(res.branch_id);
                    $("#date_of_joining").val(res.userinfo.date_of_joining);

                }
            });
        }

        function calculateRD() {
            var noticeP = parseInt($('#notice').val(), 10);
            var submitDateValue = $('#submit_date').val();

            if (!isNaN(noticeP) && submitDateValue) {
                let today = new Date(submitDateValue);
                let lastWorkingDate;

                if (noticeP > 5) {
                    lastWorkingDate = new Date(today);
                    lastWorkingDate.setDate(today.getDate() + noticeP); // Assume 1 month = 30 days
                } else {
                    lastWorkingDate = new Date(today);
                    lastWorkingDate.setMonth(today.getMonth() + noticeP);
                }

                let formattedDate = lastWorkingDate.toISOString().split('T')[0];

                $("#last_working_date").val(formattedDate);
            } else {
                alert('Please provide a valid notice period and submit date.');
            }
        }
    </script>


</x-app-layout>