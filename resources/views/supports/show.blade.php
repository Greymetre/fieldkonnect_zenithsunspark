<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card card-profile">
      <div class="card-header">
        <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" role="tablist">
            <li class="nav-item">
              <a class="nav-link active show" data-toggle="tab" href="#link7" role="tablist">
                <i class="material-icons">preview</i> Info
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link orderinfo" data-toggle="tab" href="#link8" role="tablist">
                <i class="material-icons">add_shopping_cart</i> Assigned
              </a>
            </li>
            @if(!empty($support['assigned_to']) && $support['assigned_to'] =='1')
            <li class="nav-item">
              <a class="nav-link salesinfo" data-toggle="tab" href="#link9" role="tablist">
                <i class="material-icons">feedback</i> Response
              </a>
            </li>
            @endif
            @if(!empty($support['user_id']) && $support['user_id'] == Auth::user()->id)
            <li class="nav-item">
              <a class="nav-link tasksinfo" data-toggle="tab" href="#link10" role="tablist">
                <i class="material-icons">textsms</i> Message
              </a>
            </li>
            @endif
            @if(!empty($support['assigned_to']))
            <li class="nav-item">
              <a class="nav-link workinginfo" data-toggle="tab" href="#link11" role="tablist">
                <i class="material-icons">fact_check</i> Closed
              </a>
            </li>
            @endif
            @if(!empty($support['assigned_to']))
            <li class="nav-item">
              <a class="nav-link attendanceinfo" data-toggle="tab" href="#link12" role="tablist">
                <i class="material-icons">assignment_turned_in</i> Reopened
              </a>
            </li>
            @endif
          </ul>
      </div>
      <div class="card-body">
        <div class="tab-content tab-subcategories">
          <div class="tab-pane active show" id="link7">
            <hr class="my-3">
            <h6 class="card-category text-gray">{!! $support['status'] !!}</h6>
            <h4 class="card-title">{!! $support['name'] !!}</h4>
            <div class="table-responsive">
              <table class="table table-striped text-left">
                <tbody>
                  <tr>
                  <td width="25%">{!! trans('panel.support.subject') !!}</td>
                  <td width="75%">
                    {!! $support['subject'] !!}
                  </td>
                </tr>
                <tr>
                  <td>{!! trans('panel.support.description') !!}</td>
                  <td>
                    {!! $support['description'] !!}
                  </td>
                </tr>
                <tr>
                  <td>{!! trans('panel.support.full_name') !!}</td>
                  <td>
                    {!! isset($support['full_name']) ? $support['full_name'] : $support['users']['name'] !!}
                  </td>
                </tr>
                <tr>
                  <td>{!! trans('panel.support.priority') !!}</td>
                  <td>
                    {!! $support['priorities']['priority_name'] !!}
                  </td>
                </tr>
                  <tr>
                    <td>{!! trans('panel.support.associated') !!}</td>
                    <td>
                      @if($support['associatedUsers'])
                        @foreach($support['associatedUsers'] as $row)
                        {!! $row['users']['name'] !!} ,
                        @endforeach 
                      @endif
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.duedate') !!}</td>
                    <td>
                      {!! $support['duedate'] !!}
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.last_message_at') !!}</td>
                    <td>
                      {!! $support['last_message_at'] !!}
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.last_response_at') !!}</td>
                    <td>
                      {!! $support['last_response_at'] !!}
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.assigned_at') !!}</td>
                    <td>
                      {!! $support['assigned_at'] !!}
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.transferred_at') !!}</td>
                    <td>
                      {!! $support['transferred_at'] !!}
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.isoverdue') !!}</td>
                    <td>
                      {!! ($support['isoverdue']  == 1) ? 'Yes' :'No' !!}
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.reopened') !!}</td>
                    <td>
                      {!! ($support['reopened']  == 1) ? 'Yes' :'No' !!}
                    </td>
                  </tr>
                  <tr>
                    <td>{!! trans('panel.support.isanswered') !!}</td>
                    <td>
                       {!! ($support['isanswered']  == 1) ? 'Yes' :'No' !!}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <hr class="my-3">
            <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">{!! trans('panel.note.title') !!}</h4> 
            @if($support['messages'])
             <div class="card-body">
               <ul class="timeline">
                  @foreach($support['messages'] as $rows)
                  @if($rows['is_replay'] == 'Yes')
                  <li id="panel_{!! $rows->id !!}" >
                   <div class="timeline-badge success">
                  @else
                  <li class="timeline-inverted" id="panel_{!! $rows->id !!}" >
                   <div class="timeline-badge info">
                  @endif
                     {!! date('d-M',strtotime($rows['created_at'])) !!}
                   </div>
                   <div class="timeline-panel">
                     <div class="timeline-heading">
                        <span class="badge badge-pill badge-info">{!!
                           isset($rows['statusname']['status_name']) ? $rows['statusname']['status_name'] :'' !!} </span>
                     </div>
                     <div class="timeline-body">
                       <p>{!!$rows['note'] !!}.</p>
                     </div>
                     <h6>
                        <i class="ti-time"></i> {!! isset($rows['created_at']) ? date("d-m-Y H:i A", strtotime($rows['created_at'])) :'' !!}
                     </h6>
                   </div>
                 </li>
                 @endforeach
               </ul>
            </div>
             @endif
          </div>
          <div class="tab-pane" id="link8">
            <hr class="my-3">
            <form method="POST" action="{{ route('supports.assigned') }}">
                @csrf
                <input type="hidden" name="support_id" value="{!! $support['id'] !!}">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <label class="col-md-2 col-form-label">{!! trans('panel.support.assigned_to') !!}</label>
                    <div class="col-md-10">
                      <div class="form-group has-default bmd-form-group">
                        <select class="form-control select2" name="assigned_to" style="width: 100%;">
                           <option value="">Select {!! trans('panel.support.assigned_to') !!} </option>
                           @if(@isset($users ))
                           @foreach($users as $user)
                           <option value="{!! $user['id'] !!}" {{ old( 'assigned_to' , (!empty($support->assigned_to))?($support->assigned_to):('') ) == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                           @endforeach
                           @endif
                        </select>
                      </div>
                      @if ($errors->has('assigned_to'))
                        <label class="error">{{ $errors->first('assigned_to') }}</label>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-12 text-right">
                  <button class="btn btn-info btn-link btn-lg">Assigned</button>
                </div>
              </div>
            </form>
            
          </div>
          <div class="tab-pane" id="link9">
            <form method="POST" action="{{ route('supports.response') }}">
                @csrf
                <input type="hidden" name="support_id" value="{!! $support['id'] !!}">
              <div class="row">
                <div class="col-md-12">
                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-rose">{!! trans('panel.support.message') !!}</h4>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group has-default bmd-form-group">
                          <textarea class="ckeditor form-control" name="message" id="message">
                            {!! old( 'message') !!}
                          </textarea>
                           @if ($errors->has('message'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('message') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                </div>
                <div class="col-md-12 text-right">
                  <button class="btn btn-info btn-link btn-lg">Send</button>
                </div>
              </div>
            </form>
            @if($support['messages'])
              <hr class="my-3">
              <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Response</h4>
            <div class="row">
              <div class="col-md-12">
                <ul class="timeline timeline-simple">
                  @foreach($support['messages']->where('is_replay','=','No') as $message)
                  <li class="timeline-inverted">
                    <div class="timeline-badge danger">
                      <i class="material-icons">card_travel</i>
                    </div>
                    <div class="timeline-panel">
                      <div class="timeline-heading">
                        <span class="badge badge-pill badge-danger">{!! isset($message['created_at']) ? date("d-m-Y", strtotime($message['created_at'])) :'' !!}</span>
                      </div>
                      <div class="timeline-body">
                        {!! $message['note'] !!}
                      </div>
                    </div>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
            @endif
              
          </div>
          <div class="tab-pane" id="link10">
            <form method="POST" action="{{ route('supports.message') }}">
                @csrf
                <input type="hidden" name="support_id" value="{!! $support['id'] !!}">
              <div class="row">
                <div class="col-md-12">
                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-rose">{!! trans('panel.support.message') !!}</h4>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group has-default bmd-form-group">
                          <textarea class="ckeditor form-control" name="message" id="message">
                            {!! old( 'message') !!}
                          </textarea>
                           @if ($errors->has('message'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('message') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                </div>
                <div class="col-md-12 text-right">
                  <button class="btn btn-info btn-link btn-lg">Message</button>
                </div>
              </div>
            </form>  
               
              @if($support['messages'])
              <hr class="my-3">
              <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Response</h4>
            <div class="row">
              <div class="col-md-12">
                <ul class="timeline timeline-simple">
                  @foreach($support['messages']->where('is_replay','=','Yes') as $response)
                  <li class="timeline-inverted">
                    <div class="timeline-badge danger">
                      <i class="material-icons">card_travel</i>
                    </div>
                    <div class="timeline-panel">
                      <div class="timeline-heading">
                        <span class="badge badge-pill badge-danger">{!! isset($response['created_at']) ? date("d-m-Y", strtotime($response['created_at'])) :'' !!}</span>
                      </div>
                      <div class="timeline-body">
                        {!! $response['note'] !!}
                      </div>
                    </div>
                  </li>
                  @endforeach
                </ul>
              </div>
            </div>
            @endif
          </div>
          <div class="tab-pane" id="link11">
            <form method="POST" action="{{ route('supports.closed') }}">
                @csrf
                <input type="hidden" name="support_id" value="{!! $support['id'] !!}">
              <div class="row">
                <div class="col-md-12">
                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-rose">{!! trans('panel.support.message') !!}</h4>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group has-default bmd-form-group">
                          <textarea class="ckeditor form-control" name="message" id="message">
                            {!! old( 'message') !!}
                          </textarea>
                           @if ($errors->has('message'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('message') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                </div>
                <div class="col-md-12 text-right">
                  <button class="btn btn-info btn-link btn-lg">Closed</button>
                </div>
              </div>
            </form>  
          </div>
          <div class="tab-pane" id="link12">
               <form method="POST" action="{{ route('supports.closed') }}">
                @csrf
                <input type="hidden" name="support_id" value="{!! $support['id'] !!}">
              <div class="row">
                <div class="col-md-12">
                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-rose">{!! trans('panel.support.message') !!}</h4>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group has-default bmd-form-group">
                          <textarea class="ckeditor form-control" name="message" id="message">
                            {!! old( 'message') !!}
                          </textarea>
                           @if ($errors->has('message'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('message') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                </div>
                <div class="col-md-12 text-right">
                  <button class="btn btn-info btn-link btn-lg">Open</button>
                </div>
              </div>
            </form>  
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">

</script>
</x-app-layout>
