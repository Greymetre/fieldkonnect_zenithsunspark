<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Market Intelligence
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can('market_intelligence_report_download'))
                <form method="GET" action="{{ route('market_intelligences.download') }}">
                  <div class="d-flex flex-wrap flex-row">

                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division" required>
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division['id'] !!}" {{ old( 'division_id') == $division->id ? 'selected' : '' }}>{!! $division['division_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" id="button_download" title="{!!  trans('panel.global.download') !!} Market Intelligence"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                  <!--   @if(auth()->user()->can('market_intelligence_create'))
                  <a href="{{ route('market_intelligences.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Field"><i class="material-icons">add_circle</i></a>

                  @endif -->
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
            <table id="getPumpSR" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class="text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Title</th>
                <th>Division</th>
                <th>Created By</th>
                <th>Uploaded Image</th>
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
      var table = $('#getPumpSR').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          url: "{{ route('reports.marketIntelligence') }}",
          data: function(d) {
            d.division_id = $('#division_id').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'title',
            name: 'title',
            orderable: false,
            searchable: false
          },
          {
            data: 'division.division_name',
            name: 'division.division_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'createdbyname.name',
            name: 'createdbyname.name',
            orderable: false,
            searchable: false
          },
          {
            data: 'uploaded_image',
            name: 'uploaded_image',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": ''
          }
        ]
      });

      $('#division_id').change(function() {
        table.draw();
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