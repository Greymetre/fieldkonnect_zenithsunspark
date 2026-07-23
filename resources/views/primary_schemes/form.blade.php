<x-app-layout>
   <style>
      .select2-results__options {
         overflow: auto;
         max-height: 200px !important;
      }

      .select2-results,
      .select2-search--dropdown,
      .select2-dropdown--above {
         min-width: 250px !important;
      }

      /* .select2-container {
         border-bottom: 1px solid lightgray;
      }*/
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card pt-0 mb-0 mt-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        Primary Scheme Creation </h4>
                     <!-- @if(auth()->user()->can(['district_access'])) -->
                     <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                        <li class="nav-item">
                           <a class="nav-link" href="{{ url('primary_scheme') }}">
                              <i class="material-icons">next_plan</i> Primary Scheme
                              <div class="ripple-container"></div>
                           </a>
                        </li>
                     </ul>
                     <!-- @endif -->

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
               {!! Form::model($schemes,[
               'route' => $schemes->exists ? ['primary_scheme.update', $schemes->id ] : 'primary_scheme.store',
               'method' => $schemes->exists ? 'PUT' : 'POST',
               'id' => 'storeSchemeData',
               'files'=>true
               ]) !!}
               <div class="row">
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.orderschemes.fields.scheme_name') !!} </label>
                        <input type="text" name="scheme_name" class="form-control" value="{!! old( 'scheme_name', $schemes['scheme_name']) !!}">
                        @if ($errors->has('scheme_name'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('scheme_name') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Select Customer Type</label>
                        <select class="select2 form-control" multiple name="customer_type[]" id="customer_type">
                           <!-- <option value="" selected disabled>Select Customer Type</option> -->
                           <option value="" disabled>Select Customer Type</option>
                           @if($customer_types && count($customer_types) > 0)
                           @foreach($customer_types as $customer_type)
                           <option value="{{ $customer_type->id }}"
                              {!! in_array($customer_type->id, old('customer_type', explode(',', $schemes->customer_type)) ?? []) ? 'selected' : '' !!}>
                              {{ $customer_type->customertype_name }}
                           </option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('customer_type'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('customer_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Select Division</label>
                        <select class="select2 form-control" name="division" id="division" required>
                           <option value="" selected disabled>Select Division</option>
                           @if($primary_divs && count($primary_divs) > 0)
                           @foreach($primary_divs as $primary_div)
                           <option value="{{$primary_div->division}}" {!! $primary_div->division == old('division', $schemes['division']) ?'selected':'' !!} >{{$primary_div->division}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('division'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('division') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Select Repetition </label>
                        <select name="repetition" class="select2 form-control" id="repetition">
                           <option value="">Select Repetition </option>
                           <option value="1" {{(old('repetition', $schemes->repetition) == '1')?'selected':''}}>Day</option>
                           <option value="2" {{(old('repetition', $schemes->repetition) == '2')?'selected':''}}>Week</option>
                           <option value="3" {{(old('repetition', $schemes->repetition) == '3')?'selected':''}}>Month</option>
                           <option value="4" {{(old('repetition', $schemes->repetition) == '4')?'selected':''}}>Year</option>
                           <option value="5" {{(old('repetition', $schemes->repetition) == '5')?'selected':''}}>Quarter</option>
                        </select>
                        @if ($errors->has('repetition'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('repetition') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Select order schemes</label>
                        <select name="scheme_type" id="scheme_type" class="select2 form-control" id="schemetype">
                           <option value="">{!! trans('panel.orderschemes.fields.scheme_type') !!}</option>
                           <option value="lp" {{(old('scheme_type', $schemes->scheme_type) == 'lp')?'selected':''}}>LP Price</option>
                           <option value="Qty" {{(old('scheme_type', $schemes->scheme_type) == 'Qty')?'selected':''}}>Quantity</option>
                           <option value="grp_Qty" {{(old('scheme_type', $schemes->scheme_type) == 'grp_Qty')?'selected':''}}>Group Quantity</option>
                           <option value="gift" {{(old('scheme_type', $schemes->scheme_type) == 'gift')?'selected':''}}>Gift</option>
                        </select>
                        @if ($errors->has('scheme_type'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('scheme_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>

               </div>

               <div class="quarter-selection row d-none">
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Select Quarter</label>
                        <select name="quarter" class="select2 form-control" id="week_repeat">
                           <option value="">Select Quarter </option>
                           <option value="1" {{(old('quarter', $schemes->quarter) == '1')?'selected':''}}>Q1</option>
                           <option value="2" {{(old('quarter', $schemes->quarter) == '2')?'selected':''}}>Q2</option>
                           <option value="3" {{(old('quarter', $schemes->quarter) == '3')?'selected':''}}>Q3</option>
                           <option value="4" {{(old('quarter', $schemes->quarter) == '4')?'selected':''}}>Q4</option>
                        </select>
                        @if ($errors->has('week_repeat'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('week_repeat') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="weekDays row d-none">
                  <div class="col-md-6">
                     <div class="input_section {{$errors->has('day_repeat') ? config('constants.ERROR_FORM_GROUP_CLASS') : ''}}">
                        <label class="col-form-label" for="day_repeat">Repeat on days<span style="color:red">*</span></label>

                        @foreach(config('constants.WEEK_LIST') as $key => $week)
                        <input type="checkbox" value="{{$key}}" class="weeks_data" name=week[] checked><span class="week_data"> {{$week}}</span>
                        @endforeach
                        @if($errors->has('day_repeat'))
                        <strong for="day_repeat" class="help-block">{{ $errors->first('day_repeat') }}</strong>
                        @endif

                     </div>
                  </div>
               </div>
               <div class="weeks row d-none">
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Select Week</label>
                        <select name="week_repeat" class="select2 form-control" id="week_repeat">
                           <option value="">Select Week </option>
                           <option value="1" {{(old('week_repeat', $schemes->week_repeat) == '1')?'selected':''}}>First Week</option>
                           <option value="2" {{(old('week_repeat', $schemes->week_repeat) == '2')?'selected':''}}>Second Week</option>
                           <option value="3" {{(old('week_repeat', $schemes->week_repeat) == '3')?'selected':''}}>Third Week</option>
                           <option value="4" {{(old('week_repeat', $schemes->week_repeat) == '4')?'selected':''}}>Last Week</option>
                        </select>
                        @if ($errors->has('week_repeat'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('week_repeat') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="dateRang row d-none">
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.orderschemes.fields.start_date') !!} </label>
                        <input type="text" name="start_date" class="form-control datepicker1" value="{!! old( 'start_date', $schemes['start_date']) !!}" autocomplete="off" readonly id="start_date">
                        @if ($errors->has('start_date'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('start_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.orderschemes.fields.end_date') !!} </label>
                        <input type="text" name="end_date" class="form-control datepicker1" value="{!! old( 'end_date', $schemes['end_date']) !!}" autocomplete="off" readonly id="end_date">
                        @if ($errors->has('end_date'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('end_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>

               <div class="row">
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Scheme Based On</label>
                        <select name="scheme_basedon" class="select2 form-control" id="schemebasedon">
                           <option value="">Scheme Based On</option>
                           <option value="value" {{(old('scheme_basedon', $schemes->scheme_basedon) == 'value')?'selected':''}}>Value</option>
                           <option value="percentage" {{(old('scheme_basedon', $schemes->scheme_basedon) == 'percentage')?'selected':''}}>Percentage</option>

                        </select>
                        @if ($errors->has('scheme_basedon'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('scheme_basedon') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.orderschemes.fields.scheme_description') !!} </label>
                        <textarea class="form-control" rows="4" name="scheme_description">{!! old( 'scheme_description', $schemes->scheme_description) !!}</textarea>
                        @if ($errors->has('scheme_description'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('scheme_description') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6 d-none" id="per_pcs_div">
                     <div class="input_section">
                        <label class="col-form-label">Per Pcs?</label>
                        <input type="checkbox" name="per_pcs" id="per_pcs" value="1" {!! old( 'per_pcs', $schemes->per_pcs) == 1 ?'checked':'' !!}>
                        @if ($errors->has('per_pcs'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('per_pcs') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>

               </div>

               <div class="row" id="min_max" style="display:none;">
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.orderschemes.fields.minimum') !!} </label>
                        <input type="number" name="minimum" class="form-control" value="{!! old( 'minimum', $schemes['minimum']) !!}">
                        @if ($errors->has('minimum'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('minimum') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.orderschemes.fields.maximum') !!} </label>
                        <input type="number" name="maximum" class="form-control" value="{!! old( 'maximum', $schemes['maximum']) !!}">
                        @if ($errors->has('maximum'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('maximum') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>


               <div class="row">
                  <div class="col-md-6">
                     <div class="input_section">
                        <label class="col-form-label">Assign To</label>
                        <select name="assign_to" placeholder="Select Branch" class="select2 form-control" id="assign_to">
                           <option value="" disabled selected>Assign To</option>
                           <option {!! ($schemes->assign_to == 'all' ) ? "selected" : ''!!} value="all">All</option>
                           <option {!! ($schemes->assign_to == 'branch' ) ? "selected" : ''!!} value="branch">Branch</option>
                           <option {!! ($schemes->assign_to == 'state' ) ? "selected" : ''!!} value="state">State</option>
                           <option {!! ($schemes->assign_to == 'customer' ) ? "selected" : ''!!} value="customer">Customer</option>
                        </select>
                        @if ($errors->has('assign_to'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('assign_to') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6" id="branch">
                     <div class="input_section">
                        <label class="col-form-label">Select Branch</label>
                        <select name="branch[]" multiple placeholder="Select Branch" class="select2 form-control" required style="width:100%;">

                           @if($branchs && count($branchs) > 0)
                           @foreach($branchs as $branch)
                           <option value="{{$branch->id}}" {!! in_array($branch->id, old( 'branch[]', explode(',', $schemes['branch']))) ?'selected':'' !!} >{{$branch->branch_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('branch'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('branch') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6" id="state">
                     <div class="input_section">
                        <label class="col-form-label">Select States</label>
                        <select name="state[]" multiple placeholder="Select States" class="select2 form-control" required style="width:100%;">

                           @if($states && count($states) > 0)
                           @foreach($states as $state)
                           <option value="{{$state->id}}" {!! in_array($state->id, old( 'state[]', explode(',', $schemes['state']))) ?'selected':'' !!}>{{$state->state_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('state'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('state') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6" id="customer">
                     <div class="input_section">
                        <label class="col-form-label">Select customer </label>
                        <select name="customer[]" multiple id='customer_select' class="select2" value="{!! old( 'customer', $schemes['customer']) !!}">
                           @if($customers && count($customers) > 0)
                           @foreach($customers as $customer)
                           <option value="{{$customer->id}}" {!! in_array($customer->id, old( 'customer[]', explode(',', $schemes['customer']))) ?'selected':'' !!}>{{$customer->name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('customer'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('customer') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>

               <!--  <div class="row redemption">
                  <div class="col-md-6">
                     <div class="row">
                        <label class="col-sm-3 col-form-label">{!! trans('panel.orderschemes.fields.points_start_date') !!}</label>
                        <div class="col-sm-9">
                           <div class="form-group bmd-form-group is-filled">
                              <input type="text" name="points_start_date" class="form-control datepicker" value="{!! old( 'points_start_date') !!}" autocomplete="off" readonly>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="row">
                        <label class="col-sm-3 col-form-label">{!! trans('panel.orderschemes.fields.points_end_date') !!}</label>
                        <div class="col-sm-9">
                           <div class="form-group bmd-form-group is-filled">
                              <input type="text" name="points_end_date" class="form-control datepicker" value="{!! old( 'points_end_date') !!}" autocomplete="off" readonly>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row redemption">
                  <div class="col-md-6">
                     <div class="row">
                        <label class="col-sm-3 col-form-label">{!! trans('panel.scheme.fields.block_points') !!}</label>
                        <div class="col-sm-9">
                           <div class="form-group bmd-form-group is-filled">
                              <input class="form-control" name="block_points" type="text">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="row">
                        <label class="col-sm-3 col-form-label">{!! trans('panel.scheme.fields.block_percents') !!}</label>
                        <div class="col-sm-9">
                           <div class="form-group bmd-form-group is-filled">
                              <input class="form-control" name="block_percents" type="text">
                           </div>
                        </div>
                     </div>
                  </div>
               </div>-->


               <div class="row clearfix earnscheme">
                  <div class="col-md-12">
                     <span class="mt-2" style="float: right;">
                        <span style="background: #00aadb;color: #fff;padding: 5px;border-radius: 5px;font-weight: 500;">*Import product in this scheme please check first template</span>
                        <div class="d-flex flex-row-reverse mb-3">
                           <div class="">
                              <div class="fileinput fileinput-new text-center d-flex flex-row-reverse" data-provides="fileinput">
                                 <span class="btn btn-just-icon btn-theme btn-file">
                                    <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="hidden">
                                    <input type="file" name="import_file" accept=".xls,.xlsx" />
                                 </span>
                                 <a href="{{ URL::to('primary_scheme_template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.scheme.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                                 <a href="{{ URL::to('primary_scheme_report/template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} Group {!! trans('panel.scheme.title_singular') !!}"><i class="material-icons">description</i></a>
                                 @if( $schemes->exists && isset($schemes['primaryscheme_details']) )
                                 <a href="{{ URL::to('orderschemes-download') }}?id={{$schemes->id}}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.scheme.title') !!}"><i class="material-icons">cloud_download</i></a>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </span>
                     <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                        <thead>
                           <tr>
                              <th class="text-center"> # </th>
                              <th class="text-center group_type_th">Group Type</th>
                              <th class="text-center category"> {!! trans('panel.orderschemes.fields.category_id') !!} </th>
                              <th class="text-center sub-category"> {!! trans('panel.orderschemes.fields.subcategory_id') !!}</th>
                              <th class="text-center product"> {!! trans('panel.orderschemes.fields.product_id') !!} </th>
                              <!-- <th class="text-center"> {!! trans('panel.orderschemes.fields.maximum') !!}</th> -->
                              <th class="text-center point"> {!! trans('panel.orderschemes.fields.points') !!} </th>
                              <th class="text-center"> </th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr id='addr1'></tr>
                           @if( $schemes->exists && isset($schemes['primaryscheme_details']) )
                           @foreach($schemes['primaryscheme_details'] as $k=>$row)
                           <tr>
                              <td>
                                 <div class="input_section">
                                    <input type="hidden" name="detail_id[]" id="detail_id" value="{{$row['id']}}">
                                    {{$k+1}}
                                 </div>
                              </td>
                              @if( isset($row['categories']['category_name']))
                              <td>
                                 <div class="input_section">
                                    <select name="category_id[]" class="form-control rowchange">
                                       <option value="{{ $row['category_id'] }}">{{ $row['categories']['category_name'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['groups']))
                              <td>
                                 <div class="input_section">
                                    <select name="group_type[]" class="form-control rowchange">
                                       <option value="" disabled selected>Select Group Type</option>
                                       <option value="group_1" {{$row['group_type'] == 'group_1' ? 'selected' : ''}}>group_1</option>
                                       <option value="group_2" {{$row['group_type'] == 'group_2' ? 'selected' : ''}}>group_2</option>
                                       <option value="group_3" {{$row['group_type'] == 'group_3' ? 'selected' : ''}}>group_3</option>
                                       <option value="group_4"{{$row['group_type'] == 'group_4' ? 'selected' : ''}}>group_4</option>
                                    </select>
                                 </div>
                              </td>
                              <td>
                                 <div class="input_section">
                                    <select name="groups[]" class="form-control rowchange">
                                       <option value="{{ $row['groups'] }}">{{ $row['groups'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['min']))
                              <td>
                                 <div class="input_section">
                                    <select name="min[]" class="form-control rowchange">
                                       <option value="{{ $row['min'] }}">{{ $row['min'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['max']))
                              <td>
                                 <div class="input_section">
                                    <select name="max[]" class="form-control rowchange">
                                       <option value="{{ $row['max'] }}">{{ $row['max'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['slab_min']))
                              <td>
                                 <div class="input_section">
                                    <select name="slab_min[]" class="form-control rowchange">
                                       <option value="{{ $row['slab_min'] }}">{{ $row['slab_min'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['slab_max']))
                              <td>
                                 <div class="input_section">
                                    <select name="slab_max[]" class="form-control rowchange">
                                       <option value="{{ $row['slab_max'] }}">{{ $row['slab_max'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['gift']))
                              <td>
                                 <div class="input_section">
                                    <select name="gift[]" class="form-control rowchange">
                                       <option value="{{ $row['gift'] }}">{{ $row['gift'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['subcategories']['subcategory_name']))
                              <td>
                                 <div class="input_section">
                                    <select name="subcategory_id[]" class="form-control rowchange">
                                       <option value="{{ $row['subcategory_id'] }}">{{ $row['subcategories']['subcategory_name'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['products']['product_name']))
                              <td>
                                 <div class="input_section">
                                    <select name="product_id[]" class="form-control rowchange">
                                       <option value="{{ $row['product_id'] }}">{{ $row['products']['product_name'] }}</option>
                                    </select>
                                 </div>
                              </td>
                              @endif
                              @if( isset($row['points']) && $row['points'] > 0)
                              <td>
                                 <div class="input_section">
                                    <input type="text" name="points[]" class="form-control points rowchange" value="{{ $row['points'] }}" />
                                 </div>
                              </td>
                              @endif
                              <td class="td-actions text-center">
                                 <a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a>
                              </td>
                           </tr>
                           @endforeach
                           @endif
                        </tbody>
                     </table>
                  </div>
                  <div class="row clearfix">
                     <div class="col-md-12">
                        <table class="table">
                           <tbody>
                              <tr>
                                 <td class="td-actions">
                                    <a href="#" title="" class="btn btn-success btn-just-icon btn-sm add-rows" onclick="getcategorylist()"> <i class="fa fa-plus"></i> </a>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
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

   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
      if ("{{count($schemes['primaryscheme_details'])}}" > 0) {
         counter = "{{count($schemes['primaryscheme_details'])}}";
      } else {
         counter = 0;
      }
      $(document).ready(function() {

         // columnDisplay();
         var $table = $('table.kvcodes-dynamic-rows-example');
         $('a.add-rows').click(function(event) {
            event.preventDefault();
            var schemetype = $("#scheme_type").val();
            counter++;
            if (schemetype == 'grp_Qty') {
               var newRow =
                  '<tr> <td>' + counter + '</td>' +
                  '<td class="group" style="width:30%"><div class="input_section"><select required name="group_type[]' + counter + '" class="form-control select2  set_group_type_' + counter + ' group_type_drop rowchange"><option value="group_1">group_1</option><option value="group_2">group_2</option><option value="group_3">group_3</option><option value="group_4">group_4</option></select></td>' +
                  '<td class="group" style="width:30%"><div class="input_section"><select required name="groups[]' + counter + '" class="form-control select2  set_cat_' + counter + ' group_drop rowchange"></select></td>' +
                  '<td style="width:30%" class="subCat"><div class="input_section"><input type="number" name="min[]' + counter + '" class="form-control  set_min_' + counter + '"></div></td>' +
                  '<td style="width:30%"><div class="input_section"><input type="number" name="max[]' + counter + '" class="form-control  set_max_' + counter + '"></div></td>' +
                  '<td><div class="input_section"><input required type="number" name="points[]' + counter + '"class="form-control points rowchange" /></div></td>' +
                  '<td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></td> </tr>';
            } else if (schemetype == 'gift') {
               var newRow =
                  '<tr> <td>' + counter + '</td>' +
                  '<td class="group" style="width:30%"><div class="input_section"><select required name="group_type[]' + counter + '" class="form-control select2  set_group_type_' + counter + ' group_type_drop rowchange"><option value="group_1">group_1</option><option value="group_2">group_2</option><option value="group_3">group_3</option><option value="group_4">group_4</option></select></td>' +
                  '<td class="group" style="width:20%"><div class="input_section"><select required name="groups[]' + counter + '" class="form-control select2  set_cat_' + counter + ' group_drop rowchange"></select></td>' +
                  '<td style="width:10%" class="subCat"><div class="input_section"><input type="number" name="min[]' + counter + '" class="form-control  set_min_' + counter + '"></div></td>' +
                  '<td style="width:10%"><div class="input_section"><input type="number" name="max[]' + counter + '" class="form-control  set_max_' + counter + '"></div></td>' +

                  
                  '<td style="width:10%" class="subCat"><div class="input_section"><input type="number" name="slab_min[]' + counter + '" class="form-control  set_slab_min_' + counter + '"></div></td>' +
                  '<td style="width:10%"><div class="input_section"><input type="number" name="slab_max[]' + counter + '" class="form-control  set_slab_max_' + counter + '"></div></td>' +



                  '<td style="width:30%"><div class="input_section"><input required type="text" name="gift[]' + counter + '"class="form-control gift rowchange" /></div></td>' +
                  '<td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></td> </tr>';
            } else {
               var newRow =
                  '<tr> <td>' + counter + '</td>' +
                  '<td class="category" style="width:30%"><div class="input_section"><select required name="category_id[]' + counter + '" class="form-control  set_cat_' + counter + ' category_drop rowchange"> </select></td>' +
                  '<td style="width:30%" class="subCat"><div class="input_section"><select required style="max-width: 300px;" name="subcategory_id[]' + counter + '" class="form-control select2 sub_category rowchange" onchange="getproductinfo(this.value)"/> </select></div></td>' +
                  '<td class="product" style="width:30%"><div class="input_section"><select required name="product_id[]' + counter + '" class="form-control select2 product_drop rowchange" placeholder="selected" /></select></div></td>' +
                  '<td><div class="input_section"><input required type="number" name="points[]' + counter + '"class="form-control points rowchange" /></div></td>' +
                  '<td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></td> </tr>';
            }

            $table.append(newRow);
            $('.select2bs4').select2({
               theme: 'bootstrap4'
            })
         });

         $table.on('click', '.remove-rows', function() {
            var tr = $(this).closest('tr');
            var inputVal = tr.find('input[name="detail_id[]"]').val();
            if (inputVal) {
               $.ajax({
                  url: "/schemesdetails/remove",
                  data: {
                     "id": inputVal
                  },
                  success: function(data) {
                     console.log(data);
                  }
               });
            }
            $(this).closest('tr').remove();
         });
      });

      function columnDisplay() {
         var schemetype = $("#schemetype option:selected").val();
         if (schemetype === 'invoiceValue') {
            $('.category').hide();
            $('.product').hide();
            $('.redemption').hide();
            $('.earnscheme').show();
         } else if (schemetype === 'redemption') {
            $('.redemption').show();
            $('.earnscheme').hide();
         } else {
            $('.category').show();
            $('.product').show();
            $('.earnscheme').show();
            $('.redemption').hide();
         }
      }

      $(function() {
         $("#schemetype").on('change', function() {

            var schemetypeqty = $("#schemetype option:selected").val();
            if (schemetypeqty === 'Qty') {
               $('#min_max').show();
            } else {
               $('#min_max').hide();
            }

            columnDisplay();
         })

      }).trigger('change');


      function getcategorylist() {
         if ($("#scheme_type").val() == 'grp_Qty' || $("#scheme_type").val() == 'gift') {
            $.ajax({
               //url: "/getCategoryData",
               url: "{{url('/getPrimaryGroup?gruop_type=group_1')}}",
               success: function(data) {
                  var html = '<option value="">Select Group</option>';
                  $.each(data, function(k, v) {
                     if(v != null){
                        html += '<option value="' + v + '">' + v + '</option>';
                     }
                  });
                  $('.set_cat_' + counter).html(html);
                  $('.select2').select2();
               }
            });
         } else {
            $.ajax({
               //url: "/getCategoryData",
               url: "{{url('/getCategoryData')}}",
               success: function(data) {
                  var html = '<option value="">Select Category</option>';
                  $.each(data, function(k, v) {
                     html += '<option value="' + v.id + '">' + v.category_name + '</option>';
                  });
                  $('.set_cat_' + counter).html(html);
               }
            });
         }
      }


      $(document).on("change", ".category_drop", function() {
         var cat = $(this).val();
         var tr = $(this).closest('tr');
         var subInput = tr.find('.sub_category');
         $.ajax({
            //url: "/getSubCategoryData",
            url: "{{url('/getSubCategoryData')}}",
            data: {
               'cat_id': cat
            },
            success: function(data) {
               var html = '<option value="">Select Sub Category</option>';
               $.each(data, function(k, v) {
                  html += '<option value="' + v.id + '">' + v.subcategory_name + '</option>';
               });
               $(subInput).html(html);
            }
         });
      })

      $(document).on("change", ".sub_category", function() {
         var cat = $(this).val();
         var tr = $(this).closest('tr');
         var proInput = tr.find('.product_drop');
         $.ajax({
            //url: "/getProductData",
            url: "{{url('/getProductData')}}",
            data: {
               'sub_cat': cat
            },
            success: function(data) {
               var html = '<option value="">Select Product</option>';
               $.each(data, function(k, v) {
                  html += '<option value="' + v.id + '">' + v.product_name + '</option>';
               });
               $(proInput).html(html);
            }
         });
      })

      $("#assign_to").on("change", function() {
         var assignTo = $(this).val();
         if (assignTo == 'branch') {
            $('#branch').show();
            $('#state').hide();
            $('#customer').hide();
         } else if (assignTo == 'state') {
            $('#state').show();
            $('#branch').hide();
            $('#customer').hide();
         } else if (assignTo == 'customer') {
            $('#customer').show();
            $('#state').hide();
            $('#branch').hide();
            // var selectedCustomersString = "{!! $schemes['customer'] !!}";
            // var selectedCustomersArray = selectedCustomersString.split(',').map(Number);
            // setTimeout(() => {
            //    var $customerSelect = $('#customer_select').select2({
            //       placeholder: 'Customer Select...',
            //       multiple: true,
            //       allowClear: true,
            //       ajax: {
            //          url: "{{ route('getCustomerDataSelect') }}",
            //          dataType: 'json',
            //          delay: 250,
            //          data: function(params) {
            //             return {
            //                term: params.term || '',
            //                page: params.page || 1
            //             }
            //          },
            //          cache: true
            //       }
            //    });
            // }, 1000);
         } else {
            $('#state').hide();
            $('#customer').hide();
            $('#branch').hide();

         }

      }).trigger('change');
   </script>


   <script>
      $(function() {
         $("#start_date").datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(selected) {
               $("#end_date").datepicker("option", "minDate", selected);
            }
         });

         $("#end_date").datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(selected) {
               $("#start_date").datepicker("option", "maxDate", selected);
            }
         });
      });


      // for years
      $("#ones_in_year").datepicker({
         dateFormat: 'dd-MM',

      });


      //for month date
      $("#date_of_month").datepicker({
         dateFormat: 'dd',

      });
   </script>

   <script>
      $(document).ready(function() {

         $('#repetition').on('change', function() {

            var repetition = $(this).val();
            if (repetition == 1) {
               $('.weekDays').removeClass('d-none');
               $('.dateRang').addClass('d-none');
               $('.weeks').addClass('d-none');
               $('.quarter-selection').addClass('d-none');
            } else if (repetition == 2) {
               $('.weeks').removeClass('d-none');
               $('.weekDays').addClass('d-none');
               $('.dateRang').addClass('d-none');
               $('.quarter-selection').addClass('d-none');
            } else if (repetition == 5) {
               $('.quarter-selection').removeClass('d-none');
               $('.weeks').addClass('d-none');
               $('.weekDays').addClass('d-none');
               $('.dateRang').addClass('d-none');
            } else {
               $('.weeks').addClass('d-none');
               $('.weekDays').addClass('d-none');
               $('.dateRang').removeClass('d-none');
               $('.quarter-selection').addClass('d-none');
            }


         }).trigger('change');

      });

      $('#scheme_type').on('change', function() {
         // $('table.kvcodes-dynamic-rows-example tbody').html('');
         var schemeType = $(this).val();
         if (schemeType == 'grp_Qty') {
            $('#tab_logic .group_type_th').removeClass('d-none');
            $('#tab_logic .category').html('Group');
            $('#tab_logic .sub-category').html('Min');
            $('#tab_logic .product').html('Max');
            $('#tab_logic .point').html('Discount');
            $('#tab_logic thead tr th.slab_max').remove();
            $('#tab_logic thead tr th.giftth').remove();

            $('#min_max').hide();
         } else if (schemeType == 'gift') {
            $('#tab_logic .group_type_th').removeClass('d-none');
            $('#tab_logic .category').html('Group');
            $('#tab_logic .sub-category').html('Min');
            $('#tab_logic .product').html('Max');
            $('#tab_logic .point').html('Slab Min');
            $('#tab_logic thead tr th.point').after('<th class="text-center giftth">Gift</th>');
            $('#tab_logic thead tr th.point').after('<th class="text-center slab_max">Slab Max</th>');
            $('#min_max').hide();
         } else if (schemeType == 'Qty') {
            $('#min_max').show();
            $('#tab_logic .group_type_th').addClass('d-none');
            $('#tab_logic .category').html('Category');
            $('#tab_logic .sub-category').html('Sub category');
            $('#tab_logic .product').html('Product');
            $('#tab_logic .point').html('Discount');
            $('#tab_logic thead tr th.slab_max').remove();
            $('#tab_logic thead tr th.giftth').remove();

         }else{
            $('#min_max').hide();
            $('#tab_logic .group_type_th').addClass('d-none');
            $('#tab_logic .category').html('Category');
            $('#tab_logic .sub-category').html('Sub category');
            $('#tab_logic .product').html('Product');
            $('#tab_logic .point').html('Discount');
            $('#tab_logic thead tr th.slab_max').remove();
            $('#tab_logic thead tr th.giftth').remove();

         }
      }).trigger('change');

      $("#schemebasedon").on("change", function(){
         var basedOn = $(this).val();
         if(basedOn == 'value'){
            $("#per_pcs_div").removeClass('d-none');
         }else{
            $("#per_pcs_div").addClass('d-none');
         }
      }).trigger("change");

      $(document).on('change', '.group_type_drop', function() {
         var gruop_type = $(this).val();
         let currentRow = $(this).closest('tr');
         let groupDrop = currentRow.find('.group_drop');
         $.ajax({
               //url: "/getCategoryData",
               url: "{{url('/getPrimaryGroup?gruop_type=')}}"+gruop_type,
               success: function(data) {
                  var html = '<option value="">Select Group</option>';
                  $.each(data, function(k, v) {
                     if(v != null){
                        html += '<option value="' + v + '">' + v + '</option>';
                     }
                  });
                  $(groupDrop).html(html);
                  $('.select2').select2();
               }
            });
      })

   </script>



</x-app-layout>