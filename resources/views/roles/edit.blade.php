<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card mt-0 pt-0">
        <div class="card-header m-0 card-header-tabs card-header-warning">
          <div class="nav-tabs-navigation">
            <div class="nav-tabs-wrapper new_id">
              <h4 class="card-title ">{{ trans('panel.global.edit') }} {{ trans('panel.role.title_singular') }}   </h4>
                @if(auth()->user()->can(['role_access']))
                <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ url('roles') }}">
                      <i class="material-icons">next_plan</i> {!! trans('panel.role.title') !!}
                      <div class="ripple-container"></div>
                    </a>
                  </li>
                </ul>
                @endif
            
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
          <form method="POST" action="{{ route('roles.update', [$role->id]) }}" enctype="multipart/form-data" id="storeRoleData">
            @method('PUT')
            @csrf
            <input type="hidden" name="id" id="role_id" value="{!! $role->id !!}">
            <div class="row">
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label required" for="name">{{ trans('panel.role.fields.name') }}</label>
                  <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required>
                  @if($errors->has('name'))
                  <div class="invalid-feedback">
                    {{ $errors->first('name') }}
                  </div>
                  @endif
                  <span class="help-block">{{ trans('panel.role.fields.name_helper') }}</span>

                </div>
              </div>
              <div class="col-md-6">
                <div class="input_section">
                  <label class="col-form-label required" for="display_name">{{ trans('panel.role.fields.display_name') }}</label>
                  <input class="form-control {{ $errors->has('display_name') ? 'is-invalid' : '' }}" type="text" name="display_name" id="display_name" value="{{ old('name', $role->name) }}" required>
                  @if($errors->has('display_name'))
                  <div class="invalid-feedback">
                    {{ $errors->first('display_name') }}
                  </div>
                  @endif
                  <span class="help-block">{{ trans('panel.role.fields.name_helper') }}</span>

                </div>
              </div>
              <div class="col-md-12">
                <div class="input_section">
                  <label class="col-form-label required" for="permissions">{{ trans('panel.role.fields.permissions') }}</label>
                  <div style="padding-bottom: 4px">
                    <span class="btn btn-info btn-xs select-all" style="border-radius: 0">{{ trans('panel.global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all" style="border-radius: 0">{{ trans('panel.global.deselect_all') }}</span>
                  </div>
                  <select class="form-control select2 {{ $errors->has('permissions') ? 'is-invalid' : '' }}" name="permissions[]" id="permissions" multiple required>
                    @foreach($permissions as $id => $permissions)
                    <option value="{{ $id }}" {{ (in_array($id, old('permissions', [])) || $role->permissions->contains($id)) ? 'selected' : '' }}>{{ $permissions }}</option>
                    @endforeach
                  </select>
                  @if($errors->has('permissions'))
                  <div class="invalid-feedback">
                    {{ $errors->first('permissions') }}
                  </div>
                  @endif
                  <span class="help-block">{{ trans('panel.role.fields.permissions_helper') }}</span>
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
  <script>
    $(".select-all").on("click", function() {
      if (confirm("Are you sure want to give all permission?")) {
        $('#permissions option').prop('selected', true);
        $('#permissions').trigger('change');
      }
    })
    $(".deselect-all").on("click", function() {
      if (confirm("Are you sure want to remove all permission?")) {
        $('#permissions option').prop('selected', false);
        $('#permissions').trigger('change');
      }
    })
  </script>
</x-app-layout>