<x-app-layout>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body card-header card-header-icon card-header-theme">
            <div class="row">
              <div class="col-12">
                <h4 class="card-title pb-3">{!! trans('panel.sale.title_singular') !!}</h3>
              </div>
              <!-- /.col -->
            </div>

            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <small class="float-right">Date: {!! $sales['invoice_date'] !!}</small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-5 invoice-col">
                  From
                  <address>
                    <strong>{!! $sales['sellers']['name'] ?? '' !!}</strong><br>

                    @if(isset($sales['sellers']['customeraddress']))
                    {!! $sales['sellers']['customeraddress']['address1'] ?? '' !!}
                    @if(!empty($sales['sellers']['customeraddress']['address2'])), {!! $sales['sellers']['customeraddress']['address2'] !!}@endif<br>

                    {!! $sales['sellers']['customeraddress']['locality'] ?? '' !!},
                    {!! $sales['sellers']['customeraddress']['cityname']['city_name'] ?? '' !!}
                    {!! $sales['sellers']['customeraddress']['pincodename']['pincode'] ?? '' !!}<br>

                    @if(!empty($sales['sellers']['mobile']))
                    Phone: {!! $sales['sellers']['mobile'] !!}<br>
                    @endif

                    @if(!empty($sales['sellers']['email']))
                    Email: {!! $sales['sellers']['email'] !!}
                    @endif
                    @endif
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-5 invoice-col">
                  To
                  <address>
                    <strong>{!! $sales['buyers']['name'] ?? '' !!}</strong><br>

                    @if(isset($sales['buyers']['customeraddress']))
                    {!! $sales['buyers']['customeraddress']['address1'] ?? '' !!}
                    @if(!empty($sales['buyers']['customeraddress']['address2'])), {!! $sales['buyers']['customeraddress']['address2'] !!}@endif<br>

                    {!! $sales['buyers']['customeraddress']['locality'] ?? '' !!},
                    {!! $sales['buyers']['customeraddress']['cityname']['city_name'] ?? '' !!}
                    {!! $sales['buyers']['customeraddress']['pincodename']['pincode'] ?? '' !!}<br>

                    @if(!empty($sales['buyers']['mobile']))
                    Phone: {!! $sales['buyers']['mobile'] !!}<br>
                    @endif

                    @if(!empty($sales['buyers']['email']))
                    Email: {!! $sales['buyers']['email'] !!}
                    @endif
                    @endif
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-2 invoice-col">
                  <b>Invoice #{!! $sales['invoice_no'] !!}</b><br>
                  <br>
                  <b>Order ID:</b> {!! $sales['order_id'] !!}<br>
                  <b>Payment Due:</b> {!! $sales['orderno'] !!}<br>
                  <b>No:</b> {!! $sales['sales_no'] !!}
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>{!! trans('panel.global.products') !!}</th>
                        <th>{!! trans('panel.global.product_detail') !!}</th>
                        <th>{!! trans('panel.global.price') !!}</th>
                        <th>{!! trans('panel.global.quantity') !!}</th>
                        <th>{!! trans('panel.global.amount') !!}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($sales->exists && isset($sales['saledetails']))
                      @foreach($sales['saledetails'] as $rows )
                      <tr>
                        <td>{!! $rows['products']['product_name'] !!}</td>
                        <td>GST Percent : <span class="gst_percent">{!! isset($rows['productdetails']['gst']) ? $rows['productdetails']['gst'] : '' !!}</span> <br>
                          GST Amount : <span class="gstamount">{!! $rows['tax_amount'] !!}</span> <br>
                        </td>
                        <td>{!! $rows['price'] !!}</td>
                        <td>{!! $rows['quantity'] !!}</td>
                        <td>{!! $rows['line_total'] !!}</td>
                      </tr>
                      @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead">{!! trans('panel.sale.fields.description') !!}:</p>
                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    {!! $sales['description'] !!}
                  </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <div class="table-responsive">
                    <table class="table">
                      <tbody>
                        <tr>
                          <th style="width:50%">Subtotal:</th>
                          <td>{!! $sales['sub_total'] !!}</td>
                        </tr>
                        <tr>
                          <th>Tax</th>
                          <td>{!! $sales['total_gst'] !!}</td>
                        </tr>
                        <tr>
                          <th>Total:</th>
                          <td>{!! $sales['grand_total'] !!}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
  </section>
  <!-- /.content -->
</x-app-layout>