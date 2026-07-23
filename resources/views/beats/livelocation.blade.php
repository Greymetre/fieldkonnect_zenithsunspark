<x-app-layout>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18" type="text/javascript"></script>
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card mt-4" data-animation="true">
                <div class="card-body">
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
                    <h5 class="font-weight-normal mt-4">User Live Location</h5>
                    <form target="_blank" method="post" action="{{url('map-all')}}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="dropdown bootstrap-select show-tick">
                                    <select class="selectpicker" multiple id="branch_id" name="branch_id" data-style="select-with-transition" title="Choose Branch" data-size="10" tabindex="-98">
                                        <option disabled=""> Select Branch</option>
                                        @if(@isset($branches ))
                                        @foreach($branches as $branch)
                                        <option value="{!! $branch['id'] !!}">{!! $branch['name'] !!}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="dropdown bootstrap-select show-tick">
                                    <select class="selectpicker" multiple id="division_id" name="division_id" data-style="select-with-transition" title="Choose Division" data-size="10" tabindex="-98">
                                        <option disabled=""> Select Division</option>
                                        @if(@isset($divisions ))
                                        @foreach($divisions as $division)
                                        <option value="{!! $division['id'] !!}">{!! $division['name'] !!}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="dropdown bootstrap-select show-tick">
                                    <select class="selectpicker" multiple id="department_id" name="department_id" data-style="select-with-transition" title="Choose Department" data-size="10" tabindex="-98">
                                        <option disabled=""> Select Department</option>
                                        @if(@isset($departments ))
                                        @foreach($departments as $department)
                                        <option value="{!! $department['id'] !!}">{!! $department['name'] !!}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="dropdown bootstrap-select show-tick">
                                    <select class="select2" id="user_id" name="user_id" data-style="select-with-transition" title="Choose User" data-size="10" tabindex="-98" required>
                                        <option disabled="" selected> Select Users</option>
                                        @if(@isset($users ))
                                        @foreach($users as $user)
                                        <option {{(!empty($user_id) && $user_id == $user['id'])?'selected':''}} value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-default bmd-form-group">
                                    <input type="text" class="form-control datepicker" id="date" required name="date" value="{{$date??''}}" placeholder="Date From" autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group has-default bmd-form-group">
                                    <input type="text" class="form-control datepicker" id="to_date" required name="to_date"
                                        value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" placeholder="Select Date"
                                        autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="col-md-2 p-0 text-center">
                                <button type="button" class="btn btn-info" onclick="getActivityData()">Activity</button>
                            </div>
                            <div class="col-md-2 p-0 text-center">
                                <!-- <button type="button" class="btn btn-info btn-sm" onclick="getLocationData()">Location</button> -->
                                <input type="submit" name="submit" class="btn btn-primary" value="Complete Map">
                            </div>
                            <div class="col-md-2 p-0 text-center">
                                <input type="submit" name="submit" class="btn btn-primary" value="Track">
                            </div>
                        </div>
                    </form>
                    <div class="row p-3">
                        <div class="col-md-7">
                            <div id="map" style="width: 500px; height: 400px;"></div>
                        </div>
                        <div class="col-md-5 mt-2" id="custom-scroll" style="overflow-y: scroll; height:400px">
                            <ul class="timeline timeline-simple" id="todayActivity">

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            // getActivityData();
            $('#loader').hide();
            getActivityData();
        })

        function getLocationData(lat, lang) {
            if (lat != '' && lang != '') {
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 12,
                    center: new google.maps.LatLng(lat, lang),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });
                var infowindow = new google.maps.InfoWindow();
                var marker;
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lang),
                    map: map
                });
                google.maps.event.addListener(marker, 'click', (function(marker) {
                    return function() {
                        infowindow.setContent(lat);
                        infowindow.open(map, marker);
                    }
                })(marker));
            } else {
                $("#map").html('No location found.');
            }


        }

        function getActivityData() {
            $("#todayActivity").empty();
            $("#todayActivity").append('Please Wait...');
            var date = $("input[name=date]").val();
            var user_id = $("select[name=user_id]").val();
            $.ajax({
                url: "{{ url('getUserActivityData') }}",
                dataType: "json",
                type: "POST",
                data: {
                    _token: "{{csrf_token()}}",
                    date: date,
                    user_id: user_id
                },
                success: function(res) {
                    $("#todayActivity").empty();
                    if (res.length > 0) {
                        $.each(res, function(index, item) {
                            var classname = 'success';
                            switch (item.title) {
                                case 'Punchin':
                                    var classname = 'primary';
                                    break;
                                case 'Punchout':
                                    var classname = 'warning';
                                    break;
                                case 'Checkin':
                                    var classname = 'info';
                                    break;
                                case 'Checkout':
                                    var classname = 'danger';
                                    break;
                                case 'Order':
                                    var classname = 'success';
                                    break;
                                default:
                                    var classname = 'default';
                            }
                            $("#todayActivity").append('<li class="timeline-inverted">' +
                                '<div class="timeline-badge ' + classname + '">' +
                                '<i class="material-icons">card_travel</i>' +
                                '</div>' +
                                '<div class="timeline-panel">' +
                                '<div class="timeline-heading">' +
                                '<span class="badge badge-pill badge-' + classname + '">' + item.time + '</span>' +
                                '</div>' +
                                '<div class="timeline-body">' +
                                '<h5 style="font-weight: bold;">' + item.title + '</h5>' +
                                '</div>' +
                                '<h6><i class="ti-time"></i> ' + item.msg + '</h6>' +
                                '<button class="btn btn-info btn-sm" onclick="getLocationData(' + item.latitude + ',' + item.longitude + ')">Location</button>' +
                                '</div>' +
                                '</li>');
                        });
                    } else {
                        $("#todayActivity").append('<h5 style="font-weight: bold;">No Activity Found</h5>');
                    }

                }
            });
        }

        $("#branch_id").on('change', function() {
            var search_branches = $(this).val();
            $.ajax({
                url: "{{ url('livelocation') }}",
                data: {
                    "search_branches": search_branches
                },
                success: function(res) {
                    if (res.status == true) {
                        var select = $('#user_id');
                        select.empty();
                        select.append('<option>Select User</option>');
                        $.each(res.users, function(k, v) {
                            select.append('<option value="' + v.id + '" >' + v.name + '</option>');
                        });
                        select.selectpicker('refresh');
                    }
                }
            });

        })

        $("#division_id").on('change', function() {
            var search_divisions = $(this).val();
            $.ajax({
                url: "{{ url('livelocation') }}",
                data: {
                    "search_divisions": search_divisions
                },
                success: function(res) {
                    if (res.status == true) {
                        var select = $('#user_id');
                        select.empty();
                        select.append('<option>Select User</option>');
                        $.each(res.users, function(k, v) {
                            select.append('<option value="' + v.id + '" >' + v.name + '</option>');
                        });
                        select.selectpicker('refresh');
                    }
                }
            });

        })

        $("#department_id").on('change', function() {
            var search_departments = $(this).val();
            $.ajax({
                url: "{{ url('livelocation') }}",
                data: {
                    "search_departments": search_departments
                },
                success: function(res) {
                    if (res.status == true) {
                        var select = $('#user_id');
                        select.empty();
                        select.append('<option>Select User</option>');
                        $.each(res.users, function(k, v) {
                            select.append('<option value="' + v.id + '" >' + v.name + '</option>');
                        });
                        select.selectpicker('refresh');
                    }
                }
            });
        })
    </script>
</x-app-layout>