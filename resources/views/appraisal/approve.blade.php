<x-app-layout>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Appraisal Approve</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
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
                               
                                {!! Form::open(['method' => 'POST','files'=>true,'route' => ['appraisals.updateapproval'],'class' => 'form-horizontal','id' => 'frmBuilder']) !!}
                                <div class="row">
                        
                                    <!-- <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" name="f_year" id="f_year" data-style="select-with-transition" title="Select Financial Year">
                                            <option value="">Select Financial Year</option>
                                            <option value="{{Carbon\Carbon::now()->format('Y')-1}}_{{Carbon\Carbon::now()->format('y')}}">{{Carbon\Carbon::now()->format('Y')-1}}-{{Carbon\Carbon::now()->format('y')}}</option>
                                        </select>
                                    </div> -->

                                     @foreach($appraisal_details as $appraisal_detail)

                                     @endforeach 

                                

                                    <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" name="f_year" id="f_year" data-style="select-with-transition">
                                            <!-- <option value="">Select Financial Year</option> -->
                                            <option value="{{$appraisal_detail->year}}"> {{$appraisal_detail->year}}</option>
                                        </select>
                                    </div>

                            

                                    <div class="p-2" id="session_div" style="width: 250px;">
                                        <select class="selectpicker" name="appraisal_session" id="appraisal_session" data-style="select-with-transition" title="Select Appraisal Type">
                                        </select>
                                    </div>
                                      <input type="hidden" name="user_id" value="{{$user_id}}">

                                    <div class="table-responsive w-100">
                                        <table class="table kvcodes-dynamic-rows-example" id="table_appraisal">
                                            <thead>
                                                <tr class="card-header-warning text-white" id="headings">
                                                    <th class="text-center">Category </th>
                                                    <th class="text-center">KRA </th>
                                                    <th class="text-center">Measure/Indicator</th>
                                                    <th class="text-center">Target Per Annum</th>
                                                    <th class="text-center">Weightage</th>
                                                    <th class="text-center">Target</th>
                                                    <th class="text-center">Achivment</th>
                                                    <th class="text-center">Acual</th>
                                                    <th class="text-center">Max Rating</th>
                                                    <th class="text-center">Rating</th>
                                                    @php
                                                    $check_old = array();
                                                    @endphp
                                                    @foreach($appraisal_details as $appraisal_detail)
                                                    @php
                                                    $appraisal_detail->rating_bys = explode(',', $appraisal_detail->rating_bys);
                                                    @endphp
                                                    @foreach($appraisal_detail->rating_bys as $keys => $rating_by_id)
                                                    @php
                                                    $reting_user = App\models\User::find($rating_by_id);
                                                    @endphp
                                                    @if($rating_by_id != Auth::user()->id && !in_array($rating_by_id, $check_old) && explode(',',$appraisal_detail->user_ids)[$keys] != $rating_by_id)
                                                    @php array_push($check_old, $rating_by_id) @endphp
                                                        <th>{{$reting_user->name}}({{$reting_user->getdesignation->designation_name}})</th>
                                                    @endif
                                                    @if(explode(',',$appraisal_detail->user_ids)[$keys] == $rating_by_id && !in_array($rating_by_id, $check_old))
                                                    @php array_push($check_old, $rating_by_id) @endphp
                                                    <th>Self Rating</th>
                                                    @endif
                                                    @endforeach
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php $i = 0; ?>

                                                @foreach($appraisal_details as $appraisal_detail)

                                                <?php 

                                               // $categories = explode(",",$sale_weightage->category_name);
                                               // $name = explode(",",$sale_weightage->name);
                                               // $weightage = explode(",",$sale_weightage->weightage);
                                               // $indicator = explode(",",$sale_weightage->indicator);
                                               // $annum_target = explode(",",$sale_weightage->annum_target);
                                               // $i=0;

                                                  ?> 
                                              
                                                
                                                <tr>
                                                    <input type="hidden" name="appraisal_ids[]" value="{{$appraisal_detail->id}}">

                                                    <input type="hidden" name="sale_weightage_ids[]" value="{{$appraisal_detail->weightage_id}}">

                                                    <input type="hidden" name="kra_names[]" value="{{$appraisal_detail->kra}}">

                                                     <td>
                                                        <h6>{{$appraisal_detail->sales_weightage->category_name??''}}</h6>
                                                    </td>
                                                    <td>
                                                        <h6>{{$appraisal_detail->sales_weightage->name??''}}</h6>
                                                    </td>
                                                    <td>
                                                        {{$appraisal_detail->sales_weightage->indicator??''}}
                                                    </td>
                                                     <td>
                                                        {{$appraisal_detail->sales_weightage->annum_target??''}}
                                                    </td>
                                                    <td>
                                                        {{$appraisal_detail->sales_weightage->weightage??''}}%
                                                    </td>
                                                           
                                                    <td>
                                                        <input type="number" name="target[]" class="target" value="{{explode(',',$appraisal_detail->targets)[0]??''}}">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="achivment" name="achivment[]" value="{{explode(',',$appraisal_detail->achivments)[0]??''}}">
                                                    </td>

                                                    <td>
                                                        <input name="acual[]" class="acual" type="text" value="{{explode(',',$appraisal_detail->acuals)[0]??''}}" readonly>
                                                    </td>

                                                
                                                    <input name="weightage[]" class="weightage" type="hidden" value="{{$appraisal_detail->sales_weightage->weightage??''}}" readonly>
                                                    


                                                    <td>10</td>
                                                    
                                                    @if(in_array(auth()->user()->id, $appraisal_detail->rating_bys))
                                                    @foreach(explode(',',$appraisal_detail->ratings) as $keys => $rating)
                                                    @if($appraisal_detail->rating_bys[$keys] == Auth::user()->id)
                                                    <td>
                                                    <input name="rating[]" class="all_rating" max="10" type="number" value="{{$rating??''}}">
                                                        <p class="rat-err"></p>
                                                    </td>
                                                  @endif
                                                  @endforeach
                                                    @else
                                                    <td> {{$appraisal_detail->rating_by}}
                                                        <input name="rating[]" class="all_rating" max="10" type="number" value="">
                                                        <p class="rat-err"></p>
                                                    </td>

                                                  @endif

                                                    @foreach(explode(',',$appraisal_detail->ratings) as $keys => $rating)

                                                     

                                                     @if($appraisal_detail->rating_bys[$keys] != Auth::user()->id)
                                                    <td>
                                                    <input class="all_rating" max="10" type="number" value="{{$rating??''}}" readonly>
                                                        <p class="rat-err"></p>
                                                    </td>
                                                      @endif
                                                     @endforeach 


                                                 
                                              
                                                   
                                                </tr>

                                                  <?php $i++; ?>                                                 
                                                   @endforeach 

                                            </tbody>
                                        </table>
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" class="form-control" name="remark" id="remark" placeholder="Remark" value="{{$appraisal_detail->remark}}">
                                        </div>
                                    </div>
                                    {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <script>
        $("#branch_id").on('change', function() {
            var search_branches = $(this).val();
            $.ajax({
                url: "{{ url('reports/attendancereport') }}",
                data: {
                    "search_branches": search_branches
                },
                success: function(res) {
                    if (res.status == true) {
                        var select = $('#executive_id');
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

        $("#executive_id").on("change", function() {
            $('#appraisalCreateForm').find("input:not(:submit, :hidden)").val('');
            $(".appended").remove();
            var executive_id = $(this).val();
            var f_year = $("#f_year").val();
            var appraisal_type = $("#appraisal_type").val();
            var appraisal_session = $("#appraisal_session").val();

            getAllRatings(executive_id, f_year, appraisal_type, appraisal_session);
        })

        $("#f_year").on("change", function() {
            $(".appended").remove();
            $('#appraisalCreateForm').find("input:not(:submit, :hidden)").val('');
            var executive_id = $("#executive_id").val();
            var f_year = $(this).val();
            var appraisal_type = $("#appraisal_type").val();
            var appraisal_session = $("#appraisal_session").val();

            getAllRatings(executive_id, f_year, appraisal_type, appraisal_session);
        })

        $(document).ready(function() {
            $('#session_div').hide();
        })

        $("#appraisal_type").on('change', function() {
            $(".appended").remove();
            $('#appraisalCreateForm').find("input:not(:submit, :hidden)").val('');
            var appraisal_type = $(this).val();
            var executive_id = $("#executive_id").val();
            var f_year = $("#f_year").val();
            var appraisal_session = $("#appraisal_session").val();

            getAllRatings(executive_id, f_year, appraisal_type, appraisal_session);

            var select = $('#appraisal_session');
            if (appraisal_type == 'quarterly' || appraisal_type == 'half_yearly') {
                select.empty();
                $('#session_div').show();
                select.append('<option value="">Select Appraisal Session</option>');
                if (appraisal_type == 'quarterly') {
                    select.append('<option value="Q1">Q1</option>');
                    select.append('<option value="Q2">Q2</option>');
                    select.append('<option value="Q3">Q3</option>');
                    select.append('<option value="Q4">Q4</option>');
                } else if (appraisal_type == 'half_yearly') {
                    select.append('<option value="first_half">First Half</option>');
                    select.append('<option value="second_half">Second Half</option>');
                }
                select.selectpicker('refresh');
            } else {
                $('#session_div').hide();
                select.selectpicker('refresh');
            }
        })

        $(".achivment").on('keyup', function() {
            var achivment = $(this).val();
            var trElement = $(this).closest('tr');
            var targetInput = trElement.find('.target');
            var acualInput = trElement.find('.acual');
            var target = targetInput.val();
            if (achivment > 0 && target > 0) {
                let acual = ((achivment / target) * 100).toFixed(2);
                acualInput.val(acual + "%");
            } else {
                acualInput.val("");
            }
        })

        $(".target").on('keyup', function() {
            var target = $(this).val();
            var trElement = $(this).closest('tr');
            var achivmentInput = trElement.find('.achivment');
            var acualInput = trElement.find('.acual');
            var achivment = achivmentInput.val();
            if (achivment > 0 && target > 0) {
                let acual = ((achivment / target) * 100).toFixed(2);
                acualInput.val(acual + "%");
            } else {
                acualInput.val("");
            }
        })
        $(".all_rating").on('keyup', function() {
            $ival = $(this).val();
            if (parseInt($ival) > 10) {
                $(this).closest('tr').find('.rat-err').show();
                $(this).closest('tr').find('.rat-err').html('*Rating not greater than to max rating');
            } else {
                $(this).closest('tr').find('.rat-err').hide();
                $(this).closest('tr').find('.rat-err').html('');
            }
        })

        function getAllRatings(executive_id, f_year, appraisal_type, appraisal_session){
            if (executive_id != '' && f_year != '' && appraisal_type != '') {
                if (appraisal_type == 'quarterly' || appraisal_type == 'half_yearly') {
                    if (appraisal_session != '') {
                        $.ajax({
                            url: "{{ url('getappraisal') }}",
                            data: {
                                "executive_id": executive_id,
                                "f_year": f_year,
                                "appraisal_type": appraisal_type,
                            },
                            success: function(res) {
                                if (res.length > 0) {
                                    var old_data = '';
                                    var tttr = '<tr class="appended"><td colspan="6" class="tottal-td">Total</td><td class="tottal-td" id="auth-user-per"></td>';
                                    var tttr2 = '<tr class="appended"><td colspan="6" class="tottal-td">Grade</td><td class="tottal-td" id="auth-user-grade"></td>';
                                    var totalPer = 0;
                                    var selftotalPer = 0;
                                    $.each(res, function(i, item) {
                                        var tdElement = $('td:contains(' + item.sales_weightage.name + ')');
                                        var trElement = tdElement.closest('tr');
                                        if ('{{auth()->user()->id}}' == item.rating_by) {
                                            $("#remark").val(item.remark);
                                            $(trElement).find('td').each(function() {
                                                var targetInputtarget = $(this).find('input[name="target[]"]');
                                                var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                                var targetInputacual = $(this).find('input[name="acual[]"]');
                                                var targetInputrating = $(this).find('input[name="rating[]"]');

                                                if (targetInputtarget.length > 0) {
                                                    targetInputtarget.val(item.target);
                                                }
                                                if (targetInputachivment.length > 0) {
                                                    targetInputachivment.val(item.achivment);
                                                }
                                                if (targetInputacual.length > 0) {
                                                    targetInputacual.val(item.acual);
                                                }
                                                if (targetInputrating.length > 0) {
                                                    targetInputrating.val(item.rating);
                                                }
                                            });
                                            selftotalPer += (parseInt(item.rating) * parseInt(item.sales_weightage.weightage) / 10);
                                        } else if ('{{auth()->user()->id}}' != item.rating_by) {
                                            $(trElement).find('td').each(function() {
                                                var targetInputtarget = $(this).find('input[name="target[]"]');
                                                var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                                var targetInputacual = $(this).find('input[name="acual[]"]');

                                                if (targetInputtarget.length > 0) {
                                                    targetInputtarget.val(item.target);
                                                }
                                                if (targetInputachivment.length > 0) {
                                                    targetInputachivment.val(item.achivment);
                                                }
                                                if (targetInputacual.length > 0) {
                                                    targetInputacual.val(item.acual);
                                                }
                                            });
                                            var NewTh = '<th class="text-center appended">';
                                            if (item.user_id != item.rating_by_user.id) {
                                                 NewTh += item.rating_by_user.name+'('+item.rating_by_user.getdesignation.designation_name+')';
                                            } else {
                                                NewTh += 'Self';
                                            }
                                            NewTh += '  Rating</th>';
                                            trElement.append('<td class="appended">' + item.rating + '</td>');
                                            if (old_data != item.rating_by_user.roles[0].id) {
                                                $("#headings").append(NewTh);
                                                old_data = item.rating_by_user.roles[0].id;
                                            }
                                            totalPer += (parseInt(item.rating) * parseInt(item.sales_weightage.weightage) / 10);
                                            if (item.sales_weightage.id == 6) {
                                                if (totalPer < 51) {
                                                    tttr2 += '<td class="tottal-td">GRADE-C(Poor)</td>';
                                                } else if (totalPer > 50 && totalPer < 61) {
                                                    tttr2 += '<td class="tottal-td">GRADE - B(Average)</td>';
                                                } else if (totalPer > 60 && totalPer < 71) {
                                                    tttr2 += '<td class="tottal-td">GRADE-B+(Good)</td>';
                                                } else if (totalPer > 70 && totalPer < 81) {
                                                    tttr2 += '<td class="tottal-td">GRADE-A(EXCELLENT)</td>';
                                                } else if (totalPer > 80) {
                                                    tttr2 += '<td class="tottal-td">GRADE-A+(SPECIAL)</td>';
                                                }
                                                tttr += '<td class="tottal-td">' + totalPer + '%</td>';
                                                totalPer = 0;
                                            }
                                        }
                                    });
                                    tttr += '</tr>';
                                    tttr2 += '</tr>';
                                    $('#table_appraisal').append(tttr);
                                    $('#table_appraisal').append(tttr2);
                                    if (selftotalPer < 51) {
                                        $("#auth-user-grade").html("GRADE-C(Poor)");
                                    } else if (selftotalPer > 50 && selftotalPer < 61) {
                                        $("#auth-user-grade").html("GRADE-B(Average)");
                                    } else if (selftotalPer > 60 && selftotalPer < 71) {
                                        $("#auth-user-grade").html("GRADE-B+(Good)");
                                    } else if (selftotalPer > 70 && selftotalPer < 81) {
                                        $("#auth-user-grade").html("GRADE-A(EXCELLENT)");
                                    } else if (selftotalPer > 80) {
                                        $("#auth-user-grade").html("GRADE-A+(SPECIAL)");
                                    }
                                    $("#auth-user-per").html(selftotalPer+"%");
                                    if (selftotalPer < 51) {
                                        $("#auth-user-grade").html("GRADE-C(Poor)");
                                    } else if (selftotalPer > 50 && selftotalPer < 61) {
                                        $("#auth-user-grade").html("GRADE-B(Average)");
                                    } else if (selftotalPer > 60 && selftotalPer < 71) {
                                        $("#auth-user-grade").html("GRADE-B+(Good)");
                                    } else if (selftotalPer > 70 && selftotalPer < 81) {
                                        $("#auth-user-grade").html("GRADE-A(EXCELLENT)");
                                    } else if (selftotalPer > 80) {
                                        $("#auth-user-grade").html("GRADE-A+(SPECIAL)");
                                    }
                                    $("#auth-user-per").html(selftotalPer+"%");
                                } else {
                                    $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                                    const rows = document.querySelectorAll('tr');
                                    rows.forEach(row => {
                                        if (row.classList.contains('appended')) {
                                            row.remove();
                                        }
                                    });
                                    $(".appended").remove();
                                }
                            }
                        });
                    } else {
                        $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                    }
                } else {
                    $.ajax({
                        url: "{{ url('getappraisal') }}",
                        data: {
                            "executive_id": executive_id,
                            "f_year": f_year,
                            "appraisal_type": appraisal_type,
                        },
                        success: function(res) {
                            if (res.length > 0) {
                                var old_data = '';
                                var tttr = '<tr class="appended"><td colspan="6" class="tottal-td">Total</td><td class="tottal-td" id="auth-user-per"></td>';
                                    var tttr2 = '<tr class="appended"><td colspan="6" class="tottal-td">Grade</td><td class="tottal-td" id="auth-user-grade"></td>';
                                var totalPer = 0;
                                var selftotalPer = 0;
                                $.each(res, function(i, item) {
                                    var tdElement = $('td:contains(' + item.sales_weightage.name + ')');
                                    var trElement = tdElement.closest('tr');
                                    if ('{{auth()->user()->id}}' == item.rating_by) {
                                        $("#remark").val(item.remark);
                                        $(trElement).find('td').each(function() {
                                            var targetInputtarget = $(this).find('input[name="target[]"]');
                                            var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                            var targetInputacual = $(this).find('input[name="acual[]"]');
                                            var targetInputrating = $(this).find('input[name="rating[]"]');

                                            if (targetInputtarget.length > 0) {
                                                targetInputtarget.val(item.target);
                                            }
                                            if (targetInputachivment.length > 0) {
                                                targetInputachivment.val(item.achivment);
                                            }
                                            if (targetInputacual.length > 0) {
                                                targetInputacual.val(item.acual);
                                            }
                                            if (targetInputrating.length > 0) {
                                                targetInputrating.val(item.rating);
                                            }
                                        });
                                        selftotalPer += (parseInt(item.rating) * parseInt(item.sales_weightage.weightage) / 10);
                                    } else if ('{{auth()->user()->id}}' != item.rating_by) {
                                        $(trElement).find('td').each(function() {
                                            var targetInputtarget = $(this).find('input[name="target[]"]');
                                            var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                            var targetInputacual = $(this).find('input[name="acual[]"]');

                                            if (targetInputtarget.length > 0) {
                                                targetInputtarget.val(item.target);
                                            }
                                            if (targetInputachivment.length > 0) {
                                                targetInputachivment.val(item.achivment);
                                            }
                                            if (targetInputacual.length > 0) {
                                                targetInputacual.val(item.acual);
                                            }
                                        });
                                        var NewTh = '<th class="text-center appended">';
                                        if (item.user_id != item.rating_by_user.id) {
                                             NewTh += item.rating_by_user.name+'('+item.rating_by_user.getdesignation.designation_name+')';
                                        } else {
                                            NewTh += 'Self';
                                        }
                                        NewTh += '  Rating</th>';
                                        trElement.append('<td class="appended">' + item.rating + '</td>');
                                        if (old_data != item.rating_by_user.roles[0].id) {
                                            $("#headings").append(NewTh);
                                            old_data = item.rating_by_user.roles[0].id;
                                        }
                                        totalPer += (parseInt(item.rating) * parseInt(item.sales_weightage.weightage) / 10);
                                        if (item.sales_weightage.id == 6) {
                                            if (totalPer < 51) {
                                                tttr2 += '<td class="tottal-td">GRADE-C(Poor)</td>';
                                            } else if (totalPer > 50 && totalPer < 61) {
                                                tttr2 += '<td class="tottal-td">GRADE - B(Average)</td>';
                                            } else if (totalPer > 60 && totalPer < 71) {
                                                tttr2 += '<td class="tottal-td">GRADE-B+(Good)</td>';
                                            } else if (totalPer > 70 && totalPer < 81) {
                                                tttr2 += '<td class="tottal-td">GRADE-A(EXCELLENT)</td>';
                                            } else if (totalPer > 80) {
                                                tttr2 += '<td class="tottal-td">GRADE-A+(SPECIAL)</td>';
                                            }
                                            tttr += '<td class="tottal-td">' + totalPer + '%</td>';
                                            totalPer = 0;
                                        }
                                    }
                                });
                                tttr += '</tr>';
                                tttr2 += '</tr>';
                                $('#table_appraisal').append(tttr);
                                $('#table_appraisal').append(tttr2);
                                if (selftotalPer < 51) {
                                    $("#auth-user-grade").html("GRADE-C(Poor)");
                                } else if (selftotalPer > 50 && selftotalPer < 61) {
                                    $("#auth-user-grade").html("GRADE-B(Average)");
                                } else if (selftotalPer > 60 && selftotalPer < 71) {
                                    $("#auth-user-grade").html("GRADE-B+(Good)");
                                } else if (selftotalPer > 70 && selftotalPer < 81) {
                                    $("#auth-user-grade").html("GRADE-A(EXCELLENT)");
                                } else if (selftotalPer > 80) {
                                    $("#auth-user-grade").html("GRADE-A+(SPECIAL)");
                                }
                                $("#auth-user-per").html(selftotalPer+"%");
                            } else {
                                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                                const rows = document.querySelectorAll('tr');
                                rows.forEach(row => {
                                    if (row.classList.contains('appended')) {
                                        row.remove();
                                    }
                                });
                                $(".appended").remove();
                            }
                        }
                    });
                }
            } else {
                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                const rows = document.querySelectorAll('tr');
                rows.forEach(row => {
                    if (row.classList.contains('appended')) {
                        row.remove();
                    }
                });
                $(".appended").remove();
            }
        }




        $("#division_id").on('change', function() {
            var division_id = $(this).val();

            $.ajax({
                url: "{{ url('getSalesweitagDetail') }}",
                data: {
                    "division_id": division_id
                },
                success: function(res) {
                    
                }
            });

        })


    </script>

</x-app-layout>