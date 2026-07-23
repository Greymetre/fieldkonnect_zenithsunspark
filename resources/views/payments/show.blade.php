<x-app-layout>
<div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12 mx-auto p-3">
          <form class="" action="index.html" method="post">
            <div class="card my-sm-5">
              <div class="card-header text-left">
                <div class="row justify-content-between">
                  <div class="col-md-4 text-start">
                    <img class="mb-2 w-25 p-2" src="{!! asset('assets/img/logo.png') !!}" alt="Logo">
                    <h6>
                      {!! config('app.name') !!}
                    </h6>
                    <p class="d-block text-secondary">Address: </p>
                  </div>
                  <div class="col-lg-3 col-md-7 text-md-end text-start mt-5">
                    <h6 class="d-block mt-2 mb-0">From: {!! isset($payment['customer_name']) ? $payment['customer_name'] :'' !!}</h6>
                    <p class="text-secondary">{!! isset($payment['customers']['mobile']) ? $payment['customers']['mobile'] :'' !!} <br>{!! isset($payment['customers']['customeraddress']['address1']) ? $payment['customers']['customeraddress']['address1'] :'' !!}<br>
                      {!! isset($payment['customers']['customeraddress']['address2']) ? $payment['customers']['customeraddress']['address2'] :'' !!}
                      
                    </p>
                  </div>
                </div>
                <br>
                <div class="row justify-content-md-between">
                  <div class="col-lg-5 col-md-7 mt-auto">
                    <div class="row mt-md-5 mt-4 text-md-end text-start">
                      <div class="col-md-6">
                        <h6 class="text-secondary font-weight-normal mb-0 text-left">Date</h6>
                      </div>
                      <div class="col-md-6">
                        <h6 class="text-dark mb-0 text-left">{!! isset($payment['payment_date']) ? $payment['payment_date'] :'' !!}</h6>
                      </div>
                    </div>
                    <div class="row text-md-end text-start">
                      <div class="col-md-6">
                        <h6 class="text-secondary font-weight-normal mb-0 text-left">Reference No:</h6>
                      </div>
                      <div class="col-md-6">
                        <h6 class="text-dark mb-0 text-left">{!! isset($payment['reference_no']) ? $payment['reference_no'] :'' !!}</h6>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-5 col-md-7 mt-auto">
                    <div class="row mt-md-5 mt-4 text-md-end text-start">
                      <div class="col-md-6">
                        <h6 class="text-secondary font-weight-normal mb-0 text-left">Payment Mode:</h6>
                      </div>
                      <div class="col-md-6">
                        <h6 class="text-dark mb-0 text-left">{!! isset($payment['payment_mode']) ? $payment['payment_mode'] :'' !!}</h6>
                      </div>
                    </div>
                    <div class="row text-md-end text-start">
                      <div class="col-md-6">
                        <h6 class="text-secondary font-weight-normal mb-0 text-left">Payment Type:</h6>
                      </div>
                      <div class="col-md-6">
                        <h6 class="text-dark mb-0 text-left">{!! isset($payment['payment_type']) ? $payment['payment_type'] :'' !!}</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <div class="table-responsive">
                      <table class="table text-left">
                        <thead>
                          <tr>
                            <th scope="col" class="pe-2 text-start ps-2">Invoice Date</th>
                            <th scope="col" class="pe-2">Invoice No</th>
                            <th scope="col" class="pe-2" colspan="2">Invoice Amount</th>
                            <th scope="col" class="pe-2">Paid Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          @if($payment->exists && isset($payment['paymentdetails']))
                              @foreach($payment['paymentdetails'] as $key => $rows )
                          <tr>
                            <td class="text-start">{!! isset($rows['sales']['invoice_date']) ? $rows['sales']['invoice_date']: '' !!}</td>
                            <td class="ps-4">{!! isset($rows['invoice_no']) ? $rows['invoice_no']: '' !!}</td>
                            <td class="ps-4" colspan="2">{!! isset($rows['sales']['grand_total']) ? $rows['sales']['grand_total']: '' !!}</td>
                            <td class="ps-4">{!! isset($rows['amount']) ? $rows['amount']: '' !!}</td>
                          </tr>
                            @endforeach
                          @endif
                        </tbody>
                        <tfoot>
                          <tr>
                            <th></th>
                            <th></th>
                            <th class="h5 ps-4" colspan="2">Total</th>
                            <th colspan="1" class="text-right h5 ps-4">{!! isset($payment['amount']) ? $payment['amount'] :'' !!}</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 text-left">
                    <p class="text-secondary text-sm">{!! isset($payment['description']) ? $payment['description'] :'' !!}</p>
                    <h6 class="text-secondary font-weight-normal mb-0">
                      email:
                      <span class="text-dark"></span>
                    </h6>
                  </div>
                </div>
              </div>
              <div class="card-footer mt-md-5 mt-4">
                <div class="row">
                  <div class="col-lg-12 text-left">
                    <h5>Thank you!</h5>
                  </div>
                  <!-- <div class="col-lg-7 text-md-end mt-md-0 mt-3">
                    <button class="btn bg-gradient-info mt-lg-7 mb-0" onclick="window.print()" type="button" name="button">Print</button>
                  </div> -->
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
</x-app-layout>

