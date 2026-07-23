<x-app-layout>
  <style>
    span.select2-dropdown.select2-dropdown--below {
      z-index: 99999 !important;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Visitor Log {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                 
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
          @if(session('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_success') }}
            </span>
          </div>
          @endif
          @if(session('message_info'))
          <div class="alert alert-info">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session('message_info') }}
            </span>
          </div>
          @endif
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getDamageEntries" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.expenses.fields.date') !!}</th>
                <th>IP Address</th>
                <th>Country</th>
                <th>State</th>
                <th>city</th>
                <th>System Name</th>
                <th>Device</th>
                <th>Browser</th>
                <th>Mobile/System</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getDamageEntries').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('visitor') }}",
          'data': function(d) {
            d.status = $('#status').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.scheme_name = $('#scheme_name').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'ip_address',
            name: 'ip_address',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'country',
            name: 'country',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'state',
            name: 'state',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'city',
            name: 'city',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'system_name',
            name: 'system_name',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'device',
            name: 'device',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'browser',
            name: 'browser',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'is_mobile',
            name: 'is_mobile',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
    });
  </script>
</x-app-layout>