<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-tabs card-header-warning">
        <div class="nav-tabs-navigation">
          <div class="nav-tabs-wrapper">
            <h4 class="card-title ">{{ trans('global.edit') }} {{ trans('cruds.permission.title_singular') }}
            @if(auth()->user()->can(['permission_access']))
            <ul class="nav nav-tabs pull-right" data-tabs="tabs">
              <li class="nav-item">
                <a class="nav-link" href="{{ url('permissions') }}">
                  <i class="material-icons">next_plan</i> {!! trans('panel.city.title') !!}
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
         <form method="POST" action="{{ route('permissions.update', [$permission->id]) }}" enctype="multipart/form-data" id="storePermissionData">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="title">{{ trans('cruds.permission.fields.title') }}</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', $permission->title) }}" required>
                @if($errors->has('title'))
                    <div class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.permission.fields.title_helper') }}</span>
            </div>
        <div class="card-footer">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
        </div>
        {{ Form::close() }} 
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/validation_users.js') }}"></script>
</x-app-layout>