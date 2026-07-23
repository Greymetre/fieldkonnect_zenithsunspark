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
                            <h4 class="card-title">Holiday</h3>
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

                                {!! Form::model($holidays,[
                                'route' => $holidays->exists ? ['holidays.update', $holidays->id] : 'holidays.store',
                                'method' => $holidays->exists ? 'PUT' : 'POST',
                                'files'=>true
                                ]) !!}

                                <div class="p-2 form-group">
                                    <label for="branch">Branch</label>
                                    <select class="form-control select2 {{ $errors->has('branch') ? 'is-invalid' : '' }}" name="branch" id="branch" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branche)
                                        <option value="{{ $branche->id }}" <?php if($holidays->branch== $branche->id){ echo "selected";} ?> >{{ $branche->branch_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('branch'))
                                    <div class="error col-lg-12">
                                        <p class="text-danger">{{ $errors->first('branch') }}</p>
                                    </div>
                                    @endif
                                </div>

                                 
                             <div class="col-md-1">
                            <button type="submit" class=" btn btn-theme btn-sm add_more" id="add_more" style="margin-top:20%">{{ __('+') }}</button>     
                            </div>

                            <div id="add_sales_detail">

                                 <?php 
                               $holiday_names = explode(",",$holidays->name);
                               $holiday_dates = explode(",",$holidays->holiday_date);

                                $i=0;
                                  ?>  

                                     @foreach($holiday_dates as $holiday_date) 

                                   <div class="col-md-1">
                                <button class="btn btn-danger btn-sm  deleteRowhstry{{$i}}"  type="button"><i class="bi bi-trash"></i>-</button></div>   

                                 <div class="p-2 form-group history{{$i}}">
                                    <label for="holiday_date">Holiday Date</label>
                                    <input type="text" name="holiday_date[]" id="holiday_date1" value="{{isset($holiday_date)?$holiday_date:''}}" class="form-control datepicker">
                                     @if ($errors->has('holiday_date'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('holiday_date') }}</p>
                                 </div>
                                 @endif
                                </div>

                                 <div class="p-2 form-group history{{$i}}">
                                    <label for="name">Holiday Name</label>
                                    <input type="text" name="name[]" id="name" value="{{$holidays?$holiday_names[$i]:''}}" class="form-control">
                                    @if ($errors->has('name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                 </div>
                                 @endif
                                 </div>

                                <?php $i++; ?>
                                @endforeach 
                              
                            </div>

                                {{ Form::submit('Save', array('class' => 'btn btn-theme pull-right')) }}
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

  

    <script type="text/javascript">


 // $("#add_more").click(function (e) {
   $(".add_more").click(function (e) {     
        e.preventDefault();

    var saleAdd = '<div class="delete_row_data">'+
            
            '<div class="col-md-1">'+
          '<button class="btn btn-danger btn-sm  deleteRowhstry"  type="button">'+
          '<i class="bi bi-trash"></i>-</button>'+
          '</div>'+
             '<div class="p-2 form-group">'+
                '<label for="holiday_date">Holiday Date</label>'+
                '<input value="" type="text" name="holiday_date[]"  class="form-control datepicker">'+
             '</div>'+
            '<div class="p-2 form-group">'+
                '<label for="name">Holiday Name</label>'+
                '<input value="" type="text" name="name[]" id="name" class="form-control">'+
            '</div>'+
            '</div>';

          $('#add_sales_detail').append(saleAdd);

    $(".datepicker").datepicker({
        createButton: false,
        displayClose: true,
        closeOnSelect: false,
        selectMultiple: true,
        dateFormat: 'yy-mm-dd',
        beforeShow: function(input) {
          $(input).css({
            "position": "relative",
            "z-index": 999999
          });
        },
        onClose: function() {
          $('.ui-datepicker').css({
            'z-index': 0
          });
        }
      });


    $('.deleteRowhstry').on('click', function(e) {
     e.preventDefault();
      $(this).parents(".delete_row_data").remove();
      //$(this).remove();
    });

  }); 


   
    <?php $i=0; ?>
    @foreach($holiday_dates as $holiday_date) 
  $('.deleteRowhstry'+{{$i}}).on('click', function(e) {
     e.preventDefault();
     $(".history"+{{$i}}).remove();
     $(this).remove();
    });
      <?php $i++; ?>
      @endforeach  



    </script>


</x-app-layout>