<x-app-layout>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    /*pagination*/

    .dataTables-wrapper {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px;
    }

    .custom-select {
      border-radius: 6px;
      box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .pagination .page-item .page-link {
      border-radius: 50% !important;
      margin: 0 3px;
      padding: 5px 12px;
      color: #3b5998;
    }

    .bmd-form-group input::placeholder {
      font-size: 14px !important;
      color: #636363 !important;
      font-weight: 400;
    }

    ul.pagination li.active a {
      background: unset !important;
      background-color: #3779B7 !important;
      color: #fff !important;
      font-weight: bold !important;
      border-color: #3779B7 !important;
    }

    .pagination .page-item.active .page-link {
      color: #fff !important;
    }

    .pagination .page-item .page-link {
      border-radius: 5px !important;
    }

    .pagination .page-item .page-link {
      margin: 0 3px;
      padding: 5px 12px;
      text-transform: capitalize;
      font-size: 14px;
      font-weight: 400;
      color: #6F6F6F !important;
      display: flex;
      align-items: center;
    }

    /*searh data*/

    .search-container i.fas.fa-plus {
      background: #fff;
      color: #2f5a95;
      border-radius: 10px;
      padding: 3px;
      font-size: 11px;
      width: 20px;
      height: 20px;
      border-radius: 50px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .search-container button:focus {
      outline: 0px !important;
    }

    .search-container {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .search-input {
      border-radius: 8px;
      padding-left: 0px;
    }

    .search-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #888;
    }

    .btn-circle {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #3b5998;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    .btn-circle:hover {
      background-color: #2d4373;
    }

    .input-wrapper {
      position: relative;
      flex: 1;
    }

    /*end*/


    .date-input {
      border-radius: 50px;
      border: 1px solid #E8E8E8;
      padding: 0.5rem 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      cursor: pointer;
    }

    .date-input input {
      border: none;
      outline: none;
      box-shadow: none;
      font-size: 14px;
      width: 100%;
      background: transparent;
    }

    .date-input .input-group-prepend,
    .date-input .input-group-append {
      border: none;
      background: transparent;
    }

    .date-input .input-group-text {
      border: none;
      background: transparent;
    }

    .date-input {
      padding: 5px 9px !important;
    }

    /*--end data---*/

    .input-group .input-group-text {
      padding: 0 5px !important;
    }

    .heading_top {
      font-size: 20px;
      font-weight: 700;
      color: #3C4858;
      lighting-color: 1.5;
      text-transform: capitalize;
      font-family: 'Poppins', sans-serif;
    }

    .inerbox {
      font-size: 14px;
      font-weight: 400;
      color: #6F6F6F;
      font-family: 'Poppins', sans-serif;
    }

    .btn-date {
      border-radius: 30px;
      background: #fff;
      border: 1px solid #ddd;
      padding: 6px 15px;
      display: flex;
      align-items: center;
    }

    .btn-date i {
      margin-right: 6px;
      color: #555;
    }

    .search-box {
      position: relative;
      width: 100%;
    }

    .search-box input {
      border-radius: 30px;
      padding-right: 45px;
    }

    .search-box button {
      position: absolute;
      right: 5px;
      top: 50%;
      transform: translateY(-50%);
      border-radius: 50%;
      background: #3b5998;
      color: #fff;
      border: none;
      width: 35px;
      height: 35px;
    }

    .card {
      border-radius: 12px !important;
      border: 1px solid #E8E8E8 !important;
    }

    body .table thead tr th {
      font-size: 14px !important;
      font-weight: 600 !important;
      letter-spacing: 0px;
      font-family: 'Poppins', sans-serif !important;
      color: #262A2A !important;
    }

    body .table>tbody>tr>td {
      font-size: 12px !important;
      padding: 10px 10px !important;
      width: 4% !important;
    }

    .table thead tr th {
      padding: 10px 10px !important;
    }

    .card .card-body {
      padding: 0px 10px !important;
    }

    .table .thead-light th {
      background-color: #fff !important;
      text-transform: uppercase;
    }

    .table-striped>tbody>tr:nth-of-type(odd),
    .table-striped>tbody>tr:nth-of-type(even) {
      background: #fff !important;
    }

    body .table thead {
      background-color: #fff !important;
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border: 1px solid #E8E8E8 !important;
    }

    body .hasDatepicker {
      height: 25px !important;
      border: 0px !important;
      font-size: 12px !important;
    }

    .search-input {
      text-indent: 30px !important;
    }

    .inerbox {
      display: flex;
      align-items: center;
    }

    .inerbox select {
      margin: 0px 8px;
    }

    span.badge-paid {
      color: #62BE5A;
      border: 1px solid #62BE5A;
      border-radius: 50px;
      background: #E6FFE4;
      padding: 0px 28px;
    }

    .table {
      border-radius: 6px !important;
      background-color: #fff !important;
      border: 1px solid #E8E8E8 !important;
      overflow: unset !important;
    }
  </style>
  <section class="invocie_main">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <h3 class="heading_top"> All Invoices</h3>
        </div>
      </div>
      <div class="card shadow">
        <div class="card-header bg-white">
          <div class="row">
            <!-- Start Date -->
            <div class="col-md-7">
              <div class="row">
                <div class="col-md-3 mb-3">
                  <div class="input-group date date-input" id="startDatePicker" data-target-input="nearest">
                    <div class="input-group-prepend" data-target="#startDatePicker" data-toggle="datetimepicker">
                      <!-- <span class="input-group-text"><i class="far fa-calendar-alt"></i></span> -->
                      <span class="input-group-text">
                        <img src="https://demo.fieldkonnect.io/public/assets/img/Group_calandar.png"></span>
                    </div>
                    <input type="text" class="form-control datepicker" id="start_date" data-target="#startDatePicker" placeholder="Start Date" />
                    <div class="input-group-append" data-target="#startDatePicker" data-toggle="datetimepicker">
                      <span class="input-group-text"><i class="fas fa-chevron-down"></i></span>
                    </div>
                  </div>
                </div>
                <!-- End Date -->
                <div class="col-md-3 mb-3">
                  <div class="input-group date date-input" id="endDatePicker" data-target-input="nearest">
                    <div class="input-group-prepend" data-target="#endDatePicker" data-toggle="datetimepicker">
                      <!-- <span class="input-group-text"><i class="far fa-calendar-alt"></i></span> -->
                      <span class="input-group-text">
                        <img src="https://demo.fieldkonnect.io/public/assets/img/Group_calandar.png"></span>
                    </div>
                    <input type="text" class="form-control datepicker" id="end_date" data-target="#endDatePicker" placeholder="End Date" />
                    <div class="input-group-append" data-target="#endDatePicker" data-toggle="datetimepicker">
                      <span class="input-group-text"><i class="fas fa-chevron-down"></i></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="search-container">
                <!-- Search Input -->
                <div class="input-wrapper w-100">
                  <i class="fas fa-search search-icon"></i>
                  <input type="text" id="searchInput" class="form-control search-input" placeholder="Search Contact">
                </div>

                <!-- Round Button -->
                @if(Auth::user()->can('invoice_create'))
                 <a href="{{ route('tax_invoice.create') }}" title="Add Invoice">
                <button class="btn-circle" title="Add Invoice">
                  <i class="fas fa-plus"></i>
                </button>
              </a>
              @endif
              @if(Auth::user()->can('invoice_export'))
              <a id="downloadInvoice" href="javascript:void(0);" title="Download Invoice">
                <button class="btn-circle" title="Download Invoice">
                  <i class="fas fa-download"></i>
                </button>
              </a>
              @endif
              </div>

            </div>


          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="invoiceTable" class="table" style="width:100%">
              <thead class="thead-light">
                <tr>
                  <th>Date</th>
                  <th>Invoice #</th>
                  <th>Order Number</th>
                  <th>Customer Name</th>
                  <th>INVOICE STATUS</th>
                  <th>Due Date</th>
                  <th>INVOICE AMOUNT</th>
                  <th>Balance</th>
                  <th>Sub Total</th>
                </tr>
              </thead>
              {{--<tbody>
                @forelse($invoices as $invoice)
                <tr>
                  <td>{{ $invoice->invoice_date }}</td>
              <td>{{ $invoice->invoice_no }}</td>
              <td>{{ $invoice->order_no }}</td>
              <td>{{ $invoice->customer->name }}</td>
              <td><span class="badge badge-paid">Paid</span></td>
              <td>{{ $invoice->due_date }}</td>
              <td>${{ number_format($invoice->grand_total, 2) }}</td>
              <td>-</td>
              <td>${{ number_format($invoice->sub_total, 2) }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center">No invoices found</td>
              </tr>
              @endforelse
              </tbody>--}}
            </table>
          </div>
        </div>

        {{--<div class="dataTables-wrapper d-flex justify-content-between align-items-center">
          <!-- Show Entries -->
          <div class="inerbox">
            Show
            <select class="custom-select custom-select-sm w-auto"
              onchange="window.location.href='?per_page=' + this.value">
              <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
              <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
              <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
              <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            entries
          </div>

          <!-- Laravel Pagination -->
          <div>
            {{ $invoices->appends(request()->query())->links('pagination::bootstrap-4') }}
          </div>
        </div> --}}

      </div>
  </section>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js"></script>

  <!-- <script>
    $(function() {
      $('#startDatePicker').datetimepicker({
        format: 'DD-MM-YYYY'
      });
      $('#endDatePicker').datetimepicker({
        format: 'DD-MM-YYYY',
        useCurrent: false
      });
      $("#startDatePicker").on("change.datetimepicker", function(e) {
        $('#endDatePicker').datetimepicker('minDate', e.date);
      });
      $("#endDatePicker").on("change.datetimepicker", function(e) {
        $('#startDatePicker').datetimepicker('maxDate', e.date);
      });
    });
  </script> -->


  <script>
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#invoiceTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        columnDefs: [{
          className: 'control',
          orderable: false,
          targets: -1
        }],
        "order": [
          [0, 'desc']
        ],
        "retrieve": true,
        ajax: {
          url: "{{ route('tax_invoice.index', ['dev' => true]) }}",
          data: function(d) {
            d.searchInput = $('#searchInput').val(),
            d.start_date = $('#start_date').val(),
            d.end_date = $('#end_date').val()
          }
        },
        columns: [{
            data: 'invoice_date',
            name: 'invoice_date',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'invoice_no',
            name: 'invoice_no',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'order_no',
            name: 'order_no',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'customer.name',
            name: 'customer.name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'status',
            name: 'status',
            "defaultContent": 'Paid',
            orderable: false,
          },
          {
            data: 'due_date',
            name: 'due_date',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'grand_total',
            name: 'grand_total',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'balance',
            name: 'balance',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'sub_total',
            name: 'sub_total',
            "defaultContent": '',
            orderable: false
          },
        ],
        dom: 't<"bottom"lip>',
      });

      $('#searchInput').on('input', function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
        $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });
    });

    $('#downloadInvoice').click(function() {
      var startDate = $('#start_date').val();
      var endDate = $('#end_date').val();
      var searchInput = $('#searchInput').val();
      var url = "{{ route('invoice.export') }}?start_date=" + startDate + "&end_date=" + endDate + "&searchInput=" + searchInput;
      window.location.href = url;
    });
  </script>


  <!--  <script>
  $(document).ready(function() {
    $('#invoiceTable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
    });
  });
</script> -->
</x-app-layout>