<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{{ trans('panel.global.create') }} {!! trans('panel.brand.title_singular') !!}
          <span class="pull-right">
            <div class="btn-group">
              @if(auth()->user()->can(['brand_access']))
              <a href="{{ url('brands') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.brand.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
        
        {!! Form::model($brands,[
            'route' => $brands->exists ? ['brands.update', encrypt($brands->id)] : 'brands.store',
            'method' => $brands->exists ? 'PUT' : 'POST',
            'id' => 'createCategory',
            'files'=>true
          ]) !!}
          <div class="row">
            <div class="col-md-8">
                <div class="input_section">
                  <label class="col-form-label">{!! trans('panel.brand.fields.brand_name') !!} <span class="text-danger"> *</span></label>
                
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="brand_name" class="form-control" value="{!! old( 'brand_name', $brands['brand_name']) !!}" maxlength="200" required>
                      @if ($errors->has('brand_name'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('brand_name') }}</p></div>
                      @endif
                    </div>
                
                </div>
              </div>
            <div class="col-md-3 col-sm-3">
              <div class="input_section">
              <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                   <div class="selectThumbnail">
                     <span class="btn btn-just-icon btn-round btn-file">
                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                       <span class="fileinput-exists">Change</span>
                       <input type="file" name="image" class="getimage1">
                     </span>
                     <br>
                     <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                   </div>
                   <div class="fileinput-new thumbnail">
                     <img src="{!! ($brands['brand_image']) ? asset($brands['brand_image']) : asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                   </div>
                   <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                   <label class="bmd-label-floating">{!! trans('panel.brand.fields.brand_image') !!}</label>
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
