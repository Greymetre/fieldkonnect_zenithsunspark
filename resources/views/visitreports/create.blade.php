<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card pt-0 mt-0">
      <div class="card-header m-0 card-header-tabs card-header-warning">
        <div class="nav-tabs-navigation">
          <div class="nav-tabs-wrapper new_id">
            <h4 class="card-title ">{!! trans('panel.visittype.title_singular') !!}    </h4>
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
        
        {!! Form::model($visitreports,[
          'route' => $visitreports->exists ? ['visitreports.update', encrypt($visitreports->id)] : 'visitreports.store',
          'method' => $visitreports->exists ? 'PUT' : 'POST',
          'id' => 'createvisittype',
          'files'=>true
        ]) !!}
        <div class="row">

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
