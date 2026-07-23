<x-app-layout>
  <style>
    .table.new-table th,
    .table.new-table td {
      border-top: 0px !important;
    }

    b {
      font-weight: 600;
    }

    .img-div {
      width: 25%;
      text-align: center;
      border: 1px solid #ab9a9a;
      margin: 2px 10px;
      border-radius: 5px;
      background: radial-gradient(#c5b0b0, transparent);
    }

    button.delete-img-btn {
      position: absolute;
      cursor: pointer;
      right: 0;
    }
  </style>
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

        @if(Session::has('success'))
        <div class="alert alert-success" id="hide_div">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('success') !!}</strong>
        </div>
        @endif

        @if(Session::has('danger'))
        <div class="alert alert-danger" id="hide_danger">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('danger') !!}</strong>
        </div>
        @endif


        <div class="alert" style="display: none;" id="hide_check">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <strong class="message"></strong>
        </div>



        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-4">
                <h3 class="card-title pb-3">Service Bill View</h3>
              </div>
              <div class="col-8 text-right">


              @if(auth()->user()->hasRole(['superadmin']) || auth()->user()->hasRole(['Sub_Admin']) || auth()->user()->hasRole(['Service Admin']))

                @if($serviceBill->status == '0') // Draft
                @can('claim_company')
                <button type="button" class="btn btn-sm btn-warning company_claim"><b>Claim to company</b></button>
                @endcan

                @can('customer_pay')
                <button type="button" class="btn btn-sm btn-info customer_pay"><b>Customer payble</b></button>
                @endcan

                @can('cancel_service_bill')
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                @endcan

                @elseif($serviceBill->status == '1') // Claimed
                @can('draft_service_bill')
                <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                @endcan

                @can('approve_service_bill')
                <button type="button" class="btn btn-sm btn-success approve_status"><b>Approve</b></button>
                @endcan

                @can('cancel_service_bill')
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                @endcan

                @elseif($serviceBill->status == '2') // Customer payble
                @can('draft_service_bill')
                <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                @endcan

                @can('cancel_service_bill')
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                @endcan

                @elseif($serviceBill->status == '3') // Approved
                @can('draft_service_bill')
                <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                @endcan

                @can('cancel_service_bill')
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                @endcan

                @elseif($serviceBill->status == '4') // Cancel
                @can('draft_service_bill')
                <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                @endcan
                @endif


                @else
                @if($serviceBill->status == '0')
                @can('claim_company')
                <button type="button" class="btn btn-sm btn-warning company_claim"><b>Claim to company</b></button>
                @endcan

                @can('customer_pay')
                <button type="button" class="btn btn-sm btn-info customer_pay"><b>Customer payble</b></button>
                @endcan

                {{--@can('cancel_service_bill')
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                @endcan--}}

                @elseif($serviceBill->status == '1')
                @can('draft_service_bill')
                <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                @endcan

                @can('approve_service_bill')
                <button type="button" class="btn btn-sm btn-success approve_status"><b>Approve</b></button>
                @endcan

                @can('cancel_service_bill')
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                @endcan

                @elseif($serviceBill->status == '2')
                @auth
                @if(auth()->user()->hasRole('CRM_Support'))
                @can('draft_service_bill')
                <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                @endcan
                @endif
                @endauth

                @can('cancel_service_bill')
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                @endcan

                @elseif($serviceBill->status == '3')
                <!-- @can('draft_service_bill')
                        <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                    @endcan

                    @can('cancel_service_bill')
                        <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel</b></button>
                    @endcan -->

                @elseif($serviceBill->status == '4')
                <!--  @can('draft_service_bill')
                        <button type="button" class="btn btn-sm btn-primary draft_status"><b>Draft</b></button>
                    @endcan -->
                @endif
                @endif

                <?php
                if (auth()->user()->can(['service_bill_edit']) || auth()->user()->hasRole(['superadmin']) || auth()->user()->hasRole(['Sub_Admin']) || auth()->user()->hasRole(['Service Admin'])) { ?>

                  <a class="btn btn-warning btn-sm" href="{{route('service_bills.edit', $serviceBill->id)}}"><b>Edit</b></a>

                <?php } ?>

                <a class="btn btn-primary btn-sm" href="{{route('service_bills.index')}}"><b>Back</b></a>


              </div>
              <!-- /.col -->
            </div>
            <input type="hidden" id="service_bill_id" name="service_bill_id" value="{{$serviceBill['id']}}">
            <hr>

            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-11">
                  <h4 class="float-left">
                    <small>Service Bill No. <p style="font-size: 22px; color:#5252b7">#{!! $serviceBill['bill_no'] !!}</p></small>
                  </h4>
                  <h4 class="float-right">
                    <small>Complaint No. <p style="font-size: 22px; color:#5252b7">#{!! $serviceBill->complaint->complaint_number !!}</p></small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <hr>

              <table class="table responsive border-0 mt-4 new-table">
                <tr>
                  <td><em>Division</em></td>
                  <td><em>Category Of Complaint </em></td>
                  <td><em>Complaint Type</em></td>
                  <td><em>Complaint Reason</em></td>
                </tr>
                <tr>
                  <th class="pt-0">{{$serviceBill->division_details?$serviceBill->division_details->category_name:'-'}}</th>
                  <th class="pt-0">{{$serviceBill->category??'-'}}</th>
                  <th class="pt-0">{{$serviceBill->complaint_type??'-'}}</th>
                  <th class="pt-0">{{$serviceBill->complaint_reason??'-'}}</th>
                </tr>

                <tr>
                  <td><em>Condition Of Service </em></td>
                  <td><em>Received Product </em></td>
                  <td><em>Nature Of Fault </em></td>
                  <td><em>Service Location </em></td>
                </tr>
                <tr>
                  <th class="pt-0">{{$serviceBill->condition_of_service??'-'}}</th>
                  <th class="pt-0">{{$serviceBill->received_product??'-'}}</th>
                  <th class="pt-0">{{$serviceBill->nature_of_fault??'-'}}</th>
                  <th class="pt-0">{{$serviceBill->service_location??'-'}}</th>
                </tr>

                <tr>
                  <td><em>Repaired / Replacement </em></td>
                  <td><em>Replacement Tag </em></td>
                  <td colspan="2"><em>Replacement Tag Number </em></td>
                </tr>
                <tr>
                  <th class="pt-0">{{$serviceBill->repaired_replacement??'-'}}</th>
                  <th class="pt-0">{{$serviceBill->replacement_tag??'-'}}</th>
                  <th colspan="2" class="pt-0">{{$serviceBill->replacement_tag_number??'-'}}</th>
                </tr>

              </table>

              <hr class="mt-3 mb-3">

              <div class="row invoice-info mt-4">
                <div class="col-md-4">
                  <h4><b>Warranty Details</b></h4>
                </div>
                <div class="col-md-4 text-center">
                </div>
                <div class="col-md-4"></div>
              </div>

              <table class="table table-striped responsive">
                <tr>
                  <th>Product Serial No.</th>
                  <th>Product</th>
                  <th>Warranty Start Date</th>
                  <th>Warranty Upto</th>
                  <th>Warranty Status</th>
                </tr>
                <tr>
                  <td>{{strtoupper($serviceBill->complaint->product_serail_number)}}</td>
                  <td>[{{$serviceBill->complaint->product_code}}] {{$serviceBill->complaint->product_name}}</td>
                  <td>
                    @if ($serviceBill->complaint->customer_bill_date)
                    {{ date('d-m-Y', strtotime($serviceBill->complaint->customer_bill_date)) }}
                    @else
                    -
                    @endif
                  </td>
                  <td>
                    @if ($serviceBill->complaint->customer_bill_date)
                    @php
                    $today = Carbon\Carbon::today();
                    $date = Carbon\Carbon::parse($serviceBill->complaint->customer_bill_date);
                    if ($date !== false) {
                    $date->addMonths(18);
                    } else {
                    $date = null;
                    }
                    @endphp
                    @if ($date)
                    {{ $date->format('d-m-Y') }}
                    @else
                    Invalid date
                    @endif
                    @else
                    -
                    @endif
                  </td>
                  <td>
                    @if ($serviceBill->complaint->customer_bill_date)
                    @if ($date)
                    @if ($date->gt($today))
                    <span class="badge badge-success">In Warranty</span>
                    @else
                    <span class="badge badge-danger">Out Of Warranty</span>
                    @endif
                    @else
                    Invalid date
                    @endif
                    @else
                    -
                    @endif
                  </td>
                </tr>

              </table>

              <hr class="mt-3 mb-3">

              <div class="row invoice-info mt-4">
                <div class="col-md-4">
                  <h4><b>Service Bill Product Details</b></h4>
                </div>
                <div class="col-md-4 text-center">
                </div>
                <div class="col-md-4"></div>
              </div>

              <table class="table table-striped responsive">
                <tr>
                  <th>Service Type</th>
                  <th>Job Type / Description /HP</th>
                  <th>Quantity</th>
                  <th>Distance (KM)</th>
                  <th>Appreciation Charges</th>
                  <th>Charge Value</th>
                  <th>Sub Total</th>
                </tr>
                @php $total_pr = 0; $total_qua = 0; @endphp
                @if(count($serviceBill->service_bill_products) > 0)
                @foreach($serviceBill->service_bill_products as $service_bill_products)
                <tr>
                  <td>{{ucfirst($service_bill_products->service_type_details?$service_bill_products->service_type_details->charge_type:'-')}}</td>
                  <td>{{ucfirst($service_bill_products->product?$service_bill_products->product->product_name:$service_bill_products->product_id)}}</td>
                  <td>{{$service_bill_products->quantity??'-'}}</td>
                  <td>{{$service_bill_products->distance??'0'}}</td>
                  <td>{{$service_bill_products->appreciation??'0'}}</td>
                  <td>{{$service_bill_products->price??'0'}}</td>
                  <td>{{$service_bill_products->subtotal??'0'}}</td>
                </tr>
                @php $total_pr += $service_bill_products->subtotal??0; $total_qua += $service_bill_products->quantity??0; @endphp
                @endforeach
                @endif
                <tr>
                  <th colspan="2">Total : </th>
                  <th>{{$total_qua}}</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <!-- <th>{{$total_pr}}</th> -->
                  <th>{{$total_pr}}</th>
                </tr>

              </table>

              <hr class="mt-3 mb-3">

              <div class="row invoice-info mt-4">
                <div class="col-md-4">
                  <h4><b>Photos :</b></h4>
                </div>
                <div class="col-md-4 text-center">
                </div>
                <div class="col-md-4"></div>
              </div>

              <div class="row">
                @if($serviceBill->exists && $serviceBill->getMedia('*')->count() > 0)
                @foreach($serviceBill->getMedia('*') as $k=>$media)
                <div style="position: relative;" class="img-div">
                  <button title="Delete Image" type="button" class="badge badge-danger delete-img-btn" data-mediaid="{{$media->id}}">X</button>
                  <a href="{{$media->getFullUrl()}}" download target="_blank">
                    <img class="m-2 rounded img-fluid" src="{!! $media->getFullUrl() !!}" style="width: 170px;height:170px;">
                  </a>
                </div>
                @endforeach
                @endif
              </div>


              <!-- /.row -->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>



      <!-- <div class="col-3">
        <h4>Time Line</h4>

        <div class="card">
          <div class="card-body">
            <h4>Time Line</h4>

            <div class="row">
              <div class="col-12">
                <h3 class="card-title pb-3">Time Line</h3>
                <hr>
                <p class="lead"></p>
                {{--@if(count($timelines) > 0)
                @foreach($timelines as $timeline)
                @if($timeline->status == '0')
                @php $status_is = 'Open'; @endphp
                @elseif($timeline->status == '1')
                @php $status_is = 'Pending'; @endphp
                @elseif($timeline->status == '2')
                @php $status_is = 'Work Done'; @endphp
                @elseif($timeline->status == '3')
                @php $status_is = 'Completed'; @endphp
                @elseif($timeline->status == '4')
                @php $status_is = 'Closed'; @endphp
                @elseif($timeline->status == '5')
                @php $status_is = 'Canceled'; @endphp
                @endif
                <div class="d-flex">
                  <i class="material-icons">double_arrow</i>
                  <p>
                     Complaint <b> {!! $serviceBill->complaint['complaint_number'] !!} </b> moved to <b>{{$status_is}}</b> by <b>{{$timeline->created_by_details->name}}</b> on <b>{{date("d M Y, h:i a", strtotime($timeline->created_at));}}.</b>
                </p>
              </div>
              @endforeach
              @endif

              <p>
                <b> #{!! $serviceBill->complaint['complaint_number'] !!} Created on {{date("d M Y, h:i a", strtotime($serviceBill->complaint->created_at));}}
              </p> --}}



            </div>

          </div>
        </div>
      </div> -->
    </div>


    <!-- /.row -->


    <!-- new model for reject status -->

    <div class="modal fade bd-example-modal-lg" id="reject_expense" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card">
          <div class="card-header card-header-icon card-header-theme">
            <div class="card-icon">
              <i class="material-icons">perm_identity</i>
            </div>
            <h4 class="card-title">
              <span class="modal-title">Submit </span> Reject <span class="pull-right">
                <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                  <i class="material-icons">clear</i>
                </a>
              </span>
            </h4>
          </div>
          <div class="modal-body">
            <form method="POST" action="{{ route('cancelComplaint') }}" enctype="multipart/form-data" id="createleadstagesForm_new"> @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group input-group-outline my-3">
                    <label class="form-label">Reason</label>
                    <input type="text" name="reason" id="reason" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                    <input type="text" name="cancel_complaint_id" id="cancel_complaint_id" class="form-control" hidden>
                  </div>
                </div>
              </div>
              <button class="btn btn-info save">Reject</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- end model for status -->


    <!-- new model for approve status -->

    <div class="modal fade bd-example-modal-lg" id="approve_expense" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content card">
          <div class="card-header card-header-icon card-header-theme">
            <div class="card-icon">
              <i class="material-icons">perm_identity</i>
            </div>
            <h4 class="card-title">
              <span class="modal-title">Submit </span> Approve <span class="pull-right">
                <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                  <i class="material-icons">clear</i>
                </a>
              </span>
            </h4>
          </div>
          <div class="modal-body">
            <form method="POST" action="{{ route('approveExpense') }}" enctype="multipart/form-data" id="createleadstagesForms"> @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="input-group input-group-outline my-3">
                    <label class="form-label">Approve Amount</label>
                    <input type="text" name="approve_amnt" id="approve_amnt" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                    <input type="text" name="expense_new_id" id="expense_new_id" class="form-control" hidden>
                  </div>

                  <div class="input-group input-group-outline my-3">
                    <label class="form-label">Reason</label>
                    <input type="text" name="reasons" id="reasons" class="form-control" value="{!! old( 'reasons') !!}"> <br><br>
                  </div>


                </div>
              </div>
              <button class="btn btn-info save">Approve</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- end model for status -->

    <!-- Custom styles for this page -->
    <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

    <script src="{{ url('/').'/'.asset('lightboxx/js/lightbox-plus-jquery.min.js') }}"></script>

    <!-- for checked -->
    <script type="text/javascript">
      $('body').on('click', '.approve_status', function() {
        var id = $('#service_bill_id').val();
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: "{{ url('service-bill-approve') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
              setTimeout(function() {
                location.reload();
              }, 500);

            } else {
              $('.alert').addClass("alert-danger");
              // setTimeout(function() {
              //   location.reload();
              // }, 3000);
            }
            $('.message').append(data.message);
          },
        });
      });
      $('body').on('click', '.draft_status', function() {
        var id = $('#service_bill_id').val();
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: "{{ url('service-bill-draft') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
              setTimeout(function() {
                location.reload();
              }, 500);

            } else {
              $('.alert').addClass("alert-danger");
              // setTimeout(function() {
              //   location.reload();
              // }, 3000);
            }
            $('.message').append(data.message);
          },
        });
      });

      $('body').on('click', '.company_claim', function() {
        var id = $('#service_bill_id').val();
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: "{{ url('service-bill-company-claim') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
              setTimeout(function() {
                location.reload();
              }, 500);

            } else {
              $('.alert').addClass("alert-danger");
              // setTimeout(function() {
              //   location.reload();
              // }, 3000);
            }
            $('.message').append(data.message);
          },
        });
      });

      $('body').on('click', '.cancel_status', function() {
        var id = $('#service_bill_id').val();
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: "{{ url('service-bill-cancel') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
              setTimeout(function() {
                location.reload();
              }, 500);

            } else {
              $('.alert').addClass("alert-danger");
              // setTimeout(function() {
              //   location.reload();
              // }, 3000);
            }
            $('.message').append(data.message);
          },
        });
      });

      $('body').on('click', '.customer_pay', function() {
        var id = $('#service_bill_id').val();
        var token = $("meta[name='csrf-token']").attr("content");
        $.ajax({
          url: "{{ url('service-bill-customer-pay') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
              setTimeout(function() {
                location.reload();
              }, 500);

            } else {
              $('.alert').addClass("alert-danger");
              // setTimeout(function() {
              //   location.reload();
              // }, 3000);
            }
            $('.message').append(data.message);
          },
        });
      });

      $(document).on("click", ".delete-img-btn", function() {
        var id = $(this).data('mediaid');
        Swal.fire({
          title: "ARE YOU SURE TO DELETE ATTACHMENT ?",
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: "YES",
          denyButtonText: `Don't`
        }).then((result) => {
          if (result.value) {
            $(this).closest('.img-div').remove();
            $.ajax({
              url: "{{ url('service-bill-attach-delete') }}",
              dataType: "json",
              type: "POST",
              data: {
                _token: "{{csrf_token()}}",
                id: id
              },
              success: function(res) {
                if (res.status === true) {
                  Swal.fire("Attachment delete successfully !", res.msg, "success");
                } else {
                  Swal.fire("Somthing went wrong", "", "error");
                }
              }
            });
          }
        });
      })
    </script>
  </section>
  <!-- /.content -->
</x-app-layout>