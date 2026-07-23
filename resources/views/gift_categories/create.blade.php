<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{{ trans('panel.global.create') }} {!! trans('panel.category.title_singular') !!}   </h4>
          <span class="pull-right">
            <div class="btn-group">
              @if(auth()->user()->can(['category_access']))
              <a href="{{ url('categories') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.category.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
              @endif
            </div>
          </span>
     
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
        {!! Form::model($categories,[
            'route' => $categories->exists ? ['categories.update', encrypt($categories->id)] : 'categories.store',
            'method' => $categories->exists ? 'PUT' : 'POST',
            'id' => 'createCategory',
            'files'=>true
          ]) !!}
          <input type="hidden" name="category_id" name="category_id" value="{!! $categories->id !!}">
          <div class="row">
            <div class="col-md-6">
                <div class="input_Section">
                  <label class="col-form-label">{!! trans('panel.category.fields.category_name') !!} <span class="text-danger"> *</span></label>
                  
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="category_name" class="form-control" value="{!! old( 'category_name', $categories['category_name']) !!}" maxlength="200" required>
                      @if ($errors->has('category_name'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('category_name') }}</p></div>
                      @endif
                    </div>
                 
                </div>
              </div>
            <div class="col-md-3 col-sm-3">
              <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                
                   <div class="fileinput-new thumbnail">
                     <img src="{!! ($categories['category_image']) ? asset($categories['category_image']) : asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                        <div class="selectThumbnail">
                     <span class="btn btn-just-icon btn-round btn-file">
                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                       <span class="fileinput-exists">Change</span>
                       <input type="file" name="image" class="getimage1">
                     </span>
                     <br>
                     <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                   </div>
                   </div>
                   <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                   <label class="bmd-label-floating">{!! trans('panel.category.fields.category_image') !!}</label>
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
<script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
</x-app-layout>
