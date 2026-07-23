<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-tabs card-header-warning">
        <div class="nav-tabs-navigation">
          <div class="nav-tabs-wrapper">
            <h4 class="card-title ">{!! trans('panel.status.title_singular') !!}
            @if(auth()->user()->can(['status_access']))
            <ul class="nav nav-tabs pull-right" data-tabs="tabs">
              <li class="nav-item">
                <a class="nav-link" href="{{ url('status') }}">
                  <i class="material-icons">next_plan</i> {!! trans('panel.status.title') !!}
                  <div class="ripple-container"></div>
                </a>
              </li>
            </ul>
            @endif
          </h4>
          </div>
        </div>
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
        {!! Form::model($status,[
            'route' => $status->exists ? ['status.update', encrypt($status->id)] : 'status.store',
            'method' => $status->exists ? 'PUT' : 'POST',
            'id' => 'createCategory',
            'files'=>true
          ]) !!}
          <div class="row">
            <div class="col-md-6">
                <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.status.fields.status_name') !!} </label>
                    <input type="text" name="status_name" class="form-control" value="{!! old( 'status_name', $status['status_name']) !!}" >
                  @if ($errors->has('status_name'))
                    <div class="error"><p class="text-danger">{{ $errors->first('status_name') }}</p></div>
                  @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.status.fields.status_message') !!}</label>
                    <input type="text" name="status_message" class="form-control" value="{!! old( 'status_message', $status['status_message']) !!}" >
                    @if ($errors->has('status_message'))
                    <div class="error"><p class="text-danger">{{ $errors->first('status_message') }}</p></div>
                  @endif
                </div>
            </div>

               <div class="col-md-6">
                <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.status.fields.display_name') !!} </label>
                    <input type="text" name="display_name" class="form-control" value="{!! old( 'display_name', $status['display_name']) !!}" >
                  @if ($errors->has('display_name'))
                    <div class="error"><p class="text-danger">{{ $errors->first('display_name') }}</p></div>
                  @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="input_section">
                    <label class="col-form-label">{!! trans('panel.status.fields.module') !!}</label>
                    <select class="form-control select2 module" name="module" style="width: 100%;" required >
                       <option value="">Select {!! trans('panel.status.fields.module') !!}</option>
                       <option value="Customer" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Customer' ? 'selected' : '' }}>Customer</option>
                       <option value="Order" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Order' ? 'selected' : '' }}>Order</option>
                       <option value="LeadStatus" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'LeadStatus' ? 'selected' : '' }}>Lead Status</option>
                        <option value="Coupons" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Coupons' ? 'selected' : '' }}>Coupons</option>
                        <option value="Campaign Status" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Campaign Status' ? 'selected' : '' }}>Campaign Status</option>
                        <option value="Payment Status" {{ old( 'module' , (!empty($status->module))?($status->module):('') ) == 'Payment Status' ? 'selected' : '' }}>Payment Status</option>
                    </select>
                    @if ($errors->has('module'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('module') }}</p></div>
                  @endif
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
</x-app-layout>
