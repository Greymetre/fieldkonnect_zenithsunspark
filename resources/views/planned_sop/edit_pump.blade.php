<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.edit') }} Pumps & Motor Planned S&OP
            <span class="pull-right">
              <div class="btn-group">
                <!-- @if(auth()->user()->can(['product_access'])) -->
                <a href="{{ url('planned-sop') }}" class="btn btn-just-icon btn-theme" title="Planned S&OP"><i class="material-icons">next_plan</i></a>
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
          {!! Form::model($plannedsop,[
          'route' => $plannedsop->exists ? ['planned-sop.update', encrypt($plannedsop->id) ] : 'planned-sop.store',
          'method' => $plannedsop->exists ? 'PUT' : 'POST',
          'id' => 'editesopForm',
          'files'=>true
          ]) !!}

          <div class="row">

          </div>
        <div class="row">
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Month<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                 <input type="text" class="form-control datepicker" id="start_month" 
                         name="planning_month" placeholder="S&OP Month" 
                         autocomplete="off" readonly required
                         value="{{ old('planning_month', \Carbon\Carbon::parse($plannedsop->planning_month)->format('F Y')) }}">
                  @if ($errors->has('planning_month'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('planning_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Division<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="hidden" value="{{$plannedsop->getproduct->category_id ?? ''}}" name="product_division">
                  <select class="form-select select2" name="product_division_1" id="product_division" disabled>
                         <option value=''>Select Division</option>
                         @foreach($divisions as $division)
                              <option value="{{ $division->id }}"  {{isset($plannedsop->getproduct->category_id) && $plannedsop->getproduct->category_id == $division->id ? "Selected" : '' }}>{{ $division->category_name}}</option>
                         @endforeach
                  </select>
                  @if ($errors->has('product_division'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_division') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Branch Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="branch_id" id="branch_id" disabled>
                         <option value=''>Select Branch</option>
                         @foreach($branches as $branch)
                              <option value="{{ $branch->id }}" {{isset($plannedsop->branch_id) && $plannedsop->branch_id == $branch->id ? "Selected" : '' }}>{{ $branch->branch_name}}</option>
                         @endforeach
                  </select>
                  @if ($errors->has('product_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Group Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_group_name" id="product_group_name" value="{{$plannedsop->getproduct->subcategories->subcategory_name ?? ''}}" readonly>
                  @if ($errors->has('product_group_name'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_group_name') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product  Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="hidden" class="form-control" name="price" id="price" value="{{$plannedsop->getproduct->productdetails[0]->price ?? ''}}" readonly>
                <input type="text" class="form-control" name="product_name" id="product_name" value="{{$plannedsop->getproduct->product_name ?? ''}}" readonly>
                  @if ($errors->has('product_name'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_group_name') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Code<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_code" id="product_code" value="{{$plannedsop->getproduct->product_code ?? ''}}" readonly>
                  @if ($errors->has('product_code'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_code') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-12 mt-2">
               <div class="input_section text-center" style="font-size: 16px: !important">
                <label class="col-form-label" style="color: #221606">Last Year Sale</label>
              </div>
            </div>
            <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Apr - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_1" id="year_month_1" value="{{ $plannedsop->primarySale->month_1 ?? ''}}" readonly>
                </div>
              </div>
            </div>
            <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    May - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_2" id="year_month_2" value="{{ $plannedsop->primarySale->month_2 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Jun - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_3" id="year_month_3"value="{{ $plannedsop->primarySale->month_3 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Jul - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_4" id="year_month_4" value="{{ $plannedsop->primarySale->month_4 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Aug - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_5" id="year_month_5" value="{{ $plannedsop->primarySale->month_5 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Sep - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_6" id="year_month_6" value="{{ $plannedsop->primarySale->month_6 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Oct - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_7" id="year_month_7" value="{{ $plannedsop->primarySale->month_7 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Nov - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_8" id="year_month_8" value="{{ $plannedsop->primarySale->month_8 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Dec - <span class="year">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->subYear()->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_9" id="year_month_9" value="{{ $plannedsop->primarySale->month_9 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Jan - <span class="year-next">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_10" id="year_month_10" value="{{ $plannedsop->primarySale->month_10 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Feb - <span class="year-next">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="year_month_11" id="year_month_11" value="{{ $plannedsop->primarySale->month_11 ?? ''}}" readonly>
                </div>
              </div>
            </div>
             <div class="col-md-1">
              <div class="input_section">
                <label class="col-form-label">
                    Mar - <span class="year-next">{{ isset($plannedsop->planning_month) ? \Carbon\Carbon::parse($plannedsop->planning_month)->format('Y') : '' }}</span>
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="hidden" class="form-control" name="min" id="min" value="{{ $plannedsop->primarySale->min ?? ''}}" readonly>
                <input type="hidden" class="form-control" name="max" id="max" value="{{ $plannedsop->primarySale->max ?? ''}}" readonly>
                <input type="hidden" class="form-control" name="avg" id="avg" value="{{ $plannedsop->primarySale->avg ?? ''}}" readonly>
                <input type="text" class="form-control" name="year_month_12" id="year_month_12" value="{{ $plannedsop->primarySale->month_12 ?? ''}}" readonly>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">
                    Forecast qty (Sales plan)
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="plan_next_month" id="plan_next_month" value="{{ $plannedsop->plan_next_month ?? ''}}" placeholder="Enter  Forecast qty (Sales plan)">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">
                    Forecast Value (Sales plan)
                </label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="plan_next_month_value" id="plan_next_month_value" value="{{ $plannedsop->plan_next_month_value ?? ''}}" readonly>
                </div>
              </div>
            </div>
          </div> 
          <div class="pull-right col-md-12">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
<script>
  $(document).on('change' , '#plan_next_month',function(){
    var value = $(this).val();
    if ($.isNumeric(value)) {
        var price = $('#price').val();
        let total = ((price * 0.59 ) * value).toFixed(2);
        $('#plan_next_month_value').val(total);
    } else {
        // Not a number – clear the field
        $(this).val('');
    }

  })
</script>
</x-app-layout>