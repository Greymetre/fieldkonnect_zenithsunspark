<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-tabs card-header-warning">
        <div class="nav-tabs-navigation">
          <div class="nav-tabs-wrapper new_id">
            <h4 class="card-title ">{!! trans('panel.visittype.title_singular') !!}     </h4>
            @if(auth()->user()->can(['visitreport_access']))
            <ul class="nav nav-tabs pull-right" data-tabs="tabs">
              <li class="nav-item">
                <a class="nav-link" href="{{ url('visitreports') }}">
                  <i class="material-icons">next_plan</i> {!! trans('panel.visittype.title') !!}
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
        {!! Form::model($visittypes,[
          'route' => $visittypes->exists ? ['visittypes.update', encrypt($visittypes->id)] : 'visittypes.store',
          'method' => $visittypes->exists ? 'PUT' : 'POST',
          'id' => 'createvisittype',
          'files'=>true
        ]) !!}
         <div class="row">
            <div class="col-md-12">
              <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.visittype.type_name') !!} </label>
                  <input type="text" name="type_name" class="form-control" value="{!! old( 'type_name', $visittypes['type_name']) !!}" >
                @if ($errors->has('type_name'))
                  <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('type_name') }}</p></div>
                @endif
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
</x-app-layout>
