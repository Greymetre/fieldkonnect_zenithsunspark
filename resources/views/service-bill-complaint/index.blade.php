<x-app-layout>
  <style>
    .table-input::placeholder {
          color: rgba(0, 0, 0, 0.5); /* Light greyish-white for better visibility */
          opacity: 1; /* Ensures visibility in all browsers */
      }

    .table-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .hover-effect {
      cursor: pointer;
      transition: transform 0.2s ease-in-out;
    }

    .hover-effect:hover {
      transform: scale(1.05); /* Slightly increase size on hover */
    }

    .table-input {
        width: 100% !important
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        outline: none;
        background: white;
        width: 335px;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Service Complaint Types {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
                 <div class="next-btn">
                      <a href="{{ route('service-bills-complaints-type.create') }}" class="btn btn-just-icon btn-theme mr-2" title="Add Service Complaint Type"><i class="material-icons">add_circle</i></a>

                     <button class="btn btn-just-icon btn-theme" id="button_download" type="button" title="{!!  trans('panel.global.download') !!} Service Bill Complaint Type"><i class="material-icons">cloud_download</i></button>

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
                <tr>
                  <th></th>
                  <th></th>
                  <th><input type="text" class="form-control table-input" name="complaint_type" placeholder="Search..." autocomplete="off"></th>
                  <th><input type="text" class="form-control table-input" name="group_name" placeholder="Search..." autocomplete="off"></th>
                </tr>
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  <th>{!! trans('panel.global.action') !!}</th>
                  <th>Complaint Type</th>
                  <th>Product Group</th>
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
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getscheme').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          type:'POST',
          url: "{{ route('getServiceComplaintType') }}",
          data: function (d) {
                d._token = token,
                d.complaint_type = $('input[name="complaint_type"]').val();
                d.group_name = $('input[name="group_name"]').val();
            }
        },
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
            data: 'service_bill_complaint_type_name',
            name: 'service_bill_complaint_type_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'subcategory',
            name: 'subcategory',
            orderable: false,
            "defaultContent": ''
          }
        ]
      });

      $('.table-input').on('keyup change', function () {
        table.draw();
      });

      $('#button_download').on('click', function() {
             let form = $('<form>', {
                method: 'POST',
                action: "{{ URL::to('service_bill_complaint_type_download') }}"
            });

            form.append($('<input>', {type: 'hidden', name: '_token', value: $('input[name="_token"]').val()}));

            $('.table-input, .table-select').each(function() {
                let inputName = $(this).attr('name');
                let inputValue = $(this).val();
                form.append($('<input>', {type: 'hidden', name: inputName, value: inputValue}));
            });

            $('body').append(form);
            form.submit();
        });

    });

    $(document).on('click', '.delete-coplaint-Type', function (e) {
          e.preventDefault(); // Prevent default button action

          let id = $(this).data('id'); // Get SOP ID

          Swal.fire({
              title: "Are you sure?",
              text: "To Delete This Service Bill Complaint Type.",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#d33",
              cancelButtonColor: "#3085d6",
              confirmButtonText: "Yes, delete it!"
          }).then((result) => {
                if (result.value == true) {
                    $('.delete-form-' + id).submit(); // Submit the correct form
                }
          });
      });
  </script>
</x-app-layout>