<x-app-layout>
  <style>
    .modal-content {
      border-radius: 12px;
    }

    .invocie_main form .form-group select.form-control {
      position: unset !important;
    }

    .modal-header {
      border-bottom: none;
    }

    .modal-footer {
      border-top: none;
    }

    .btn-primary {
      background-color: #1a73e8;
      border-radius: 8px;
      padding: 6px 20px;
    }

    .btn-outline-secondary {
      border-radius: 8px;
      padding: 6px 20px;
    }

    .form-control {
      border-radius: 8px;
    }

    .modal-dialog .modal-content {
      border-radius: 14px !important;
    }

    .modal-dialog .modal-header .modal-title {
      text-align: left;
    }

    .modal-body form .form-group select.form-control {
      position: unset !important;
    }

    .modal-body label {
      color: #3779B7;
      font-size: 14px !important;
      font-weight: 600;
    }

    .modal-body input.form-control,
    .modal-body select.form-control {
      border: 1px solid #D1D5DB !important;
      border-radius: 8px !important;
      padding: 0px 8px !important;
    }

    .modal .modal-header .close span {
      color: #000;
      font-size: 20px;
      font-weight: 400;
    }

    .modal .modal-header .close {
      color: #000;
      width: 22px;
      height: 22px;
      padding: 0px;
      border: 1px solid #000;
      border-radius: 50px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .modal-dialog .modal-header .close {
      position: absolute;
      top: 39px;
      right: 34px;
    }

    .modal-footer button.btn-outline-secondary {
      background: #fff !important;
      border: 1px solid #3B82F6 !important;
      outline: 0px;
      box-shadow: unset;
      color: #3B82F6;
      height: 35px;
    }

    .modal-footer button.btn-primary {
      background: #3B82F6 !important;
      border-radius: 8px;
      font-weight: 600;
      height: 35px;
      margin-right: 8px !important;
    }

    .modal-footer select {
      -moz-appearance: auto !important;
      -webkit-appearance: auto !important;
    }

    select,
    select.form-control {
      -moz-appearance: auto !important;
      -webkit-appearance: auto !important;
    }


    /*end modal*/

    .summary-box span {
      color: #111827;
      font-weight: 600;
      font-size: 14px;
    }

    .summary-box select.form-control.form-control-sm {
      border: 1px solid #D1D5DB;
      background: #fff;
      height: 30px !important;
      color: #111827;
      font-weight: 400;
      padding: 0px 10px;
    }

    .summary-box input#adjustment {
      border: 1px solid #D1D5DB;
      background: #fff;
      height: 30px;
      padding: 0px 13px;
      font-size: 14px !important;
      color: #111827 !important;
      font-weight: 600;
    }

    .summary-box label.summary-label {
      color: #374151 !important;
      font-weight: 400 !important font-size: 15px !important;
    }

    .summary-box input#discount {
      border: 1px solid #D1D5DB;
      background: #fff;
      height: 30px;
      padding: 0px 13px;
      font-size: 14px !important;
      color: #111827 !important;
      font-weight: 600;
    }

    .summary-box {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
      max-width: 600px;
      margin: 20px auto;
    }

    .summary-label {
      font-weight: 600;
      font-size: 14px;
    }

    .summary-box .form-group {
      /*margin-bottom: 1rem;*/
    }

    .summary-box .col-3,
    .summary-box .col-6 {
      display: flex;
      align-items: center;
    }

    .summary-box .col-3.text-right {
      justify-content: flex-end;
    }

    .summary-box .custom-control {
      margin-right: 1rem;
    }

    .summary-box .total-label,
    .summary-box .total-value {
      font-weight: 700;
      color: #111827;
    }

    .summary-box .border-top {
      border-top: 1px solid #dee2e6 !important;
    }

    body .invocie_main .customerNotes {
      border-radius: 12px !important;
      padding: 12px !important;
      border: 1px solid #D1D5DB;
      height: 144px !important;
    }

    .upload-box {
      border: 2px dashed;
      #D1D5DB;
      border-radius: 16px;
      padding: 21px 20px;
      text-align: center;
      color: #6c757d;
      cursor: pointer;
      transition: background-color 0.3s;
      background-color: #fff;
      user-select: none;
    }

    .upload-box:hover {
      background-color: #f8f9fa;
    }

    .upload-icon {
      font-size: 36px;
      margin-bottom: 10px;
      color: #6c757d;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    select {
      padding: 0px 10px !important;
    }

    .upload-label {
      color: #007bff;
      font-weight: 500;
      display: block;
      margin-bottom: 5px;
    }

    .upload-note {
      font-size: 12px;
      color: #6B7280;
      font-weight: 400;
      display: block;
    }

    input[type="file"] {
      display: none;
    }

    body nav.navbar.navbar-expand-lg.navbar-transparent.navbar-absolute.fixed-top {
      z-index: 99999 !important;
    }

    /*3 box*/

    body .invocie_main .table>tbody>tr>td {
      padding: 15px 10px !important;
      width: 4% !important;
    }

    body .invocie_main .table thead tr th {
      padding: 12px 10px !important;
    }

    body .invocie_main .table thead tr th {
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
      color: #6B7280 !important;

    }

    .invocie_main .table>tbody>tr>td.action-cell {
      color: red !important;
      cursor: pointer !important;
      font-weight: bold !important;
    }

    .invocie_main .add-row-btn {
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      color: #007bff;
      background: none;
      border: none;
      padding: 0;
      outline: 0px !important;
    }

    .invocie_main button.add-row-btn:focus {
      outline: 0px;
    }

    .invocie_main .add-row-btn:hover {
      text-decoration: underline;
    }

    .invocie_main input[type="text"],
    .invocie_main input[type="number"],
    .invocie_main select {
      border: none;
      background: transparent;
      width: 100%;
      padding: 0;
      margin: 0;
      color: #6B7280 !important;
    }

    .invocie_main input[type="text"]:focus,
    .invocie_main input[type="number"]:focus,
    .invocie_main select:focus {
      outline: none;
      border-bottom: 1px solid #007bff;
      background: white;
      color: #6B7280 !important;
    }


    /*edit box*/

    .invocie_main label.bmd-label-static {
      font-weight: 600;
      font-size: 14px;
      color: #3779B7;
    }

    .invocie_main label.bmd-label-static.othercolor {
      color: #374151 !important;
    }

    label.othercolor {
      color: #374151 !important;
    }

    .custom-select-box {
      border: 1px solid #D1D5DB !important;
      border-radius: 12px !important;
      font-size: 16px !important;
      color: #111827 !important;
      padding: 8px 12px;
      /* spacing */
      height: 45px;
      /* avoid fixed Bootstrap height */
    }

    .custom-input-box {
      border: 1px solid #D1D5DB !important;
      border-radius: 12px !important;
      font-size: 16px !important;
      color: #111827 !important;
      padding: 8px 12px;
      height: 40px;
      /* allow natural height */
      text-indent: 10px;
    }

    select#exampleSelect {
      /*background-image: url(https://demo.fieldkonnect.io/public/assets/img/barrow.png);
    background-size: 2%;
    background-position: 98% 50%;
    background-repeat: no-repeat;*/
    }


    .address-box {
      color: #111827 !important;
    }

    p.subh {
      color: #111827 !important;
    }

    .address-box p {
      font-size: 14px !important;
      font-weight: 400;
    }

    span.address-title {
      color: #374151;
      font-weight: 700;
    }

    .address-box {
      padding: 20px;
      background: #fff;
      position: relative;
    }

    .address-title {
      font-weight: bold;
      text-transform: uppercase;
      font-size: 14px;
    }

    .edit-icon {
      position: absolute;
      top: 22px;
      color: #888;
      cursor: pointer;
      left: 159px;
    }

    .edit-icon:hover {
      color: #007bff;
    }


    /*end*/

    .invocie_main h5 {
      font-size: 20px;
      line-height: 1.4em;
      margin-bottom: 15px;
      color: #000;
      font-weight: bold;
      color: #3C4858;
      font-family: 'Poppins', sans-serif;
    }

    .search button.btn.btn-primary {
      background: unset !important;
      background-color: #35A3D8 !important;
      width: 40px !important;
      height: 40px !important;
      border-radius: 50px !important;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 10px 6px;
    }

    select#customerName {
      border: 0px;
    }

    .innerborder {
      width: 100%;
      height: 50px !important;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 5px 5px !important;
      /* border: 1px solid #D1D5DB;
      border-radius: 10px; */
      background: #fff;
    }

    .table {
      overflow: unset !important;
    }

    body .table>tbody>tr>td {
      padding: 10px 20px !important;
    }

    body .table thead {
      background-color: #fff !important;
      box-shadow: 0px 4px 4px 0px #DBDBDB40 !important;
      border: 1px solid #E5E7EB !important;
    }

    body .table tbody tr {
      box-shadow: unset !important;
    }

    body .table thead tr th {
      font-size: 14px !important;
      font-weight: 600 !important;
      color: #262A2A !important;
      font-family: 'Poppins', sans-serif !important;
      color: #6B7280 !important;
    }


    .custom-select:focus {
      box-shadow: unset !important;
    }

    .custom-select {
      background: #fff !important;
    }

    .table {
      border: 1px solid #E5E7EB !important;
    }

    .table .thead-light th {
      background: #F9FAFB !important;
    }

    .form-check,
    label {
      font-size: 14px;
      font-weight: 600;
      color: #3779B7;
    }

    .custom-select {
      /* appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: none !important;*/
    }

    .button-section {
      border-top: 1px solid #e5e7eb;
      /* thin border like in image */
      padding: 15px 0;
      text-align: right;
      /* align buttons to right */
    }

    .button-section button.btn.btn-primary {
      width: 100% !important;
      border-radius: 8px !important;
      max-width: 153px;
      height: 40px;
      background: #3B82F6 !important;
      font-size: 16px;
      font-weight: 600;
    }

    .button-section button.btn-outline-primary {
      background: #fff !important;
      border-radius: 8px;
      border: 1px solid #3B82F6;
      color: #3B82F6;
      font-size: 16px;
      font-weight: 600;
      height: 40px;
      width: 100%;
      max-width: 148px;
      margin-left: 10px;
      padding: 0px 5px;
    }

    .select2-container--default .select2-selection--single {
      background-color: #FFF !important;
      border-radius: 12px !important;
      height: 100% !important;
      padding-bottom: 4px !important;
      font-weight: 400 !important;
      border: 1px solid #D1D5DB !important;
      color: #6B7280 !important;
      font-size: 16px !important;
      text-transform: capitalize !important;
    }

    .button-section button.btn-outline-primary:hover {
      background: #fff !important;
      border: 1px solid #3B82F6;
      color: #3B82F6;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      font-size: 16px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
      color: #6B7280 !important;
    }

    .button-section {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      margin-top: 35px;
    }

    .modal-dialog .modal-body table input {
      width: 230px;
      padding: 5px 9px;
      border: 0px !important;
    }


    @media (min-width: 576px) {
      .modal-dialog {
        max-width: 390px !important;
        margin: 1.75rem auto;
      }

      body #paymentterm .modal-dialog {
        max-width: 599px !important;
        margin: 1.75rem auto;
        top: 120px !important;
      }
    }

    #invoice_no_div {
      position: relative;
    }

    #invoice_no_settings {
      position: absolute;
      top: 30px;
      right: 5px;
      color: #3B82F6 !important;
      cursor: pointer;
    }

    /* style the generated LI whose id ends with __add__ */
    .select2-results__option[id$="__add__"] {
      background: #f8f9fa !important;
      color: #007bff !important;
      font-weight: 600;
      text-align: center;
      border-top: 1px solid #ddd;
      cursor: pointer;
      padding: 8px 12px;
      display: block;
    }

    /* hover */
    .select2-results__option[id$="__add__"]:hover {
      background: #007bff !important;
      color: #fff !important;
    }

    #invoiceNoModal .modal-dialog .modal-content {
      width: 650px !important;
    }

    .swal2-container.swal2-top-end {
      top: 40px !important;
      z-index: 999999999 !important;
    }

    .is-invalid {
      border: 1px solid red !important;
    }
  </style>
  <section class="invocie_main">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    <div class="container-fluid">
      <div class="card p-4">
        <form action="{{ route('tax_invoice.store') }}" method="post" id="createInvoiceForm" enctype="multipart/form-data">
          @csrf
          <h5><i class="fa fa-file-text-o"></i> New Invoice</h5>
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label for="customer_id">Customer Name <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="innerborder">
                    <!-- Dropdown -->
                    <select class="custom-select select2" id="customer_id" name="customer_id" required>
                      <option value="">Select Customer</option>
                      <option value="__add__">➕ Add New</option>
                      @if(!empty($customers))
                      @foreach($customers as $customer)
                      <option value="{!! $customer['id'] !!}" {{ isset($tax_invoice) && $tax_invoice->customer_id == $customer['id'] ? 'selected' : '' }}>{!! $customer['name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <!-- Billing Address -->
            <div class="col-md-6 mb-3">
              <div class="address-box d-none" id="billing_address"></div>
            </div>

            <!-- Shipping Address -->
            <div class="col-md-6 mb-3">
              <div class="address-box d-none" id="shipping_address"></div>
            </div>
          </div>
          <div class="row">
            <!-- Column with form-group -->
            <div class="col-md-6 d-none" id="place_of_supply_div">
              <div class="form-group mb-3">
                <label for="place_of_supply">Place of Supply</label>
                <select class="form-control custom-select-box select2" id="place_of_supply" name="place_of_supply">
                  <option value="">Select State</option>
                  @if(!empty($states))
                  @foreach($states as $state)
                  <option value="{!! $state['id'] !!}" {{ old( 'place_of_supply') == $state['state_name'] ? 'selected' : '' }}>{!! $state['state_name'] !!}</option>
                  @endforeach
                  @endif
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-3" id="invoice_no_div">
                <label for="invoice_no">Invoice#*</label>
                <input type="text" class="form-control custom-input-box" id="invoice_no" name="invoice_no" value="{!! isset($tax_invoice) && $tax_invoice->invoice_no ? $tax_invoice->invoice_no : $invoiceNumber !!}" readonly required><i title="Configure Invoice Number" class="material-icons icon" id="invoice_no_settings" data-toggle="modal" data-target="#invoiceNoModal">settings</i>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="order_no" class="othercolor">Order Number</label>
                <input type="text" class="form-control custom-input-box" id="order_no" name="order_no">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group mb-3">
                <label for="invoice_date">Invoice Date*</label>
                <input type="date" class="form-control custom-input-box" id="invoice_date" name="invoice_date" required>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-3 bmd-form-group">
                <label for="user_id" class="othercolor bmd-label-static">Salesperson</label>
                <select class="form-control select2 custom-select-box" id="user_id" name="user_id">
                  <option value="">Select Salesperson</option>
                  @if(!empty($users))
                  @foreach($users as $user)
                  <option value="{!! $user['id'] !!}" {{ old( 'user_id') == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                  @endforeach
                  @endif
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-3 bmd-form-group">
                <label for="payment_term" class="othercolor bmd-label-static">Terms</label>
                <select class="form-control select2 custom-select-box" id="payment_term" name="payment_term">
                  <option value="">Select Terms</option>
                  @if(!empty($payment_terms))
                  @foreach($payment_terms as $term)
                  <option value="{{ $term->id }}" data-days="{{ $term->number_of_days }}">{{ $term->term_name }}</option>
                  @endforeach
                  @endif
                  <option value="__add__">➕ Add New</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group mb-3">
                <label for="due_date" class="othercolor">Due Date</label>
                <input type="date" class="form-control custom-input-box readonlys" id="due_date" name="due_date">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h6>Item Table</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <thead class="thead-light">
                    <tr>
                      <th>Item Details</th>
                      <th>HSN/SAC Code</th>
                      <th>Quantity</th>
                      <th>Rate</th>
                      <th>Tax</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="item-table-body">
                    <tr>
                      <td>
                        <select class="form-control select2 product_rowchange" id="product_id" name="product_id[]" required>
                          <option value="">Select Item</option>
                          @if(!empty($products))
                          @foreach($products as $product)
                          <option value="{!! $product['id'] !!}">{!! $product['description'] !!}</option>
                          @endforeach
                          @endif
                        </select>
                        <input type="text" class="form-control d-none" placeholder="Description" name="product_dec[]" id="product_dec" />
                      </td>
                      <td><input type="text" placeholder="HSN/SAC Code" name="hsn_sac[]" /> <input style="color: #35A3D8!important;" placeholder="Type" type="text" name="hsn_sac_type[]"></td>
                      <td><input type="number" class="form-control quantity_rowchange" name="quantity[]" min="0" step="0.01" value="1.00" /></td>
                      <td><input type="number" class="form-control mrp_rowchange" name="mrp[]" min="0" step="0.01" value="0.00" /> <input type="hidden" name="tax_amount[]"></td>
                      <td>
                        <select name="tax[]" class="form-control tax_rowchange">
                          <option value="">Select Tax</option>
                          <option value="__add__"> ➕ Add New </option>
                          @if(!empty($all_tax))
                          @foreach($all_tax as $tax)
                          <option value="{!! $tax['id'] !!}" data-rate="{!! $tax['tax_percentage'] !!}">{!! $tax['tax_percentage'] !!}% </option>
                          @endforeach
                          @endif
                        </select>
                      </td>
                      <td><input type="text" class="amount" name="amount[]" value="0.00" readonly /></td>
                      <td class="action-cell" onclick="removeRow(this)">×</td>
                    </tr>
                  </tbody>
                </table>
                <button type="button" class="add-row-btn" onclick="addRow()">+ Add New Row</button>
              </div>
            </div>
          </div>
          <div class="row align-items-center">
            <div class="col-md-4">
              <div class="form-group">
                <label for="customerNotes" class="othercolor">Customer Notes</label>
                <textarea
                  class="form-control customerNotes "
                  id="customerNotes"
                  rows="4"
                  name="customer_notes"
                  placeholder="Note..."></textarea>
              </div>
            </div>
            <div class="col-md-4">
              <label class="othercolor mb-0" for="fileUpload">Attach File(s) to Invoice</label>
              <div class="upload-box" id="uploadBox">
                <svg xmlns="http://www.w3.org/2000/svg" class="upload-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="36" height="36">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12" />
                </svg>
                <span class="upload-label">Upload File</span>
                <span class="upload-note">You can upload a maximum of 10 files, 10MB each</span>
              </div>
              <input type="file" id="fileUpload" name="files[]" multiple />
            </div>
            <div class="col-md-4">
              <div class="summary-box">
                <div class="form-group d-flex justify-content-between align-items-center mb-2">
                  <label class="summary-label mb-0">Sub Total</label>
                  <input type="text" class="" value="0.00" name="sub_total" id="sub_total" style="text-align: right;font-weight: 500;color: #000 !important;" readonly>
                </div>
                <div class="form-group d-flex align-items-center">
                  <div class="col-3 px-0">
                    <label class="summary-label mb-0" for="discount">Discount</label>
                  </div>
                  <div class="col-6 px-1">
                    <input type="number" class="form-control form-control-sm" name="discount" id="discount" value="0" min="0" style="border-radius: 5px 0px 0px 5px;" />
                    <select name="discount_type" id="discount_type" class="form-control form-control-sm" style="width: 60px;padding: 0 !important;border-radius: 0px 5px 5px 0px;">
                      <option value="percentage">%</option>
                      <option value="amount">₹</option>
                    </select>
                  </div>
                  <div class="col-3 px-0 text-right">
                    <input type="text" class="" value="0.00" name="discount_amount" id="discount_amount" style="text-align: right;font-weight: 500;color: #000 !important;" readonly>
                  </div>
                </div>


                <div id="taxSummary" class="d-none" style="color: #000;margin: 15px 0px;"></div>

                <div class="form-group d-flex align-items-center">
                  <div class="col-3 px-0 d-flex">
                    <label class="summary-label mb-0" for="TDS">TDS</label>
                  </div>
                  <div class="col-6 px-1">
                    <select class="form-control form-control-sm" name="tds" id="tds">
                      <option value="">Select TDS</option>
                      <option value="__add__"> ➕ Add New </option>
                      @if(!empty($all_tds))
                      @foreach($all_tds as $tds)
                      <option value="{!! $tds['id'] !!}" data-rate="{!! $tds['rate'] !!}="">{!! $tds['tax_name'] !!} ({!! $tds['rate'] !!}% )</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                  <div class=" col-3 px-0 text-right">
                        <input type="text" class="" value="0.00" name="tds_amount" style="text-align: right;font-weight: 500;color: #000 !important;" id="tds_amount" readonly>
                  </div>
                </div>

                <div class="form-group d-flex align-items-center">
                  <div class="col-3 px-0">
                    <label class="summary-label mb-0" for="adjustment">Adjustment</label>
                  </div>
                  <div class="col-6 px-1">
                    <input
                      type="number"
                      class="form-control form-control-sm"
                      step="0.01"
                      value="0.00"
                      id="adjustment"
                      name="adjustment"
                      placeholder="Text" />
                  </div>
                  <div class="col-3 px-0 text-right">
                    <span id="adjustment_amount">0.00</span>
                  </div>
                </div>
                <div class="form-group d-flex justify-content-between align-items-center mt-2 pt-3 border-top">
                  <div class="total-label">Total ( ₹ )</div>
                  <div class="total-value"><input type="text" class="" value="0.00" name="grand_total" style="text-align: right;font-weight: 500;color: #000 !important;" id="grand_total" readonly></div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="customerNotes" class="othercolor">Terms & Conditions</label>
                <textarea
                  class="form-control customerNotes "
                  id="customerNotes"
                  rows="4"
                  name="t_c"
                  placeholder="Terms & Conditions ...."></textarea>
              </div>

            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="button-section">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-outline-primary">Cancel</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 1 -->
    <div class="modal fade" id="paymentterm" tabindex="-1" role="dialog" aria-labelledby="newpaymenttermModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="newpaymenttermModalLabel">Configure Payment Terms</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-bordered text-center" id="termsTable" style="width: 85%;">
                <thead class="thead-light">
                  <tr>
                    <th>TERMS NAME</th>
                    <th>NUMBERS OF DAYS</th>
                  </tr>
                </thead>
                <tbody style="text-align: left;">
                  @if(!empty($payment_terms))
                  @foreach($payment_terms as $term)
                  <tr>
                    <td>{{$term->term_name}}</td>
                    <td>{{$term->number_of_days}}</td>
                  </tr>
                  @endforeach
                  @endif
                </tbody>
              </table>
            </div>
            <a href="javascript:void(0)" id="addNewRow">+ Add New</a>
          </div>

          <div class="modal-footer">
            <button type="button" id="savePaymentTerms" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal 2 -->
    <div class="modal fade" id="invoiceNoModal" tabindex="-1" role="dialog" aria-labelledby="newinvoiceNoModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="newinvoiceNoModalLabel">Configure Invoice Number Preferences</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <p>Your invoice numbers are set on auto-generate mode to save your time.
              Are you sure about changing this setting?</p>
            <div class="row">
              <div class="col-md-6">
                <label for="prefix" class="othercolor">Prefix</label>
                <input type="text" class="form-control custom-input-box"
                  id="prefix" name="prefix" value="{{ $prefixValue }}">
              </div>
              <div class="col-md-6">
                <label for="next_number" class="othercolor">Next Number</label>
                <input type="text" class="form-control custom-input-box"
                  id="next_number" name="next_number" value="{{ $nextNumberValue }}">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="updateInvoiceNumber" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal 3 -->
    <div class="modal fade" id="taxModal" tabindex="-1" role="dialog" aria-labelledby="newTaxModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="newTaxModalLabel">New Tax</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <label for="tax_name" class="othercolor">Tax Name</label>
                <input type="text" class="form-control custom-input-box"
                  id="tax_name" name="tax_name">
              </div>
              <div class="col-md-12">
                <label for="tax_percentage" class="othercolor">Tax Percentage</label>
                <input type="text" class="form-control custom-input-box"
                  id="tax_percentage" name="tax_percentage">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="addTax" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          </div>

        </div>
      </div>
    </div>
    <!-- Modal 4 -->
    <div class="modal fade" id="tdsModal" tabindex="-1" role="dialog" aria-labelledby="newTdsModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title" id="newTdsModalLabel">New TDS</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <label for="tax_name_tds" class="othercolor">Tax Name</label>
                <input type="text" class="form-control custom-input-box"
                  id="tax_name_tds" name="tax_name_tds">
              </div>
              <div class="col-md-12">
                <label for="rate" class="othercolor">Rate</label>
                <input type="text" class="form-control custom-input-box"
                  id="rate" name="rate">
              </div>
              <div class="col-md-12">
                <label for="section" class="othercolor">Section</label>
                <input type="text" class="form-control custom-input-box"
                  id="section" name="section">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="addTds" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          </div>

        </div>
      </div>
    </div>
  </section>
  <script src="{{ url('/').'/'.asset('assets/js/validation_invoice.js?v='.time()) }}') }}"></script>
  <script>
    $(document).ready(function() {
      $("#addNewRow").click(function() {
        $(this).hide();
        $("#termsTable tbody").append(`
        <tr>
          <td><input type="text" placeholder="Enter New Terms" name="term_names"></td>
          <td><input type="number" placeholder="Enter No. of Days" name="number_of_days"></td>
        </tr>
      `);
      });
    });
  </script>
  <script>
    // Get references to elements
    const uploadBox = document.getElementById('uploadBox');
    const fileInput = document.getElementById('fileUpload');

    // When upload box is clicked, trigger the hidden file input click
    uploadBox.addEventListener('click', () => {
      fileInput.click();
    });

    // Optional: handle selected files
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0) {
        alert(`${fileInput.files.length} file(s) selected.`);
      }
    });
  </script>
  <script>
    function addRow() {
      let $tableBody = $("#item-table-body");

      // Clone the first row
      let $newRow = $tableBody.find("tr:first").clone();

      // Reset values in cloned row
      $newRow.find("input").val("");
      $newRow.find("input[name='quantity[]']").val("1.00");
      $newRow.find("input[name='mrp[]']").val("0.00");
      $newRow.find("input[name='amount[]']").val("0.00");
      $newRow.find("select").val(""); // reset dropdowns

      // Remove any duplicate select2 container if exists
      $newRow.find(".select2").next(".select2-container").remove();

      // Append the cloned row
      $tableBody.append($newRow);

      // Reinitialize select2 on the new row
      $newRow.find(".select2").select2();
    }

    function removeRow(el) {
      let rowCount = $("#item-table-body tr").length;
      if (rowCount > 1) {
        $(el).closest("tr").remove();
      }
      var sub_total = 0;
      calculateTaxes();
    }

    $(document).ready(function() {
      let csrfToken = $('meta[name="csrf-token"]').attr('content');
      $('#payment_term').select2({
        width: '100%',
        templateResult: function(data) {
          if (data.id === '__add__') return $('<span style="color:green;">' + data.text + '</span>');
          return data.text;
        }
      });

      $('#payment_term').on('select2:select', function(e) {
        const selectedVal = e.params.data.id;

        if (selectedVal === '__add__') {
          $(this).val(null).trigger('change');
          $('#paymentterm').modal('show');
          return;
        } else {
          var paymentTerm = $(this).find('option:selected').data('days');
          chnageDueDate(paymentTerm);
        }
      });

      $('#savePaymentTerms').on('click', function(e) {
        e.preventDefault();
        var term_name = $('input[name="term_names"]').val();
        var number_of_days = $('input[name="number_of_days"]').val();
        if (!term_name || !number_of_days) {
          alert('Please fill all the fields');
          return;
        }
        $.ajax({
          url: '{{ route("add_payment_term") }}', // your route
          method: 'POST',
          data: {
            term_name: $('input[name="term_names"]').val(),
            number_of_days: $('input[name="number_of_days"]').val(),
          },
          headers: {
            'X-CSRF-TOKEN': csrfToken
          },
          success: function(response) {
            ;
            if (response.status) {
              $('#paymentterm').modal('hide');

              // Clear input field
              $('#payment_term').val('');

              // Add new option just before the last option ("Add Department")
              $('#payment_term')
                .find('option:last')
                .before(`<option value="${response.data.id}" data-days="${response.data.number_of_days}" selected>${response.data.term_name}</option>`);
              // Set the newly added option as selected
              $('#payment_term').val(response.data.id).trigger('change');
              chnageDueDate(response.data.number_of_days);
            }
          },
          error: function(xhr) {
            console.error(xhr.responseText);
            alert('Failed to add payment term. Please try again.');
          }
        });
      });

      $('#invoice_date').on('change', function() {
        var paymentTerm = $('#payment_term').find('option:selected').data('days');
        if (paymentTerm != undefined) {
          chnageDueDate(paymentTerm);
        }
      });

      $('#customer_id').on('change', function() {
        var customerID = $(this).val();
        if (customerID == '__add__') {
          window.location.href = "{{ route('customers.create') }}";
        }
        $.ajax({
          url: "{{ url('getCustomerAddress') }}",
          dataType: "json",
          type: "POST",
          data: {
            _token: "{{csrf_token()}}",
            customer_id: customerID
          },
          success: function(res) {
            if (res.status) {
              $('#shipping_address').removeClass('d-none');
              $('#billing_address').removeClass('d-none');
              $('#place_of_supply_div').removeClass('d-none');

              function safe(obj, key, subkey) {
                if (!obj) return '-';
                if (subkey && obj[key]) return obj[key][subkey] ?? '-';
                return obj[key] ?? '-';
              }

              function renderAddress(address, phone) {
                var html = '';
                html += '<p class="mb-0">' + safe(address, 'address1') + '</p>';
                html += '<p class="mb-0">' + safe(address, 'cityname', 'city_name') + ', ' + safe(address, 'districtname', 'district_name') + '</p>';
                html += '<p class="mb-0">' + safe(address, 'statename', 'state_name') + ' - ' + safe(address, 'pincodename', 'pincode') + '</p>';
                html += '<p class="mb-0">' + safe(address, 'countryname', 'country_name') + '</p>';
                html += '<p class="mb-3">Phone: +' + (phone ?? '-') + '</p>';
                return html;
              }

              function renderBillingExtras(gstin_no) {
                var gstTreatment = (gstin_no && gstin_no.trim() !== '') ? "Registered Business" : "Not Registered";
                var gstNumber = (gstin_no && gstin_no.trim() !== '') ? gstin_no : "-";

                var html = '';
                html += '<p class="mb-0">GST Treatment: ' + gstTreatment + '</p>';
                html += '<p class="mb-0">GSTIN: ' + gstNumber + '</p>';
                return html;
              }
              $('#place_of_supply').val(res.data.billing_address.state_id).trigger('change');
              var billingHtml = '<span class="address-title">Billing Address</span>';
              var shippingHtml = '<span class="address-title subh">Shipping Address</span>';
              if (res.data.same_address) {
                billingHtml += renderAddress(res.data.billing_address, res.data.phone) + renderBillingExtras(res.data.gstin_no);
                shippingHtml += renderAddress(res.data.billing_address, res.data.phone);
                $('#billing_address').html(billingHtml);
                $('#shipping_address').html(shippingHtml);
              } else {
                billingHtml += renderAddress(res.data.billing_address, res.data.phone) + renderBillingExtras(res.data.gstin_no);
                shippingHtml += renderAddress(res.data.shipping_address, res.data.phone);

                $('#billing_address').html(billingHtml);
                $('#shipping_address').html(shippingHtml);
              }
            }


          }
        })
      });

      $('#updateInvoiceNumber').on('click', function() {
        let prefix = $('#prefix').val().trim();
        let nextNumber = $('#next_number').val().trim();

        if (prefix === '' || nextNumber === '') {
          swal({
            toast: true,
            position: 'top-end',
            type: 'warning',
            title: 'Both Prefix and Next Number are required!',
            showConfirmButton: false,
            timer: 3000
          });
          return;
        }
        if (!isNaN(nextNumber)) {
          nextNumber = nextNumber.padStart(2, '0');
        }
        $('#invoice_no').val(prefix + '/' + nextNumber);
        $('#invoiceNoModal').modal('hide');
        swal({
          toast: true,
          position: 'top-end',
          type: 'success',
          title: 'Invoice number updated!',
          showConfirmButton: false,
          timer: 2000
        });
      });


    });

    function chnageDueDate(paymentTerm) {
      var days = parseInt(paymentTerm, 10);
      if (isNaN(days)) {
        $('#due_date').val('');
        return;
      }
      if ($('#invoice_date').val() == '') {
        var dueDate = new Date();
      } else {
        var dueDate = new Date($('#invoice_date').val());
      }
      dueDate.setDate(dueDate.getDate() + days);
      var formattedDate = dueDate.toISOString().split('T')[0];
      $('#due_date').val(formattedDate);
    }

    $(document).on("change", ".product_rowchange", function() {
      let $row = $(this).closest("tr");
      $row.find("input[name='product_dec[]']").removeClass("d-none");
      let productId = $(this).val();

      if (productId) {
        $.ajax({
          url: "{{ url('getProductInfo') }}", // your Laravel route
          type: "GET",
          data: {
            product_id: productId
          },
          success: function(res) {
           if(res.hsn_sac != null){
              $row.find("input[name='hsn_sac[]']").val(res.hsn_sac_no);
              $row.find("input[name='hsn_sac_type[]']").val(res.hsn_sac);
            }else{
              $row.find("input[name='hsn_sac[]']").val('');
              $row.find("input[name='hsn_sac_type[]']").val('');
            }
            $row.find("input[name='mrp[]']").val(res.mrp);
            var qty = $row.find("input[name='quantity[]']").val();
            var total = qty * res.mrp;
            $row.find("input[name='amount[]']").val(total.toFixed(2));
            calculateTaxes();
          },
          error: function() {
            alert("Something went wrong while fetching product info.");
          }
        });
      }
    });

    $(document).on('input', '.quantity_rowchange', function() {
      var $row = $(this).closest('tr');
      var mrp = parseFloat($row.find('input[name="mrp[]"]').val());
      var quantity = parseFloat($(this).val());
      var total = mrp * quantity;
      $row.find('input[name="amount[]"]').val(total.toFixed(2));

      var sub_total = 0;
      $('.amount').each(function() {
        sub_total += parseFloat($(this).val());
      });

      $('#sub_total').val(sub_total.toFixed(2));
      $('#discount').trigger('input');
    });
    $(document).on('input', '.mrp_rowchange', function() {
      var $row = $(this).closest('tr');
      var mrp = parseFloat($(this).val());
      var quantity = parseFloat($row.find('input[name="quantity[]"]').val());
      var total = mrp * quantity;
      $row.find('input[name="amount[]"]').val(total.toFixed(2));

      var sub_total = 0;
      $('.amount').each(function() {
        sub_total += parseFloat($(this).val());
      });

      $('#sub_total').val(sub_total.toFixed(2));
      $('#discount').trigger('input');
    });

    let currentTaxDropdown = null;

    $(document).on('change', '.tax_rowchange', function(e) {
      const selectedVal = $(this).val();
      if (selectedVal === '__add__') {
        $(this).val('');
        currentTaxDropdown = $(this); // save reference of current dropdown
        $('#taxModal').modal('show');
        return;
      } else {
        var row = $(this).closest('tr');
        var amount = parseFloat(row.find('input[name="amount[]"]').val());
        var tax = parseFloat($(this).find('option:selected').data('rate'));
        var tax_amount = (amount * tax) / 100;
        row.find('input[name="tax_amount[]"]').val(tax_amount.toFixed(2));
        calculateTaxes();
      }
    });
    $(document).on('change', '#tds', function(e) {
      const selectedVal = $(this).val();
      if (selectedVal === '__add__') {
        $(this).val('');
        $('#tdsModal').modal('show');
        return;
      } else {
        console.log('tds');
        calculateTaxes();
      }
    });
    $('#addTax').on('click', function(e) {
      e.preventDefault();

      var tax_name = $('input[name="tax_name"]').val();
      var tax_percentage = $('input[name="tax_percentage"]').val();

      if (!tax_name || !tax_percentage) {
        swal({
          toast: true,
          position: 'top-end',
          type: 'warning', // modern SweetAlert2 uses `icon`
          title: 'Both Tax Name and Tax Percentage are required!',
          showConfirmButton: false,
          timer: 3000
        });
        return;
      }

      $.ajax({
        url: '{{ route("add_tax") }}',
        method: 'POST',
        data: {
          tax_name: tax_name,
          tax_percentage: tax_percentage,
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.status) {
            $('#taxModal').modal('hide');

            if (currentTaxDropdown) {
              currentTaxDropdown
                .find('option:last')
                .after(`<option value="${response.data.id}" data-rate="${response.data.tax_percentage}" selected>
                        ${response.data.tax_percentage}%
                    </option>`);

              currentTaxDropdown.val(response.data.id).trigger('change');

              currentTaxDropdown = null; // reset reference
            }
          }
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          alert('Failed to add tax. Please try again.');
        }
      });
    });
    $('#addTds').on('click', function(e) {
      e.preventDefault();

      var tax_name = $('input[name="tax_name_tds"]').val();
      var rate = $('input[name="rate"]').val();
      var section = $('input[name="section"]').val();

      if (!tax_name || !rate) {
        swal({
          toast: true,
          position: 'top-end',
          type: 'warning', // modern SweetAlert2 uses `icon`
          title: 'Both Tax Name and Rate are required!',
          showConfirmButton: false,
          timer: 3000
        });
        return;
      }

      $.ajax({
        url: '{{ route("add_tds") }}',
        method: 'POST',
        data: {
          tax_name: tax_name,
          rate: rate,
          section: section
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.status) {
            $('#tdsModal').modal('hide');
            $('#tds').find('option:last').after(`<option value="${response.data.id}" data-rate="${response.data.rate}" selected>
                        ${response.data.tax_name} (${response.data.rate}%)
                    </option>`);
            $('#tds').val(response.data.id).trigger('change');
          }
        },
        error: function(xhr) {
          console.error(xhr.responseText);
          alert('Failed to add tds. Please try again.');
        }
      });
    });

    $('#discount').on('input', function() {
      calculateTaxes();
    });

    $('#discount_type').on('change', function() {
      calculateTaxes();
    });
    $('#place_of_supply').on('change', function() {
      calculateTaxes();
    });

    $('#adjustment').on('input', function() {
      var adjustment = parseFloat($(this).val()) || 0;
      $('#adjustment_amount').text(adjustment.toFixed(2));
      calculateTaxes();
    });

    function calculateTaxes() {
      let taxTotals = {};

      var discount_type = $('#discount_type').val();
      var discount = parseFloat($('#discount').val()) || 0;
      var sub_total = 0;

      // First calculate total without discount
      $('#item-table-body tr').each(function() {
        var rowAmount = parseFloat($(this).find('input[name="amount[]"]').val()) || 0;
        sub_total += rowAmount;
      });

      // Calculate discount amount
      var discount_amount = 0;
      let tds = parseFloat($('#tds option:selected').data('rate')) || 0;
      if (discount_type === 'percentage') {
        discount_amount = (sub_total * discount) / 100;
      } else if (discount_type === 'amount') {
        discount_amount = discount;
      }
      let tds_amount = 0;
      if (tds > 0) {
        tds_amount = ((sub_total - discount_amount) * tds) / 100;
        $('#tds_amount').val('-' + tds_amount.toFixed(2));
      } else {
        $('#tds_amount').val('0.00');
      }

      $('#discount_amount').val('-' + discount_amount.toFixed(2));

      var grand_total = 0;
      var $summary = $("#taxSummary");
      $summary.empty();
      let totalgst = 0;

      // Now distribute discount row-wise & calculate taxes
      $('#item-table-body tr').each(function() {
        let $row = $(this);
        let rowAmount = parseFloat($row.find('input[name="amount[]"]').val()) || 0;

        // Apply discount proportionally
        let rowDiscount = 0;
        if (discount_type === 'percentage') {
          rowDiscount = (rowAmount * discount) / 100;
        } else if (discount_type === 'amount') {
          rowDiscount = (rowAmount / sub_total) * discount_amount; // proportional share
        }

        let discountedRowAmount = rowAmount - rowDiscount;

        // Recalculate tax for this row
        let taxRate = parseFloat($row.find('.tax_rowchange option:selected').data('rate')) || 0;
        let taxAmount = (discountedRowAmount * taxRate) / 100;

        $row.find('input[name="tax_amount[]"]').val(taxAmount.toFixed(2));

        // Collect tax summary
        let taxName = $row.find('.tax_rowchange option:selected').text().trim();
        if (taxName && taxName !== "Select Tax" && taxName !== "➕ Add New") {
          if (!taxTotals[taxName]) {
            taxTotals[taxName] = 0;
          }
          taxTotals[taxName] += taxAmount;
        }
        // Add row total into grand total
        grand_total += discountedRowAmount + taxAmount;
      });

      // Update tax summary section
      $.each(taxTotals, function(tax, amount) {
        totalgst += amount;
        let placeOfSupply = $('#place_of_supply').val();

        if (placeOfSupply == "1") {
          // Try to extract % number from tax name (like "IGST 18%")
          let rateMatch = tax.match(/(\d+(\.\d+)?)%/);
          let rate = rateMatch ? parseFloat(rateMatch[1]) : null;
          let halfAmount = amount / 2;
          let halfRate = rate ? (rate / 2) : null;

          // Show CGST + SGST with halved amount and rate
          $summary.append(
            `<div class="d-flex justify-content-between">
        <span>CGST[${halfRate ? halfRate + '%' : ''}]</span>
        <span>${halfAmount.toFixed(2)}</span>
      </div>`
          );
          $summary.append(
            `<div class="d-flex justify-content-between">
        <span>SGST[${halfRate ? halfRate + '%' : ''}]</span>
        <span>${halfAmount.toFixed(2)}</span>
      </div>`
          );
        } else {
          // Keep old logic (show merged tax as-is)
          $summary.append(
            `<div class="d-flex justify-content-between">
        <span>IGST[${tax}]</span><span>${amount.toFixed(2)}</span>
      </div>`
          );
        }
      });

      $summary.removeClass('d-none');

      var adjustment = parseFloat($('#adjustment').val()) || 0;

      if (adjustment != 0) {
        grand_total += adjustment;
      }

      // Update totals
      $('#sub_total').val(sub_total.toFixed(2));
      $('#grand_total').val((grand_total - tds_amount).toFixed(2));
    }
  </script>
</x-app-layout>