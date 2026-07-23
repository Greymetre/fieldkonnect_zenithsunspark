<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Service Product {!! trans('panel.division.title_singular') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                @if(auth()->user()->can(['services_product_division_create']))
                  <a data-toggle="modal" data-target="#createDivision" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.division.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
                  <form method="post" action="{{ URL::to('service-charge/dividsions/download') }}" class="form-horizontal">
                    @csrf
                    @if(auth()->user()->can(['services_product_division_download']))
                    <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Division Report" name="export_division_report" value="true"><i class="material-icons">cloud_download</i></button>
                    @endif
                  </form>
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
            <table id="getbrand" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
              <thead class=" text-primary">
                <th>ID</th>
                <th>{!! trans('panel.global.action') !!}</th>
                <th>{!! trans('panel.global.active') !!}</th>

                <th>{!! trans('panel.division.fields.division_name') !!}</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade bd-example-modal-lg" id="createDivision" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.division.title_singular') !!}
            <span class="pull-right">
              <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
            </span>
          </h4>
        </div>
        <div class="modal-body">
          {!! Form::open(['route' => 'servicecharge.dividsions.add','id' => 'createDivisionForm','files'=>true ]) !!}
          <div class="row">
            <div class="col-md-12">
              <div class="input_section">
                <label class="col-form-label">{!! trans('panel.division.fields.division_name') !!} <span class="text-danger"> *</span></label>
               >
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="division_name" class="form-control" id="division_name" value="{!! old( 'division_name') !!}" maxlength="200" required>
                    @if ($errors->has('division_name'))
                    <div class="error col-lg-12">
                      <p class="text-danger">{{ $errors->first('division_name') }}</p>
                    </div>
                    @endif
                  </div>
               
              </div>
            </div>

          </div>
        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
          <input type="hidden" name="id" id="brand_id" />
          {{ Form::submit('Submit', array('class' => 'btn btn-info save')) }}
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="{{ url('/').'/'.asset('assets/js/validation_products.js') }}"></script>
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
          ajax: "{{ route('servicecharge.dividsions.index') }}",
          columns: [{
              data: 'id',
              name: 'id',
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
              data: 'division_name',
              name: 'division_name',
              "defaultContent": ''
            },
          ]
        });


        $('body').on('click', '.divisionActive', function() {
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
            url: "{{ url('service-charge/dividsions') }}/" + id + '/active',
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
        $('.create').click(function() {
          $('#brand_id').val('');
          $('#division_name').val('');
          $('#createBrandForm').trigger("reset");
          $('.modal-title').text('{!! trans("panel.global.add") !!}');
        });

        $(document).on('click', '.edit', function() {
          var divisionId = $(this).data('division-id');
          // Make AJAX request to update data
          $.ajax({
            url: "{{ url('service-charge/dividsions') }}/" + divisionId + '/edit',
            type: 'GET',
            dataType: 'json',
            data: {

            },
            success: function(data) {
              $('#division_name').val(data.division_name);
              $('#division_id').val(data.id);
              var title = '{!! trans("panel.global.edit") !!}';
              $('.modal-title').text(title);
              $('#action_button').val('Edit');
              $('#createDivision').modal('show');
              $('#brand_id').val(data.id);
            },
            error: function(error) {
              console.error('Error updating branch data:', error);
            }
          });

        });

        $('body').on('click', '.delete', function() {
          var id = $(this).attr("value");
          var token = $("meta[name='csrf-token']").attr("content");
          if (!confirm("Are You sure want to delete ?")) {
            return false;
          }
          $.ajax({
            url: "{{ url('service-charge/dividsions') }}" + '/' + id + '/delete',
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