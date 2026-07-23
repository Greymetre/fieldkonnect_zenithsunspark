<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Service Charge Product {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  @if(auth()->user()->can(['services_product_products_create']))
                  <a class="btn btn-just-icon btn-theme" href="{{route('servicecharge.products.create')}}" title="{!!  trans('panel.global.add') !!} Service Charge Product"><i class="material-icons">add_circle</i></a>
                  @endif
                  <form method="post" action="{{ URL::to('service-charge/products/download') }}" class="form-horizontal">
                    @csrf
                    @if(auth()->user()->can(['services_product_products_download']))
                    <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Service Charge Products" name="export_division_report" value="true"><i class="material-icons">cloud_download</i></button>
                    @endif
                  </form>
                  @if(auth()->user()->can(['services_product_products_upload']))
                  <form action="{{ URL::to('service-charge/products/upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" title="Select File For Import Data" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                      <div class="input-group-append">
                        <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} Service Charge Products">
                          <i class="material-icons">cloud_upload</i>
                          <div class="ripple-container"></div>
                        </button>
                      </div>
                    </div>
                  </form>
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
            <span class="message">
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
            <table id="getbrand" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.active') !!}</th>
                <th>Product Name</th>
                <th>Cahrge Type</th>
                <th>Division</th>
                <th>Category</th>
                <th>Price(charge)</th>
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
    $(document).ready(function() {
      $(function() {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        var table = $('#getbrand').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('servicecharge.products.index') }}",
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
              data: 'active',
              name: 'active',
              "defaultContent": '',
              orderable: false,
              searchable: false
            },

            {
              data: 'product_name',
              name: 'product_name',
              "defaultContent": ''
            },
            {
              data: 'charge_type.charge_type',
              name: 'charge_type.charge_type',
              "defaultContent": ''
            },
            {
              data: 'division.division_name',
              name: 'division.division_name',
              "defaultContent": ''
            },
            {
              data: 'category.category_name',
              name: 'category.category_name',
              "defaultContent": ''
            },
            {
              data: 'price',
              name: 'price',
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
            url: "{{ url('service-charge/products') }}/" + id + '/active',
            type: 'PATCH',
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
            url: "{{ url('service-charge/products') }}/" + id + '/delete',
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
    });
  </script>
</x-app-layout>