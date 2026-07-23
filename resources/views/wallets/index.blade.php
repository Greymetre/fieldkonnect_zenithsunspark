<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.wallet.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="pull-right">
                <div class="btn-group">
                 <!--  @if(auth()->user()->can(['wallet_upload']))
                  <form action="{{ URL::to('admin/wallets-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.wallet.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif -->
                  @if(auth()->user()->can(['wallet_download']))
                  <a href="{{ URL::to('wallets-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.wallet.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                 <!--  @if(auth()->user()->can(['wallet_template']))
                  <a href="{{ URL::to('admin/wallets-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.wallet.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['wallet_create']))
                  <a class="btn btn-just-icon btn-info create_record" data-toggle="modal" data-target="#couponScan" title="{!!  trans('panel.global.add') !!} {!! trans('panel.wallet.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif -->
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
        <div class="table-responsive">
          <table id="getwallet" class="table table-striped- table-bwalleted table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.global.buyer_id') !!}</th>
              <th>{!! trans('panel.global.buyer_name') !!}</th>
              <th>{!! trans('panel.wallet.fields.points') !!}</th>
              <th>{!! trans('panel.wallet.fields.point_type') !!}</th>
              <th>{!! trans('panel.wallet.fields.transaction_type') !!}</th>
              <th>{!! trans('panel.wallet.fields.invoice_amount') !!}</th>
              <th>{!! trans('panel.wallet.fields.invoice_no') !!}</th>
              <th>{!! trans('panel.wallet.fields.invoice_date') !!}</th>
              <th>{!! trans('panel.wallet.fields.scheme') !!}</th>
              <th>{!! trans('panel.wallet.fields.sales_id') !!}</th>
              <th>{!! trans('panel.global.created_at') !!}</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade bd-example-modal-md" id="couponScan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title modal-title"> {!! trans('panel.wallet.coupon_scan') !!}
          <span class="pull-right">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </span>
        </h4>
      </div>
      <div class="modal-body">
            {!! Form::open(['route' => 'wallets.store','id' => 'storeWalletdData']) !!}
            <div class="row">
              <label class="col-md-4 col-form-label">{!! trans('panel.wallet.fields.coupon') !!}<span class="text-danger"> *</span></label>
              <div class="col-md-8">
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="coupon_code" class="form-control" id="coupon" value="{!! old( 'coupon_code') !!}" required>
                    @if ($errors->has('coupon_code'))
                      <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('coupon_code') }}</p></div>
                    @endif
                </div>
              </div>
            </div>
            <div class="row">
              <label class="col-md-4 col-form-label">{!! trans('panel.global.customer') !!}<span class="text-danger"> *</span></label>
              <div class="col-md-8">
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" name="customer_id" style="width: 100%;" required>
                       <option value="">Select {!! trans('panel.global.customer') !!}</option>
                       @if(@isset($customers ))
                       @foreach($customers as $customer)
                       <option value="{!! $customer['id'] !!}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{!! $customer['name'] !!}</option>
                       @endforeach
                       @endif
                    </select>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
            {{ Form::close() }}
          </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
<script type="text/javascript">
$(document).ready(function() {
    oTable = $('#getwallet').DataTable({
        "processing": true,
        "serverSide": true,
        "wallet": [ [0, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('wallets.index') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '', walletable: false, searchable: false},
            {data: 'customer_id', name: 'customer_id',"defaultContent": ''},
            {data: 'customers.name', name: 'customers.name',"defaultContent": ''},
            {data: 'points', name: 'points',"defaultContent": ''},
            {data: 'point_type', name: 'point_type',"defaultContent": ''},
            {data: 'transaction_type', name: 'transaction_type',"defaultContent": ''},
            {data: 'invoice_amount', name: 'invoice_amount',"defaultContent": ''},
            {data: 'invoice_no', name: 'invoice_no',"defaultContent": ''},
            {data: 'invoice_date', name: 'invoice_date',"defaultContent": ''},
            {data: 'schemes.scheme_name', name: 'schemes.scheme_name',"defaultContent": ''},
            {data: 'sales_id', name: 'sales_id',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
