<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Market Intelligence Field {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  @if(auth()->user()->can('market_intelligence_create'))
                  <a href="{{ route('market_intelligences.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Field"><i class="material-icons">add_circle</i></a>

                  @endif
                  @if(auth()->user()->can('market_intelligence_report_download'))

                  <a href="{{ route('market_intelligences.download') }}" class="btn btn-just-icon btn-theme" title="Download Market Intelligence Report"><i class="material-icons">download</i></a>
                  @endif
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
            <table id="getfields" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th> Lable Name</th>
                <th> Place Holder</th>
                <th> Field Type</th>
                <!-- <th>Module Name</th> -->
                <th>{!! trans('panel.global.created_at') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
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
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getfields').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: "{{ route('market_intelligences.index') }}",
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'label_name',
            name: 'label_name',
            "defaultContent": ''
          },
          {
            data: 'placeholder',
            name: 'placeholder',
            "defaultContent": ''
          },
          {
            data: 'field_type',
            name: 'field_type',
            "defaultContent": ''
          },
          // {
          //   data: 'customertypes.customertype_name',
          //   name: 'customertypes.customertype_name',
          //   "defaultContent": ''
          // },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            className: 'td-actions text-center',
            orderable: false,
            searchable: false
          },
        ]
      });


      $('body').on('click', '.is_active', function() {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if (active == 'Y') {
          status = 'Incative ?';
        } else {
          status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want " + status)) {
          return false;
        }
        $.ajax({
          url: "{{ url('fields-active') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id,
            active: active
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });


      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('market_intelligences') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });

    });
  </script>
</x-app-layout>