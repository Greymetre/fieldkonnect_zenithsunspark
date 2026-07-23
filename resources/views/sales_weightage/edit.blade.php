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
                    <div class="card">
                        <div class="card-header card-header-icon card-header-theme">
                            <h4 class="card-title">Sale Weightage Create</h4>
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
                                {!! Form::model($sales_weightage,[
                                'route' => $sales_weightage->exists ? ['salesweightage.multiupdate'] : 'sales_weightage.store',
                                'method' => 'POST',
                                'id' => 'createCompany',
                                'files'=>true
                                ]) !!}



                                <div class="input_section">
                                    <label  class="col-form-label" for="display_name">Display Name</label>
                                    <input type="text" name="display_name" id="display_name" value="{{$sales_weightage?$sales_weightage->display_name:''}}" class="form-control">
                                </div>

                                <div class="input_section">
                                    <label  class="col-form-label" for="financial_year">Financial Year</label>
                                    <input type="text" name="financial_year" id="financial_year" value="{{$sales_weightage?$sales_weightage->financial_year:''}}" class="form-control">
                                </div>

                                <div class="input_section">
                                    <label  class="col-form-label" for="category">Division</label>
                                    <select class="form-control select2 {{ $errors->has('division') ? 'is-invalid' : '' }}" name="division" id="division" required>
                                        <option value="">Select Division</option>
                                        @foreach($devisions as $devision)
                                        <option value="{{ $devision->id }}" <?php if ($sales_weightage->division_id == $devision->id) {echo "selected";} ?>>{{ $devision->division_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('division'))
                                    <div class="error">
                                        <p class="text-danger">{{ $errors->first('division') }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="input_section">
                                    <label  class="col-form-label" for="category">Department</label>
                                    <select class="form-control select2 {{ $errors->has('department') ? 'is-invalid' : '' }}" name="department" id="department" required>
                                    <option value="">Select Department</option>
                                        @foreach($departments as $department)
                                        <option value="{{ $department->id }}" <?php if ($sales_weightage->department_id == $department->id) {echo "selected";} ?> >{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('department'))
                                    <div class="error">
                                        <p class="text-danger">{{ $errors->first('department') }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="input_section">
                                    <label  class="col-form-label" for="category">Designation</label>

                                    <?php
                                    $selected_desig = array();
                                    if($sales_weightage->designation_id){
                                    $selected_desig = explode(",",$sales_weightage->designation_id);
                                     }
                                     ?>

                                 <select class="form-control select2 {{ $errors->has('designation') ? 'is-invalid' : '' }}" name="designation[]" id="designation" multiple required>
                                    <option value="">Select designation</option>
                                        @foreach($designations as $designation)
                                        <option value="{{ $designation->id }}" <?php if(in_array($designation->id, $selected_desig)) {echo "selected";} ?> >{{ $designation->designation_name }}</option>
                                        @endforeach
                                 </select>
                                    @if ($errors->has('designation'))
                                    <div class="error">
                                        <p class="text-danger">{{ $errors->first('designation') }}</p>
                                    </div>
                                    @endif
                                </div>
                               

                             <div class="">
                            <button type="submit" class=" btn btn-theme" id="add_more" style="margin-top:2%">{{ __('+') }}</button>     
                                </div>

                            <div id="add_sales_detail">

                               <?php 
                               $categories = explode(",",$sales_weightage->category_names);
                               $name = explode(",",$sales_weightage->names);
                               $weightage = explode(",",$sales_weightage->weightages);
                               $indicator = explode(",",$sales_weightage->indicators);
                               $annum_target = explode(",",$sales_weightage->annum_targets);
                               $weightages_id = explode(",",$sales_weightage->ids);
                                $i=0;
                                  ?>  
                                    @foreach($categories as $category) 

                            
                                <div class="">
                                <button class="btn btn-danger btn-sm  deleteRowhstry{{$i}}"  type="button"><i class="bi bi-trash"></i>-</button></div>


                                <div class="input_section history{{$i}}">
                                    <label  class="col-form-label" for="category">KRA Category</label>
                                    <input value="{{isset($category)?$category:''}}" type="text" name="category[]" id="category" class="form-control">
                                    <input type="hidden" name="weightages_ids[]" value="{{$weightages_id[$i]}}">
                                </div>
                               

                                <div class="input_section history{{$i}}">
                                    <label  class="col-form-label" for="name">KRA Name</label>
                                    <input value="{{$sales_weightage?$name[$i]:''}}" type="text" name="name[]" id="name" class="form-control">
                                </div>

                                <div class="input_section history{{$i}}">
                                    <label  class="col-form-label" for="weightage">Weightage</label>
                                    <input type="text" name="weightage[]" id="weightage" value="{{$weightage[$i]??''}}" class="form-control">
                                </div>

                                <div class="input_section history{{$i}}">
                                    <label  class="col-form-label" for="indicator">Indicator</label>
                                    <input type="text" name="indicator[]" id="indicator" value="{{$indicator[$i]??''}}" class="form-control">
                                </div>
                                <div class="input_section history{{$i}}">
                                    <label  class="col-form-label" for="annum_target">Target Per Annum</label>
                                    <input type="text" name="annum_target[]" id="annum_target" value="{{$annum_target[$i]??''}}" class="form-control">
                                </div>
                                
                                <?php $i++; ?>
                                @endforeach 
                            
                            </div>

                                @if($sales_weightage->name)
                                {{ Form::submit('Update', array('class' => 'btn btn-theme pull-right')) }}
                                @else
                                {{ Form::submit('Save', array('class' => 'btn btn-theme pull-right')) }}
                                @endif
                                {{ Form::close() }}
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
            var executive_id = $(this).val();
            var f_year = $("#f_year").val();
            var appraisal_type = $("#appraisal_type").val();
            var appraisal_session = $("#appraisal_session").val();

            if (executive_id != '' && f_year != '' && appraisal_type != '') {
                if (appraisal_type == 'quarterly' || appraisal_type == 'half_yearly') {
                    if (appraisal_session != '') {
                        $.ajax({
                            url: "{{ url('getappraisal') }}",
                            data: {
                                "executive_id": executive_id,
                                "f_year": f_year,
                                "appraisal_type": appraisal_type,
                                "appraisal_session": appraisal_session
                            },
                            success: function(res) {
                                if (res.length > 0) {
                                    $.each(res, function(i, item) {
                                        var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                        var trElement = tdElement.closest('tr');

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
                                        // console.log(trElement);
                                    });
                                } else {
                                    $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
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
                                $.each(res, function(i, item) {
                                    var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                    var trElement = tdElement.closest('tr');

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
                                    // console.log(trElement);
                                });
                            } else {
                                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                            }
                        }
                    });
                }
            } else {
                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
            }
        })

        $(document).ready(function() {
            // $('#session_div').hide();
        })

        $("#appraisal_type").on('change', function() {
            var appraisal_type = $(this).val();
            var executive_id = $("#executive_id").val();
            var f_year = $("#f_year").val();
            var appraisal_session = $("#appraisal_session").val();

            if (executive_id != '' && f_year != '' && appraisal_type != '') {
                if (appraisal_type == 'quarterly' || appraisal_type == 'half_yearly') {
                    if (appraisal_session != '') {
                        $.ajax({
                            url: "{{ url('getappraisal') }}",
                            data: {
                                "executive_id": executive_id,
                                "f_year": f_year,
                                "appraisal_type": appraisal_type,
                                "appraisal_session": appraisal_session
                            },
                            success: function(res) {
                                if (res.length > 0) {
                                    $.each(res, function(i, item) {
                                        var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                        var trElement = tdElement.closest('tr');

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
                                        // console.log(trElement);
                                    });
                                } else {
                                    $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
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
                                $.each(res, function(i, item) {
                                    var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                    var trElement = tdElement.closest('tr');

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
                                    // console.log(trElement);
                                });
                            } else {
                                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                            }
                        }
                    });
                }
            } else {
                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
            }
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

        $("#achivment").on('keyup', function() {
            var achivment = $(this).val();
            var target = $("#target").val();
            if (achivment > 0 && target > 0) {
                let acual = ((achivment / target) * 100).toFixed(2);
                $("#acual").val(acual + "%");
                let rating = (acual / 10).toFixed(1);
                $("#rating").val(rating);
            }
        })
    </script>



    <script type="text/javascript">


 $("#add_more").click(function (e) {
        e.preventDefault();

    var saleAdd = '<div class="delete_row_data">'+
            '<div class="col-md-1">'+
          '<button class="btn btn-danger btn-sm  deleteRowhstry"  type="button">'+
         '<i class="bi bi-trash"></i>-</button>'+
        '</div>'+
             '<div class="p-2 form-group">'+
                '<label for="category">KRA Category</label>'+
                '<input value="" type="text" name="category[]" id="category" class="form-control">'+
                 '<input type="hidden" name="weightages_ids[]">'+
             '</div>'+
            '<div class="p-2 form-group">'+
                '<label for="name">KRA Name</label>'+
                '<input value="" type="text" name="name[]" id="name" class="form-control">'+
            '</div>'+

            '<div class="p-2 form-group">'+
                '<label for="weightage">Weightage</label>'+
                '<input type="text" name="weightage[]" id="weightage" value="" class="form-control">'+
            '</div>'+

            '<div class="p-2 form-group">'+
                '<label for="indicator">Indicator</label>'+
                '<input type="text" name="indicator[]" id="indicator" value="" class="form-control">'+
            '</div>'+
            '<div class="p-2 form-group">'+
                '<label for="annum_target">Target Per Annum</label>'+
                '<input type="text" name="annum_target[]" id="annum_target" value="" class="form-control">'+
            '</div>'+
            '</div>';



          $('#add_sales_detail').append(saleAdd);

    $('.deleteRowhstry').on('click', function(e) {
     e.preventDefault();
      $(this).parents(".delete_row_data").remove();
      //$(this).remove();
    });

  }); 

     

    <?php $i=0; ?>
    @foreach($categories as $category) 
  $('.deleteRowhstry'+{{$i}}).on('click', function(e) {
     e.preventDefault();
     $(".history"+{{$i}}).remove();
     $(this).remove();
    });
      <?php $i++; ?>
      @endforeach   

    </script>


</x-app-layout>