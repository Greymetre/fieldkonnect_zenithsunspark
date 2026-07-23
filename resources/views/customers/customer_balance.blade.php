<x-app-layout>
<style>
  .table tbody tr td img{
    width: 120px;
    height: 120px;
  }
</style>
<div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Upload Balance Confirmation
            <span class="pull-right">
              <div class="btn-group">
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
          @if(auth()->user()->hasRole('Customer Dealer'))
          {!! Form::open([
          'route' => 'customer_balance.update',
          'method' => 'POST',
          'id' => 'storeCustomerData',
          'files' => true
          ]) !!}

          <input type="hidden" name="id" id="customer_id" value="">
          <div class="first-box">
            <div class="row">
              <div class="col-md-3 ml-auto mr-auto">
                <div class="fileinput fileinput-new" data-provides="fileinput">

                  <div class="fileinput-new thumbnail">
                    <img src="{!! !empty($customers['profile_image']) ? $customers['profile_image'] : url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview7">
                    <div class="selectThumbnail">
                      <span class="btn btn-just-icon btn-round btn-file">
                        <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                        <span class="fileinput-exists">Change</span>
                        <input type="file" name="image" class="getimage7" accept="image/*">
                      </span>
                      <br>
                      <a href="#pablo" class="btn btn-danger btn-round fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times"></i> Remove</a>
                    </div>
                  </div>
                  <div class="fileinput-preview fileinput-exists thumbnail img-circle"></div>
                  <label class="bmd-label-floating">Balance Confirmation(Upload Here)</label>
                  @if ($errors->has('image'))
                  <div class="error col-lg-12">
                    <p class="text-danger">{{ $errors->first('image') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="card-footer pull-right">
              {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
            </div>
            {{ Form::close() }}
            @else
            <h4 class="text-dark text-center mt-4">Upload balance comfirmation is for only Dealers.</h4>
            @endif
            @if(auth()->user()->hasRole('superadmin'))
            <div class="table-responsive">
              <table id="getcustomersbalance" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
                <thead class=" text-primary">
                  <th>S. No.</th>
                  <th>Customer Name</th>
                  <th>Uploaded Balance Confirmation</th>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>

  <script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getcustomersbalance').DataTable({
        processing: true,
        serverSide: true,
        columnDefs: [ {
            className: 'control',
            orderable: false,
            targets:   -1
        } ],
        "order": [ [0, 'desc'] ],
        "retrieve": true,
        ajax: {
          url: "{{ route('customer_balance.list') }}",
          data: function (d) {
                d.executive_id = $('#executive_id').val(),
                d.start_date = $('#start_date').val()
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'customer.name', name: 'customer.name', orderable: false},
            { data: 'upload_iamge', name: 'upload_iamge',"defaultContent": '', orderable: false, searchable: false},           
        ]
    });

    $('#end_date').change(function(){
        table.draw();
    });
    $('#start_date').change(function(){
      var selectedStartDate = $('#start_date').datepicker('getDate');
      $('#end_date').datepicker("option", "minDate", selectedStartDate);
        table.draw();
    });
    });
</script>

</x-app-layout>