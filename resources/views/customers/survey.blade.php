<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Customer Survey
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="GET" action="{{ URL::to('survey-download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2">
                      <select class="form-control select2" name="module" style="width: 100%;" required>
                        <option value="">Select {!! trans('panel.customers.fields.customertype') !!}</option>
                        @if(@isset($customertype ))
                        @foreach($customertype as $type)
                        <option value="{!! $type['id'] !!}">{!! $type['customertype_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" title="Checkin Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </div>
                </form>
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
            <table id="getcustomersurvey" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.customers.fields.name') !!}</th>
                <th>Survey Data</th>
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
      oTable = $('#getcustomersurvey').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('customers.survey') }}",
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
            data: 'survey',
            name: 'survey',
            "defaultContent": ''
          },
        ]
      });
    });
  </script>
</x-app-layout>