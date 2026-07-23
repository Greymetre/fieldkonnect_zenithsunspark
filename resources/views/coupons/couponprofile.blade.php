<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-tabs card-header-warning">
        <div class="nav-tabs-navigation">
          <div class="nav-tabs-wrapper">
            <h4 class="card-title ">{!! trans('panel.coupon_profile.title_singular') !!}{!! trans('panel.global.list') !!}
            @if(auth()->user()->can(['coupon_create']))
            <ul class="nav nav-tabs pull-right" data-tabs="tabs">
              <li class="nav-item">
                <a class="nav-link" href="{{ route('coupons.create') }}">
                  <i class="material-icons">add_circle</i> {!!  trans('panel.global.add') !!} {!! trans('panel.coupon.title_singular') !!}
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
        <div class="table-responsive">
            <table id="getcoupon" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.coupon_profile.profile_name') !!}</th>
              <th>{!! trans('panel.coupon_profile.coupon_length') !!}</th>
              <th>{!! trans('panel.coupon_profile.excluding_character') !!}</th>
              <th>{!! trans('panel.coupon_profile.coupon_count') !!}</th>
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
        "ajax": "{{ route('coupons.couponprofile') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'profile_name', name: 'profile_name',"defaultContent": ''},
            {data: 'coupon_length', name: 'coupon_length',"defaultContent": ''},
            {data: 'excluding_character', name: 'excluding_character',"defaultContent": ''},
            {data: 'coupon_count', name: 'coupon_count',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
