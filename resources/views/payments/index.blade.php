<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.payment.title_singular') !!}{!! trans('panel.global.list') !!}
              <span class="pull-right">
                <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  @if(auth()->user()->can(['payments_upload']))
                  <form action="{{ URL::to('payments-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.payment.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  @endif
                  @if(auth()->user()->can(['payments_download']))
                  <a href="{{ URL::to('payments-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.payment.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif
                  @if(auth()->user()->can(['payments_template']))
                  <a href="{{ URL::to('payments-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.payment.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['payments_create']))
                  <a href="{{ route('payments.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.payment.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
                </div>
                </div>
              </span>
          </h4>
      </div>
         <div class="card-body">
          <div class="table-responsive">
            <table id="getpayments" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                <thead>
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th>{!! trans('panel.global.action') !!}</th>
                  <th>Customer Name</th>
                  <th>Payment Date</th>
                  <th>Payment Mode</th>
                  <th>Payment Type</th>
                  <th>Amount</th>
                  <th>Description</th>
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
$(document).ready(function() {
    oTable = $('#getpayments').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [0, 'desc'] ],
        //"dom": 'Bfrtip',
        "ajax": "{{ route('payments.index') }}",
        "columns": [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'customer_name', name: 'customer_name',"defaultContent": ''},
            {data: 'payment_date', name: 'payment_date',"defaultContent": ''},
            {data: 'payment_mode', name: 'payment_mode',"defaultContent": ''},
            {data: 'payment_type', name: 'payment_type',"defaultContent": ''},
            {data: 'amount', name: 'amount',"defaultContent": ''},
            {data: 'description', name: 'description',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });

    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('payments') }}"+'/'+id,
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
              oTable.draw();
            },
        });
    });
});
</script>
</x-app-layout>
