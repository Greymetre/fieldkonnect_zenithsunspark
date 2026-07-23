<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{{ trans('panel.global.create') }} {{ trans('panel.role.title_singular') }}
          <span class="pull-right">
            <div class="btn-group">
              @if(auth()->user()->can(['role_access']))
              <a href="{{ url('roles') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.role.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
         <form method="POST" action="{{ route("roles.store") }}" enctype="multipart/form-data" id="storeRoleData">
            @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="inpu_section">
                <label class="col-form-label">{{ trans('panel.role.fields.name') }}<span class="text-danger"> *</span></label>
               
                  <div class="form-group has-default bmd-form-group">
                      <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="name" id="title" value="{{ old('name', '') }}" maxlength="200" required>
                      @if($errors->has('name'))
                          <div class="invalid-feedback">
                              {{ $errors->first('name') }}
                          </div>
                      @endif
                  
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="inpu_section">
                <label class="col-form-label">{{ trans('panel.role.fields.display_name') }}<span class="text-danger"> *</span></label>
          
                  <div class="form-group has-default bmd-form-group">
                      <input class="form-control {{ $errors->has('display_name') ? 'is-invalid' : '' }}" type="text" name="display_name" id="display_name" value="{{ old('display_name', '') }}" maxlength="200" required>
                      @if($errors->has('display_name'))
                          <div class="invalid-feedback">
                              {{ $errors->first('display_name') }}
                          </div>
                      @endif
                  </div>
                </div>
             
            </div>
            <div class="col-md-6">
              <div class="inpu_section">
                <label class="col-form-label">{{ trans('panel.role.fields.permissions') }}<span class="text-danger"> *</span></label>
            
                  <div style="padding-bottom: 4px">
                        <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('panel.select_all') }}</span>
                        <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('panel.deselect_all') }}</span>
                    </div>
                  <div class="form-group has-default bmd-form-group">
                      <select class="form-control select2 {{ $errors->has('permissions') ? 'is-invalid' : '' }}" name="permissions[]" id="permissions" multiple required>
                        @foreach($permissions as $id => $permissions)
                            <option value="{{ $id }}" {{ in_array($id, old('permissions', [])) ? 'selected' : '' }}>{{ $permissions }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('permissions'))
                        <div class="invalid-feedback">
                            {{ $errors->first('permissions') }}
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
<script src="{{ url('/').'/'.asset('assets/js/validation_users.js') }}"></script>
</x-app-layout>