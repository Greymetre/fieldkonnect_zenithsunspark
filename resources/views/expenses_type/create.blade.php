<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} {{ trans('panel.expenses_type.title_singular') }}
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['expenses_type']))
                <a href="{{ url('expenses_type') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.expenses_type.title_singular') !!} {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          <form method="POST" action="{{ route("expenses_type.store") }}" enctype="multipart/form-data" id="storeExpensesTypeData">
            @csrf
            <div class="row">


            <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses_type.fields.pay_roll') }}<span class="text-danger"> *</span></label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <select name="payroll_id" id="payroll_id" class="form-control {{ $errors->has('payroll_id') ? 'is-invalid' : '' }}">
                        <option value="" disabled selected>Please select pay roll</option>
                        @foreach($pay_rolls as $key=>$payroll)
                        <option value="{{$key}}">{{$payroll}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('name'))
                      <div class="invalid-feedback">
                        {{ $errors->first('payroll_id') }}
                      </div>
                      @endif
                    </div>
                
                </div>
              </div>






              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses_type.fields.allowance_type') }}<span class="text-danger"> *</span></label>
                
                    <div class="form-group has-default bmd-form-group">
                      <select name="allowance_type_id" id="allowance_type_id" class="form-control {{ $errors->has('allowance_type_id') ? 'is-invalid' : '' }}">
                        <option value="" disabled selected>Please select allowance type</option>
                        @foreach($allowance_type as $k=>$val)
                        <option value="{{$k}}">{{$val}}</option>
                        @endforeach
                      </select>
                      @if($errors->has('name'))
                      <div class="invalid-feedback">
                        {{ $errors->first('allowance_type_id') }}
                      </div>
                      @endif
                    </div>
             
                </div>
              </div>

              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses_type.fields.name') }}<span class="text-danger"> *</span></label>
                
                    <div class="form-group has-default bmd-form-group">
                      <input placeholder="Expenses type name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" maxlength="200" required>
                      @if($errors->has('name'))
                      <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                      </div>
                      @endif
                   
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label">{{ trans('panel.expenses_type.fields.rate') }}</label>
                 
                    <div class="form-group has-default bmd-form-group">
                      <input class="form-control {{ $errors->has('rate') ? 'is-invalid' : '' }}" type="" name="rate" id="rate" value="{{ old('rate', '') }}" pattern="^\d*(\.\d{0,2})?$" placeholder="0.00">
                      @if($errors->has('display_name'))
                      <div class="invalid-feedback">
                        {{ $errors->first('rate') }}
                      </div>
                      @endif
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
  <script src="{{ url('/').'/'.asset('assets/js/validation_expenses_type.js') }}"></script>
</x-app-layout>