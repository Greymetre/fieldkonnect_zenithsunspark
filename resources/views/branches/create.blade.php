<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{{ trans('panel.global.create') }} {!! trans('panel.branch.title_singular') !!}
          <span class="pull-right">
            <div class="btn-group">
              @if(auth()->user()->can(['brand_access']))
              <a href="{{ url('branches') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.branch.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
              @endif
            </div>
          </span>
        </h4>
      </div>
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
      <div class="card-body">
        
        {!! Form::model($branches,[
            'route' => $branches->exists ? ['branches.update', encrypt($branches->id)] : 'branches.store',
            'method' => $branches->exists ? 'PUT' : 'POST',
            'id' => 'createBranch',
            'files'=>true
          ]) !!}
          <div class="row">
            <div class="col-md-8">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.branch.fields.branch_name') !!} <span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="branch_name" class="form-control" value="{!! old( 'branch_name', $branches['branch_name']) !!}" maxlength="200" required>
                      @if ($errors->has('branch_name'))
                        <div class="error"><p class="text-danger">{{ $errors->first('branch_name') }}</p></div>
                      @endif
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.branch.fields.branch_code') !!} <span class="text-danger"> *</span></label>       
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="branch_code" class="form-control" value="{!! old( 'branch_code', $branches['branch_code']) !!}" maxlength="200" required>
                      @if ($errors->has('branch_code'))
                        <div class="error"><p class="text-danger">{{ $errors->first('branch_code') }}</p></div>
                      @endif
                  </div>
                </div>
              </div>

          </div>
        <div class="card-footer pull-right">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
        </div>
        {{ Form::close() }} 
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
</x-app-layout>
