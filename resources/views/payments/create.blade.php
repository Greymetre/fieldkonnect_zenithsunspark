<x-app-layout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card">
              <div class="card-header card-header-icon card-header-theme">
                <h4 class="card-title">Payment Recieved</h4>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
                <div class="card-body ">
                  <ul class="nav nav-pills nav-pills-theme pl-0 nev_item_list" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" data-toggle="tab" href="#link1" role="tablist" >
                        Invoice payment
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" data-toggle="tab" href="#link2" role="tablist">
                        Customer Advance
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content tab-space">
                    {!! Form::model($payment,[
                      'route' => $payment->exists ? ['payments.update', $payment->id] : 'payments.store',
                      'method' => $payment->exists ? 'PUT' : 'POST',
                      'id' => 'createCompany',
                      'files'=>true
                    ]) !!}
                      <div class="row">
                        <input type="hidden" name="customer_id" id="customer_id" value="{!! old( 'customer_id', $payment['customer_id']) !!}">
                        <input type="hidden" name="payment_type" id="paymentType" value="Invoice payment">
                        <div class="col-md-6">
                          <div class="input_section">
                            <label>Select Customer</label>
                            <div class="form-group bmd-form-group">
                              <input class="form-control" id="customer_name" name="customer_name" list="customerList" value="{!! old( 'customer_name', $payment['customer_name']) !!}" autocomplete="off"  required/>
                            <datalist id="customerList">
                                @if($customers->isNotEmpty())
                                      @foreach ($customers as $customer)
                                        <option value="{!! $customer['name'] !!}" id="{!! $customer['id'] !!}" />
                                    @endforeach
                                 @endif
                            </datalist>
                               @if($errors->has('customer_name'))
                                <div class="invalid-feedback">
                                   {{ $errors->first('customer_name') }}
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input_section">
                            <label>Payment Date</label>
                            <div class="form-group bmd-form-group">
                              <input type="text" name="payment_date" class="form-control datepicker" value="{!! date('Y-m-d') !!}" autocomplete="off" readonly required>
                               @if($errors->has('payment_date'))
                                <div class="invalid-feedback">
                                   {{ $errors->first('payment_date') }}
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input_section">
                            <label>Payment Mode</label>
                            <div class="form-group bmd-form-group">
                              <select class="form-control" name="payment_mode" style="width: 100%;" required >
                                 <option value="">Select Payment Mode</option>
                                 <option value="Cash" {{ old( 'payment_mode' , (!empty($payment->payment_mode))?($payment->payment_mode):('') ) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                 <option value="Bank Transfer" {{ old( 'payment_mode' , (!empty($payment->payment_mode))?($payment->payment_mode):('') ) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                 <option value="Cheque" {{ old( 'payment_mode' , (!empty($payment->payment_mode))?($payment->payment_mode):('') ) == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                 <option value="Credit Card" {{ old( 'payment_mode' , (!empty($payment->payment_mode))?($payment->payment_mode):('') ) == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                 <option value="UPI" {{ old( 'payment_mode' , (!empty($payment->payment_mode))?($payment->payment_mode):('') ) == 'UPI' ? 'selected' : '' }}>UPI</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input_section">
                            <label>Amount Received </label>
                            <div class="form-group bmd-form-group">
                              <input type="number" class="form-control" name="amount" value="{!! old( 'amount', $payment['amount']) !!}">
                               @if($errors->has('amount'))
                                <div class="invalid-feedback">
                                   {{ $errors->first('amount') }}
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input_section">
                            <label>Reference no </label>
                            <div class="form-group bmd-form-group">
                              <input type="text" class="form-control" name="reference_no" value="{!! old( 'reference_no', $payment['reference_no']) !!}">
                               @if($errors->has('reference_no'))
                                <div class="invalid-feedback">
                                   {{ $errors->first('reference_no') }}
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input_section">
                            <label>Bank Name</label>
                            <div class="form-group bmd-form-group">
                              <input type="text" class="form-control" name="bank_name" value="{!! old( 'bank_name', $payment['bank_name']) !!}">
                               @if($errors->has('bank_name'))
                                <div class="invalid-feedback">
                                   {{ $errors->first('bank_name') }}
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="input_section">
                            <label>Description </label>
                            <div class="form-group bmd-form-group">
                              <textarea class="form-control" rows="5" name="description">{!! old( 'description', $payment['description']) !!}</textarea>
                               @if($errors->has('reference_no'))
                                <div class="invalid-feedback">
                                   {{ $errors->first('reference_no') }}
                                </div>
                                @endif
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="tab-pane active" id="link1">
                      <div class="row">
                        <h4 class="section-heading mb-3  h4 mt-0 text-center">Unpaid Invoices</h4> 
                        <div class="table-responsive w-100">
                          <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                             <thead>
                                <tr class="card-header-warning text-white bg_color_change">
                                  <th class="text-center"> # </th>
                                  <th class="text-center"> Invoice Date </th>
                                  <th class="text-center"> Invoice No</th>
                                  <th class="text-center"> Invoice Amount</th>
                                  <th class="text-center"> Amount Due</th>
                                  <th class="text-center"> Amount</th>
                                </tr>
                             </thead>
                             <tbody id="uppaidinvouces">
                              @if($payment->exists && isset($payment['paymentdetails']))
                              @foreach($payment['paymentdetails'] as $key => $rows )
                              <tr id='addr{{ $key }}' value="{{ $key +1 }}">
                                 <td>{{ $key + 1 }} <input type="hidden"  name="detail[{{ $key + 1 }}][detail_id]" value="{!! isset($rows['id']) ? $rows['id']: '' !!}"></td>
                                 <td class="text-center">{!! isset($rows['sales']['invoice_date']) ? $rows['sales']['invoice_date']: '' !!}</td>
                                 <td class="text-center"><input type="hidden"  name="detail[{{ $key + 1 }}][invoice_no]" value="{!! isset($rows['invoice_no']) ? $rows['invoice_no']: '' !!}">{!! isset($rows['invoice_no']) ? $rows['invoice_no']: '' !!}</td>
                                 <td class="text-center">{!! isset($rows['sales']['grand_total']) ? $rows['sales']['grand_total']: '' !!}</td>
                                 <td class="text-center">{!! isset($rows['sales']['grand_total']) ? $rows['sales']['grand_total']-$rows['sales']['paid_amount']: '' !!}</td>
                                 <td class="text-center"><input type="hidden"  name="detail[{{ $key + 1 }}][sales_id]" value="{!! isset($rows['sales_id']) ? $rows['sales_id']: '' !!}"><input type="number"  name="detail[{{ $key + 1 }}][amount]" value="{!! isset($rows['amount']) ? $rows['amount']: '' !!}"></td>
                              </tr>
                              @endforeach
                              @endif
                             </tbody>
                          </table>
                       </div>
                     </div>
                    </div>
                    <div class="tab-pane" id="link2">
                    </div>
                    {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
                    {{ Form::close() }} 
                  </div>
                </div>
            </div>
            </div>
        </div>
      </div>
    </section>
<script type="text/javascript">
    $(function() {
        $('#customer_name').on('input',function() {
            var opt = $('option[value="'+$(this).val()+'"]');
            var customer_id = opt.attr('id');
            $('#customer_id').val(customer_id);
            if(customer_id){
                $.ajax({
                    url: "{{ url('getUppaidInvouces') }}",
                    dataType: "json",
                    type: "POST",
                    data:{ _token: "{{csrf_token()}}", customer_id:customer_id},
                    success: function(res){
                        $("#uppaidinvouces").empty();
                        $('#customer_id').empty();
                        if(res)
                        {
                          var html = "";
                          $.each(res, function(index,item) {  
                            var counter = ++index ;
                          html += '<tr>'+
                                    '<td class="text-center">'+ counter +'</td>'+
                                    '<td class="text-center">'+item.invoice_date+'</td>'+
                                    '<td class="text-center"><input type="hidden"  name="detail[' + counter + '][invoice_no]" value="'+item.invoice_no+'">'+item.invoice_no+' </td>'+
                                    '<td class="text-center">'+item.grand_total+'</td>'+
                                    '<td class="text-center">'+item.amount_unpaid+' </td>'+
                                    '<td class="text-center"><input type="hidden"  name="detail[' + counter + '][sales_id]" value="'+item.id+'"><input type="number"  name="detail['+ counter +'][amount]"></td>'+
                                  '</tr>';
                          });
                          $("#uppaidinvouces").append(html);
                        }
                    }
                });
            }
            else
            {
              $('#customer_id').empty();
              $("#uppaidinvouces").val(''); 
            }
        });
    });

</script> 
</x-app-layout>
