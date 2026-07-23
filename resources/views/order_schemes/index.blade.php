<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.orderschemes.title_singular') !!} {!! trans('panel.global.list') !!}
               </h4>
            <span class="">
              <div class="btn-group header-frm-btn">
              <div class="next-btn">                
                @if(auth()->user()->can(['scheme_create']))
                <a href="{{ route('orderschemes.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.orderschemes.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                @endif
              </div>
            </span>
       
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getscheme" class="table table-striped- table-bschemeed table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.scheme_name') !!}</th>
                <th>{!! trans('panel.scheme.fields.scheme_description') !!}</th>
                <th>{!! trans('panel.scheme.fields.start_date') !!}</th>
                <th>{!! trans('panel.scheme.fields.end_date') !!}</th>
                <th>{!! trans('panel.scheme.fields.scheme_type') !!}</th>
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
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getscheme').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": "{{ route('orderschemes.index') }}",
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
            className: 'td-actions text-center',
            orderable: false,
            searchable: false
          },
          {
            data: 'scheme_name',
            name: 'scheme_name',
            "defaultContent": ''
          },
          {
            data: 'scheme_description',
            name: 'scheme_description',
            "defaultContent": ''
          },
          {
            data: 'start_date',
            name: 'start_date',
            "defaultContent": ''
          },
          {
            data: 'end_date',
            name: 'end_date',
            "defaultContent": ''
          },
          {
            data: 'scheme_type',
            name: 'scheme_type',
            "defaultContent": ''
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
        ]
      });


      $('body').on('click', '.orderschemeActive', function() {
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
          url: "{{ url('orderschemes-active') }}",
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
          url: "{{ url('orderschemes') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
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


    });
  </script>
</x-app-layout>