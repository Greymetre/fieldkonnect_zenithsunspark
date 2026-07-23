<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title">{!! trans('panel.holidays.title') !!} {!! trans('panel.global.list') !!}
          <span class="">
            <div class="btn-group header-frm-btn">
            <div class="next-btn">
             <!--  @if(auth()->user()->can(['customer_template']))
              <a href="{{ URL::to('customers-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.customers.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
              @endif -->

              <!-- @if(auth()->user()->can(['customer_create'])) -->
              <a href="{{ route('holidays.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.holidays.title_singular') !!}"><i class="material-icons">add_circle</i></a>
              <!-- @endif  -->

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
            <table id="getcustomers" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.holidays.fields.branch_name') !!}</th>
              <th>{!! trans('panel.global.created_at') !!}</th>
              <th>{!! trans('panel.global.created_by') !!}</th>
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
    var table = $('#getcustomers').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [ {
            className: 'control',
            orderable: false,
            targets:   -1
        } ],
        "order": [ [0, 'desc'] ],
        "retrieve": true,
        ajax: {
          url: "{{ route('holidays.index') }}",
          // data: function (d) {
          //       d.executive_id = $('#executive_id').val(),
          //       //d.beat_id = $('#beat_id').val(),
          //       d.branch_id = $('#branch_id').val(),
          //       d.state_id = $('#state_id').val(),
          //       d.city_id = $('#city_id').val(),
          //       d.customertype = $('#customertype').val(),
          //       d.search = $('input[type="search"]').val()
          //   }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false},
            {data: 'getbranch.branch_name', name: 'getbranch.branch_name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
            {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": '', orderable: false},
           
        ]
    });

    // $('#executive_id').change(function(){
    //     table.draw();
    // });
  
    // $('#state_id').change(function(){
    //     table.draw();
    // });
    // $('#city_id').change(function(){
    //     table.draw();
    // });
    // $('#customertype').change(function(){
    //     table.draw();
    // });
    // $('#branch_id').change(function(){
    //     table.draw();
    // });
         
    $('body').on('click', '.customerActive', function () {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if(active == 'Y')
        {
          status = 'Incative ?';
        }
        else
        {
           status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want "+status)) {
           return false;
        }
        $.ajax({
            url: "{{ url('departments-active') }}",
            type: 'POST',
            data: {_token: token,id: id,active:active},
            success: function (data) {
              $('.message').empty();
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
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('holidays') }}"+'/'+id,
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
