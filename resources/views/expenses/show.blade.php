<div class="row">
  <div class="col-9">

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
            <h3 class="card-title pb-3">Approve View</h3>
          </div>
          <div class="col-8">


            @if($expense->checker_status=='3')

            @if(auth()->user()->can(['expense_unchecked']))
            <button type="button" class="btn btn-dark unchecked_status">Unchecked</button>
            @endif

            @if(auth()->user()->can(['expense_approve']))
            <button type="button" class="btn btn-success approve_status">Approved</button>
            @endif

            @if(auth()->user()->can(['expense_reject']))
            <button type="button" class="btn btn-danger reject_status">Rejected</button>
            @endif

            @if(auth()->user()->can(['expense_hold']))
            <button type="button" class="btn btn-dark hold_status">Hold</button>
            @endif

            @elseif($expense->checker_status=='1')
            @if(auth()->user()->can(['expense_reject']))
            <button type="button" class="btn btn-danger reject_status">Rejected</button>
            @endif

            @elseif($expense->checker_status=='4')

            @if(auth()->user()->can(['expense_unchecked']))
            <button type="button" class="btn btn-dark checked_status">Checked</button>
            @endif

            @if(auth()->user()->can(['expense_approve']))
            <button type="button" class="btn btn-success approve_status">Approved</button>
            @endif

            @if(auth()->user()->can(['expense_reject']))
            <button type="button" class="btn btn-danger reject_status">Rejected</button>
            @endif

            @if(auth()->user()->can(['expense_hold']))
            <button type="button" class="btn btn-dark hold_status">Hold</button>
            @endif

            @elseif($expense->checker_status=='5')

            @if(auth()->user()->can(['expense_unchecked']))
            <button type="button" class="btn btn-dark checked_status">Checked</button>
            @endif

            @if(auth()->user()->can(['expense_approve']))
            <button type="button" class="btn btn-success approve_status">Approved</button>
            @endif

            @if(auth()->user()->can(['expense_reject']))
            <button type="button" class="btn btn-danger reject_status">Rejected</button>
            @endif

            @else

            @if(auth()->user()->can(['expense_checked']))
            <button type="button" class="btn btn-info checked_by_reporting_status">Checked By Reporting</button>
            @endif

            @if(auth()->user()->can(['expense_checked']))
            <button type="button" class="btn btn-dark checked_status">Checked</button>
            @endif

            @if(auth()->user()->can(['expense_reject']))
            <button type="button" class="btn btn-danger reject_status">Rejected</button>
            @endif

            @if(auth()->user()->can(['expense_hold']))
            <button type="button" class="btn btn-dark hold_status">Hold</button>
            @endif

            @endif


            <?php
            if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Sub_Admin') || Auth::user()->hasRole('HR_Admin') || Auth::user()->hasRole('HO_Account')) { ?>

              <a class="btn btn-warning" href="{{route('expenses.edit', ['expense' => $expense->id])}}" role="button">Edit</a>

              <?php } else {

              $created_at  = $expense->created_at;
              $startDate = Carbon\Carbon::parse($created_at);
              $endDate = Carbon\Carbon::now();
              //$timeDifference = $startDate->diff($endDate)->format('%h hours and %i minutes');
              $diffInHours = $startDate->diffInHours($endDate);

              if ($diffInHours <= 24 && $expense->checker_status == 0) {

              ?>

                @if($expense->checker_status=='3' || $expense->checker_status=='1')
                @if(auth()->user()->can(['expenses_edit']))
                <a class="btn btn-warning" href="javascript:void(0)" role="button">Edit</a>
                @endif
                @else
                @if(auth()->user()->can(['expenses_edit']))
                <a class="btn btn-warning" href="{{route('expenses.edit', ['expense' => $expense->id])}}" role="button">Edit</a>
                @endif
                @endif

              <?php
              } else {
              ?>
                @if(auth()->user()->can(['expenses_edit']))
                <a class="btn btn-warning" href="javascript:void(0)" role="button">Edit</a>
                @endif
            <?php
              }
            }

            ?>


            <!-- <a class="btn btn-primary" href="{{route('expenses.index')}}?executive_id={{$expense->user_id}}" role="button">Back</a> -->


          </div>
          <!-- /.col -->
        </div>
        <input type="hidden" id="expenseid" name="expense" value="{{$expense['id']}}">
        <hr>

        <div class="invoice p-3 mb-3">
          <!-- title row -->
          <div class="row">
            <div class="col-4">
              <h4>
                <small class="float-left">{{ trans('panel.expenses.title') }} #{!! $expense['id'] !!}</small>
              </h4>
            </div>

            <!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              From
              <address>
                <strong>{!! isset($expense['users']['name']) ? $expense['users']['name'] :'' !!} ({{$expense->users->getdesignation->designation_name??''}}) </strong><br>
              </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
              Expense Type
              <address>
                <strong>{!! $expense['expense_type']['name'] !!}
                  ( {!! isset($expense['expense_type']['allowance_type_id'])?config('constants.allowance_type.'.$expense['expense_type']['allowance_type_id']):''!!} )
                </strong><br>
              </address>


            </div>

            <div class="col-sm-4 invoice-col">
              Date
              <address>
                <!-- <strong>{!! $expense['date'] !!}</strong><br> -->
                <strong>{!! date("d/m/Y g:i a", strtotime($expense['date'])) !!}</strong><br>
              </address>
            </div>
          </div>

          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              Expense Status
              <address>
                <strong> @if($expense->checker_status=='1')
                  Approve
                  @elseif($expense->checker_status=='2')
                  Reject
                  @elseif($expense->checker_status=='3')
                  Checked
                  @elseif($expense->checker_status=='4')
                  Checked By Reporting
                  @elseif($expense->checker_status=='5')
                  Hold
                  @else
                  Pending
                  @endif

                </strong><br>
              </address>
            </div>
            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
              Status Change Reason
              <address>
                <strong>{{$expense->reason??""}}</strong><br>
              </address>
            </div>
            <div class="col-sm-4 invoice-col">
              Department
              <address>
                <strong>{{$expense['users']['getdepartment']?$expense['users']['getdepartment']['name']:'-'}}</strong><br>
              </address>
            </div>
            <div class="col-sm-6 invoice-col"></div>
            <div class="col-sm-6 invoice-col">
              <div class="row">
                <div class="col-md-6">
                  <h6>Today Plan - {{$paln->town??""}} </h6>
                  <h6>Today Visit - {{$paln->town??""}}</h6>
                  <h6>Live Location - <a href="{{url('/livelocation').'?user_id='.$expense->user_id.'&date='.$expense->date}}"><i class="material-icons">location_on</i></a></h6>
                </div>
                <div class="col-md-6">
                  <h6>Total Visit - {{$total_visit??"0"}}</h6>
                  <h6>Total KM Run - {{$total_dis? number_format($total_dis, 2) :"0.00"}}</h6>
                </div>
              </div>
            </div>
          </div>




          @if($expense->expense_type->allowance_type_id == '1')

          <!-- Table row -->
          <div class="row">
            <div class="col-12 table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>{{ trans('panel.expenses.fields.start_km') }}</th>
                    <th>{{ trans('panel.expenses.fields.stop_km') }}</th>
                    <th>{{ trans('panel.expenses.fields.total_km') }}</th>
                    <th>{{ trans('panel.expenses.fields.rate') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{!! $expense['start_km']??0 !!}</td>
                    <td>{!! $expense['stop_km']??0 !!}</td>
                    <td>{!! $expense['total_km']??0 !!}</td>
                    <td>{!! $expense['expense_type']['rate']??0 !!}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          @else

          <!-- Table row -->
          <div class="row">
            <div class="col-12 table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>{{ trans('panel.expenses.fields.rate') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>{!! $expense['expense_type']['rate']??0 !!}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          @endif



          <div class="row">
            <div class="col-6">
              <div class="table-responsive">
                <table class="table">
                  <tbody>
                    <tr>
                      <th style="width:20%">Claim Amount:</th>
                      <td>{{$expense['claim_amount']??0}}</td>
                      <input type="text" name="claims" id="claim_new_amount" value="{{$expense['claim_amount']??0}}" hidden>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-6">
              <div class="table-responsive">
                <table class="table">
                  <tbody>
                    <tr>
                      <th style="width:25%">Approved Amount</th>
                      <td>{{$expense['approve_amount']??0}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- accepted payments column -->
            <div class="col-6">
              <p class="lead"></p>
              <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                <strong> Note:</strong>
                {!! $expense['note'] !!}
              </p>
            </div>
          </div>

          <div class="row">
            <!-- accepted payments column -->
            <div class="col-6">
              <p class="lead"></p>
              <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                <strong> Image and doc:</strong>
                @if(isset($expense) && $expense->getMedia('expense_file')->count() > 0)
              <div class="form-group col-md-12">
                @foreach($expense->getMedia('expense_file') as $image)

                @if(Storage::disk('s3')->exists($image->getPath()))
                <?php
                $infoPath = pathinfo($image->getFullUrl());
                $extension = $infoPath['extension'];
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {
                ?>
                  <a target="_blank" href="{{ $image->getFullUrl()}}" data-lightbox="mygallery">
                    <img class="img-fluid" src="{{ $image->getFullUrl() }}" style="width:80px;height: 80px;">
                  </a>
                <?php } else { ?>
                  <p>
                    <a target="_blank" href="{{ $image->getFullUrl()}}" download>Download File</a>
                  </p>
                <?php } ?>

                <a class="btn btn-danger" href="{{route('deleteview', ['id' => $image->id,'expense_id'=>$expense->id])}}" role="button" onclick="return confirm('are you sure do you want delete file')" style="width:5px !important;">X</a>
                @endif
                @endforeach
              </div>
              @endif

              </p>
            </div>
          </div>

          <!-- /.row -->
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
    <!-- /.col -->
  </div>



  <div class="col-3">
    <!-- <h4>Time Line</h4> -->

    <div class="card">
      <div class="card-body">
        <!-- <h4>Time Line</h4> -->

        <div class="row">
          <div class="col-12">
            <h3 class="card-title pb-3">Time Line</h3>
            <hr>
            <p class="lead"></p>

            @foreach($logdetails as $logdetail)
            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
              {{ trans('panel.expenses.title') }} <b> #{!! $expense['id'] !!} {{$logdetail->status_type??''}}</b>, by <b>{{$logdetail->logusers->employee_codes??''}} {{$logdetail->logusers->name??''}}</b> on {{date("d-m-Y g:i a", strtotime($logdetail->created_at));}}
            </p>
            @endforeach


          </div>

        </div>
      </div>
    </div>
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
          <form method="POST" action="{{ route('rejectExpense') }}" enctype="multipart/form-data" id="rejectExpenseForm"> @csrf
            <div class="row">
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Reason </label>
                  <input type="text" name="reason" id="reason" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                  <input type="text" name="expense_id" id="reject_expense_id" class="form-control" hidden>
                </div>
              </div>
            </div>
            <button type="buuton" class="btn btn-info save-rjc" onclick="disableButtonreject()">Reject</button>
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
          <form method="POST" action="{{ route('approveExpense') }}" enctype="multipart/form-data" id="approveExpenseForms"> @csrf
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
            <input type="button" class="btn btn-info save-apr" onclick="disableButton()" value="Approve">
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- end model for status -->

  <!-- Custom styles for this page -->

  <script type="text/javascript">
    $("document").ready(function() {
      setTimeout(function() {
        $("#hide_div").remove();
      }, 3000); // 3 secs

    });
  </script>

  <script type="text/javascript">
    $("document").ready(function() {
      setTimeout(function() {
        $("#hide_danger").remove();
      }, 3000);
    });
  </script>