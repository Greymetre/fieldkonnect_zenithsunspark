<x-app-layout>
  <style>

    #copyText {
      cursor: pointer;
      font-weight: 800;
      color: #000;
      text-shadow: 0 0 3px #fff;
    }
   .table-input {
        width: 100% !important;
        padding: 5px !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        font-size: 14px !important;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        outline: none !important;
        background: white !important;
        width: 150px !important;
    }

    .select2-container--default .select2-selection {
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        background: white !important;
        min-height: 35px !important;
        width: 150px !important;
    }

   .select2-container--default .select2-selection--multiple {
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        background: white !important;
        min-height: 35px !important;
        width: 150px !important;
    }

    .select2-container .select2-selection--multiple .select2-selection__choice {
        background-color: #f0f0f0 !important;
        border: 1px solid #ccc !important;
        color: black !important;
        padding: 3px 5px !important;
        border-radius: 4px !important;
        position: relative;
    }

    /* Make the remove (X) button red */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: red !important;  /* Change the X button color */
        font-weight: bold;
        margin-right: 5px;
    }

    /* Optional: Change color when hovering over the X button */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: darkred !important;
    }



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
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.complaint.title_singular') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
              @if(auth()->user()->can(['complaint_download']))
                <form method="POST" action="{{ URL::to('complaint_download') }}" class="form-horizontal">
                  @csrf
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
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" id="button_download" type="button" title="{!!  trans('panel.global.download') !!} {!! trans('panel.complaint.title_singular') !!}"><i class="material-icons">cloud_download</i></button></div>
                     <div class="p-2">@if(auth()->user()->can(['complaint_create']))
                        <a href="{{ route('complaints.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.complaint.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                        @endif
                      </div>
                  </div>
                </form>
                @endif
              </div>
            </span>
          </h4>
        </div>
        <div class="card-body">
          <div class="col-md-12 p-3">
            <div class="row">
              <div class="col-sm">
                <div class="card text-center m-1 hover-effect">
                  <div class="card-body " onclick="change_complaints_type('')">
                    <h4 class="card-text">All</h4>
                    <h5 class="card-title" id="all_complaints"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1 hover-effect">
                  <div class="card-body " onclick="change_complaints_type(1)">
                    <h4 class="card-text">Pending</h4>
                    <h5 class="card-title" id="complaints_pending"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1 hover-effect">
                  <div class="card-body " onclick="change_complaints_type(0)">
                    <h4 class="card-text text-center">Open</h4>
                    <h5 class="card-title" id="complaints_in_process"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1 hover-effect">
                  <div class="card-body hover-effect" onclick="change_complaints_type(2)">
                    <h4 class="card-text text-center">Work Done</h4>
                    <h5 class="card-title" id="complaints_work_done"></h5>
                  </div>
                </div>
              </div>
               <div class="col-sm">
                <div class="card text-center m-1 hover-effect">
                  <div class="card-body " onclick="change_complaints_type(3)">
                    <h4 class="card-text text-center">Complete </h4>
                    <h5 class="card-title" id="complaints_complete"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1 hover-effect">
                  <div class="card-body" onclick="change_complaints_type(4)">
                    <h4 class="card-text text-center">Closed </h4>
                    <h5 class="card-title" id="complaints_closed"></h5>
                  </div>
                </div>
              </div>
              <div class="col-sm">
                <div class="card text-center m-1 hover-effect">
                  <div class="card-body " onclick="change_complaints_type(5)">
                    <h4 class="card-text text-center">Cancelled </h4>
                    <h5 class="card-title" id="complaints_cancelled"></h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getComplaints" class="table table-striped- table-bschemeed table-hover table-checkable  no-wrap">
              <thead class=" text-primary">
               <tr>
                    <th>
                        <!-- <button type="button" class="btn btn-secondary" id="resetFilters">Reset</button> -->
                    </th>
                    <th><input type="text" class="form-control table-input " placeholder="Date..." name="complaint_date" id="complaint_date" autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="complaint_number" placeholder="Search..." autocomplete="off"></th>
                    <!-- <th><input type="text" class="form-control table-input" name="service_center_name" placeholder="Search..." autocomplete="off"></th> -->
                     <th>
                        <select class="form-control table-input select2" name="service_center_name[]" id="service_center_name" multiple>
                            @if(count($service_centers) > 0)
                               @foreach($service_centers as $service_center)
                                     <option value="{{$service_center->id}}">{{$service_center->name}}</option>
                               @endforeach
                            @endif
                        </select>
                    </th>
                    <th>
                        <select class="form-control table-input select2" name="assign_user[]" id="assign_user" multiple>
                            @if(count($assign_users) > 0)
                               @foreach($assign_users as $assign_user)
                                     <option value="{{$assign_user->id}}">{{$assign_user->name}}</option>
                               @endforeach
                            @endif
                        </select>
                    </th>
                    <th><input type="text" class="form-control table-input" name="service_center_code" placeholder="Search..." autocomplete="off"></th>
                    <th>
                        <select class="form-control table-input" name="status" id="status">
                            <option value="">Select</option>
                            <option value="0">Open</option>
                            <option value="1">Pending</option>
                            <option value="2">Work Done</option>
                            <option value="3">Completed</option>
                            <option value="4">Closed</option>
                            <option value="5">Canceled</option>
                        </select>
                    </th>
                    <th>
                      <select class="form-control table-input select2" name="category_name_1" id="category_name_1">
                          <option value="">Select Type</option>
                          @if(count($categories) > 0)
                             @foreach($categories as $categorie)
                                   <option value="{{$categorie->category_name}}">{{$categorie->category_name}}</option>
                             @endforeach
                          @endif
                      </select>
                    </th>
                    <th><input type="text" class="form-control table-input" name="seller"  placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_name" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_email" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_number" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_address" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_place" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="pincode" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_country" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_state" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_city" placeholder="Search..." autocomplete="off"></th>
                     <th>
                        <select class="form-control table-input select2" name="customer_complaint_type" id="customer_complaint_type">
                            <option value="">Select Type</option>
                            @if(count($complaint_types) > 0)
                               @foreach($complaint_types as $complaint_type)
                                     <option value="{{$complaint_type->name}}">{{$complaint_type->name}}</option>
                               @endforeach
                            @endif
                        </select>
                    </th>
                    <!-- <th><input type="text" class="form-control table-input" name="customer_complaint_type" placeholder="Search..." autocomplete="off"></th> -->
                    <th><input type="text" class="form-control table-input" name="category_name" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="product_name" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="product_code" placeholder="Search..." autocomplete="off"></th>
                  
                    <th><input type="text" class="form-control table-input" name="product_serail_number" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="specification" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="product_no" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="phase" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input datepicker" placeholder="Date..." name="customer_bill_date" id="customer_bill_date" autocomplete="off"></th>
                     <th>
                        <select class="form-control table-input" name="service_type">
                            <option value="">Select</option>
                            <option value="Paid">Paid</option>
                            <option value="Free">Free</option>
                        </select>
                    </th>
                    <!-- <th><input type="text" class="form-control table-input" name="service_type" placeholder="Search..." autocomplete="off"></th> -->
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><input type="text" class="form-control table-input datepicker" placeholder="Date..." name="last_update_date" id="last_update_date" autocomplete="off"></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th>
                        <select class="form-control table-input" name="service_status">
                            <option value="">Select</option>
                            <option value="0">Draft</option>
                            <option value="1">Claimed</option>
                            <option value="2">Customer Payable</option>
                            <option value="3">Approved</option>
                            <option value="4">Cancelled</option>
                        </select>
                    </th>
                    <th><div style="width:150px"></div></th>
                    <th><input type="text" class="form-control table-input" name="description" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="service_branch" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="purchased_party_name" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="warranty_bill" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="customer_bill_no" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input datepicker" placeholder="Date..." name="customer_bill_date_1" id="customer_bill_date_1" autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input" name="under_warranty" placeholder="Search..." autocomplete="off"></th>
                     <!-- <th><input type="text" class="form-control table-input" name="service_type_1" placeholder="Search..." autocomplete="off"></th> -->
                    <th><input type="text" class="form-control table-input" name="company_sale_bill_no" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input datepicker" placeholder="Date..." name="company_sale_bill_date" id="company_sale_bill_date" autocomplete="off"></th>
                    <th><div style="width:150px"></div></th>
                    <th><input type="text" class="form-control table-input" name="register_by" placeholder="Search..." autocomplete="off"></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><div style="width:150px"></div></th>
                    <th><input type="text" name="createdbyname_name" class="form-control table-input" placeholder="Search..." autocomplete="off"></th>
                    <th><input type="text" class="form-control table-input datepicker" placeholder="Date..." name="created_at" id="created_at" autocomplete="off"></th>
                    
                </tr>
                <tr>
                  <th>{!! trans('panel.global.no') !!}</th>
                  <!-- <th>{!! trans('panel.global.action') !!}</th> -->
                  <th>Date</th>
                  <th>Complaint Number</th>
                  <th>Service Center</th>
                  <th>Service Eng</th>
                  <th>Service Center Code</th>
                  <th>Complaint Status</th>
                  <th>Division</th> 
                  <th>Seller Name</th>
                  <th>Customer Name</th>
                  <th>Customer Email</th>
                  <th>Contact No</th>
                  <th>Address</th>
                  <th>Place</th>
                  <th>Pincode</th>
                  <th>Country</th>
                  <th>State</th>
                  <th>City</th>
                  <th>Customer Complaint Type</th>      
                  <th>Division</th> 
                  <th>Product Name</th>
                  <th>Product Code</th> 
                         
                  <th>Product Serial No</th>  
                  <th>HP</th> 
                  <th>Stage</th>
                  <th>Phase</th>
                  <th>Warranty Customer Bill Date</th>
                  <th>Service Paid/Free</th>
                  <th>Work Done Time</th>
                  <th>Action Done By ASC</th>
                  <th>Service Center Remark</th>
                  <!-- <th>Last Status</th> -->
                  <th>Last Status Update Time</th>
                  <th>Pending TAT</th>
                  <th>Open TAT</th>
                  <th>Cancelled TAT</th>
                  <th>Work Done TAT</th>
                  <th>Completed TAT</th>
                  <th>Close TAT</th>
                  <th>Serive Bill status</th>
                  <th>Service Bill Approved Date</th>
                  <th>Description</th>
                  <th>Service Branch</th>
                  <th>Purchased Party Name</th>
                  <th>Warrenty Bill</th>
                  <th>Customer Bill No </th>
                  <th>Customer Bill Date</th>
                  <th>Under Warranty</th>
                  <!-- <th>Service Type</th> -->
                  <th>Company Sale Bill No.</th>
                  <th>Company Sale Bill Date</th>
                  <th>Service Centre Remarks</th>
                  <th>Complaint Register By</th>
                  <!-- <th>Division Name</th> -->
                  <th>Work Completed Duration</th>
                  <th>Open Duration</th>
                  <th>Closed Date</th>
                  <!-- <th>Complaint Feedback Type</th>
                  <th>Feedback</th> -->
                  <th>Created By</th>
                  <th>Created At</th>

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
  <!-- Load jQuery and moment.js -->
  <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

  <!-- Load Daterangepicker -->
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  <!-- Load jQuery UI (AFTER daterangepicker) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css" />

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getComplaints').DataTable({
          processing: true,
          serverSide: true,
          order: [[0, 'desc']],
          ajax: {
              url: "{{ route('getComplaints') }}",
              type: "POST",  // Change GET to POST
              data: function (d) {
                d._token = "{{ csrf_token() }}"; // CSRF Token
                d.complaint_date = $('input[name="complaint_date"]').val();
                d.complaint_number = $('input[name="complaint_number"]').val();
                d.service_center_name = $('select[name="service_center_name[]"]').val() || [];
                d.assign_user = $('select[name="assign_user[]"]').val() || [];
                d.service_center_code = $('input[name="service_center_code"]').val();
                d.seller = $('input[name="seller"]').val();
                d.customer_name = $('input[name="customer_name"]').val();
                d.customer_email = $('input[name="customer_email"]').val();
                d.customer_number = $('input[name="customer_number"]').val();
                d.customer_address = $('input[name="customer_address"]').val();
                d.customer_place = $('input[name="customer_place"]').val();
                d.customer_country = $('input[name="customer_country"]').val();
                d.customer_state = $('input[name="customer_state"]').val();
                d.customer_city = $('input[name="customer_city"]').val();
                d.pincode       = $('input[name="pincode"]').val();
                d.customer_complaint_type = $('select[name="customer_complaint_type"]').val();
                d.category_name = $('input[name="category_name"]').val();
                d.product_name = $('input[name="product_name"]').val();
                d.product_code = $('input[name="product_code"]').val();
                d.product_serail_number = $('input[name="product_serail_number"]').val();
                d.specification = $('input[name="specification"]').val();
                d.product_no = $('input[name="product_no"]').val();
                d.phase = $('input[name="phase"]').val();
                d.category_name_1 = $('select[name="category_name_1"]').val();
                d.customer_bill_date = $('input[name="customer_bill_date"]').val();
                d.service_type = $('select[name="service_type"]').val();
                d.last_update_date = $('input[name="last_update_date"]').val();
                d.service_status = $('select[name="service_status"]').val();
                d.description    = $('input[name="description"]').val();
                d.service_branch = $('input[name="service_branch"]').val();
                d.purchased_party_name = $('input[name="purchased_party_name"]').val();
                d.warranty_bill = $('input[name="warranty_bill"]').val();
                d.customer_bill_no = $('input[name="customer_bill_no"]').val();
                d.customer_bill_date_1 = $('input[name="customer_bill_date_1"]').val();
                d.under_warranty = $('input[name="under_warranty"]').val();
                d.service_type_1 = $('input[name="service_type_1"]').val();
                d.company_sale_bill_no = $('input[name="company_sale_bill_no"]').val();
                d.company_sale_bill_date = $('input[name="company_sale_bill_date"]').val();
                d.register_by = $('input[name="register_by"]').val();
                d.createdbyname_name = $('input[name="createdbyname_name"]').val();
                d.created_at = $('input[name="created_at"]').val();
                d.status = $('select[name="status"]').val();
            }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'complaint_date', name: 'complaint_date', orderable: false, defaultContent: ''},
              {data: 'complaint_number', name: 'complaint_number', orderable: false, defaultContent: ''},
              {data: 'service_center_details.name', name: 'service_center_details.name', orderable: false, defaultContent: '', searchable: false},
              {data: 'assign_users.name', name: 'assign_users.name', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_center_details.customer_code', name: 'service_center_details.customer_code', orderable: false, defaultContent: '', searchable: false},
              {data: 'status', name: 'status', orderable: false, defaultContent: ''},
              {data: 'product_details.categories.category_name', name: 'product_details.categories.category_name', orderable: false, defaultContent: '', searchable: false},  
              {data: 'seller', name: 'seller', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_name', name: 'customer.customer_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_email', name: 'customer.customer_email', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_number', name: 'customer.customer_number', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_address', name: 'customer.customer_address', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_place', name: 'customer.customer_place', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer_pindcode', name: 'customer_pindcode', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_country', name: 'customer.customer_country', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_state', name: 'customer.customer_state', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_city', name: 'customer.customer_city', orderable: false, defaultContent: '', searchable: false},
              {data: 'complaint_type_details.name', name: 'complaint_type_details.name', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.categories.category_name', name: 'product_details.categories.category_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.product_name', name: 'product_details.product_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.product_code', name: 'product_details.product_code', orderable: false, defaultContent: '', searchable: false},
              
              {data: 'product_serail_number', name: 'product_serail_number', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.specification', name: 'product_details.specification', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.product_no', name: 'product_details.product_no', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.phase', name: 'product_details.phase', orderable: false, defaultContent: '', searchable: false},
              {data: 'warranty_details.warranty_date', name: 'warranty_details.warranty_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_type', name: 'service_type', orderable: false, defaultContent: '', searchable: false},
              {data: 'work_done_time', name: 'work_done_time', orderable: false, defaultContent: '', searchable: false},
              {data: 'complaint_work_dones', name: 'complaint_work_dones', orderable: false, defaultContent: '', searchable: false},
              {data: 'complaint_work_remark', name: 'complaint_work_remark', orderable: false, defaultContent: '', searchable: false},
              {data: 'updated_at', name: 'updated_at', orderable: false, defaultContent: '', searchable: false},
              {data: 'pending_tat', name: 'pending_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'open_tat', name: 'open_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'canceled_tat', name: 'canceled_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'work_done_tat', name: 'work_done_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'compleated_tat', name: 'compleated_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'close_tat', name: 'close_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_bill_status', name: 'service_bill_status', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_bill_date', name: 'service_bill_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'description', name: 'description', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_branch', name: 'service_branch', orderable: false, defaultContent: '', searchable: false},
              {
                  data: null,
                  orderable: false,
                  render: function(data, type, full, meta) {
                      return data.customer.customer_name + ' (' + data.customer.customer_number + ')';
                  }
              },
              {data: 'warranty_bill', name: 'warranty_bill', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer_bill_no', name: 'customer_bill_no', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer_bill_date', name: 'customer_bill_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'under_warranty', name: 'under_warranty', orderable: false, defaultContent: '', searchable: false},
              // {data: 'service_type', name: 'service_type', orderable: false, defaultContent: '', searchable: false},
              {data: 'company_sale_bill_no', name: 'company_sale_bill_no', orderable: false, defaultContent: '', searchable: false},
              {data: 'company_sale_bill_date', name: 'company_sale_bill_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_center_remark', name: 'service_center_remark', orderable: false, defaultContent: '', searchable: false},
              {data: 'register_by', name: 'register_by', orderable: false, defaultContent: '', searchable: false},
              // {data: 'product_details.categories.category_name', name: 'product_details.categories.category_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'work_complated_duration', name: 'work_complated_duration', orderable: false, defaultContent: '', searchable: false},
              {data: 'open_duration', name: 'open_duration', orderable: false, defaultContent: '', searchable: false},
              {data: 'closed_date', name: 'closed_date', orderable: false, defaultContent: '', searchable: false},
              // {data: 'complaint_feedback', name: 'complaint_feedback', orderable: false, defaultContent: '', searchable: false},
              // {data: 'feedback', name: 'feedback', orderable: false, defaultContent: '', searchable: false},
              {data: 'createdbyname.name', name: 'createdbyname.name', orderable: false, defaultContent: '', searchable: false},
              {data: 'created_at', name: 'created_at', orderable: false, defaultContent: '', searchable: false},

          ]
      });

      $(document).ready(function () {
          // Initialize datepickers


          $('.datepicker').datepicker({
              maxDate: 0,
              dateFormat: 'dd-mm-yy',
              onClose: function () {
                  $(this).blur(); // Ensure input loses focus when closed
              }
          });

          // Close datepicker or blur input/select on key press (except Tab)
          $(document).on('keydown', function (e) {
              if (e.key !== 'Tab') {
                  $('.datepicker').datepicker('hide');
                  // $('input.table-input, select.table-input').blur();
              }
          });

           $('#service_center_name').select2({
              placeholder: "Select Service Centers",
              allowClear: true,
              width: '150px' // Set width for better alignment
          });

           $('#assign_user').select2({
              placeholder: "Service Eng",
              allowClear: true,
              width: '150px' // Set width for better alignment
          });

           $('#category_name_1').select2({
              placeholder: "Select Division",
              allowClear: true,
              width: '150px' // Set width for better alignment
          });

          // Close datepicker or blur input/select on mouse wheel scroll
          $(document).on('wheel', function () {
              $('.datepicker').datepicker('hide');
              // $('input.table-input, select.table-input').blur();
          });

          // Close datepicker or blur input/select on mouse click outside any input/select
          $(document).on('click', function (event) {
              if (!$(event.target).closest('input.table-input, select.table-input').length) {
                  $('.datepicker').datepicker('hide');
                  $('select.table-input').blur();
              }
          });

          // Close datepicker or blur input/select on arrow key movement
          $(document).on('keydown', function (e) {
              if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                  $('.datepicker').datepicker('hide');
                  // $('input.table-input, select.table-input').blur();
              }
          });
      });


      $('.table-input').on('keyup change', function () {
        table.draw();
        getCountsOfComplaints();
      });

      // $('#complaint_date').datepicker({
      //     maxDate: 0,
      //     dateFormat: 'dd-mm-yy',
      //  });

      $('#complaint_date').daterangepicker({
          autoUpdateInput: false
      });

      // Update input field when a date is selected
      $('#complaint_date').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
          table.draw();
          getCountsOfComplaints();
      });

      // Clear input when canceled
      $('#complaint_date').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
          table.draw();
          getCountsOfComplaints();
      });


      $('#customer_bill_date').datepicker({
          dateFormat: 'dd-mm-yy',
      });

      $('#last_update_date').datepicker({
          maxDate: 0,
          dateFormat: 'dd-mm-yy',
      });

      $('#customer_bill_date_1').datepicker({
          dateFormat: 'dd-mm-yy',
      });

      $('#company_sale_bill_date').datepicker({
          dateFormat: 'dd-mm-yy',
      });

      $('#created_at').datepicker({
          dateFormat: 'dd-mm-yy',
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
      $('#button_download').on('click', function() {
          let form = $('<form>', {
              method: 'POST',
              action: "{{ URL::to('complaint_download') }}"
          });

          form.append($('<input>', {type: 'hidden', name: '_token', value: $('input[name="_token"]').val()}));

          // Collect all filter inputs (both text and select fields)
          $('.table-input, .table-select').each(function() {
              let inputName = $(this).attr('name');
              let inputValue = $(this).val();
              form.append($('<input>', {type: 'hidden', name: inputName, value: inputValue}));
          });
          form.append($('<input>', {type: 'hidden', name: 'start_date', value: $('#start_date').val()}));
          form.append($('<input>', {type: 'hidden', name: 'end_date', value: $('#end_date').val()}));

          $('body').append(form);
          form.submit();
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
          title: "Complaint number copied to clipboard: " + textToCopy
        });
      });
    });


    $(document).ready(function(){
      getCountsOfComplaints();
    })

    function change_complaints_type(type){
        $('#status').val(type).trigger('change');
    }

    function getCountsOfComplaints() {
      let formData = {
          _token: "{{ csrf_token() }}",
          start_date: $('#start_date').val(),
          end_date: $('#end_date').val(),
      };

      // Collect all filter inputs (both text and select fields)
      $('.table-input, .table-select').each(function() {
          let inputName = $(this).attr('name');
          let inputValue = $(this).val();
          formData[inputName] = inputValue;
      });
       $.ajax({
         url: "{{ url('getCountsOfComplaints') }}",
         dataType: "json",
         type: "POST",
         data: formData,
         success: function(res) {
           $('#all_complaints').html(res.all_complaints);
           $('#complaints_pending').html(res.complaints_pending);
           $('#complaints_work_done').html(res.complaints_work_done);
           $('#complaints_cancelled').html(res.complaints_cancelled);
           $('#complaints_in_process').html(res.complaints_in_process);
           $('#complaints_complete').html(res.complaints_complete);
           $('#complaints_closed').html(res.complaints_closed);
         }
       });
    }
  </script>
</x-app-layout>