<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">{!! trans('panel.global.login') !!}{!! trans('panel.global.list') !!}
            <div class="next-btn">
              @if(auth()->user()->can(['customer_user_create']))
              <a href="{{ route('customers.user.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} User From {!! trans('panel.customers.title_singular') !!}"><i class="material-icons">add_circle</i></a>
              @endif
            </div>
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
            <table id="getcustomers" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Firm Name</th>
                <th>{!! trans('panel.customers.fields.name') !!}</th>
                <th>{!! trans('panel.global.entries') !!}</th>
                <th>{!! trans('panel.global.provider') !!}</th>
                <th>{!! trans('panel.customers.fields.mobile') !!}</th>
                <th>{!! trans('panel.global.login') !!}</th>
                <th>{!! trans('panel.global.logout') !!}</th>
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
      oTable = $('#getcustomers').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('customers.customersLogin') }}",
        "columns": [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'customers.name',
            name: 'customers.name',
            "defaultContent": ''
          },
          {
            data: 'contact_person',
            name: 'contact_person',
            "defaultContent": ''
          },
          {
            data: 'entry_from',
            name: 'entry_from',
            "defaultContent": ''
          },
          {
            data: 'provider',
            name: 'provider',
            "defaultContent": ''
          },
          {
            data: 'mobile',
            name: 'mobile',
            "defaultContent": ''
          },
          {
            data: 'login_at',
            name: 'login_at',
            "defaultContent": ''
          },
          {
            data: 'logout_at',
            name: 'logout_at',
            "defaultContent": ''
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
        ]
      });
    });
  </script>
</x-app-layout>