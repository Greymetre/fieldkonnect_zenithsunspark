<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.coupon.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="pull-right">
                <div class="btn-group">
                  @if(auth()->user()->can(['coupon_upload']))
                  <form action="{{ URL::to('coupons-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.coupon.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['coupon_download']))
                  <a href="{{ URL::to('coupons-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.coupon.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['coupon_template']))
                  <a href="{{ URL::to('coupons-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.coupon.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['coupon_create']))
                  <a href="{{ route('coupons.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.coupon.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
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
            <table id="getcoupon" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.coupon.coupon_code') !!}</th>
              <th>{!! trans('panel.coupon.generated_date') !!}</th>
<!--                   <th>{!! trans('panel.coupon.expiry_date') !!}</th>
              <th>{!! trans('panel.coupon.customer_code') !!}</th>
              <th>{!! trans('panel.coupon.invoice_date') !!}</th>
              <th>{!! trans('panel.coupon.invoice_no') !!}</th>
              <th>{!! trans('panel.coupon.product_code') !!}</th> -->
             <!--  <th>{!! trans('panel.global.status') !!}</th> -->
              <th>{!! trans('panel.global.created_by') !!}</th>
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
<script type="text/javascript">
$(document).ready(function() {
    oTable = $('#getcoupon').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [0, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('coupons.index') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'coupon_code', name: 'coupon_code',"defaultContent": ''},
            {data: 'generated_date', name: 'generated_date',"defaultContent": ''},
            // {data: 'expiry_date', name: 'expiry_date',"defaultContent": ''},
            // {data: 'customer_code', name: 'customer_code',"defaultContent": ''},
            // {data: 'invoice_date', name: 'invoice_date',"defaultContent": ''},
            // {data: 'invoice_no', name: 'invoice_no',"defaultContent": ''},
            // {data: 'product_code', name: 'product_code',"defaultContent": ''},
            // {data: 'status.status_name', name: 'status.status_name',"defaultContent": ''},
            {data: 'couponprofiles.createdbyname.name', name: 'couponprofiles.createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
