<x-app-layout>

{!! Form::model($beats,[
      'route' => $beats->exists ? ['beats.update', encrypt($beats->id) ] : 'beats.store',
      'method' => $beats->exists ? 'PUT' : 'POST',
      'id' => 'storeBeatData',
      'files'=>true
      ]) !!}
<div class="row mt-4">
	<div class="col-lg-6">
		<div class="card p-0" data-animation="true">
			<div class="card-body">
				<h5 class="newdata"> Beat Info</h5>
            <div class="row">
               <div class="col-md-12">
                  <div class="input_section">
                     <label class="col-form-label">{!! trans('panel.beat.beat_name') !!} <span class="text-danger"> *</span></label>
                     <input type="text" name="beat_name" id="beat_name" class="form-control" value="{!! old( 'beat_name', $beats['beat_name']) !!}"  maxlength="200" required>
                     @if ($errors->has('beat_name'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('beat_name') }}</p>
                        </div>
                     @endif
                  </div>
               </div>
     <div class="col-md-12">
                  <div class="input_section">
                     <label class="col-form-label">{!! trans('panel.global.state') !!} </label>
                     <select class="form-control select2 state" name="state_id" style="width: 100%;" onchange="getDistrictList()">
                        <option value="">Select {!! trans('panel.global.state') !!}</option>
                        @if(@isset($states))
                        @foreach($states as $state)
                        <option value="{!! $state['id'] !!}" {!! ($beats['state_id'] ==  $state['id']) ? 'selected' : ''!!}>{!! $state['state_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                     @if ($errors->has('state_id'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('state_id') }}</p>
                        </div>
                     @endif
                  </div>
               </div>
  <div class="col-md-12">
                  <div class="input_section">
                     <label class="col-form-label">{!! trans('panel.global.district') !!} </label>
                     <select class="form-control select2 district" name="district_id[]" multiple style="width: 100%;" onchange="getCityListMultiDis()">
                        <option value="">Select {!! trans('panel.global.district') !!}</option>
                        @if(@isset($districts))
                        @foreach($districts as $district)
                        <option value="{!! $district['id'] !!}" @if(in_array($district['id'],explode(',',$beats['district_id']))) selected @endif>{!! $district['district_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                     @if ($errors->has('district_id'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('district_id') }}</p>
                        </div>
                     @endif
                  </div>
                   </div>
 <div class="col-md-12">
                  <div class="input_section">
                     <label class="col-form-label">{!! trans('panel.global.city') !!} </label>
                     <select class="form-control select2 city" id="city_id" name="city_id[]" multiple style="width: 100%;">
                        <option value="">Select {!! trans('panel.global.city') !!}</option>
                        @if(@isset($cities))
                        @foreach($cities as $city)
                        <option value="{!! $city['id'] !!}" @if(in_array($city['id'],explode(',',$beats['city_id']))) selected @endif>{!! $city['city_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                     @if ($errors->has('city_id'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('city_id') }}</p>
                        </div>
                     @endif
                  </div>
               </div>

                 <div class="col-md-12">
                  <div class="input_section">
                     <label class="col-form-label">{!! trans('panel.beat.description') !!} <span class="text-danger"> *</span></label>
                     <textarea name="description" class="form-control" rows="5">{!! old( 'description', $beats['description']) !!}</textarea>
                     @if ($errors->has('description'))
                     <div class="error">
                        <p class="text-danger">{{ $errors->first('description') }}</p>
                     </div>
                     @endif
                  </div>
               </div>





            </div>
          
         
            @if($beats->exists)
               {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }} 
            @endif
			</div>
		</div>
	</div>
   @if($beats->exists)
      {{ Form::close() }}
   @endif
   <div class="col-lg-6">
		<div class="card p-0" data-animation="true">
			<div class="card-body">
				<h5 class="newdata"> Beat User</h5>
            <div class="row p-2">
               <div class="table-responsive">
               <a href="javascript:;" title="Add a row" class="btn btn-just-icon btn-success pull-right add-users-rows" onclick="getUserlist()">+</a>
               @if($beats->exists)
                  <form action="{{ URL::to('add-beatusers') }}" class="form-horizontal" method="post">
                  {{ csrf_field() }}

                  <input type="hidden" name="beat_id" id="beat_id" value="{!! old( 'beat_id', $beats->id) !!}">
               @endif
                     <table class="table beat-users-rows" id="tab_beat_users">
                        <thead>
                           <tr class="item-row">
                              <th>No</th>
                              <th>User</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                     </table>
                     @if($beats->exists)
                     <button class="btn btn-theme pull-right"> Add</button>
                     </form>
                     @endif
                  </div>
                  <div class="table-responsive">
                     <table class="table">
                        <tbody>
                           @if($beats->exists && isset($beats['beatusers']))
                              @foreach($beats['beatusers'] as $key => $index )
                                 <tr>
                                    <td>{!! $key+1 !!}</td>
                                    <td>
<div class="input_section">
                                     <select  class="form-control user" disabled><option value="{!! $index['user_id'] !!}" selected>{!! $index['users']['name'] !!}</option></select>
                                  </div></td>
                                    <td class="td-actions text-right">
                                     <a class="btn btn-danger" title="Remove row" onclick="deleteUserFromBeat({!! $index['id'] !!})"><i class="material-icons">close</i>
                                     </a>
                                   </td>
                                 </tr>
                              @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
            </div>
            
			</div>
		</div>
	</div>

   <div class="col-lg-6">
		<div class="card p-0" data-animation="true">
			<div class="card-body">
				<h5 class="newdata"> Beat Customer</h5>

            <div class="row p-3">
               <div class="table-responsive">
               <a href="javascript:;" title="Add a row" class="btn pull-right btn-just-icon btn-info add-customer-rows" onclick="getRetailerlist()">+</a>
               @if($beats->exists)
               <form action="{{ URL::to('add-beatcustomers') }}" class="form-horizontal" method="post">
                  {{ csrf_field() }}
                  <input type="hidden" name="beat_id" value="{!! old( 'beat_id', $beats->id) !!}">
               @endif
                     <table class="table beat-customer-rows" id="tab_beat_customer">
                        <thead>
                           <tr class="item-row">
                              <th>No</th>
                              <th>Customer</th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                     @if($beats->exists)
                     <button class="btn btn-theme pull-right"> Add</button>
                     </form>
                     @endif
                     <table class="table">
                        <tbody>
                           @if($beats->exists && isset($beats['beatcustomers']))
                              @foreach($beats['beatcustomers'] as $key => $rows )
                                 <tr>
                                    <td>{!! $key+1 !!}</td>
                                    <td>{!! $rows['customers']['name'] !!}</td>
                                    <td class="td-actions text-right">
                                     <a class="btn btn-danger" title="Remove row" onclick="deletecustomers({!! $rows['id'] !!})"><i class="material-icons">close</i>
                                     </a>
                                   </td>
                                 </tr>
                              @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
            </div>
            
			</div>
		</div>
	</div>

      <div class="col-lg-6">
		<div class="card mt-4 p-0" data-animation="true">
			<div class="card-body">
				<h5 class="newdata"> Schedule Beat</h5>
            <div class="row p-3">
            <div class="table-responsive">
            <a href="javascript:;" title="Add a row" class="btn btn-just-icon btn-success pull-right add-schedule-rows" onclick="getScheduleUserlist()">+</a>
               @if($beats->exists)
                  <form action="{{ URL::to('updateschedule') }}" class="form-horizontal" method="post">
                     {{ csrf_field() }}
                     <input type="hidden" name="beat_id" value="{!! old( 'beat_id', $beats->id) !!}">
               @endif
                     <table class="table beat-schedule-rows" id="tab_beat_schedule">
                        <thead>
                           <tr class="item-row">
                              <th></th>
                              <th>User Name</th>
                              <th>Date</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
              
                        </tbody>
                     </table>
                     @if($beats->exists)
                     <button class="btn btn-theme pull-right"> Add</button>
                     </form>
                     @endif
                     <table class="table">
                        <tbody>
                        @if($beats->exists && isset($beats['beatschedules']))
                              @foreach($beats['beatschedules'] as $key => $rows )
                                 <tr>
                                    <td>{!! $rows['id'] !!}</td>
                                    <td>{!! $rows['users']['name'] !!}</td>
                                    <td>{!! $rows['beat_date'] !!}</td>
                                    <td class="td-actions text-right">
                                      @if(auth()->user()->can(['beat_delete']))
                                        <a class="btn btn-danger" title="Remove row" onclick="deleteschedules({!! $rows['id'] !!})"><i class="material-icons">close</i>
                                        </a>
                                     @endif
                                   </td>
                                 </tr>
                              @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
               </div>
			</div>
		</div>
      @if(!$beats->exists)
      {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }} 
      @endif
	</div>
</div>
@if(!$beats->exists)
{{ Form::close() }}
@endif
<!-- <div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-tabs card-header-warning">
            <div class="nav-tabs-navigation">
               <div class="nav-tabs-wrapper">
                  <h4 class="card-title ">
                     {!!  trans('panel.global.add') !!} {!! trans('panel.beat.title_singular') !!}
                     @if(auth()->user()->can(['district_access']))
                     <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                        <li class="nav-item">
                           <a class="nav-link" href="{{ url('beats') }}">
                              <i class="material-icons">next_plan</i> {!! trans('panel.beat.title') !!}
                              <div class="ripple-container"></div>
                           </a>
                        </li>
                     </ul>
                     @endif
                  </h4>
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
            
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="bmd-label-floating">{!! trans('panel.beat.beat_name') !!} <span class="text-danger"> *</span></label>
                     <input type="text" name="beat_name" class="form-control" id="customerInputName" value="{!! old( 'beat_name', $beats['beat_name']) !!}" >
                     @if ($errors->has('beat_name'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('beat_name') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <select class="form-control select2" name="district_id" style="width: 100%;">
                        <option value="">Select {!! trans('panel.global.district') !!}</option>
                        @if(@isset($districts))
                        @foreach($districts as $district)
                        <option value="{!! $district['id'] !!}" {!! (isset($beats['district_id']) ? $beats['district_id'] :'' ==  $district['id']) ? 'selected' : ''!!}>{!! $district['district_name'] !!}</option>
                        @endforeach
                        @endif
                     </select>
                     @if ($errors->has('district_id'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('district_id') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="form-group">
                     <label class="bmd-label-floating">{!! trans('panel.beat.description') !!} <span class="text-danger"> *</span></label>
                     <textarea name="description" class="form-control" rows="5">{!! old( 'description', $beats['description']) !!}</textarea>
                     @if ($errors->has('description'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('description') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
            <hr>
            <div class="row">
               <div class="col-md-6">
                  <div class="table-responsive">
                     <table class="table beat-customer-rows" id="tab_beat_customer">
                        <thead>
                           <tr class="item-row">
                              <th>No</th>
                              <th>Customer</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($beats->exists && isset($beats['beatcustomers']))
                              @foreach($beats['beatcustomers'] as $key => $rows )
                                 <tr>
                                    <td>{!! $key+1 !!}</td>
                                    <td>{!! $rows['customers']['name'] !!}</td>
                                    <td class="td-actions text-right">
                                     <a class="btn btn-danger" title="Remove row" onclick="deletecustomers({!! $rows['id'] !!})"><i class="material-icons">close</i>
                                     </a>
                                   </td>
                                 </tr>
                              @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
                  <a href="javascript:;" title="Add a row" class="btn btn-just-icon btn-success pull-left add-customer-rows" onclick="getRetailerlist()">+</a>
               </div>
               <div class="col-md-6">
                  <div class="table-responsive">
                     <table class="table beat-users-rows" id="tab_beat_users">
                        <thead>
                           <tr class="item-row">
                              <th>No</th>
                              <th>User</th>
                           </tr>
                        </thead>
                        <tbody>
                           @if($beats->exists && isset($beats['beatusers']))
                              @foreach($beats['beatusers'] as $key => $index )
                                 <tr>
                                    <td>{!! $key+1 !!}</td>
                                    <td>{!! $index['users']['name'] !!}</td>
                                    <td></td>
                                 </tr>
                              @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
                  <a href="javascript:;" title="Add a row" class="btn btn-just-icon btn-success pull-right add-users-rows" onclick="getUserlist()">+</a>
               </div>
            </div>
            <hr class="my-1">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Schedule Beat</h4> 
            <div class="row">
               <div class="col-md-8">
                  <div class="table-responsive">
                     <table class="table beat-schedule-rows" id="tab_beat_schedule">
                        <thead>
                           <tr class="item-row">
                              <th></th>
                              <th>User Name</th>
                              <th>Date</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>

                        </tbody>
                     </table>
                  </div>
                  <a href="javascript:;" title="Add a row" class="btn btn-just-icon btn-success pull-right add-schedule-rows" onclick="getScheduleUserlist()">+</a>
               </div>
            </div>
            
         </div>
      </div>
   </div>
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-tabs card-header-warning">
            <div class="nav-tabs-navigation">
               <div class="nav-tabs-wrapper">
                  <h4 class="card-title ">User List</h4>
               </div>
            </div>
         </div>
         <div class="card-body">
         
            <table class="table">
              <thead>
                <tr class="item-row">
                  <th> # </th>
                  <th>User</th>
                  <th>Date</th>
                  <th></th>
                </tr>
              </thead>
              @if($beats->exists && !empty($beats->beatschedules))
                      @foreach($beats->beatschedules as $key => $rows )
                  <tr>
                    <td class="text-primary"> {!! $key+1 !!}</td>
                    <td>{!! $rows['users']['name'] !!}</td>
                    <td>{!! $rows['beat_date'] !!}</td>
                    <td class="td-actions text-right">
                      <a class="btn btn-danger" title="Remove row" onclick="deleteschedules({!! $rows['id'] !!})"><i class="material-icons">close</i>
                      </a>
                    </td>
                  </tr>
                @endforeach
              @endif
            </table>
         </div>
      </div>
    </div>
</div> -->
<script src="https://silver.fieldkonnect.io//public/assets/js/core/jquery.validate.js"></script>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/jquery.beat.js') }}"></script>
</x-app-layout>