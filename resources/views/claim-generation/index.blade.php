<x-app-layout>
  <style>
    .custom-btn {
        width: 90px; /* Smaller width */
        font-weight: bold;
        font-size: 13px;
        border-radius: 6px;
        padding: 6px 8px;
        border: none;
        color: white;
        background: linear-gradient(135deg, #007bff, #0056b3); /* Gradient Blue */
        transition: all 0.3s ease-in-out;
        box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.1);
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Claim Generation
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="POST" action="{{ route('claim-generation.store') }}" class="form-horizontal" id="generateClaim">
                  @csrf
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width:200px !important ;">
                        <input type="text" class="form-control datepicker" id="start_month" name="start_month" placeholder="From Month" autocomplete="off" readonly required> 
                    </div>
                    <div class="p-2" style="width:200px !important ;">
                        <input type="text" class="form-control datepicker" id="end_month" name="end_month" placeholder="To Month" autocomplete="off" readonly required>
                    </div>
                    <div class="p-2" style="width:200px !important ;">
                        <select class="form-control select2" id="service_center" name="service_center">
                          <option value="">Select Service Center</option>
                            @if($service_centers)
                             @foreach($service_centers as $service_center)
                             <option value="{{$service_center->id}}" {!! old('service_center')!!} >[{{$service_center->customer_code}}] {{$service_center->name}}</option>
                             @endforeach
                           @endif
                        </select>
                    </div>
                   @if(auth()->user()->can(['view_claim']))
                        <div class="p-2" style="width:150px !important ;">
                            <button class="btn custom-btn" id="button_view" type="button" title="View Claim">
                                View Claim
                            </button>
                        </div>
                    @endif
                    @if(auth()->user()->can(['generate_claim']))
                        <div class="p-2" style="width:150px !important ;">
                            <button class="btn custom-btn" id="button_generate" type="submit" title="Generate Claim">
                                Generate Claim
                            </button>
                        </div>
                    @endif
                    @if(auth()->user()->can(['export_claim_report']))
                        <div class="p-2" style="width:150px !important ;">
                            <button class="btn custom-btn" id="button_export" type="button" title="Export Claim Report">
                                Export
                            </button>
                        </div>
                    @endif
                  </div>
                </form>
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
                {{ session()->get('message_success') }}
              </span>
            </div>
          @endif
          @if(session()->has('message_danger'))
            <div class="alert alert-danger">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="material-icons">close</i>
              </button>
              <span>
                {{ session()->get('message_danger') }}
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
            <table id="getClaims" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Action</th>
                <th>Service Center</th>
                <th>Month-Year</th>
                <th>Claim Number</th>
                <th>Claim Amount</th>
                <th>Courier Details</th>
                <th>Courier Date</th>
                <th>ASC Bill No.</th>
                <th>ASC Bill Date</th>
                <th>ASC Bill Amount</th>
                <th>Claim Sattelment Details</th>
                <th>Submitted By SE</th>
                <th>Claim Approved</th>
                <th>Claim Done</th>
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
    $(document).ready(function(){
        $('#button_view').on('click' , function(){
            getClaims();
        })
        $('#generateClaim').validate({
            rules: {
                start_month: {
                    required: true,
                },
                end_month: {
                    required: true,
                    equalTo: "#start_month" // Ensure it's the same as start_month
                }
            },
            errorPlacement: function(error, element) {
                error.addClass('text-danger'); // Add Bootstrap error styling
                error.insertAfter(element.closest('.form-control')); // Insert after the select field
            },
            highlight: function(element) {
              $(element).closest('.error').css("display", "none");
            },
            unhighlight: function(element) {
              $(element).closest('.error').css("display", "block");
            },
            messages:{
              name:{
                minlength: "Please enter a valid Award Name.",
                required: "Please enter Award Name",
              },
              description:{
                required: "Please enter Description",
              },
            }
        });
    })

    function getClaims(){
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      if ($.fn.DataTable.isDataTable('#getClaims')) {
          $('#getClaims').DataTable().destroy();
      }
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getClaims').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          type:'POST',
          url: "{{ route('getClaims') }}",
          data: function (d) {
                d._token = token,
                d.start_month = $('input[name="start_month"]').val();
                d.end_month = $('input[name="end_month"]').val();
                d.service_center = $('select[name="service_center"]').val();
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
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'service_center_name',
            name: 'service_center_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'month_year',
            name: 'month_year',
            orderable: false,
            "defaultContent": ''
          },
         {
            data: 'claim_number',
            name: 'claim_number',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'claim_amount',
            name: 'claim_amount',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'courier_details',
            name: 'courier_details',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'courier_date',
            name: 'courier_date',
            orderable: false,
            "defaultContent": ''
          },
         {
            data: 'asc_bill_no',
            name: 'asc_bill_no',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'asc_bill_date',
            name: 'asc_bill_date',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'asc_bill_amount',
            name: 'asc_bill_amount',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'claim_sattlement_details',
            name: 'claim_sattlement_details',
            orderable: false,
            "defaultContent": ''
          },
         {
            data: 'submitted_by_se',
            name: 'submitted_by_se',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'claim_approved',
            name: 'claim_approved',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'claim_done',
            name: 'claim_done',
            orderable: false,
            "defaultContent": ''
          }
        ]
      });
    }
  </script>
  <script>
    $(document).ready(function(){
         $("#start_month").datepicker({
            dateFormat: "MM yy", // Show only month and year
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            closeText: "Select", // Custom text for closing
            maxDate: new Date(), // Restrict to the current month
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month option:selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year option:selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));

                // Update minDate for end_month to start_month onward
                $("#end_month").datepicker("option", "minDate", new Date(year, month, 1));
            }
        }).focus(function () {
            $(".ui-datepicker-calendar").hide(); // Hide date picker
        });

        $("#end_month").datepicker({
            dateFormat: "MM yy", // Show only month and year
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            closeText: "Select", // Custom text for closing
            maxDate: new Date(), // Restrict to the current month
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month option:selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year option:selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
            }
        }).focus(function () {
            $(".ui-datepicker-calendar").hide(); // Hide date picker
        });

        $('#button_export').on('click', function () {
          let startMonth = $('#start_month').val();
          let endMonth = $('#end_month').val();
          let serviceCenter = $('#service_center').val();

          // Create a new form dynamically
          let form = $('<form>', {
              method: 'POST',
              action: "{{ route('claim-generation.export') }}", 
          });

          // Add CSRF token
          let csrfToken = $('<input>', {
              type: 'hidden',
              name: '_token',
              value: '{{ csrf_token() }}'
          });

          // Add search parameters
          let startMonthField = $('<input>', {
              type: 'hidden',
              name: 'start_month',
              value: startMonth
          });

          let endMonthField = $('<input>', {
              type: 'hidden',
              name: 'end_month',
              value: endMonth
          });

          let serviceCenterField = $('<input>', {
              type: 'hidden',
              name: 'service_center',
              value: serviceCenter
          });

          // Append inputs to form
          form.append(csrfToken, startMonthField,endMonthField, serviceCenterField);

          // Append form to body and submit
          $('body').append(form);
          form.submit();

          // Remove form after submission to prevent duplication
          form.remove();
      });
    });
  </script>
</x-app-layout>