<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-tabs card-header-warning">
        <div class="nav-tabs-navigation">
          <div class="nav-tabs-wrapper">
            <h4 class="card-title ">{!! trans('panel.coupon.title_singular') !!}
             @if(auth()->user()->can(['coupon_access']))
            <ul class="nav nav-tabs pull-right" data-tabs="tabs">
              <li class="nav-item">
                <a class="nav-link" href="{{ url('coupons') }}">
                  <i class="material-icons">next_plan</i> {!! trans('panel.coupon.title') !!}
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
         {!! Form::model($coupons,[
            'route' => $coupons->exists ? ['coupons.update', encrypt($coupons->id) ] : 'coupons.store',
            'method' => $coupons->exists ? 'PUT' : 'POST',
            'id' => 'storeCouponsData',
            'files'=>true
          ]) !!}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="bmd-label-floating">{!! trans('panel.coupon_profile.profile_name') !!} <span class="text-danger"> *</span></label>
                <input type="text" name="profile_name" class="form-control" value="{!! old( 'profile_name', $coupons['profile_name']) !!}" >
                @if ($errors->has('profile_name')) 
                  <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('profile_name') }}</p></div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
               <div class="form-group">
                  <label class="bmd-label-floating">{!! trans('panel.coupon_profile.excluding_character') !!} </label>
                  <input type="text" name="excluding_character" class="form-control" value="{!! old( 'excluding_character', $coupons['excluding_character']) !!}" >
                @if ($errors->has('excluding_character'))
                  <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('excluding_character') }}</p></div>
                @endif
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="bmd-label-floating">{!! trans('panel.coupon_profile.coupon_length') !!} <span class="text-danger"> *</span></label>
                  <input type="text" name="coupon_length" class="form-control" value="{!! old( 'coupon_length', $coupons['coupon_length']) !!}" >
                  @if ($errors->has('coupon_length'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('coupon_length') }}</p></div>
                  @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="bmd-label-floating">{!! trans('panel.coupon_profile.coupon_count') !!} <span class="text-danger"> *</span></label>
                  <input type="text" name="coupon_count" class="form-control" value="{!! old( 'coupon_count', $coupons['coupon_count']) !!}" >
                @if ($errors->has('coupon_count'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('coupon_count') }}</p></div>
                @endif
              </div>
            </div>
            </div>
          </div>
        <div class="card-footer">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
        </div>
        {{ Form::close() }} 
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
</x-app-layout>
