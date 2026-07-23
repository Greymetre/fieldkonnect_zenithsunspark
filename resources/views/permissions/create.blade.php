<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{{ trans('panel.global.create') }} {{ trans('panel.permission.title_singular') }}
          <span class="pull-right">
            <div class="btn-group">
              @if(auth()->user()->can(['permission_access']))
              <a href="{{ url('permissions') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.permission.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
        <form method="POST" action="{{ route("permissions.store") }}" enctype="multipart/form-data" id="storePermissionData">
            @csrf

<div class="row">
             <div class="col-md-12">
            <div class="input_Section">
              <label class="col-form-label">{{ trans('panel.permission.fields.title') }}<span class="text-danger"> *</span></label>
             
                <div class="form-group has-default bmd-form-group">
                  <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="name" id="title" value="{{ old('name', '') }}" maxlength="200" required>
                  @if($errors->has('title'))
                      <div class="invalid-feedback">
                          {{ $errors->first('title') }}
                      </div>
                  @endif
                </div>
              </div>
            </div>
        <div class="col-md-12 pull-right">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
        </div>
        {{ Form::close() }} 
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/validation_users.js') }}"></script>
</x-app-layout>