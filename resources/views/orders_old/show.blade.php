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
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                      <h3 class="card-title pb-3">{!! trans('panel.order.title_singular') !!} Detail</h3>
                    </div>
                    <!-- /.col -->
                  </div>

                <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-4">
                  <h4>
                    <small class="float-left">{!! trans('panel.order.id') !!} # {!! $orders['id'] !!}</small>
                  </h4>
                </div>
                <div class="col-4">
                  <h4>
                    <!-- <img src="" class="brand-image" width="70px" alt="Logo"> <span> </span> -->
                    <small class="float-left">{!! trans('panel.order.orderno') !!}: {!! $orders['orderno'] !!}</small>
                  </h4>
                </div>
                <div class="col-4">
                  <h4>
                    <!-- <img src="" class="brand-image" width="70px" alt="Logo"> <span> </span> -->
                    <small class="float-right">Date: {!! date("d-M-Y", strtotime($orders['order_date'])) !!}</small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                 <div class="col-sm-6 invoice-col">
                  From
                  <address>
                    <strong>{!! isset($orders['sellers']['name']) ? $orders['sellers']['name'] :'' !!} </strong><br>
                   {!! $orders['sellers']['customeraddress']['address1'] !!} ,{!! $orders['sellers']['customeraddress']['address2'] !!}<br>
                    {!! $orders['sellers']['customeraddress']['locality'] !!}, {!! $orders['sellers']['customeraddress']['cityname']['city_name'] !!} {!! $orders['sellers']['customeraddress']['pincodename']['pincode'] !!}<br>
                    Phone: {!! $orders['sellers']['mobile'] !!}<br>
                    Email: {!! $orders['sellers']['email'] !!}
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-6 invoice-col">
                  To
                  <address>
                    <strong>{!! $orders['buyers']['name'] !!}</strong><br>
                   {!! $orders['buyers']['customeraddress']['address1'] !!} ,{!! $orders['buyers']['customeraddress']['address2'] !!}<br>
                    {!! $orders['buyers']['customeraddress']['locality'] !!}, {!! $orders['buyers']['customeraddress']['cityname']['city_name'] !!} {!! $orders['buyers']['customeraddress']['pincodename']['pincode'] !!}<br>
                    Phone: {!! $orders['buyers']['mobile'] !!}<br>
                    Email: {!! $orders['buyers']['email'] !!}
                  </address>
                </div>
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
                    @if($orders->exists && isset($orderdetails))
                        @foreach($orderdetails as $rows )
                        <tr>
                            <td>{!! $rows['products']['display_name'] !!} <br>
                              Detail Title : <span class="prd_title">{!! isset($rows['productdetails']['detail_title']) ? $rows['productdetails']['detail_title'] : '' !!}</span>
                            </td>
                            <td>
                              GST Percent : <span class="gst_percent">{!! isset($rows['productdetails']['gst']) ? $rows['productdetails']['gst'] : '' !!}</span> <br>
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
                  <p class="lead"></p>
                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    {!! $orders['description'] !!}
                  </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <p class="lead"></p>

                  <div class="table-responsive">
                    <table class="table">
                      <tbody><tr>
                        <th style="width:50%">Subtotal:</th>
                        <td>{!! $orders['sub_total'] !!}</td>
                      </tr>
                      <tr>
                        <th>Tax</th>
                        <td>{!! $orders['total_gst'] !!}</td>
                      </tr>
                      <tr>
                        <th>Total:</th>
                        <td>{!! $orders['grand_total'] !!}</td>
                      </tr>
                    </tbody></table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <div class="col-12">
                  @if($orders['status_id'] != 1)
                    <a href="{!! url('order-dispatched/'.encrypt($orders->id)) !!}" class="btn btn-success float-right">Dispatched</a>
                  @endif

                  @if($orders['status_id'] != 1)
                    <a href="{!! url('order-partially-dispatched/'.encrypt($orders->id)) !!}" class="btn btn-theme float-right">Partially Dispatched</a>
                  @endif
                </div>
              </div>
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

