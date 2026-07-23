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
                        Transaction Coupon History Creation
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
               {!! Form::model($transaction_history,[
               'route' => $transaction_history->exists ? ['transaction_history.manualupdate', encrypt($transaction_history->id) ] : 'transaction_history.manualstore',
               'method' => $transaction_history->exists ? 'PUT' : 'POST',
               'id' => 'storeTransactionHistoryData',
               'files'=>true
               ]) !!}
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-2">
                        <label for="customer_id" class="form-control">Customer :</label>
                     </div>
                     <div class="col-md-10">
                        <select name="customer_id" placeholder="Select Customers" class="select2 form-control" required>
                           <option value="" disabled selected>Select Customer</option>
                           @if($customers && count($customers) > 0)
                           @foreach($customers as $customer)
                           <option value="{{$customer->id}}" {!! in_array($customer->id, old( 'customer[]', explode(',', $transaction_history['customer_id']))) ?'selected':'' !!}>{{$customer->name}}({{$customer->mobile}})</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('customer_id'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div id="copen_code_div">
                     <div class="row">
                        <div class="col-md-2">
                           <label for="point_type" class="form-control">Point Type :</label>
                        </div>
                        <div class="col-md-4">
                           <select name="point_type" id="point_type" class="select2 form-control" required>
                              <option value="">Select Type</option>
                              <option value="0">Provision</option>
                              <option value="1">Active</option>
                           </select>
                           @if ($errors->has('point_type'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('point_type') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="points" class="form-control">Points :</label>
                        </div>
                        <div class="col-md-4">
                           <input type="number" name="points" id="points" class="form-control" required>
                           @if ($errors->has('points'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('points') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-2">
                     <label for="remark" class="form-control">Remark :</label>
                  </div>
                  <div class="col-md-6">
                     <textarea name="remark" id="remark" class="form-control" cols="50" rows="3" required></textarea>
                     @if ($errors->has('remark'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('remark') }}</p>
                     </div>
                     @endif
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
</x-app-layout>