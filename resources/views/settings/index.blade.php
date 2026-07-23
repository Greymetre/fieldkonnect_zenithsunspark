<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="row g-0">
        <div class="col-md-3">
          <div class="card-header card-header-warning">
            <div class="nav-tabs-navigation">
              <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs" id="myTabJust" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="general-tab-just" data-toggle="tab" href="#general-just" role="tab" aria-controls="general-just"
                    aria-selected="true">General Settings</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="lead-tab-just" data-toggle="tab" href="#lead-just" role="tab" aria-controls="lead-just"
                    aria-selected="false">Lead Settings</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="sales-tab-just" data-toggle="tab" href="#sales-just" role="tab" aria-controls="invoice-just"
                    aria-selected="false">Sales Settings</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="order-tab-just" data-toggle="tab" href="#order-just" role="tab" aria-controls="invoice-just"
                    aria-selected="false">Order Settings</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="customer-tab-just" data-toggle="tab" href="#customer-just" role="tab" aria-controls="invoice-just"
                    aria-selected="false">Customer Settings</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="dashboard-setting-tab-just" data-toggle="tab" href="#dashboard-setting-just" role="tab" aria-controls="invoice-just"
                    aria-selected="false">Dashboard Settings</a>
                </li>

              </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-9">
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
            <div class="tab-content" id="myTabContentJust">
              <div class="tab-pane fade show active" id="general-just" role="tabpanel" aria-labelledby="general-tab-just">
                <form method="POST" action="{{ url('settingSubmit') }}" enctype="multipart/form-data">
                    @csrf
                <div class="row">
                  <label class="col-md-2 col-form-label">{!!  trans('panel.global.date_format') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="hidden" name="detail[1][key_name]" value="date_format">
                      <select class="form-control" name="detail[1][value]" required="">
                        <option value="d-m-Y" @isset($settings['date_format']) {!! ($settings['date_format'] == "d-m-Y") ? 'selected' : ''!!} @endisset>d-m-Y</option>
                        <option value="d/m/Y" @isset($settings['date_format']) {!! ($settings['date_format'] == "d/m/Y") ? 'selected' : ''!!} @endisset>d/m/Y</option>
                        <option value="m-d-Y" @isset($settings['date_format']) {!! ($settings['date_format'] == "m-d-Y") ? 'selected' : ''!!} @endisset>m-d-Y</option>
                        <option value="m.d.Y" @isset($settings['date_format']) {!! ($settings['date_format'] == "m.d.Y") ? 'selected' : ''!!} @endisset>m.d.Y</option>
                        <option value="m/d/Y" @isset($settings['date_format']) {!! ($settings['date_format'] == "m/d/Y") ? 'selected' : ''!!} @endisset>m/d/Y</option>
                        <option value="Y-m-d" @isset($settings['date_format']) {!! ($settings['date_format'] == "Y-m-d") ? 'selected' : ''!!} @endisset>Y-m-d</option>
                        <option value="d.m.Y" @isset($settings['date_format']) {!! ($settings['date_format'] == "d.m.Y") ? 'selected' : ''!!} @endisset>d.m.Y</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-md-2 col-form-label">{!!  trans('panel.global.time_format') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                       <input type="hidden" name="detail[2][key_name]" value="time_format">
                      <select class="form-control" name="detail[2][value]" required="">
                        <option value="H:i:s" @isset($settings['time_format']) {!! ($settings['time_format'] == "H:i:s") ? 'selected' : ''!!} @endisset>24 hours</option>
                        <option value="g:i a" @isset($settings['time_format']) {!! ($settings['time_format'] == "g:i a") ? 'selected' : ''!!} @endisset>12 hours</option>
                      </select>
                    </div>
                  </div>
                </div>   
                <div class="row">
                  <label class="col-md-2 col-form-label">{!!  trans('panel.global.time_zone') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="hidden" name="detail[3][key_name]" value="time_zone">
                      <select class="form-control" name="detail[3][value]" required="">
                        <option value="Asia/Kolkata" @isset($settings['time_zone']) {!! ($settings['time_zone'] == "Asia/Kolkata") ? 'selected' : ''!!} @endisset>Asia/Kolkata</option>
                      </select>
                    </div>
                  </div>
                </div> 
                <div class="row">
                  <label class="col-md-2 col-form-label">{!!  trans('panel.global.default_language') !!}</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="hidden" name="detail[4][key_name]" value="default_language">
                      <select class="form-control" name="detail[4][value]" required="">
                        <option value="en" @isset($settings['time_zone']){!! ($settings['default_language'] == "en") ? 'selected' : ''!!}@endisset>English</option>
                      </select>
                    </div>
                  </div>
                </div>  
                <div class="row">
                  <label class="col-md-2 col-form-label">{!!  trans('panel.global.default_language') !!}</label>
                  <div class="col-sm-4 checkbox-radios">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="exampleRadios" value="option2"> Radio is off
                        <span class="circle">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </div>
                  <div class="col-sm-5 checkbox-radios">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="exampleRadios" value="option1" checked=""> Radio is on
                        <span class="circle">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label label-checkbox">Inline checkboxes</label>
                  <div class="col-sm-10 checkbox-radios">
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" value=""> a
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" value=""> b
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" value=""> c
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label">With help</label>
                  <div class="col-sm-10">
                    <div class="form-group bmd-form-group">
                      <input type="text" class="form-control">
                      <span class="bmd-help">A block of help text that breaks onto a new line.</span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 text-right">
                    {{ Form::submit('Submit', array('class' => 'btn btn-sm btn-info')) }}
                  </div>
                </div>
                </form>
              </div>

              <div class="tab-pane fade" id="lead-just" role="tabpanel" aria-labelledby="lead-tab-just">
                <form method="POST" action="{{ url('settingSubmit') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                  <h4 class="text-info pull-center">Lead Status Box</h4>
                  <table class="table table-hover">
                    <thead class="text-warning">
                      <tr>
                        <th width="25%">Title Name</th>
                        <th width="50%">Colour</th>
                        <th width="25%">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($leadstatus)
                        @foreach($leadstatus  as $index => $lead)
                          <tr>
                            <td><input type="text" name="lead[{!! $index!!}][title]" class="form-control" value="{!! $lead['title'] !!}"></td>
                            <td>
                              <div class="row">
                                <div class="col-md-6">
                                  <input class="form-check-input" type="radio" name="lead[{!! $index!!}][key_name]" value="primary" @if($lead['key_name'] == 'primary') checked="" @endif> <span class="text-primary"> Pink </span>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="radio" name="lead[{!! $index!!}][key_name]" value="success" @if($lead['key_name'] == 'success') checked="" @endif> <span class="text-success"> Green </span>
                                </div>
                                <div class="col-md-6">
                                 <input class="form-check-input" type="radio" name="lead[{!! $index!!}][key_name]" value="warning" @if($lead['key_name'] == 'warning') checked="" @endif> <span class="text-warning"> Yellow </span>
                                </div>
                                <div class="col-md-6">
                                  <input class="form-check-input" type="radio" name="lead[{!! $index!!}][key_name]" value="info" @if($lead['key_name'] == 'info') checked="" @endif> <span class="text-info"> Blue </span>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="radio" name="lead[{!! $index!!}][key_name]" value="danger" @if($lead['key_name'] == 'danger') checked="" @endif> <span class="text-danger"> Red </span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <input type="hidden" name="lead[{!! $index!!}][module]" value="lead_stages_dashboard">
                              <select class="form-control" name="lead[{!! $index!!}][value]"required>
                              <option value="yes">Select {!! trans('panel.leadstages.stage_name') !!}</option>
                              @if(@isset($leadstages ))
                                @foreach($leadstages as $stage)
                                    <option value="{!! $stage['id'] !!}" {{ ($lead->value == $stage['id']) ? 'selected' : '' }}>{!! $stage['stage_name'] !!}</option>
                                @endforeach
                              @endif
                           </select>
                            </td>
                          </tr>
                          @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <h4 class="text-info pull-center">Lead Pai Chart</h4>
                  <table class="table table-hover">
                    <thead class="text-warning">
                      <tr>
                        <th width="25%">Title Name</th>
                        <th width="50%">Colour</th>
                        <th width="25%">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($leadpaicharts)
                        @foreach($leadpaicharts  as $index => $leadchart)
                          <tr>
                            <td><input type="text" name="leadchart[{!! $index!!}][title]" class="form-control" value="{!! $leadchart['title'] !!}"></td>
                            <td>
                              <div class="row">
                                <div class="col-md-6">
                                  <input class="form-check-input" type="radio" name="leadchart[{!! $index!!}][key_name]" value="primary" @if($leadchart['key_name'] == 'primary') checked="" @endif> <span class="text-primary"> Pink </span>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="radio" name="leadchart[{!! $index!!}][key_name]" value="success" @if($leadchart['key_name'] == 'success') checked="" @endif> <span class="text-success"> Green </span>
                                </div>
                                <div class="col-md-6">
                                 <input class="form-check-input" type="radio" name="leadchart[{!! $index!!}][key_name]" value="warning" @if($leadchart['key_name'] == 'warning') checked="" @endif> <span class="text-warning"> Yellow </span>
                                </div>
                                <div class="col-md-6">
                                  <input class="form-check-input" type="radio" name="leadchart[{!! $index!!}][key_name]" value="info" @if($leadchart['key_name'] == 'info') checked="" @endif> <span class="text-info"> Blue </span>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="radio" name="leadchart[{!! $index!!}][key_name]" value="danger" @if($leadchart['key_name'] == 'danger') checked="" @endif> <span class="text-danger"> Red </span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <input type="hidden" name="leadchart[{!! $index!!}][module]"  value="lead_status_paichart">
                              <select class="form-control" name="leadchart[{!! $index!!}][value]"required>
                              <option value="">Select {!! trans('panel.leadstages.stage_name') !!}</option>
                              @if(@isset($statuses ))
                                @foreach($statuses as $status)
                                    <option value="{!! $status['id'] !!}" {{ ($leadchart->value == $status['id']) ? 'selected' : '' }}>{!! $status['status_name'] !!}</option>
                                @endforeach
                              @endif
                           </select>
                            </td>
                          </tr>
                          @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <h4 class="text-info pull-center">Bar Graph Status</h4>
                  <table class="table table-hover">
                    <thead class="text-warning">
                      <tr>
                        <th width="25%">Title Name</th>
                        <th width="50%">Colour</th>
                        <th width="25%">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($leadbargraph)
                        @foreach($leadbargraph  as $index => $leadgraph)
                          <tr>
                            <td><input type="text" name="leadgraph[{!! $index!!}][title]" class="form-control" value="{!! $leadgraph['title'] !!}"></td>
                            <td>
                              <div class="row">
                                <div class="col-md-6">
                                  <input class="form-check-input" type="radio" name="leadgraph[{!! $index!!}][key_name]" value="primary" @if($leadgraph['key_name'] == 'primary') checked="" @endif> <span class="text-primary"> Pink </span>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="radio" name="leadgraph[{!! $index!!}][key_name]" value="success" @if($leadgraph['key_name'] == 'success') checked="" @endif> <span class="text-success"> Green </span>
                                </div>
                                <div class="col-md-6">
                                 <input class="form-check-input" type="radio" name="leadgraph[{!! $index!!}][key_name]" value="warning" @if($leadgraph['key_name'] == 'warning') checked="" @endif> <span class="text-warning"> Yellow </span>
                                </div>
                                <div class="col-md-6">
                                  <input class="form-check-input" type="radio" name="leadgraph[{!! $index!!}][key_name]" value="info" @if($leadgraph['key_name'] == 'info') checked="" @endif> <span class="text-info"> Blue </span>
                                </div>
                                <div class="col-md-6">
                                    <input class="form-check-input" type="radio" name="leadgraph[{!! $index!!}][key_name]" value="danger" @if($leadgraph['key_name'] == 'danger') checked="" @endif> <span class="text-danger"> Red </span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <input type="hidden" name="leadgraph[{!! $index!!}][module]"  value="lead_bargraph_dashboard">
                              <select class="form-control" name="leadgraph[{!! $index!!}][value]"required>
                              <option value="">Select {!! trans('panel.leadstages.stage_name') !!}</option>
                              @if(@isset($leadstages ))
                                @foreach($leadstages as $stage)
                                    <option value="{!! $stage['id'] !!}" {{ ($leadgraph->value == $stage['id']) ? 'selected' : '' }}>{!! $stage['stage_name'] !!}</option>
                                @endforeach
                              @endif
                           </select>
                            </td>
                          </tr>
                          @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label label-checkbox"><h4 class="text-info pull-center">Best Industry</h4></label>
                  <div class="col-sm-10 checkbox-radios">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="bestindustry" @if(count($bestindustry) >= 1) checked="" @endif > Yes
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </div>
                  @if($bestindustry)
                    @foreach($bestindustry  as $index => $industry)
                      <div class="row bestindustory">
                        <div class="col-sm-4">
                          <div class="form-group bmd-form-group">
                            <input type="text" name="bestindustry[{!! $index!!}][title]" class="form-control" value="{!! $industry['title'] !!}" placeholder="Title">
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="row">
                            <div class="col-md-6">
                              <input class="form-check-input" type="radio" name="bestindustry[{!! $index!!}][key_name]" value="primary" @if($industry['key_name'] == 'primary') checked="" @endif> <span class="text-primary"> Pink </span>
                            </div>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio" name="bestindustry[{!! $index!!}][key_name]" value="success" @if($industry['key_name'] == 'success') checked="" @endif> <span class="text-success"> Green </span>
                            </div>
                            <div class="col-md-6">
                             <input class="form-check-input" type="radio" name="bestindustry[{!! $index!!}][key_name]" value="warning" @if($industry['key_name'] == 'warning') checked="" @endif> <span class="text-warning"> Yellow </span>
                            </div>
                            <div class="col-md-6">
                              <input class="form-check-input" type="radio" name="bestindustry[{!! $index!!}][key_name]" value="info" @if($industry['key_name'] == 'info') checked="" @endif> <span class="text-info"> Blue </span>
                            </div>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio" name="bestindustry[{!! $index!!}][key_name]" value="danger" @if($industry['key_name'] == 'danger') checked="" @endif> <span class="text-danger"> Red </span>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <input type="hidden" name="bestindustry[{!! $index!!}][module]"  value="best_industry_dashboard">
                          <select class="form-control" name="bestindustry[{!! $index!!}][value]"required>
                            <option value="">Select {!! trans('panel.leadstages.stage_name') !!}</option>
                            @if(@isset($leadstages ))
                              @foreach($leadstages as $stage)
                                  <option value="{!! $stage['id'] !!}" {{ ($industry->value == $stage['id']) ? 'selected' : '' }}>{!! $stage['stage_name'] !!}</option>
                              @endforeach
                            @endif
                         </select>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label label-checkbox"><h4 class="text-info pull-center">Best Country</h4></label>
                  <div class="col-sm-10 checkbox-radios">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" value="" name="bestcountry"  @if(count($bestcountry) >= 1) checked="" @endif> Yes
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </div>
                  @if($bestcountry)
                    @foreach($bestcountry  as $index => $country)
                      <div class="row bestcountry">
                        <div class="col-sm-4">
                          <div class="form-group bmd-form-group">
                            <input type="text" name="bestcountry[{!! $index!!}][title]" class="form-control" value="{!! $country['title'] !!}" placeholder="Title">
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="row">
                            <div class="col-md-6">
                              <input class="form-check-input" type="radio" name="bestcountry[{!! $index!!}][key_name]" value="primary" @if($country['key_name'] == 'primary') checked="" @endif> <span class="text-primary"> Pink </span>
                            </div>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio" name="bestcountry[{!! $index!!}][key_name]" value="success" @if($country['key_name'] == 'success') checked="" @endif> <span class="text-success"> Green </span>
                            </div>
                            <div class="col-md-6">
                             <input class="form-check-input" type="radio" name="bestcountry[{!! $index!!}][key_name]" value="warning" @if($country['key_name'] == 'warning') checked="" @endif> <span class="text-warning"> Yellow </span>
                            </div>
                            <div class="col-md-6">
                              <input class="form-check-input" type="radio" name="bestcountry[{!! $index!!}][key_name]" value="info" @if($country['key_name'] == 'info') checked="" @endif> <span class="text-info"> Blue </span>
                            </div>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio" name="bestcountry[{!! $index!!}][key_name]" value="danger" @if($country['key_name'] == 'danger') checked="" @endif> <span class="text-danger"> Red </span>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <input type="hidden" name="bestcountry[{!! $index!!}][module]"  value="best_country_dashboard">
                          <select class="form-control" name="bestcountry[{!! $index!!}][value]"required>
                            <option value="">Select {!! trans('panel.leadstages.stage_name') !!}</option>
                            @if(@isset($leadstages ))
                              @foreach($leadstages as $stage)
                                  <option value="{!! $stage['id'] !!}" {{ ($country->value == $stage['id']) ? 'selected' : '' }}>{!! $stage['stage_name'] !!}</option>
                              @endforeach
                            @endif
                         </select>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>
                <div class="row">
                  <label class="col-sm-2 col-form-label label-checkbox"><h4 class="text-info pull-center">Best Title</h4></label>
                  <div class="col-sm-10 checkbox-radios">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" value="" name="bestdesignation" @if(count($bestdesignation) >= 1) checked="" @endif> Yes
                        <span class="form-check-sign">
                          <span class="check"></span>
                        </span>
                      </label>
                    </div>
                  </div>
                  @if($bestdesignation)
                    @foreach($bestdesignation  as $index => $designation)
                      <div class="row bestdesignation">
                        <div class="col-sm-4">
                          <div class="form-group bmd-form-group">
                            <input type="text" name="bestdesignation[{!! $index!!}][title]" class="form-control" value="{!! $designation['title'] !!}" placeholder="Title">
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="row">
                            <div class="col-md-6">
                              <input class="form-check-input" type="radio" name="bestdesignation[{!! $index!!}][key_name]" value="primary" @if($designation['key_name'] == 'primary') checked="" @endif> <span class="text-primary"> Pink </span>
                            </div>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio" name="bestdesignation[{!! $index!!}][key_name]" value="success" @if($designation['key_name'] == 'success') checked="" @endif> <span class="text-success"> Green </span>
                            </div>
                            <div class="col-md-6">
                             <input class="form-check-input" type="radio" name="bestdesignation[{!! $index!!}][key_name]" value="warning" @if($designation['key_name'] == 'warning') checked="" @endif> <span class="text-warning"> Yellow </span>
                            </div>
                            <div class="col-md-6">
                              <input class="form-check-input" type="radio" name="bestdesignation[{!! $index!!}][key_name]" value="info" @if($designation['key_name'] == 'info') checked="" @endif> <span class="text-info"> Blue </span>
                            </div>
                            <div class="col-md-6">
                                <input class="form-check-input" type="radio" name="bestdesignation[{!! $index!!}][key_name]" value="danger" @if($designation['key_name'] == 'danger') checked="" @endif> <span class="text-danger"> Red </span>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                           <input type="hidden" name="bestdesignation[{!! $index!!}][module]"  value="best_designation_dashboard">
                          <select class="form-control" name="bestdesignation[{!! $index!!}][value]"required>
                            <option value="">Select {!! trans('panel.leadstages.stage_name') !!}</option>
                            @if(@isset($leadstages ))
                              @foreach($leadstages as $stage)
                                  <option value="{!! $stage['id'] !!}" {{ ($designation->value == $stage['id']) ? 'selected' : '' }}>{!! $stage['stage_name'] !!}</option>
                              @endforeach
                            @endif
                         </select>
                        </div>
                      </div>
                    @endforeach
                  @endif
                </div>
                <div class="row">
                  <div class="col-12 text-right">
                    {{ Form::submit('Submit', array('class' => 'btn btn-sm btn-info')) }}
                  </div>
                </div>
                </form>
              </div>
              <div class="tab-pane fade" id="sales-just" role="tabpanel" aria-labelledby="sales-tab-just">
                <form method="POST" action="{{ url('settingSubmit') }}" enctype="multipart/form-data">
                    @csrf
                  <div class="table-responsive">
                      <table class="table">
                        <thead class="">
                          <tr>
                          <th>
                            Setting Name
                          </th>
                          <th>
                            Action
                          </th>
                        </tr></thead>
                        <tbody>
                          <tr class="table-success">
                            <td>
                              {!!  trans('panel.settings.distributor_sales') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="hidden" name="detail[1][key_name]" value="distributor_sales">
                                  <input type="checkbox" name="detail[1][value]" @isset($settings['distributor_sales']) {!! $settings['distributor_sales'] == 'on' ? 'checked' :'' !!} @endisset>
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>
                              {!!  trans('panel.settings.earn_points_with_purchases') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="hidden" name="detail[2][key_name]" value="earn_points_with_purchases">
                                  <input type="checkbox" name="detail[2][value]" @isset($settings['earn_points_with_purchases']) {!! $settings['earn_points_with_purchases'] == 'on' ? 'checked' :'' !!} @endisset>
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-info">
                            <td>
                              {!!  trans('panel.settings.earn_points_with_sales') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="hidden" name="detail[3][key_name]" value="earn_points_with_sales">
                                  <input type="checkbox" name="detail[3][value]" @isset($settings['earn_points_with_sales']) {!! $settings['earn_points_with_sales'] == 'on' ? 'checked' :'' !!} @endisset>
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>
                              {!!  trans('panel.settings.sales_payment_approval') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="hidden" name="detail[4][key_name]" value="sales_payment_approval">
                                  <input type="checkbox" name="detail[4][value]" @isset($settings['sales_payment_approval']) {!! $settings['sales_payment_approval'] == 'on' ? 'checked' :'' !!} @endisset>
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-danger">
                            <td>
                              {!!  trans('panel.settings.sales_stock_update') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="hidden" name="detail[5][key_name]" value="sales_stock_update">
                                  <input type="checkbox" name="detail[5][value]" @isset($settings['sales_stock_update']) {!! $settings['sales_stock_update'] == 'on' ? 'checked' :'' !!} @endisset>
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <!-- <tr class="">
                            <td>
                              Mason Porter
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-info">
                            <td>
                              Jon Porter
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr> -->
                        </tbody>
                      </table>
                      <input type="hidden" name="module" value="sales">
                  </div>
                   
                 <div class="row">
                  <div class="col-12 text-right">
                    {{ Form::submit('Submit', array('class' => 'btn btn-sm btn-info')) }}
                  </div>
                </div>
                </form>
              </div>
              <div class="tab-pane fade" id="order-just" role="tabpanel" aria-labelledby="order-tab-just">

                <form method="POST" action="{{ url('settingSubmit') }}" enctype="multipart/form-data">
                    @csrf
                <div class="table-responsive">
                      <table class="table">
                        <thead class="">
                          <tr>
                          <th>
                            Setting Name
                          </th>
                          <th>
                            Action
                          </th>
                        </tr></thead>
                        <tbody>
                          <tr class="table-success">
                            <td>
                              {!!  trans('panel.settings.order_to_shipment') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="hidden" name="detail[1][key_name]" value="order_to_shipment">
                                  <input type="checkbox" name="detail[1][value]" @isset($settings['order_to_shipment']) {!! $settings['order_to_shipment'] == 'on' ? 'checked' :'' !!} @endisset>
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <!-- <tr class="">
                            <td>
                              {!!  trans('panel.settings.earn_points_with_purchases') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" name="earn_points_with_purchases" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-info">
                            <td>
                              {!!  trans('panel.settings.earn_points_with_sales') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" name="earn_points_with_sales" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>
                              Philip Chaney
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-danger">
                            <td>
                              Doris Greene
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>
                              Mason Porter
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-info">
                            <td>
                              Jon Porter
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr> -->
                        </tbody>
                      </table>
                      <input type="hidden" name="module" value="orders">
                  </div>
                   
                 <div class="row">
                  <div class="col-12 text-right">
                    {{ Form::submit('Submit', array('class' => 'btn btn-sm btn-info')) }}
                  </div>
                </div>
                </form>
              </div>
              <div class="tab-pane fade" id="customer-just" role="tabpanel" aria-labelledby="customer-tab-just">

                <form method="POST" action="{{ url('settingSubmit') }}" enctype="multipart/form-data">
                    @csrf
                <div class="table-responsive">
                      <table class="table">
                        <thead class="">
                          <tr>
                          <th>
                            Setting Name
                          </th>
                          <th>
                            Action
                          </th>
                        </tr></thead>
                        <tbody>
                          <tr class="table-success">
                            <td>
                              {!!  trans('panel.settings.distributor_sales') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" name="distributor_sales" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>
                              {!!  trans('panel.settings.earn_points_with_purchases') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" name="earn_points_with_purchases" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-info">
                            <td>
                              {!!  trans('panel.settings.earn_points_with_sales') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" name="earn_points_with_sales" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>
                              {!!  trans('panel.settings.sales_payment_approval') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="" name="sales_payment_approval">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-danger">
                            <td>
                              {!!  trans('panel.settings.sales_stock_update') !!}
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="" name="sales_stock_update">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <!-- <tr class="">
                            <td>
                              Mason Porter
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-info">
                            <td>
                              Jon Porter
                            </td>
                            <td>
                              <div class="togglebutton">
                                <label>
                                  <input type="checkbox" checked="">
                                  <span class="toggle"></span>
                                </label>
                              </div>
                            </td>
                          </tr> -->
                        </tbody>
                      </table>
                      <input type="hidden" name="module" value="sales">
                  </div>
                   
                 <div class="row">
                  <div class="col-12 text-right">
                    {{ Form::submit('Submit', array('class' => 'btn btn-sm btn-info')) }}
                  </div>
                </div>
                </form>
              </div>
              <div class="tab-pane fade" id="dashboard-setting-just" role="tabpanel" aria-labelledby="dashboard-setting-tab-just">
                <form method="POST" action="{{ url('settingSubmit') }}" enctype="multipart/form-data">
                    @csrf
                <div class="table-responsive">
                      <table class="table">
                        <thead class="">
                          <tr>
                          <th>
                            Dashboard Setting
                          </th>
                          <th>
                            Action
                          </th>
                        </tr></thead>
                        <tbody>
                          <tr class="table-success">
                            <td>{!!  trans('panel.settings.lead_status_boxs') !!}</td>
                            <td>
                              <div class="row">
                                <input type="hidden" name="detail[1][key_name]" value="leadstatus">
                                <label class="col-md-3 col-form-label">{!! trans('panel.lead.status_id') !!}</label>
                                <div class="col-md-9">
                                  <div class="form-group has-default bmd-form-group">
                                    <select class="form-control select2" name="detail[1][value][]" data-style="select-with-transition"  multiple="multiple" style="width: 100%;">
                                      @if(@isset($statuses ))
                                      @foreach($statuses->where('module','LeadStatus') as $id => $status)
                                      <option value="{!! $id !!}">{!! $status['status_name'] !!}</option>
                                      @endforeach
                                      @endif
                                    </select>
                                    @if ($errors->has('status_id'))
                                      <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('status_id') }}</p></div>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>{!!  trans('panel.settings.lead_status_chart') !!}</td>
                            <td>
                              <div class="row">
                                <input type="hidden" name="detail[2][key_name]" value="lead_pai_chart">
                                <label class="col-md-3 col-form-label">{!! trans('panel.lead.status_id') !!}</label>
                                <div class="col-md-9">
                                  <div class="form-group has-default bmd-form-group">
                                    <select class="form-control select2" name="detail[2][value][]" data-style="select-with-transition"  multiple="multiple" style="width: 100%;">
                                      @if(@isset($statuses ))
                                      @foreach($statuses->where('module','LeadStatus') as $id => $status)
                                      <option value="{!! $id !!}">{!! $status['status_name'] !!}</option>
                                      @endforeach
                                      @endif
                                    </select>
                                    @if ($errors->has('status_id'))
                                      <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('status_id') }}</p></div>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                          <tr class="table-info">
                            <td>
                              {!!  trans('panel.settings.lead_industry_graph') !!}
                            </td>
                            <td>
                              <div class="row">
                                <input type="hidden" name="detail[3][key_name]" value="lead_bar_graph">
                                <label class="col-md-3 col-form-label">{!! trans('panel.lead.status_id') !!}</label>
                                <div class="col-md-9">
                                  <div class="form-group has-default bmd-form-group">
                                    <select class="form-control select2" name="detail[3][value][]" data-style="select-with-transition"  multiple="multiple" style="width: 100%;">
                                      @if(@isset($industries ))
                                      @foreach($industries as $industry)
                                      <option value="{!! $industry['id'] !!}">{!! $industry['name'] !!}</option>
                                      @endforeach
                                      @endif
                                    </select>
                                    @if ($errors->has('detail[3][value]'))
                                      <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('detail[3][value]') }}</p></div>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </td>
                          </tr>
                          <tr class="">
                            <td>
                              {!!  trans('panel.settings.sales_payment_approval') !!}
                            </td>
                            <td>
                              
                            </td>
                          </tr>
                          <tr class="table-danger">
                            
                          </tr>
                        </tbody>
                      </table>
                      <input type="hidden" name="module" value="dashboard_setting">
                  </div>
                   
                 <div class="row">
                  <div class="col-12 text-right">
                    {{ Form::submit('Submit', array('class' => 'btn btn-sm btn-info')) }}
                  </div>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</x-app-layout>
