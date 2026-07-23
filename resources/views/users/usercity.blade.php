<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">User City List
            <span class="">
              <div class="btn-group header-frm-btn">
              <div class="next-btn">
                <form method="GET" action="{{ URL::to('/usercity-download') }}">
                  <div class="d-flex flex-row">
                    <input type="text" name="page_number" id="tableInfo" value="1" hidden>
                    <input type="text" name="page_length" id="page_length" value="10" hidden>

                    <div class="p-2"></div>
                    <div class=""><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('User City') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                <form action="{{ URL::to('/usercity-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="User City Upload">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                </form>
                <!-- <a href="{{ URL::to('/usercity-download') }}" class="btn btn-just-icon btn-theme" title="User City Download"><i class="material-icons">cloud_download</i></a> -->
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getaward" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>User Name</th>
                <th>User Designation</th>
                <th>Reporting Name</th>
                <th>Reporting Designation</th>
                <th>City Name</th>
                <th>Grade</th>
                <th>District Name</th>
                <th>State Name</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ asset('public/assets/js/jquery.hrms.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getaward').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: "{{ route('users.usercity') }}",
        lengthMenu: [
          [10, 25, 50, 100, 500, 1000, 5000],
          [10, 25, 50, 100, 500, 1000, 5000]
        ],

        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'userinfo.name',
            name: 'userinfo.name',
            "defaultContent": ''
          },
          {
            data: 'userinfo.getdesignation.designation_name',
            name: 'userinfo.getdesignation.designation_name',
            "defaultContent": ''
          },
          {
            data: 'reportinginfo.name',
            name: 'reportinginfo.name',
            "defaultContent": ''
          },
          {
            data: 'reportinginfo.getdesignation.designation_name',
            name: 'reportinginfo.getdesignation.designation_name',
            "defaultContent": ''
          },
          {
            data: 'cityname.city_name',
            name: 'cityname.city_name',
            "defaultContent": ''
          },
          {
            data: 'cityname.grade',
            name: 'cityname.grade',
            "defaultContent": ''
          },
          {
            data: 'cityname.districtname.district_name',
            name: 'cityname.districtname.district_name',
            "defaultContent": ''
          },
          {
            data: 'cityname.districtname.statename.state_name',
            name: 'cityname.districtname.statename.state_name',
            "defaultContent": ''
          },
        ]
      });
    });



    $(document).ready(function() {

      var table = $('#getaward').DataTable();
      var info = table.page.info();
      $('#getaward').on('page.dt', function() {
        var info = table.page.info();
        //$('#tableInfo').html(info.page+1);
        $('#tableInfo').val(info.page + 1);
      });

      table.on('length', function(e, settings, len) {
        table.ajax.reload(null, false); // user paging is not reset on reload
        $('#page_length').val(len);
      });
    });
  </script>
</x-app-layout>