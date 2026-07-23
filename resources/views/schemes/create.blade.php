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
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card pt-0 mt-0">
            <div class="card-header card-header-tabs card-header-warning m-0">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        Loyalty Scheme Creation </h4>
                     @if(auth()->user()->can(['district_access']))
                     <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                        <li class="nav-item">
                           <a class="nav-link" href="{{ url('schemes') }}">
                              <i class="material-icons">next_plan</i> {!! trans('panel.scheme.title') !!}
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
               {!! Form::model($schemes,[
               'route' => $schemes->exists ? ['schemes.update', encrypt($schemes->id) ] : 'schemes.store',
               'method' => $schemes->exists ? 'PUT' : 'POST',
               'id' => 'storeSchemeData',
               'files'=>true
               ]) !!}
               <div class="row">
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.scheme_name') !!} </label>
                        <input type="text" name="scheme_name" class="form-control" value="{!! old( 'scheme_name', $schemes['scheme_name']) !!}">
                        @if ($errors->has('scheme_name'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('scheme_name') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">Select Customer Type </label>
                        <select class="select2 form-control" name="customer_type" id="customer_type">
                           <option value="" selected disabled>Select Customer Type</option>
                           @if($customer_types && count($customer_types) > 0)
                           @foreach($customer_types as $customer_type)
                           <option value="{{$customer_type->id}}" {!! old( 'customer_type' , $schemes['customer_type'])==$customer_type->id?'selected':'' !!} >{{$customer_type->customertype_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('point_value'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('point_value') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.start_date') !!} </label>
                        <input type="text" name="start_date" class="form-control datepicker" value="{!! old( 'start_date', $schemes['start_date']) !!}" autocomplete="off" readonly>
                        @if ($errors->has('start_date'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('start_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.end_date') !!} </label>
                        <input type="text" name="end_date" class="form-control datepicker" value="{!! old( 'end_date', $schemes['end_date']) !!}" autocomplete="off" readonly>
                        @if ($errors->has('end_date'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('end_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">select </label>
                        <select name="scheme_type" class="select2 form-control" id="schemetype">
                           <option value="">{!! trans('panel.scheme.fields.scheme_type') !!}</option>
                           <option value="invoice" {!! ($schemes->scheme_type == 'invoice' ) ? "selected" : ''!!}>Invoice</option>
                           <option value="mrp" {!! ($schemes->scheme_type == 'mrp' ) ? "selected" : ''!!}>MRP</option>
                           <option value="Qty" {!! ($schemes->scheme_type == 'Qty' ) ? "selected" : ''!!}>Quantity</option>
                           <option value="coupon" {!! ($schemes->scheme_type == 'coupon' ) ? "selected" : ''!!}>Coupons</option>
                        </select>
                        @if ($errors->has('scheme_type'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('scheme_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">Scheme Based On </label>
                        <select name="scheme_basedon" class="select2 form-control" id="schemebasedon">
                           <option value="">Scheme Based On</option>
                           <option value="value" {!! ($schemes->scheme_basedon == 'value' ) ? "selected" : ''!!}>Value</option>
                           <option value="percentage" {!! ($schemes->scheme_basedon == 'percentage' ) ? "selected" : ''!!}>Percentage</option>
                           <option value="Qty" {!! ($schemes->scheme_basedon == 'Qty' ) ? "selected" : ''!!}>Quantity</option>
                           <option value="coupon" {!! ($schemes->scheme_basedon == 'coupon' ) ? "selected" : ''!!}>Coupons</option>
                        </select>
                        @if ($errors->has('scheme_basedon'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('scheme_basedon') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.scheme_description') !!} </label>
                        <textarea class="form-control" rows="4" name="scheme_description">{!! old( 'scheme_description', $schemes['scheme_description']) !!}</textarea>
                        @if ($errors->has('scheme_description'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('scheme_description') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.scheme_image') !!}</label>

                        <!-- <div class="custom-file"> -->





                        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                           <div class="fileinput-new thumbnail">
                              <img src="https://expertfromindia.in/bediya/public/assets/img/placeholder.jpg" class="imagepreview1">
                              <div class="selectThumbnail">
                                 <span class="btn btn-just-icon btn-round btn-file">
                                    <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="file" name="image" class="getimage1" accept="image/*">
                                 </span>
                                 <br>
                                 <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                              </div>
                           </div>



                           @if ($errors->has('scheme_image'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('scheme_image') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">Assign To</label>
                        <select name="assign_to" placeholder="Select Branch" class="select2 form-control" id="assign_to">
                           <option value="" disabled selected>Assign To</option>
                           <option {!! ($schemes->assign_to == 'all' ) ? "selected" : ''!!} value="all">All</option>
                           <option {!! ($schemes->assign_to == 'branch' ) ? "selected" : ''!!} value="branch">Branch</option>
                           <option {!! ($schemes->assign_to == 'state' ) ? "selected" : ''!!} value="state">State</option>
                           <option {!! ($schemes->assign_to == 'customer' ) ? "selected" : ''!!} value="customer">Customer</option>
                        </select>
                        @if ($errors->has('scheme_type'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('scheme_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4" id="branch">
                     <div class="input_section">
                        <label class="col-form-label">Select Branch</label>
                        <select name="branch[]" multiple placeholder="Select Branch" class="select2 form-control" required>
                           <!-- <option value="">select branches</option> -->
                           @if($branchs && count($branchs) > 0)
                           @foreach($branchs as $branch)
                           <option value="{{$branch->id}}" {!! in_array($branch->id, old( 'branch[]', explode(',', $schemes['branch']))) ?'selected':'' !!} >{{$branch->branch_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('branch'))
                        <div class="error">
                           <p class="text-danger">{{ $errors->first('branch') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-4" id="state">
                     <div class="input_section">
                        <label class="col-form-label">Select States</label>
                        <select name="state[]" multiple placeholder="Select States" class="select2 form-control" required>
                           <!-- <option value="">select branches</option> -->
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
                  <div class="col-md-4" id="customer">
                     <div class="input_section">
                        <!-- <select name="customer[]" id='customer_select' value="{!! old( 'customer', $schemes['customer']) !!}" ></select> -->
                        {!! Form::select('customer[]',[], old( 'customer', $schemes['customer']), ['id'=>'customer_select', 'class' => 'form-control']) !!}
                        @if ($errors->has('state'))
                        <div class="error ">
                           <p class="text-danger">{{ $errors->first('state') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>





               </div>



               <div class="row redemption">
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.points_start_date') !!}</label>

                        <div class="form-group bmd-form-group is-filled">
                           <input type="text" name="points_start_date" class="form-control datepicker" value="{!! old( 'points_start_date', $schemes['points_start_date']) !!}" autocomplete="off" readonly>
                        </div>

                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.points_end_date') !!}</label>

                        <div class="form-group bmd-form-group is-filled">
                           <input type="text" name="points_end_date" class="form-control datepicker" value="{!! old( 'points_end_date', $schemes['points_end_date']) !!}" autocomplete="off" readonly>
                        </div>

                     </div>
                  </div>

                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.block_points') !!}</label>

                        <div class="form-group bmd-form-group is-filled">
                           <input class="form-control" name="block_points" type="text">
                        </div>

                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="input_section">
                        <label class="col-form-label">{!! trans('panel.scheme.fields.block_percents') !!}</label>

                        <div class="form-group bmd-form-group is-filled">
                           <input class="form-control" name="block_percents" type="text">

                        </div>
                     </div>
                  </div>

               </div>

               <div class="row clearfix earnscheme">
                  <div class="col-md-12">
                     <span class="mt-2" style="float: right;">
                        <!-- <span style="background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%);color: #fff;padding: 5px;border-radius: 5px;font-weight: 500;">*Import product in this scheme please check first template</span> -->
                        <div class="d-flex flex-row-reverse">
                           <div class="">
                              <div class="fileinput fileinput-new text-center d-flex flex-row-reverse" data-provides="fileinput">
                                 <span class="btn btn-just-icon btn-theme btn-file">
                                    <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="hidden">
                                    <input type="file" name="import_file" accept=".xls,.xlsx" />
                                 </span>
                                 <!-- <a href="{{ URL::to('schemes-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.scheme.title_singular') !!}"><i class="material-icons">text_snippet</i></a> -->
                                 @if( $schemes->exists && isset($schemes['schemedetails']) )
                                 <a href="{{ URL::to('schemes-download') }}?id={{$schemes->id}}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.scheme.title') !!}"><i class="material-icons">cloud_download</i></a>
                                 <a href="{{ route('scheme.product-.pdf') }}?id={{$schemes->id}}&group=no" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.pdf_module.download') !!} {!! trans('panel.pdf_module.pdf') !!}"><i class="material-icons">picture_as_pdf</i></a>
                                 <a href="{{ route('scheme.product-.pdf') }}?id={{$schemes->id}}&group=yes" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.pdf_module.download') !!} Point Wise {!! trans('panel.pdf_module.pdf') !!}"><i class="material-icons">picture_as_pdf</i></a>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </span>
                     <div class="table-responsive">
                        <table id="tab_logic" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                           <thead>
                              <tr>
                                 <th class="text-center"> # </th>
                                 <th class="text-center category"> {!! trans('panel.scheme.fields.category_id') !!} </th>
                                 <th class="text-center"> Sub Category</th>
                                 <th class="text-center product"> {!! trans('panel.scheme.fields.product_id') !!} </th>
                                 <th class="text-center"> Active Points</th>
                                 <th class="text-center"> Provision Points</th>
                                 <th class="text-center"> Total {!! trans('panel.scheme.fields.points') !!} </th>
                                 <!-- <th class="text-center"> </th> -->
                              </tr>
                           </thead>
                        </table>
                     </div>
                  </div>

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


               <div class="pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
      if ("{{count($schemes['schemedetails'])}}" > 0) {
         counter = "{{count($schemes['schemedetails'])}}";
      } else {
         counter = 0;
      }
      $(document).ready(function() {
         // var assignTo = $('#assign_to').val();
         // if (assignTo == 'branch') {
         //    $('#branch').show();
         //    $('#state').hide();
         //    $('#customer').hide();
         // } else if (assignTo == 'state') {
         //    $('#state').show();
         //    $('#branch').hide();
         //    $('#customer').hide();
         // } else if (assignTo == 'customer') {
         //    $('#customer').show();
         //    $('#state').hide();
         //    $('#branch').hide();
         // } else {
         //    $('#state').hide();
         //    $('#customer').hide();
         //    $('#branch').hide();

         // }
         columnDisplay();
         var $table = $('table#tab_logic');
         $('a.add-rows').click(function(event) {
            event.preventDefault();
            var schemetype = $("#schemetype option:selected").val();
            counter++;
            if (schemetype === 'invoiceValue') {
               var newRow =
                  '<tr> <td>' + counter + '</td>' +
                  '<td><div class="input_section"><input type="text" name="points[]' + counter + '"class="form-control points rowchange" /></div></td>' +
                  '<td><a href="#" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
            } else {
               var newRow =
                  '<tr> <td>' + counter + '</td>' +
                  '<td class="category"><div class="input_section"><select required name="category_id[]' + counter + '" class="form-control set_cat_' + counter + ' category_drop rowchange"> </select></div></td>' +
                  '<td class="subCat"><div class="input_section"><select required  name="subcategory_id[]' + counter + '" class="form-control select2 sub_category rowchange" /></div> </select></td>' +
                  '<td class="product"><div class="input_section"><select required name="product_id[]' + counter + '" class="form-control select2 product_drop rowchange"/></select></div></td>' +
                  '<td><div class="input_section"><input required type="number" name="active_point[]' + counter + '"class="form-control active_point rowchange" /></div></td>' +
                  '<td><div class="input_section"><input required type="number" name="provision_point[]' + counter + '"class="form-control provision_point rowchange" /></div></td>' +
                  '<td><div class="input_section"><input required type="number" name="points[]' + counter + '"class="form-control points rowchange" /><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></div></td>' +
                  ' </tr>';
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
            columnDisplay();
         })

      });

      function getcategorylist() {
         $.ajax({
            url: "/getCategoryData",
            success: function(data) {
               var html = '<option value="">Select Category</option>';
               $.each(data, function(k, v) {
                  html += '<option value="' + v.id + '">' + v.category_name + '</option>';
               });
               $('.set_cat_' + counter).html(html);
            }
         });
      }

      // function getsubcategorylist(cat) {
      //    console.log($(this).closest('tr'));
      //    $.ajax({
      //       url: "/getSubCategoryData",
      //       data: {
      //          'cat_id': cat
      //       },
      //       success: function(data) {
      //          var html = '<option value="">Select Sub Category</option>';
      //          $.each(data, function(k, v) {
      //             html += '<option value="' + v.id + '">' + v.subcategory_name + '</option>';
      //          });
      //          $('.sub_category_' + counter).html(html);
      //       }
      //    });
      // }
      $(document).on("change", ".category_drop", function() {
         var cat = $(this).val();
         var tr = $(this).closest('tr');
         var subInput = tr.find('.sub_category');
         $.ajax({
            url: "/getSubCategoryData",
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
            url: "/getProductData",
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
   </script>
   <script type="text/javascript">
      $(function() {
         $.ajaxSetup({
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
         });
         var table = $('#tab_logic').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [
               [0, 'desc']
            ],
            //"dom": 'Bfrtip',
            ajax: {
               url: "{{ route('scheme.product.ist') }}",
               data: function(d) {
                  d.scheme_id = '{{$schemes->exists?$schemes->id:"0"}}'
               }
            },
            columns: [{
                  data: 'DT_RowIndex',
                  name: 'DT_RowIndex',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'categories.category_name',
                  name: 'categories.category_name',
                  orderable: false,
                  searchable: false
               },
               {
                  data: 'subcategories.subcategory_name',
                  name: 'subcategories.subcategory_name',
                  orderable: false
               },
               {
                  data: 'products.product_name',
                  name: 'products.product_name',
                  orderable: false
               },
               {
                  data: 'active_point',
                  name: 'active_point'
               },
               {
                  data: 'provision_point',
                  name: 'provision_point'
               },
               {
                  data: 'points',
                  name: 'points'
               },
            ]
         });
      });
   </script>
</x-app-layout>