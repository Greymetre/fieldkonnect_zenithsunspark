<x-app-layout>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Update {{ trans('panel.expenses.title_singular') }}
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['expense_access']))
                <a href="{{ url('expenses') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.expenses.title_singular') !!} {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                @endif
              </div>
            </span>
          </h4>
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

            {!! Form::open(['method' => 'POST', 'route' => isset($expense->id)?['expenses.update',$expense->id]:['expenses.store'],'class' => 'form-horizontal','id' => 'storeExpenses', 'files' => true]) !!}
            @csrf
            @if(isset($expense->id))
            @method('PUT')
            @endif


            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.user') }}<span class="text-danger"> *</span></label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <select name="user_id" id="user_id" class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }} select2">
                        <option value="" disabled selected>Please Select User</option>
                        @foreach($users as $k=>$user)
                        <option value="{{$user->id}}" <?php if($user->id == $expense->user_id){echo "selected";} ?>>{{$user->name}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('user_id'))
                      <div class="invalid-feedback">
                        {{ $errors->first('user_id') }}
                      </div>
                      @endif
<<<<<<< HEAD
              
=======
                
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.date') }}<span class="text-danger"> *</span></label>
<<<<<<< HEAD
              
=======
                 
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Expenses date" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }} datepicker" type="text" name="date" id="date" value="{{ old('date', $expense->date??'') }}" maxlength="200" required autocomplete="off">
                      @if($errors->has('date'))
                      <div class="invalid-feedback">
                        {{ $errors->first('date') }}
                      </div>
                      @endif
                    </div>
                 
                </div>
              </div>


              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.expense_type') }}<span class="text-danger"> *</span></label>
<<<<<<< HEAD
               
=======
                  
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <select name="expenses_type" id="expenses_type" class="form-control {{ $errors->has('expenses_type') ? 'is-invalid' : '' }} select2">
                        <!-- <option value="" disabled selected>Please Select Expenses Type</option> -->
                       <!--  @foreach($expensestypes as $key=>$expensestype)
                        <option value="{{$expensestype->id}}" data-allowtype="{{$expensestype->allowance_type_id}}" data-rate ="{{$expensestype->rate}}" <?php //if($expensestype->id == $expense->expenses_type){echo "selected";} ?>>{{$expensestype->name}}</option>
                        @endforeach -->
                      </select>
                      @if($errors->has('expenses_type'))
                      <div class="invalid-feedback">
                        {{ $errors->first('expenses_type') }}
                      </div>
                      @endif
                    </div>
<<<<<<< HEAD
                 
=======
                
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                </div>

              <input type="text" name="expenses_types" value="{{$expense->expenses_type??''}}" id="expenses_type_id" hidden>

              <input type="text" name="total_kms" value="{{$expense->total_km??''}}" id="total_kms" hidden> 

              <input type="text" name="total_clm" value="{{$expense->claim_amount??''}}" id="total_clm" hidden>

              </div>

              <div class="col-md-6 km" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.rate') }}<span class="text-danger"> *</span></label>
<<<<<<< HEAD
                  
=======
            
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Rate" class="form-control {{ $errors->has('rate') ? 'is-invalid' : '' }}  rate" type="text" name="rate" id="rate" value="{{ old('rate', '') }}"  autocomplete="off">
                      @if($errors->has('rate'))
                      <div class="invalid-feedback">
                        {{ $errors->first('rate') }}
                      </div>
                      @endif
                    </div>
<<<<<<< HEAD
                  </div>
               
=======
                 
                </div>
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
              </div>


              <div class="col-md-6 km" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.start_km') }}<span class="text-danger"> *</span></label>
<<<<<<< HEAD
                  
=======
                 
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Start Km" class="form-control {{ $errors->has('start_km') ? 'is-invalid' : '' }} calcu" type="text" name="start_km" id="start_km" value="{{ old('start_km',  $expense->start_km??'') }}" maxlength="200" autocomplete="off">
                      @if($errors->has('start_km'))
                      <div class="invalid-feedback">
                        {{ $errors->first('start_km') }}
                      </div>
                      @endif
                    
                  </div>
                </div>
              </div>

              <div class="col-md-6 km" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.stop_km') }}<span class="text-danger"> *</span></label>
<<<<<<< HEAD
                 
=======
                
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Stop Km" class="form-control {{ $errors->has('stop_km') ? 'is-invalid' : '' }} calcu" type="text" name="stop_km" id="stop_km" value="{{ old('stop_km', $expense->stop_km??'') }}" maxlength="200" autocomplete="off">
                      @if($errors->has('stop_km'))
                      <div class="invalid-feedback">
                        {{ $errors->first('stop_km') }}
                      </div>
                      @endif
<<<<<<< HEAD
                    </div>
                  
=======
               
                  </div>
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                </div>
              </div>
              <div class="col-md-6 km" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.total_km') }}<span class="text-danger"> *</span></label>
                  
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Total Km" class="form-control {{ $errors->has('total_km') ? 'is-invalid' : '' }} total_km claim" type="text" name="total_km" id="total_km" value="{{ old('total_km', $expense->total_km??'') }}" maxlength="200" autocomplete="off">
                      @if($errors->has('total_km'))
                      <div class="invalid-feedback">
                        {{ $errors->first('total_km') }}
                      </div>
                      @endif
<<<<<<< HEAD
                   
                  </div>
=======
                    </div>
                
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                </div>
              </div>


              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.claim_amount') }}</label>
                  
                    <div class="form-group has-default bmd-form-group">
                      <input class="form-control {{ $errors->has('claim_amount') ? 'is-invalid' : '' }} claim final_claim" type="" name="claim_amount" id="claim_amount" value="{{ old('claim_amount', $expense->claim_amount??'') }}" pattern="^\d*(\.\d{0,2})?$" placeholder="Claim Amount">
                      @if($errors->has('claim_amount'))
                      <div class="invalid-feedback">
                        {{ $errors->first('claim_amount') }}
                      </div>
                      @endif
                    </div>
<<<<<<< HEAD
                  
=======
                 
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.note') }}<span class="text-danger"> *</span></label>
<<<<<<< HEAD
                 
=======
               
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Note" class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }} " type="text" name="note" id="note" value="{{ old('note', $expense->note??'') }}"  required autocomplete="off">
                      @if($errors->has('note'))
                      <div class="invalid-feedback">
                        {{ $errors->first('note') }}
                      </div>
                      @endif
<<<<<<< HEAD
                    </div>
                 
=======
                 
                  </div>
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Status Change Reason<span class="text-danger"> *</span></label>
<<<<<<< HEAD
                  
=======
            
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="reason" class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }} " type="text" name="reason" id="reason" value="{{ old('reason', $expense->reason??'') }}"  autocomplete="off">
                      @if($errors->has('reason'))
                      <div class="invalid-feedback">
                        {{ $errors->first('reason') }}
                      </div>
                      @endif
                   
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">Approved Amount<span class="text-danger"> *</span></label>
<<<<<<< HEAD
                 
=======
                
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Approve Amount" class="form-control {{ $errors->has('approve_amount') ? 'is-invalid' : '' }} " type="text" name="approve_amount" id="approve_amount" value="{{ old('approve_amount', $expense->approve_amount??'') }}"  autocomplete="off">
                      @if($errors->has('approve_amount'))
                      <div class="invalid-feedback">
                        {{ $errors->first('approve_amount') }}
                      </div>
                      @endif
<<<<<<< HEAD
                  
                  </div>
=======
                    </div>
               
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                </div>
              </div>

         
              <div class="col-md-6">
                  @if(isset($expense) && $expense->getMedia('expense_file')->count() > 0 && Storage::disk('s3')->exists($expense->getMedia('expense_file')[0]->getPath()))
                         <div class="form-group col-md-12">
                            @foreach($expense->getMedia('expense_file') as $image)
                         <!-- <img class="img-fluid" src="{{ $image->getFullUrl() }}" style="width:80px;height: 80px;"> -->
                           <?php 


                      
                            $media_id = $image->id;

                           $infoPath = pathinfo($image->getFullUrl());
                           $extension = $infoPath['extension'];
                                 if($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg'){
                            ?>
                           <a href="{{ $image->getFullUrl()}}" data-lightbox="mygallery">
                          <img class="img-fluid" src="{{ $image->getFullUrl() }}"  style="width:80px;height: 80px;">
                           </a>
                           <?php }else{ ?>
                              <p>
                                <a href="{{ $image->getFullUrl()}}" download>Download File</a>
                              </p>
                           <?php } 

                            ?>
                         
                           <a class="btn btn-danger" href="{{route('deletImages', ['id' => $media_id,'expense_id'=>$expense->id])}}" role="button" onclick="return confirm('are you sure do you want delete file')" style="width:5px !important;">X</a>

                            @endforeach  
                        </div>
                  @endif

              
             </div>
<div class="col-md-6">
    <div class="input_section">
                   <label class="col-form-label">{!! trans('panel.expenses.fields.expense_file') !!}</label>
                    
<<<<<<< HEAD
                  
                      <div class=" has-default bmd-form-group">
=======
                   <div class="col-md-10">
                      <div class="has-default bmd-form-group">
>>>>>>> b68dd0aad605bc4dc638f53450281ed2f5f342c8
                         <input type="file" name="expense_file[]" multiple class="form-control">
                     
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
  </div>


  <!-- Custom styles for this page -->
  <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">
  
  <script src="{{ url('/').'/'.asset('lightboxx/js/lightbox-plus-jquery.min.js') }}"></script>




  <script type="text/javascript">

$(document).ready(function(){

    $('#expenses_type').change(function() { 
    var type = $(this).children(":selected").data('allowtype');
    var rate = $(this).children(":selected").data('rate');
    var total_kms = $('#total_kms').val();
    var total_clm = $('#total_clm').val();

  
    if(total_kms){
    var tol_amt = rate;
    }else{ 
    var tol_amt = total_clm; 
    }


    if(type == '1'){ 
    $('.km').show()
    $('.claim').prop("readonly", true) 
    $('#rate').val(rate);
    $('.rate').prop("readonly", true) 
    }else if(type == '2'){ 
    $('.km').hide()
    //$('.km').prop("disabled", true) 
    //$('.claim').prop("disabled", false) 
    $('.claim').prop("readonly", false) 
     $('.final_claim').val(tol_amt);
    }else{
    $('.km').hide()
    //$('.km').prop("disabled", true) 
    }


     $(".calcu").keyup(function () { 
       var start_km = $('#start_km').val();
       var stop_km = $('#stop_km').val();
       var totalkl = parseFloat(start_km)-parseFloat(stop_km); 
       var percentage = parseFloat((totalkl).toFixed(2));
       var finalval = Math.abs(percentage);

       var total_km  = $(".total_km").val(finalval);
       var rate_new = $('#rate').val();
       var rate_new_data = parseFloat(rate_new);

       var final_claim = parseFloat(((finalval * rate_new_data)).toFixed(2));
       var types = $('#expenses_type').children(":selected").data('allowtype');
       if(types =='2'){
       var claim_new = $('.final_claim').val();
       $(".final_claim").val(parseFloat(claim_new)); 
       }else{
       $(".final_claim").val(final_claim); 

       }

    

     }).trigger('keyup');


 }).trigger('change');


});
</script>



<script type="text/javascript">

    // for get expense type
  $('#user_id').change(function() {
    var user_id = $(this).val();
    var expenses_type = $('#expenses_type_id').val();
    

    $.post("{{ route('getexpenseUserTypeEdit') }}",{
        'user_id':user_id,
        'expenses_type':expenses_type,
        '_token':"{{ csrf_token() }}"
        },function(response){

          // var select = $('#expenses_type');
          // select.empty();
          // select.append(response);
          //select.selectpicker('refresh'); 

        $('#expenses_type').html(response);
        $('#expenses_type').val("{{$expense->expenses_type??''}}");


    })


  setTimeout(function() {
          $('#expenses_type').change();

            }, 1000);

   
 }).trigger('change');
    
</script>





  <script src="{{ url('/').'/'.asset('assets/js/validation_expenses_type.js') }}"></script>
</x-app-layout> 