<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title">{!! trans('panel.customers.title') !!}{!! trans('panel.global.list') !!}
          <span class="">
            <div class="btn-group header-frm-btn">
              @if(auth()->user()->can(['customer_download']))
              <form method="GET" action="{{ URL::to('customers-download') }}">
                  <div class="d-flex flex-row">
                    <div class="p-2">
                        <select class="select2" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                         <option value="" >Select User</option>
                        @if(@isset($users ))
                        @foreach($users as $user)
                         <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.customers.title') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
              </form>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
        <div class="row">
        <div class="table-responsive">
            <table id="getcustomers" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>Firm Name</th>
              <th>{!! trans('panel.customers.fields.first_name') !!}</th>
              <th>{!! trans('panel.customers.fields.last_name') !!}</th>
              <th>{!! trans('panel.customers.fields.mobile') !!}</th>
              <th>{!! trans('panel.customers.fields.profile_image') !!}</th>
              <th>{!! trans('panel.customers.fields.customertype') !!}</th>
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

<style type="text/css">
  
  select#executive_id {
    border-bottom: 1px solid #d2d2d2 !important;
    border-radius: 0px;
    height: 38px;
    text-transform: uppercase;
    font-size: 13px!important;
    color: #000;
    font-weight: 400;
}
</style>

<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getcustomers').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [ {
            className: 'control',
            orderable: false,
            targets:   -1
        } ],
        "order": [ [0, 'desc'] ],
        "ajax": "{{ route('customers.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'name', name: 'name',"defaultContent": ''},
            {data: 'first_name', name: 'first_name',"defaultContent": ''},
            {data: 'last_name', name: 'last_name',"defaultContent": ''},
            {data: 'mobile', name: 'mobile',"defaultContent": ''},
            {data: 'image', name: 'image',"defaultContent": ''},
            {data: 'customertypes.customertype_name', name: 'customertypes.customertype_name',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
           
        ]
    });
  });
</script>
</x-app-layout>
