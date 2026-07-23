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
         <div class="card p-0 mt-0">
            <div class="card-header m-0 card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper new_id">
                     <h4 class="card-title ">
                        Transaction Coupon History Creation</h4>
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('transaction_history') }}">
                                 <i class="material-icons">next_plan</i> Transaction Coupon History
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
               {!! Form::model($transaction_history,[
               'route' => $transaction_history->exists ? ['transaction_history.manualupdate', encrypt($transaction_history->id) ] : 'transaction_history.manualstore',
               'method' => $transaction_history->exists ? 'PUT' : 'POST',
               'id' => 'storeTransactionHistoryData',
               'files'=>true
               ]) !!}
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="input_section">
                        <label for="customer_id" class="col-form-label">Customer :</label>
                  
                        <select name="customer_id" id="customer_id" placeholder="Select Customers" class="select2 form-control" required>
                           
                        </select>
                        @if ($errors->has('customer_id'))
                        <div class="error ">
                           <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>

                    <div class="col-md-6">
                        <div class="input_section">
                           <label for="point_type" class="col-form-label">Point Type :</label>
                     
                           <select name="point_type" id="point_type" class="select2 form-control" required>
                              <option value="">Select Type</option>
                              <option value="0">Provision</option>
                              <option value="1">Active</option>
                           </select>
                           @if ($errors->has('point_type'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('point_type') }}</p>
                           </div>
                           @endif
                        </div>
                         </div>
                        <div class="col-md-6">
                           <div class="input_section">
                           <label for="points" class="col-form-label">Points :</label>
                     
                           <input type="number" name="points" id="points" class="form-control" required>
                           @if ($errors->has('points'))
                           <div class="error">
                              <p class="text-danger">{{ $errors->first('points') }}</p>
                           </div>
                           @endif
                        </div>
 </div>
                             <div class="col-md-6">
                  <div class="input_section">
                     <label for="remark" class="col-form-label">Remark :</label>
                
                     <textarea name="remark" id="remark" class="form-control" cols="50" rows="3" required></textarea>
                     @if ($errors->has('remark'))
                     <div class="error">
                        <p class="text-danger">{{ $errors->first('remark') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
                <div class="pull-right col-md-12">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
                     </div>
           
                   
                  </div>
               </div>
          
              
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
      setTimeout(() => {
         $('#customer_id').select2({
            placeholder: 'Select Customer',
            allowClear: true,
            ajax: {
               url: "{{ route('getRetailerDataSelect') }}",
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
         }).trigger('change');
      }, 1000);
   </script>
</x-app-layout>