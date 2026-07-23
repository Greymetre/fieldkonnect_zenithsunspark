<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} {{ trans('panel.expenses.title_singular') }}
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['expense_access']))
                <a href="{{ url('expenses') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.expenses_type.title_singular') !!} {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          <form method="POST" action="{{ route("expenses.store") }}" enctype="multipart/form-data" id="storeExpenses">
            @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.user') }}<span class="text-danger"> *</span></label>
                  
                    <div class="form-group has-default bmd-form-group">
                      <select name="user_id" id="user_id" class="form-control {{ $errors->has('user_id') ? 'is-invalid' : '' }} select2">
                        <option value="" disabled selected>Please Select User</option>
                        @foreach($users as $k=>$user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('user_id'))
                      <div class="invalid-feedback">
                        {{ $errors->first('user_id') }}
                      </div>
                      @endif
                    </div>
                
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.date') }}<span class="text-danger"> *</span></label>
                  
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Expenses date" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }} datepicker" type="text" name="date" id="date" value="{{ old('date', '') }}" maxlength="200" required autocomplete="off">
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
               
                    <div class="form-group has-default bmd-form-group">
                      <select name="expenses_type" id="expenses_type" class="form-control {{ $errors->has('expenses_type') ? 'is-invalid' : '' }} select2">
                        <option value="" disabled selected>Please Select Expenses Type</option>

                        <!--  @foreach($expensestypes as $key=>$expensestype)
                        <option value="{{$expensestype->id}}" data-allowtype="{{$expensestype->allowance_type_id}}" data-rate ="{{$expensestype->rate}}">{{$expensestype->name}}</option>
                        @endforeach  -->

                      </select>
                      @if($errors->has('expenses_type'))
                      <div class="invalid-feedback">
                        {{ $errors->first('expenses_type') }}
                      </div>
                      @endif
                    </div>
                
                </div>
              </div>

              <div class="col-md-6 km" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.rate') }}<span class="text-danger"> *</span></label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Rate" class="form-control {{ $errors->has('rate') ? 'is-invalid' : '' }}  rate" type="text" name="rate" id="rate" value="{{ old('rate', '') }}"  autocomplete="off">
                      @if($errors->has('rate'))
                      <div class="invalid-feedback">
                        {{ $errors->first('rate') }}
                      </div>
                      @endif
                    </div>
                 
                </div>
              </div>





              <div class="col-md-6 km" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.start_km') }}<span class="text-danger"> *</span></label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Start Km" class="form-control {{ $errors->has('start_km') ? 'is-invalid' : '' }} calcu" type="text" name="start_km" id="start_km" value="{{ old('start_km', '') }}" maxlength="200" autocomplete="off">
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
                  
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Stop Km" class="form-control {{ $errors->has('stop_km') ? 'is-invalid' : '' }} calcu" type="text" name="stop_km" id="stop_km" value="{{ old('stop_km', '') }}" maxlength="200" autocomplete="off">
                      @if($errors->has('stop_km'))
                      <div class="invalid-feedback">
                        {{ $errors->first('stop_km') }}
                      </div>
                      @endif
                    </div>
             
                </div>
              </div>
              <div class="col-md-6 km" style="display:none;">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.total_km') }}<span class="text-danger"> *</span></label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Total Km" class="form-control {{ $errors->has('total_km') ? 'is-invalid' : '' }} total_km claim" type="text" name="total_km" id="total_km" value="{{ old('total_km', '') }}" maxlength="200" autocomplete="off">
                      @if($errors->has('total_km'))
                      <div class="invalid-feedback">
                        {{ $errors->first('total_km') }}
                      </div>
                      @endif
                    </div>
                
                </div>
              </div>


              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.claim_amount') }}</label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <input class="form-control {{ $errors->has('claim_amount') ? 'is-invalid' : '' }} claim final_claim" type="" name="claim_amount" id="claim_amount" value="{{ old('claim_amount', '') }}" pattern="^\d*(\.\d{0,2})?$" placeholder="Claim Amount">
                      @if($errors->has('claim_amount'))
                      <div class="invalid-feedback">
                        {{ $errors->first('claim_amount') }}
                      </div>
                      @endif
                    </div>
             
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses.fields.note') }}<span class="text-danger"> *</span></label>
                
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Note" class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }} " type="text" name="note" id="note" value="{{ old('note', '') }}"  required autocomplete="off">
                      @if($errors->has('note'))
                      <div class="invalid-feedback">
                        {{ $errors->first('note') }}
                      </div>
                      @endif
                    
                  </div>
                </div>
              </div>

         
              <div class="col-md-6">
                <div class="input_section">
                   <label class="col-form-label">{!! trans('panel.expenses.fields.expense_file') !!}</label>
                  
                 
                      <div class=" has-default bmd-form-group">
                         <input type="file" name="expense_file[]" multiple class="form-control">
                      </div>
                  
                </div>
             </div>
          
            </div>


            <div class="pull-right">
              {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
            </div>
            {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">

$(document).ready(function(){

    $('#expenses_type').change(function() {

    var type = $(this).children(":selected").data('allowtype');
    var rate = $(this).children(":selected").data('rate');
    if(type == '1'){
    $('.km').show()
    $('.claim').prop("readonly", true) 
    $('#rate').val(rate);
    $('.rate').prop("readonly", true) 
    }else if(type == '2'){
    $('.km').hide()
     //$('.km').prop("disabled", true) 
     //$('.claim').prop("disabled", false) 
    $('.final_claim').val(rate);
    $('.claim').prop("readonly", false) 
    }else{
    $('.km').hide()
    $('.km').prop("disabled", true) 
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

        $(".final_claim").val(final_claim);


     });


 });

});
</script>


<script type="text/javascript">

    // for get expense type
 $('#user_id').change(function() {
    var user_id = $(this).val();
    
    $.post("{{ route('getexpenseUserType') }}",{
        'user_id':user_id,
        '_token':"{{ csrf_token() }}"
        },function(response){

          var select = $('#expenses_type');
          select.empty();
          select.append(response);
          //select.selectpicker('refresh');              

    })
   
 }).trigger('change');
    
</script>

<script src="{{ url('/').'/'.asset('assets/js/validation_expenses_type.js') }}"></script>
</x-app-layout> 