<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.checkin.title_singular') !!} {!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
		@if(auth()->user()->can(['checkin_download']))
                    <form method="GET" action="{{ URL::to('checkin-download') }}">
                        <div class="d-flex flex-row">
                          <div class="p-2"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                          <div class="p-2"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                          <div class="p-2"><button class="btn btn-just-icon btn-theme" title="Checkin Download"><i class="material-icons">cloud_download</i></button></div>
                        </div>
                    </form>
                    <div class="next-btn">
                    @endif
                    <a href="{{ URL::to('checkin-location') }}" class="btn btn-just-icon btn-theme" title="Update Location"><i class="material-icons">add_location</i></a>
                  </div>
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
          <table id="getcheckin" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>Customer Name</th>
              <th>{!! trans('panel.global.users') !!}</th>
              <th>{!! trans('panel.checkin.checkin_date') !!}</th>
              <th>{!! trans('panel.checkin.checkin_time') !!}</th>
              <th>{!! trans('panel.checkin.checkin_longitude') !!}</th>
              <th>{!! trans('panel.checkin.checkin_latitude') !!}</th>
              <th class="lenth_text">{!! trans('panel.checkin.checkin_address') !!}</th>
              <th>{!! trans('panel.checkin.checkout_date') !!}</th>
              <th>{!! trans('panel.checkin.checkout_time') !!}</th>
              <th>{!! trans('panel.checkin.checkout_latitude') !!}</th>
              <th>{!! trans('panel.checkin.checkout_longitude') !!}</th>
              <th>{!! trans('panel.checkin.checkout_address') !!}</th>
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
    oTable = $('#getcheckin').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [2, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('checkin.index') }}",
        "columns": [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'customers.name', name: 'customers.name',"defaultContent": ''},
            {data: 'users.name', name: 'users.name',"defaultContent": ''},
            {data: 'checkin_date', name: 'checkin_date',"defaultContent": ''},
            {data: 'checkin_time', name: 'checkin_time',"defaultContent": ''},
            {data: 'checkin_longitude', name: 'checkin_longitude',"defaultContent": ''},
            {data: 'checkin_latitude', name: 'checkin_latitude',"defaultContent": ''},
            {data: 'checkin_address', name: 'checkin_address',"defaultContent": ''},
            {data: 'checkout_date', name: 'checkout_date',"defaultContent": ''},
            {data: 'checkout_time', name: 'checkout_time',"defaultContent": ''},
            {data: 'checkout_latitude', name: 'checkout_latitude',"defaultContent": ''},
            {data: 'checkout_longitude', name: 'checkout_longitude',"defaultContent": ''},
            {data: 'checkout_address', name: 'checkout_address',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
});
</script>
</x-app-layout>
