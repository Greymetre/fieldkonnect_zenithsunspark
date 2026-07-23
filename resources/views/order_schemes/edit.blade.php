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

      .select2-container {
         border-bottom: 1px solid lightgray;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Order Scheme Creation
                        <!-- @if(auth()->user()->can(['district_access'])) -->
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('orderschemes') }}">
                                 <i class="material-icons">next_plan</i> {!! trans('panel.orderschemes.title') !!}
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                        </ul>
                        <!-- @endif -->
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
               {!! Form::model($schemes,[
               'route' => $schemes->exists ? ['orderschemes.update', encrypt($schemes->id) ] : 'orderschemes.store',
               'method' => $schemes->exists ? 'PUT' : 'POST',
               'id' => 'storeSchemeData',
               'files'=>true
               ]) !!}
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="bmd-label-floating">{!! trans('panel.orderschemes.fields.scheme_name') !!} </label>
                        <input type="text" name="scheme_name" class="form-control" value="{!! old( 'scheme_name', $schemes['scheme_name']) !!}">
                        @if ($errors->has('scheme_name'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('scheme_name') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <select class="select2 form-control" multiple name="customer_type[]" id="customer_type">
                           <!-- <option value="" selected disabled>Select Customer Type</option> -->
                           <option value="" disabled>Select Customer Type</option>
                           @if($customer_types && count($customer_types) > 0)
                           @foreach($customer_types as $customer_type)
                           <option value="{{$customer_type->id}}" {!! in_array($customer_type->id, old( 'customer_type[]', explode(',', $schemes['customer_type']))) ?'selected':'' !!} >{{$customer_type->customertype_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('customer_type'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('customer_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <select name="repetition" class="select2 form-control" id="repetition">
                           <option value="">Select Repetition </option>
                           <option value="1" {{($schemes->repetition == '1')?'selected':''}}>Day</option>
                           <option value="2" {{($schemes->repetition == '2')?'selected':''}}>Week</option>
                           <option value="3" {{($schemes->repetition == '3')?'selected':''}}>Month</option>
                           <option value="4" {{($schemes->repetition == '4')?'selected':''}}>Year</option>
                        </select>
                        @if ($errors->has('repetition'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('repetition') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <select name="scheme_type" class="select2 form-control" id="schemetype">
                           <option value="">{!! trans('panel.orderschemes.fields.scheme_type') !!}</option>
                           <option value="lp" {!! ($schemes->scheme_type == 'lp' ) ? "selected" : ''!!}>LP Price</option>
                           <option value="Qty" {!! ($schemes->scheme_type == 'Qty' ) ? "selected" : ''!!}>Quantity</option>
                        </select>
                        @if ($errors->has('scheme_type'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('scheme_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="weekDays row d-none">
                  <div class="col-md-6">
                     <div class="form-group {{$errors->has('day_repeat') ? config('constants.ERROR_FORM_GROUP_CLASS') : ''}}">
                        <label class="col-md-6 control-label" for="day_repeat">Repeat on days<span style="color:red">*</span></label>
                        <div class="col-md-10">
                           @foreach(config('constants.WEEK_LIST') as $key => $week)
                           <input type="checkbox" value="{{$key}}" class="weeks_data" name=week[] {{in_array($key, explode(',', $schemes->day_repeat))?'checked':''}}> {{$week}}
                           @endforeach
                           @if($errors->has('day_repeat'))
                           <strong for="day_repeat" class="help-block">{{ $errors->first('day_repeat') }}</strong>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="weeks row d-none">
                  <div class="col-md-6">
                     <div class="form-group">
                        <select name="week_repeat" class="select2 form-control" id="week_repeat">
                           <option value="">Select Week </option>
                           <option value="1" {{($schemes->week_repeat == '1')?'selected':''}}>First Week</option>
                           <option value="2" {{($schemes->week_repeat == '2')?'selected':''}}>Second Week</option>
                           <option value="3" {{($schemes->week_repeat == '3')?'selected':''}}>Third Week</option>
                           <option value="4" {{($schemes->week_repeat == '4')?'selected':''}}>Last Week</option>
                        </select>
                        @if ($errors->has('week_repeat'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('week_repeat') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="dateRang row d-none">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="bmd-label-floating">{!! trans('panel.orderschemes.fields.start_date') !!} </label>
                        <input type="text" name="start_date" class="form-control datepicker" value="{!! old( 'start_date', $schemes['start_date']) !!}" autocomplete="off" readonly>
                        @if ($errors->has('start_date'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('start_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="bmd-label-floating">{!! trans('panel.orderschemes.fields.end_date') !!} </label>
                        <input type="text" name="end_date" class="form-control datepicker" value="{!! old( 'end_date', $schemes['end_date']) !!}" autocomplete="off" readonly>
                        @if ($errors->has('end_date'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('end_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <select name="scheme_basedon" class="select2 form-control" id="schemebasedon">
                           <option value="">Scheme Based On</option>
                           <option value="value" {!! ($schemes->scheme_basedon == 'value' ) ? "selected" : ''!!}>Value</option>
                           <option value="percentage" {!! ($schemes->scheme_basedon == 'percentage' ) ? "selected" : ''!!}>Percentage</option>
                        </select>
                        @if ($errors->has('scheme_basedon'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('scheme_basedon') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-8">
                     <div class="form-group">
                        <label class="bmd-label-floating">{!! trans('panel.orderschemes.fields.scheme_description') !!} </label>
                        <textarea class="form-control" rows="4" name="scheme_description">{!! old( 'scheme_description', $schemes['scheme_description']) !!}</textarea>
                        @if ($errors->has('scheme_description'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('scheme_description') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>

               </div>

               <div class="row" id="min_max" style="display:none;">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="bmd-label-floating">{!! trans('panel.orderschemes.fields.minimum') !!} </label>
                        <input type="number" name="minimum" class="form-control" value="{!! old( 'minimum', $schemes['minimum']) !!}">
                        @if ($errors->has('minimum'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('minimum') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label class="bmd-label-floating">{!! trans('panel.orderschemes.fields.maximum') !!} </label>
                        <input type="number" name="maximum" class="form-control" value="{!! old( 'maximum', $schemes['maximum']) !!}">
                        @if ($errors->has('maximum'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('maximum') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>


               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <select name="assign_to" placeholder="Select Branch" class="select2 form-control" id="assign_to">
                           <option value="" disabled selected>Assign To</option>
                           <option {!! ($schemes->assign_to == 'all' ) ? "selected" : ''!!} value="all">All</option>
                           <option {!! ($schemes->assign_to == 'branch' ) ? "selected" : ''!!} value="branch">Branch</option>
                           <option {!! ($schemes->assign_to == 'state' ) ? "selected" : ''!!} value="state">State</option>
                           <option {!! ($schemes->assign_to == 'customer' ) ? "selected" : ''!!} value="customer">Customer</option>
                        </select>
                        @if ($errors->has('assign_to'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('assign_to') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6" id="branch">
                     <div class="form-group">
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
                     <div class="form-group">
                        <select name="state[]" multiple placeholder="Select States" class="select2 form-control" required style="width:100%;">

                           @if($states && count($states) > 0)
                           @foreach($states as $state)
                           <option value="{{$state->id}}" {!! in_array($state->id, old( 'state[]', explode(',', $schemes['state']))) ?'selected':'' !!}>{{$state->state_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('state'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('state') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6" id="customer">
                     <div class="form-group">
                        {!! Form::select('customer[]',[], old( 'customer', $schemes['customer']), ['id'=>'customer_select', 'class' => 'form-control']) !!}
                        @if ($errors->has('state'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('state') }}</p>
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
                        <div class="d-flex flex-row-reverse">
                           <div class="">
                              <div class="fileinput fileinput-new text-center d-flex flex-row-reverse mb-3" data-provides="fileinput">
                                 <span class="btn btn-just-icon btn-theme btn-file">
                                    <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="hidden">
                                    <input type="file" name="import_file" accept=".xls,.xlsx" />
                                 </span>
                                 <a href="{{ URL::to('orderschemes-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.scheme.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                                 @if( $schemes->exists && isset($schemes['orderscheme_details']) )
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
                              <th class="text-center category"> {!! trans('panel.orderschemes.fields.category_id') !!} </th>
                              <th class="text-center"> {!! trans('panel.orderschemes.fields.subcategory_id') !!}</th>
                              <th class="text-center product"> {!! trans('panel.orderschemes.fields.product_id') !!} </th>
                              <!-- <th class="text-center"> {!! trans('panel.orderschemes.fields.maximum') !!}</th> -->
                              <th class="text-center"> {!! trans('panel.orderschemes.fields.points') !!} </th>
                              <th class="text-center"> </th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr id='addr1'></tr>
                           @if( $schemes->exists && isset($schemes['orderscheme_details']) )
                           @foreach($schemes['orderscheme_details'] as $k=>$row)
                           <tr>
                              <td>
                                 <input type="hidden" name="detail_id[]" id="detail_id" value="{{$row['id']}}">
                                 {{$k+1}}
                              </td>
                              @if( isset($row['categories']['category_name']))
                              <td>
                                 <select name="category_id[]" class="form-control category rowchange" />
                                 <option value="{{ $row['category_id'] }}">{{ $row['categories']['category_name'] }}</option>
                                 </select>
                              </td>
                              @endif
                              @if( isset($row['subcategories']['subcategory_name']))
                              <td>
                                 <select name="subcategory_id[]" class="form-control category rowchange" />
                                 <option value="{{ $row['subcategory_id'] }}">{{ $row['subcategories']['subcategory_name'] }}</option>
                                 </select>
                              </td>
                              @endif
                              @if( isset($row['products']['product_name']))
                              <td>
                                 <select name="product_id[]" class="form-control product rowchange" />
                                 <option value="{{ $row['product_id'] }}">{{ $row['products']['product_name'] }}</option>
                                 </select>
                              </td>
                              @endif
                              <td>
                                 <input type="text" name="points[]" class="form-control points rowchange" value="{{ $row['points'] }}" />
                              </td>
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
      if ("{{count($schemes['orderscheme_details'])}}" > 0) {
         counter = "{{count($schemes['orderscheme_details'])}}";
      } else {
         counter = 0;
      }
      $(document).ready(function() {

         columnDisplay();
         var $table = $('table.kvcodes-dynamic-rows-example');
         $('a.add-rows').click(function(event) {
            event.preventDefault();
            var schemetype = $("#schemetype option:selected").val();
            counter++;
            if (schemetype === 'invoiceValue') {
               var newRow =
                  '<tr> <td>' + counter + '</td>' +
                  '<td><input type="text" name="points[]' + counter + '"class="form-control points rowchange" /></td>' +
                  '<td><a href="#" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
            } else {
               var newRow =
                  '<tr> <td>' + counter + '</td>' +
                  '<td class="category" style="width:30%"><select required name="category_id[]' + counter + '" class="form-control select2bs4 set_cat_' + counter + ' category_drop rowchange"> </select></td>' +
                  '<td style="width:30%" class="subCat"><select required style="max-width: 300px;" name="subcategory_id[]' + counter + '" class="form-control sub_category rowchange" onchange="getproductinfo(this.value)"/> </select></td>' +
                  '<td class="product" style="width:30%"><select required name="product_id[]' + counter + '" class="form-control select2bs4 product_drop rowchange"/></select></td>' +
                  '<td><input required type="number" name="points[]' + counter + '"class="form-control points rowchange" /></td>' +
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

      // $(function() {
      $("#schemetype").on('change', function() {

         var schemetypeqty = $("#schemetype option:selected").val();
         if (schemetypeqty === 'Qty') {
            $('#min_max').show();
         } else {
            $('#min_max').hide();
         }
         columnDisplay();
      }).trigger('change');

      // }).trigger('change');


      function getcategorylist() {
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
            var selectedCustomersString = "{!! $schemes['customer'] !!}";
            var selectedCustomersArray = selectedCustomersString.split(',').map(Number);
            setTimeout(() => {
               var $customerSelect = $('#customer_select').select2({
                  placeholder: 'Customer Select...',
                  multiple: true,
                  allowClear: true,
                  ajax: {
                     url: "{{ route('getCustomerDataSelect') }}",
                     dataType: 'json',
                     delay: 250,
                     data: function(params) {
                        return {
                           term: params.term || '',
                           page: params.page || 1
                        }
                     },
                     cache: true
                  }
               });
            }, 1000);
         } else {
            $('#state').hide();
            $('#customer').hide();
            $('#branch').hide();

         }

      }).trigger('change');


      $(document).ready(function() {

         $('#repetition').on('change', function() {

            var repetition = $(this).val();
            if (repetition == 1) {
               $('.weekDays').removeClass('d-none');
               $('.dateRang').addClass('d-none');
               $('.weeks').addClass('d-none');
            } else if (repetition == 2) {
               $('.weeks').removeClass('d-none');
               $('.weekDays').addClass('d-none');
               $('.dateRang').addClass('d-none');
            } else {
               $('.weeks').addClass('d-none');
               $('.weekDays').addClass('d-none');
               $('.dateRang').removeClass('d-none');
            }


         }).trigger('change');

      });
   </script>
</x-app-layout>