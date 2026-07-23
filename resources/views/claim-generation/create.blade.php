<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Claim Generation
            <span class="pull-right">
              <div class="btn-group">
                <!-- @if(auth()->user()->can(['product_access'])) -->
                <a href="{{ url('claim-generation') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.product.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                <!-- @endif -->
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
          {!! Form::model($claimGeneration,[
          'route' => $claimGeneration->exists ? ['claim-generation.update', encrypt($claimGeneration->id) ] : 'claim-generation.store',
          'method' => $claimGeneration->exists ? 'PUT' : 'POST',
          'id' => 'createClaimForm',
          'files'=>true
          ]) !!}

          <div class="row">
            <div class="col-12" >
                <h4 class="mb-3" style="color: #7c7c7c;font-weight: bold !important;">{{isset($claimGeneration->service_center_details) ?  $claimGeneration->service_center_details->name : ''}} - {{$claimGeneration->claim_number ?? ''}}</h4>
                <hr>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">ASC's Bill No<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control" name="asc_bill_no" id="asc_bill_no" placeholder="ASC's Bill No" value="{{$claimGeneration->asc_bill_no ?? ''}}">
                  @if ($errors->has('asc_bill_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('asc_bill_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">ASC's Bill Date<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control datepicker" name="asc_bill_date" id="asc_bill_date" readonly placeholder="ASC's Bill Date" value="{{isset($claimGeneration->asc_bill_date) ? cretaDateForFront($claimGeneration->asc_bill_date) : ''}}">
                  @if ($errors->has('asc_bill_date'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('asc_bill_date') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
             <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">ASC's Bill Amount<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control" name="asc_bill_amount" id="asc_bill_amount" placeholder="ASC's Bill Amount" value="{{$claimGeneration->asc_bill_amount ?? ''}}">
                  @if ($errors->has('asc_bill_amount'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('asc_bill_amount') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Courier Date<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control datepicker" name="courier_date" id="courier_date" readonly placeholder="Courier Date" value="{{isset($claimGeneration->courier_date) ? cretaDateForFront($claimGeneration->courier_date) : ''}}" >
                  @if ($errors->has('courier_date'))  
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('courier_date') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
                <div class="input_section">
                    <label class="col-form-label">Courier Details<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                        <textarea class="form-control" name="courier_details" id="courier_details" rows="3" placeholder="Enter courier details">{{ $claimGeneration->courier_details ?? '' }}</textarea>
                        @if ($errors->has('courier_details'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('courier_details') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input_section">
                    <label class="col-form-label">Claim Sattlement Details<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                        <textarea class="form-control" name="claim_sattlement_details" id="claim_sattlement_details" rows="3" placeholder="Enter courier details">{{ $claimGeneration->claim_sattlement_details ?? '' }}</textarea>
                        @if ($errors->has('claim_sattlement_details'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('claim_sattlement_details') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input_section">
                    <label class="col-form-label">Submitted By SE <span class="text-danger">*</span></label>
                    <div class="form-group has-default bmd-form-group">
                         <div class="form-group has-default bmd-form-group">
                          <input type="radio" class="" name="submitted_by_se" id="activeY" {{isset($claimGeneration->submitted_by_se) && $claimGeneration->submitted_by_se == '1' ? 'checked' : '' }}  value="1"><span class="yes_no">Yes</span>
                        </div>
                        <div class="form-group has-default bmd-form-group">
                             <input type="radio" name="submitted_by_se" id="activeN" value="0" 
                                {{ !isset($claimGeneration->submitted_by_se) || $claimGeneration->submitted_by_se == '0' ? 'checked' : '' }}>
                            <span class="yes_no">No</span>
                        </div>
                        @if ($errors->has('submitted_by_se'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('submitted_by_se') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="input_section">
                    <label class="col-form-label">Claim Approved <span class="text-danger">*</span></label>
                    <div class="form-group has-default bmd-form-group">
                         <div class="form-group has-default bmd-form-group">
                          <input type="radio" class="" name="claim_approved"   id="claim_approved_Y"  value="1" {{isset($claimGeneration->submitted_by_se) && $claimGeneration->submitted_by_se == '1' ? 'checked' : '' }}><span class="yes_no">Yes</span>
                        </div>
                        <div class="form-group has-default bmd-form-group">
                          <input type="radio" class="" name="claim_approved" id="claim_approved_N"  value="0"><span class="yes_no" {{ !isset($claimGeneration->claim_approved) || $claimGeneration->claim_approved == '0' ? 'checked' : '' }}>No</span>
                        </div>
                        @if ($errors->has('claim_approved'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('claim_approved') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input_section">
                    <label class="col-form-label"> Claim Done<span class="text-danger">*</span></label>
                    <div class="form-group has-default bmd-form-group">
                         <div class="form-group has-default bmd-form-group">
                          <input type="radio" class="" name="claim_done" id="claim_done_Y"  value="1" {{isset($claimGeneration->claim_done) && $claimGeneration->claim_done == '1' ? 'checked' : '' }}><span class="yes_no">Yes </span>
                        </div>
                        <div class="form-group has-default bmd-form-group">
                          <input type="radio" class="" name="claim_done" id="claim_done_N"  value="0" {{ !isset($claimGeneration->claim_done) || $claimGeneration->claim_done == '0' ? 'checked' : '' }}><span class="yes_no">No</span>
                        </div>
                        @if ($errors->has('claim_approved'))
                            <div class="error">
                                <p class="text-danger">{{ $errors->first('claim_done') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


          </div>
          <div class="pull-right col-md-12">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
          </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  </div>
<script>
  $(document).ready(function (){
       $('#createClaimForm').validate({
        rules:{
          asc_bill_no:{
            required:true,
          },
          asc_bill_date : {
            required : true,
          },
          asc_bill_amount : {
            required : true,
            number : true
          },
          courier_date:
          {
            required:true,
          },
          courier_details : {
            required : true,
          },
          claim_sattlement_details : {
            required : true,
          }
        },
        errorPlacement: function(error, element) {
            error.addClass('text-danger'); // Add Bootstrap error styling
            error.insertAfter(element.closest('.form-group')); // Insert after the select field
        },
        highlight: function(element) {
            $(element).addClass('is-invalid'); // Highlight error
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid'); // Remove error highlight
        }
      });

      $("#asc_bill_date").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: 0, // Disable future dates
      });

      $("#courier_date").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: 0, // Disable future dates
      });
  })
</script>
</x-app-layout>