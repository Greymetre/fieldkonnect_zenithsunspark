<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <label class="col-md-3 col-form-label">Select Status</label>
              <div class="col-md-9">
                <div class="form-group has-default bmd-form-group">
                  <select class="form-control select2" name="status" id="status" data-style="select-with-transition" title="Select {!! trans('panel.lead.leadstage_id') !!}">
                     <option value="" disabled selected>Select Status</option>
                     <option value="Open">Open</option>
                     <option value="in_progress">In Progress</option>
                     <option value="Answered">Answered</option>
                     <option value="Hold">Hold</option>
                     <option value="Closed">Closed</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 pull-right">
            @if(auth()->user()->can(['supports_create']))
            <a class="btn btn-just-icon btn-info pull-right" href="{{ route('supports.create') }}"  title="{!!  trans('panel.global.add') !!} {!! trans('panel.support.title_singular') !!}">
                <i class="material-icons">add_circle</i>
              </a>
            @endif
          </div>
        </div>
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
            <table id="getsupports" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                <thead class="text-rose">
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th>{!! trans('panel.global.action') !!}</th>
                  <th>{!! trans('panel.support.subject') !!}</th>
                  <th>{!! trans('panel.support.full_name') !!}</th>
                  <th>{!! trans('panel.support.priority') !!}</th>
                  <th>{!! trans('panel.global.created_at') !!}</th>
                </tr>
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
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getsupports').DataTable({
        processing: true,
        serverSide: true,
        "retrieve": true,
        ajax: {
          url: "{{ route('supports.index') }}",
          data: function (d) {
                d.status = $('#status').val()
                d.search = $('input[type="search"]').val()
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'subject', name: 'subject',"defaultContent": ''},
            {data: 'full_name', name: 'full_name',"defaultContent": ''},
            {data: 'priorities.priority_name', name: 'priorities.priority_name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });

    $('#status').change(function(){
        table.draw();
    });  
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('supports') }}"+'/'+id,
            type: 'DELETE',
            data: {_token: token,id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
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
