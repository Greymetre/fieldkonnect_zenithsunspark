<x-app-layout>
  <style>
    #copyText {
      cursor: pointer;
      font-weight: 800;
      color: #000;
      text-shadow: 0 0 3px #fff;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Service Bill {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
              @if(auth()->user()->can(['complaint_download']))
                <form method="GET" action="{{ URL::to('complaint_download') }}" class="form-horizontal">
                  <div class="d-flex flex-wrap flex-row">
                    <!-- <div class="p-2" style="width:200px;">
                      <select class="select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="product_id" id="product_id" data-style="select-with-transition" title="Select Product">
                        <option value="">Select Product</option>
                        @if(@isset($products ))
                        @foreach($products as $product)
                        <option value="{!! $product->id !!}">{!! $product->product_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>  -->
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.complaint.title_singular') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                @if(auth()->user()->can(['complaint_create']))
                <!-- <a href="{{ route('service_bills.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.complaint.title_singular') !!}"><i class="material-icons">add_circle</i></a> -->
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
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_success') !!}
            </span>
          </div>
          @endif
          @if(session()->has('message_error'))
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_error') !!}
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
                <!-- <th>{!! trans('panel.global.action') !!}</th> -->
                <th>Service Bill Number</th>
                <th>Complaint Number</th>
                <th>Complaint Type</th>
                <th>Complaint Reason</th>
                <th>Condition of Service</th>
                <th>Received Product</th>
                <th>Nature of Fault</th>                
                <th>Status</th>
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
        "ajax": "{{ route('service_bills.index') }}",
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          // {
          //   data: 'action',
          //   name: 'action',
          //   "defaultContent": '',
          //   className: 'text-center',
          //   orderable: false,
          //   searchable: false
          // },
          {
            data: 'bill_no',
            name: 'bill_no',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaint_no',
            name: 'complaint_no',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaint_type',
            name: 'complaint_type',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'complaint_reason',
            name: 'complaint_reason',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'condition_of_service',
            name: 'condition_of_service',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'received_product',
            name: 'received_product',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'nature_of_fault',
            name: 'nature_of_fault',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'status',
            name: 'status',
            orderable: false,
            "defaultContent": ''
          },
        ]
      });

      $('body').on('click', '.activeRecord', function() {
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
          url: "{{ url('schemes-active') }}",
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
          url: "{{ url('schemes') }}" + '/' + id,
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

    $(document).ready(function() {
      $("#copyText").click(function() {
        var textToCopy = $("#copyText").text();
        var tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(textToCopy).select();
        document.execCommand("copy");
        tempInput.remove();
        const Toast = Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
          }
        });
        Toast.fire({
          icon: "success",
          title: "Service Bill number copied to clipboard: " + textToCopy
        });
      });
    });
  </script>
</x-app-layout>