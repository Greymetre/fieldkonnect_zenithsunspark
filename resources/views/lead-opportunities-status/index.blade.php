<x-app-layout>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    .pream_entry .btn {
      border-radius: 50px;
      margin-right: 12px;
      font-size: 13px;
      font-weight: 500;
      text-transform: capitalize;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .pream_entry .btn i.material-icons {
      height: auto;
    }

    .pream_entry a.exportbtn {
      background: #fff !important;
      color: #787575 !important;
      border: 1px solid #787575 !important;
      box-shadow: unset !important;
    }

    .pream_entry {
      margin-top: 20px;
    }

    .pream_entry a.exportbtn i.material-icons {
      color: #787575 !important;
    }

    .table {
      background-color: #fff !important;
      border: 1px solid #E8E8E8 !important;
      border-radius: 5px !important;
    }

    .table thead tr th {
      font-size: 14px;
      font-weight: 500 !important;
      color: #262A2A;
      letter-spacing: 0px;
      font-family: 'Poppins', sans-serif !important;
    }

    input.searchbox::placeholder {
      color: #6F6F6F;
      font-size: 13px;
      font-weight: 500;
      font-family: 'Poppins', sans-serif !important;
    }

    .table>tbody>tr>td a {
      font-size: 14px !important;
      font-weight: 400 !important;
      color: #262A2A !important;
      letter-spacing: 0px;
      text-transform: capitalize;
      font-family: 'Poppins', sans-serif !important;
    }

    .table>tbody>tr>td {
      font-size: 14px !important;
      font-weight: 400 !important;
      color: #262A2A !important;
      letter-spacing: 0px;
      text-transform: capitalize;
      border-color: #E8E8E8 !important;
      font-family: 'Poppins', sans-serif !important;
    }

    .table thead tr th:last-child {
      width: 170px !important;
    }

    table.dataTable thead .sorting:after {
      display: none;
    }

    table.dataTable thead .sorting:before {
      display: none;
    }

    table.dataTable thead .sorting_desc:before {
      display: none;
    }

    body .main-panel>.content {
      margin-top: 25px;
    }

    .badge {
      border-radius: 10px;
      text-transform: capitalize;
      font-size: 14px;
      font-weight: 400;
    }

    .table thead {
      background-color: #fff !important;
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
    }

    .table tbody tr {
      box-shadow: 0px 4px 4px 0px #DBDBDB40 !important;
    }

    .search_inner {
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border: 1px solid #E8E8E8;
      height: 42px;
      border-radius: 5px;
      padding: 4px 11px;
    }

    .search {
      width: 100%;
      max-width: 500px;
    }

    .search_inner button {
      border: 0px;
      outline: 0px;
      background: transparent;
    }

    .dataTables_length label {
      color: #6F6F6F !important;
      font-size: 14px;
      font-family: 'Poppins', sans-serif !important;
    }

    #getLeads_wrapper .bottom {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }

    nav.navbar.navbar-expand-lg.navbar-transparent.navbar-absolute.fixed-top {
      position: sticky;
    }

    select.custom-select {
      border-radius: 5px !important;
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border: 1px solid #E8E8E8;
      background: #fff;
    }

    .search_inner input.searchbox {
      border: 0px;
      outline: 0px;
      font-size: 13px;
      font-weight: 500;
      font-family: 'Poppins', sans-serif !important;
      width: 95%;
    }

    .pream_entry {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      align-items: center;
    }

    .well {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    div#getLeads_info {
      display: none;
    }

    button#dropdownMenuButton {
      color: #000;
    }

    .bmd-form-group {
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border-radius: 5px;
    }

    button.sort_btns {
      background: transparent !important;
      color: #495057;
      box-shadow: 0px 4px 4px 0px #DBDBDB40;
      border: 1px solid #E8E8E8;
      border-radius: 5px;
      padding: 11px 10px;
    }

    .dd {
      display: flex;
      flex-direction: row;
      justify-content: flex-start;
      align-items: center;
    }

    .sort_btns.filter_btn {
      background: #F2F2F4 !important;
      border: 1px solid #919191;
    }

    @media (max-width: 991px) {
      .pream_entry {
        flex-direction: column;
      }

      body .main-panel>.content {
        padding-top: 25px !important;
      }

      .well {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: flex-start;
        width: 100%;
      }

      .search_inner input.searchbox {
        width: auto !important;
      }
    }

    #del_btn {
      border: 1px solid #000;
      background: #f2f2f4 !important;
      color: #000 !important;
    }

    span.brig {
    font-size: 12px;
    color: #3777B5;
    font-weight: 600;
    background: #D7F4FF;
    border-radius: 5px;
    width: 40px;
    height: 27px;
    display: inline-flex;
    text-align: center;
    padding: 0px 9px;
}

    .btn-group.kim button {
        border-radius: 50px;
        padding: 5px 20px;
        text-transform: capitalize;
        background: linear-gradient(45deg, #3860a4 0%, #3694cc 100%) !important;
        color: #fff !important;
        border-color: transparent;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <!-- <div class="card-icon">
             <i class="material-icons">perm_identity</i> 
          </div> -->
          <h4 class="card-title ">Opportunities Status<br><!--<span class="brig">123</span><br>-->
            <span class="">
              <div class="pream_entry">

                <!-- <div class="search">
                  <div class="search_inner">
                    <button type="button"> <img src="https://expertfromindia.in/bediya/public/assets/img/search.svg"></button>
                    <input type="search" class="searchbox" id="search_lead" placeholder="Search Lead">
                  </div>
                </div> -->


                <div class="both_btn">
                  <button type="button" data-toggle="modal" data-target="#addLeadModel" class="btn btn-primary btn-sm btn-icon-split float-right" id="add_lead">
                    <span class="icon text-white-50">
                      <i class="material-icons">add_circle</i>
                    </span>
                    <span class="text">Add New Status</span>
                  </button>

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
          <!--  -->
          <div class="well">
            <div class="dd">
              {{--<div class="sort_btn">
                <div class="btn-group">
                  <button class="btn sort_btns  dropdown-toggle"
                    type="button"
                    id="dropdownMenuButton"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <img src="https://expertfromindia.in/bediya/public/assets/img/sort.png"> Sort
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#"> Range</a>
                    <a class="dropdown-item" href="#"> limit</a>

                  </div>
                </div>
              </div>
              <div class="date_sarch">
                {!! Form::open(['method' => 'POST', 'class' => 'form-inline', 'id' => 'frmFilter']) !!}

                <div class="form-group mr-sm-2 col-md-12 pl-2 p-0">
                  {!! Form::text('datetime', old('datetime'), ['class' => 'form-control','placeholder'=> __('MM/DD/YYYY - MM/DD/YYYY'), 'autocomplete' => 'off', 'style' => 'width : 100%;']) !!}
                </div>

                <!--   <button type="submit" class="btn btn-responsive btn-primary mr-sm-2 mb-2">{{ __('Filter') }}</button>
                <a href="javascript:;" onclick="resetFilter();" class="btn btn-responsive btn-danger mb-2">{{ __('Reset') }}</a> -->
                {!! Form::close() !!}
              </div>
            </div>--}}

            <div class="sort_btn d-flex">
              <div class="ass_del d-none">
                <div class="btn-group" style="width: 240px;">
                  <select name="user_id" id="user_id" class="form-control">
                    <option value="">Assign Lead</option>
                    @if(@isset($users ))
                    @foreach($users as $user)
                    <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
                <button type="button" class="btn mr-3 ml-3" id="del_btn"><i class="material-icons icon">delete</i>Delete</button>
              </div>
              {{--<div class="btn-group">
                <button class="btn sort_btns filter_btn  dropdown-toggle"
                  type="button"
                  id="dropdownMenuButton"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false">
                  <img src="https://expertfromindia.in/bediya/public/assets/img/filter_ss.png"> Filter
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" href="#"> id</a>
                  <a class="dropdown-item" href="#">range </a>
                  <a class="dropdown-item" href="#">class </a>
                </div>
              </div>--}}
            </div>
          </div>
          <!--  -->
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getStatus" class="table">
              <thead class=" text-primary">
                <tr>
                  <th>Action</th>
                  <th>Status</th>
                  <th>Ordering</th>
                  <th>Created By</th>
                  <th>Created Date</th>
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
  <!--  -->
  <div class="modal fade" id="addLeadModel" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <form method="POST"
        action="{{ route('lead-opportunities-status.store') }}" class="form-horizontal" id="frmLeadsCreate" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">New Status</h4>
          </div>


          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 pr-1 pl-1">
                <div class="col-md-12 form-group">
                  <label for="status_name">Status Name <span style="color:red">*</span></label>
                  <input type="text" name="status_name" id="status_name" value="{{ old('status_name','') }}" class="form-control" placeholder="Status Name" required>
                  @if($errors->has('status_name'))
                  <p class="help-block">
                    <strong>{{ $errors->first('status_name') }}</strong>
                  </p>
                  @endif
                </div>
              </div>
              <div class="col-md-6 pr-1 pl-1">
                <div class="col-md-12 form-group">
                  <label for="ordering">Ordering <span style="color:red">*</span></label>
                  <input type="number" name="ordering" id="ordering" value="{{ old('ordering','') }}" class="form-control" placeholder="Ordering" required>
                  @if($errors->has('ordering'))
                  <p class="help-block">
                    <strong>{{ $errors->first('ordering') }}</strong>
                  </p>
                  @endif
                </div>
              </div>
              <div class="col-md-12" id="lead_exist_data">
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <input type="hidden" name="lead_id" id="id">
            <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-default" id="model_submit_btn">Create Status</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!--  -->
  <link href="{{ url('/').'/'.asset('vendor/bootstrap-daterange/daterangepicker.css') }}" rel="stylesheet">
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <!-- Load jQuery and moment.js -->
  <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getStatus').DataTable({
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
          url: "{{ route('lead-opportunities-status.index') }}",
          data: function(d) {
            d.executive_id = $('#executive_id').val(),
              d.start_date = $('#start_date').val()
          }
        },
        columns: [
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'status_name',
            name: 'status_name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'ordering',
            name: 'ordering',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'createbyname.name',
            name: 'createbyname.name',
            "defaultContent": '',
            orderable: false,
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": '',
            orderable: false,
          },

        ],
        dom: 't<"bottom"lip>',
      });

      $('#end_date').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        var selectedStartDate = $('#start_date').datepicker('getDate');
        $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
      });

      $(document).on('click', '.editStatus', function() {
        var id = $(this).attr('data-id');
        var url = "{{ route('lead-opportunities-status.edit', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
          url: url,
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            $('.modal-title').html('Edit Status');
            $('#model_submit_btn').html('Update');

            $('#id').val(data.id);
            $('#status_name').val(data.status_name);
            $('#ordering').val(data.ordering);
            $('#addLeadModel').modal('show');
          }
        });
      });

      $(document).on('click', '#add_lead', function() {
        $('#frmLeadsCreate')
          .find('input[type="text"], input[type="number"], input[type="hidden"]')
          .not('[name="_token"]')
          .val('');
        $('.modal-title').html('Add Status');
        $('#model_submit_btn').html('Create');
      });
    });

    $(document).on('click', '.deleteStatus', function() {
      var id = $(this).attr('data-id');
      var url = "{{ route('lead-opportunities-status.destroy', ':id') }}";
      url = url.replace(':id', id);
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.value) {
          $.ajax({
            url: url,
            type: 'DELETE',
            dataType: 'json',
            success: function(data) {
              $('#getStatus').DataTable().ajax.reload(null, false);
            }
          });
        }
        else{
          return false;
        }
      })
      });
  </script>
</x-app-layout>