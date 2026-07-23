<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Power BI Settings
            <span class="">
              <div class="btn-group header-frm-btn">

                <div class="next-btn">

                </div>
              </div>
            </span>
          </h4>
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {{ session()->get('message_success') }}
            </span>
          </div>
          @endif
          <div class="card">
            <div class="card-body">
              <form method="post" action="{{ URL::to('power_bi_setting/store') }}" class="form-horizontal">
                @csrf
                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="sales">Sales ifram URL :</label>
                    <input type="text" class="form-control" id="sales" name="sales" value="{{ $all_settings['sales'] ?? '' }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="employee_expense">Employee Expense ifram URL :</label>
                    <input type="text" class="form-control" id="employee_expense" name="employee_expense" value="{{ $all_settings['employee_expense'] ?? '' }}">
                  </div>
                </div>
                <div class="row">
                  <button type="submit" class="btn btn-theme m-4" >Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/validation_sales.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getMobileAppLoginUser').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'type': 'POST',
          'url': "{{ url('mobile_user_login/list') }}",
          'data': function(d) {
            d._token = token,
              d.user_id = $('#user').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
            d.designation = $('#designation').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            visible: false,
            "defaultContent": ''
          },
          {
            data: 'customer.name',
            name: 'customer.name',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'contact_person',
            name: 'contact_person',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'customer.mobile',
            name: 'customer.mobile',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'branches',
            name: 'branches',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'customer.customeraddress.statename.state_name',
            name: 'customer.customeraddress.statename.state_name',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'customer.customeraddress.districtname.district_name',
            name: 'customer.customeraddress.districtname.district_name',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'customer.customeraddress.cityname.city_name',
            name: 'customer.customeraddress.cityname.city_name',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'app_version',
            name: 'app_version',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'device_type',
            name: 'device_type',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },
          {
            data: 'device_name',
            name: 'device_name',
            orderable: true,
            searchable: true,
            "defaultContent": ''
          },

          {
            data: 'first_login_date',
            name: 'first_login_date',
            "defaultContent": ''
          },
          {
            data: 'last_login_date',
            name: 'last_login_date',
            "defaultContent": ''
          },
          {
            data: 'login_status1',
            name: 'login_status1',
            "defaultContent": ''
          },
          {
            data: 'customer.mobile',
            name: 'customer.mobile',
            searchable: true,
            visible: false,
            "defaultContent": ''
          },
        ]
      });

      $('#user').change(function() {
        table.draw();
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