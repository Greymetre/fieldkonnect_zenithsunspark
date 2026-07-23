<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.order.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="pull-right">
                <div class="btn-group">


                  @if(auth()->user()->can(['order_download']))
                <form method="GET" action="{{ URL::to('orders-download') }}">
                  <div class="d-flex flex-row">

                <div class="p-2"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.order.title') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
               </form>
                    @endif




                  @if(auth()->user()->can(['order_upload']))
                  <form action="{{ URL::to('orders-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.order.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif

                  <!-- @if(auth()->user()->can(['order_download']))
                  <a href="{{ URL::to('orders-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.order.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif -->

                  @if(auth()->user()->can(['order_template']))
                  <a href="{{ URL::to('orders-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.order.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['order_create']))
                  <a href="{{ route('orders.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.order.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
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
            <table id="getorder" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.global.buyer_name') !!}</th>
              <th>{!! trans('panel.global.seller_name') !!}</th>
              <th>{!! trans('panel.order.orderno') !!}</th>
              <th>{!! trans('panel.order.order_date') !!}</th>
              <th>{!! trans('panel.order.completed_date') !!}</th>
              <th>{!! trans('panel.order.total_gst') !!}</th>
              <th>{!! trans('panel.product.fields.suc-del') !!}</th>
              <th>{!! trans('panel.order.sub_total') !!}</th>
              <th>{!! trans('panel.order.grand_total') !!}</th>
              <th>{!! trans('panel.order.status_id') !!}</th>
              <th>{!! trans('panel.global.created_by') !!}</th>
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
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getorder').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        "ajax": "{{ route('orders.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'buyers.name', name: 'buyers.name',"defaultContent": ''},
            {data: 'sellers.name', name: 'sellers.name',"defaultContent": ''},
            {data: 'orderno', name: 'orderno',"defaultContent": ''},
            {data: 'order_date', name: 'order_date',"defaultContent": ''},
            {data: 'completed_date', name: 'completed_date',"defaultContent": ''},
            {data: 'total_gst', name: 'total_gst',"defaultContent": ''},
            {data: 'suc_del', name: 'suc_del',"defaultContent": ''},
            {data: 'sub_total', name: 'sub_total',"defaultContent": ''},
            {data: 'grand_total', name: 'grand_total',"defaultContent": ''},
            {data: 'statusname.status_name', name: 'statusname.status_name',"defaultContent": ''},
            {data: 'created_by', name: 'created_by',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
         
    $('body').on('click', '.activeRecord', function () {
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
            url: "{{ url('orders-active') }}",
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
            url: "{{ url('orders') }}"+'/'+id,
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
