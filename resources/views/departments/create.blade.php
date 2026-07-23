<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ isset($departments->id)?'Edit':'Create' }} {!! trans('panel.departments.title_singular') !!}
              <span class="pull-right">
                <div class="btn-group">
                  <!-- @if(auth()->user()->can(['customer_access']))  -->
                   <a href="{{ url('departments') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.departments.title_singular') !!} {!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a> 
                  <!-- @endif -->
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
            {!! Form::model($departments,[
            'route' => $departments->exists ? ['departments.update', $departments->id] : 'departments.store',
            'method' => $departments->exists ? 'PUT' : 'POST',
            'files'=>true
            ]) !!}
            <input type="hidden" name="id" id="subdivision_id" value="{!! $departments['id'] !!}">
            <div class="row">
              <div class="col-md-12">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.departments.title_singular') !!}<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="name" class="form-control" value="{!! old( 'name', $departments['name']) !!}" maxlength="200" required>
                      @if ($errors->has('name'))
                        <div class="error"><p class="text-danger">{{ $errors->first('name') }}</p></div>
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
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/validation_customers.js') }}"></script>
<script type="text/javascript">
   $(function () {
      //Initialize Select2 Elements
      $('.select2').select2()
   
      //Initialize Select2 Elements
      $('.select2bs4').select2({
        theme: 'bootstrap4'
      })
    })
 
</script>



</x-app-layout>