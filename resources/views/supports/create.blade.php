<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-warning card-header-icon">
            <div class="row">
              <div class="col-6">
                <div class="card-icon">
                  <i class="material-icons">category</i>
                </div>
                <h4 class="card-title"> {!! trans('panel.support.create_title') !!}</h4>
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
              {!! Form::model($supports,[
                  'route' => $supports->exists ? ['supports.update', $supports->id] : 'supports.store',
                  'method' => $supports->exists ? 'PUT' : 'POST',
                  'id' => 'storeSupportData',
                  'files'=>true
                ]) !!}
                <input type="hidden" name="full_name" class="form-control fullname">
                <input type="hidden" name="active" class="form-control" value="{!! $supports['active'] !!}">
                <div class="row">
                  <label class="col-md-2 col-form-label">{!! trans('panel.support.subject') !!}<span class="text-danger"> *</span></label>
                  <div class="col-md-10">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="subject" class="form-control subject" value="{!! old( 'subject', $supports['subject']) !!}" required>
                    </div>
                    @if ($errors->has('subject')) 
                      <label class="error">{{ $errors->first('subject') }}</label>
                    @endif
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                      <label class="col-md-3 col-form-label">{!! trans('panel.support.user_id') !!}<span class="text-danger"> *</span></label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                          <select class="form-control select2" name="user_id" style="width: 100%;" required onchange="getUserInfo()">
                             <option value="" selected disabled>Select {!! trans('panel.support.user_id') !!} </option>
                             @if(@isset($users ))
                             @foreach($users as $user)
                             <option value="{!! $user['id'] !!}" {{ old( 'user_id' , (!empty($supports->user_id))?($supports->user_id):('') ) == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                             @endforeach
                             @endif
                          </select>
                        </div>
                        @if ($errors->has('user_id'))
                          <label class="error">{{ $errors->first('user_id') }}</label>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row">
                      <label class="col-md-3 col-form-label">{!! trans('panel.support.priority') !!}<span class="text-danger"> *</span></label>
                      <div class="col-md-9">
                        <div class="form-group has-default bmd-form-group">
                          <select class="form-control select2" name="priority" style="width: 100%;" required>
                             <option value="">Select {!! trans('panel.support.priority') !!} </option>
                             @if(@isset($priorities ))
                             @foreach($priorities as $priority)
                             <option value="{!! $priority['id'] !!}" {{ old( 'priority' , (!empty($supports->priority))?($supports->priority):('') ) == $priority->id ? 'selected' : '' }}>{!! $priority['priority_name'] !!}</option>
                             @endforeach
                             @endif
                          </select>
                        </div>
                        @if ($errors->has('user_id'))
                          <label class="error">{{ $errors->first('user_id') }}</label>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                       <label class="col-sm-3 col-form-label">{!! trans('panel.support.associated') !!} <span class="text-danger"> *</span></label>
                       <div class="col-sm-9">
                          <div class="form-group bmd-form-group">
                             <select class="form-control select2" name="associated[]" style="width: 100%;" multiple="multiple" required>
                              @if(@isset($users ))
                                @foreach($users as $id => $user)
                                  <option value="{!! $user['id'] !!}" {{ (in_array($id, old('associated', [])) || $supports->associatedUsers->contains($id)) ? 'selected' : '' }}>{!! $user['id'].' '.$user['name'] !!}</option>
                                @endforeach
                              @endif
                             </select>
                          </div>
                          @if ($errors->has('associated'))
                          <div class="error col-lg-12">
                             <p class="text-danger">{{ $errors->first('associated') }}</p>
                          </div>
                          @endif
                       </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                    <div class="row">
                       <label class="col-sm-3 col-form-label">{!! trans('panel.support.dependency') !!} <span class="text-danger"> *</span></label>
                       <div class="col-sm-9">
                          <div class="form-group bmd-form-group">
                             <select class="form-control select2" name="dependency[]" style="width: 100%;" multiple="multiple" required>
                              @if(@isset($users ))
                                @foreach($users as $id => $user)
                                  <option value="{!! $user['id'] !!}" {{ (in_array($id, old('dependency', [])) || $supports->associatedUsers->contains($id)) ? 'selected' : '' }}>{!! $user['id'].' '.$user['name'] !!}</option>
                                @endforeach
                              @endif
                             </select>
                          </div>
                          @if ($errors->has('associated'))
                          <div class="error col-lg-12">
                             <p class="text-danger">{{ $errors->first('associated') }}</p>
                          </div>
                          @endif
                       </div>
                     </div>
                  </div>
                </div>
                <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-rose">{!! trans('panel.support.description') !!}</h4>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group has-default bmd-form-group">
                          <textarea class="ckeditor form-control" name="description" id="descriptions">
                            {!! old( 'description', $supports['description']) !!}
                          </textarea>
                           @if ($errors->has('description'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('description') }}</p>
                           </div>
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
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script src="{{ url('/').'/'.asset('assets/js/validation_supports.js') }}"></script>
<script type="text/javascript">
function getUserInfo()
{
  var user_id = $("select[name=user_id]").val();
  var base_url =$('.baseurl').data('baseurl'); 
  if(user_id){
    $.ajax({
      url: base_url + '/getUserInfo',
      dataType: "json",
      type: "GET",
      data:{ _token: "{{csrf_token()}}", user_id:user_id},
      success: function(res){
        $(".fullname").empty();
        if(res)
        {
          $(".fullname").val(res.name);
        }
      }
    });
  }
} 
</script>
</x-app-layout>
