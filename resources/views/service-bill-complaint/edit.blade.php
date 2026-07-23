<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.edit') }} Service Bill Complaint Type
            <span class="pull-right">
              <div class="btn-group">
                <!-- @if(auth()->user()->can(['product_access'])) -->
                <a href="{{ url('service-bills-complaints-type') }}" class="btn btn-just-icon btn-theme" title="Service Bill Complaint Types"><i class="material-icons">next_plan</i></a>
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
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_success') !!}
            </span>
          </div>
          @endif
          @if(session()->has('message_error'))
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_error') !!}
            </span>
          </div>
          @endif
          {!! Form::model($complaint_type,[
          'route' => $complaint_type->exists ? ['service-bills-complaints-type.update', encrypt($complaint_type->id) ] : 'service-bills-complaints-type.store',
          'method' => $complaint_type->exists ? 'PUT' : 'POST',
          'id' => 'editeSerCoTyForm',
          'files'=>true
          ]) !!}

          <div class="row">

          </div>   
          <div class="row">
             <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Product Group<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="subcategory_id" id="subcategory_id" {{ $complaint_type->exists ? 'disabled' : '' }}>
                         <option value=''>Product Group</option>
                         @foreach($product_groups as $product_group)
                           <option value="{{ $product_group->id }}" {{ $complaint_type->exists && $complaint_type->service_group_complaints->subcategory_id == $product_group->id ? 'selected' : '' }}>
                              {{ $product_group->subcategory_name  ?? '' }}
                          </option>
                         @endforeach
                  </select>
                  @if ($errors->has('product_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input_section">
                <label class="col-form-label">Complaint Type<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control" name="service_bill_complaint_type_name" id="service_bill_complaint_type_name" value="{{ $complaint_type->exists && $complaint_type->service_bill_complaint_type_name  ?  $complaint_type->service_bill_complaint_type_name :  '' }}" placeholder="Complaint Type">
                  @if ($errors->has('service_bill_complaint_type_name'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('service_bill_complaint_type_name') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
             <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                  <div class="table-responsive" style="max-height: 500px; overflow-y: auto; width: 100%;">
                      <table class="table kvcodes-dynamic-rows-example" id="tab_logic" >
                          <thead>
                              <tr class="text-white">
                                  <th class="text-center">#</th>
                                  <th class="text-center" >Complaints Reasons</th>
                                  <th class="text-center">Actions</th>
                              </tr>
                          </thead>
                          <tbody id="sopTableBody">
                              @if(count($complaint_type->service_complaint_reasons) > 0)
                                   @foreach($complaint_type->service_complaint_reasons as $index => $reasons)
                                         <tr id='addr{{$index}}' value="{{$index+1}}">
                                           <td>{{$index+1}}</td>
                                           <td>
                                              <div class="input_section">
                                                  <div class="form-group has-default bmd-form-group">
                                                      <input type="text" class="form-control complaints_reasons" name="complaints_reasons[]" value="{{$reasons->service_complaint_reasons ?? ''}}" required placeholder="Complaint Reasons">
                                                  </div>
                                              </div>
                                           </td>
                                           <td class="td-actions text-center">
                                               <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-minus"></i></button>
                                           </td>
                                         </tr>
                                   @endforeach
                              @else
                                 <tr id='addr0' value="1">
                                  <td>1</td>
                                  <td>
                                      <div class="input_section">
                                          <div class="form-group has-default bmd-form-group">
                                              <input type="text" class="form-control complaints_reasons" name="complaints_reasons[]" required placeholder="Complaint Reasons">
                                          </div>
                                      </div>
                                  </td>
                                     <td class="td-actions text-center">
                                      <!-- <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-minus"></i></button> -->
                                  </td>
                               </tr>
                              @endif
                          </tbody>
                      </table>
                  </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                   <table>
                      <tbody>
                         <tr>
                            <td class="td-actions text-center">
                               <a href="#" title="" class="btn btn-success btn-xs add-rows" onclick="getProductlist()"> <i class="fa fa-plus"></i> </a>
                            </td>
                         </tr>
                      </tbody>
                   </table>

                </div>
             </div>
          </div>
         <div class="pull-right col-md-12">
            {{ Form::submit($complaint_type->exists ? 'Update' : 'Submit', ['class' => 'btn btn-theme pull-right']) }}
        </div>
        </div>
      </div>
    </div>
  </div>
  </div>
<script>
   $(document).ready(function() {
      var $table = $('table.kvcodes-dynamic-rows-example'),
      counter = parseInt($('#tab_logic tr:last').attr('value')) || 0;

      $('a.add-rows').click(function(event) {
          event.preventDefault();
          counter++;

          var newRow = `
              <tr id='addr${counter}' value="${counter}">
                  <td class="row-count">${counter}</td>
                  <td>
                      <div class="input_section">
                          <div class="form-group has-default bmd-form-group">
                              <input type="text" class="form-control complaints_reasons" name="complaints_reasons[]"  required placeholder="Complaint Reasons">
                          </div>
                      </div>
                  </td>
                  <td class="td-actions text-center">
                      <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-minus"></i></button>
                  </td>
              </tr>`;
          $('#sopTableBody').append(newRow);

          // Initialize Select2 for new row
          $('.select2').select2({
              minimumResultsForSearch: 10
          });

          updateRowNumbers();
      });

      $(document).on('click', '.remove-row', function() {
          $(this).closest('tr').remove();
          counter--;

          updateRowNumbers();
      });

      function updateRowNumbers() {
          $('#sopTableBody tr').each(function(index) {
              $(this).attr('value', index + 1);
              $(this).find('.row-count').text(index + 1);
          });
          counter = $('#sopTableBody tr').length;
      }

      $('#service_bill_complaint_type_name').on('input' , function(){
           checkServiceBillComplaintType();
      })

      $('#subcategory_id').on('change' , function(){
           checkServiceBillComplaintType();
      })
  });


  function checkServiceBillComplaintType(){
       var sub_category_id = $('#subcategory_id').val();
       var complaint_type  = $('#service_bill_complaint_type_name').val();
       var is_exist = "{{ $complaint_type->exists ?? false }}";
       if(is_exist){
            return ;
       }
       if(sub_category_id != null && sub_category_id != '' && complaint_type != null && complaint_type != ''){
           $.ajax({
               url: "{{ url('checkServiceBillComplaintType') }}",
               dataType: "json",
               type: "POST",
               data: {
                   _token: "{{ csrf_token() }}",
                   sub_category_id: sub_category_id,
                   complaint_type : complaint_type
               },
               success: function(res) {
                   if (res.status === true) {
                      window.location.href = res.url;
                   }
               },
               error: function(xhr, status, error) {
                   console.error("Error fetching service charge types:", error);
               }
           });
       }
    }
</script>
</x-app-layout>