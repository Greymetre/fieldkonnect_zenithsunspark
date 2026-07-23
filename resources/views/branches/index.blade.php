<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.branch.title_singular') !!} {!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn"> 
                  <div class="next-btn">
                
                  @if(auth()->user())
                  <a data-toggle="modal" data-target="#createbranch" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.branch.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
                  <form method="post" action="{{ URL::to('branch_report/download') }}" class="form-horizontal">
                      @csrf
                      @if(auth()->user()->can(['branch_report_download']))
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Branch Report" name="export_branch_report" value="true"><i class="material-icons">cloud_download</i></button>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
          
        <div class="table-responsive">
          <table id="getbranch" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
               <th>{!! trans('panel.global.active') !!}</th>
              <th>{!! trans('panel.branch.fields.branch_name') !!}</th>
              <th>{!! trans('panel.branch.fields.branch_code') !!}</th>
              <th>{!! trans('panel.global.created_by') !!}</th>
              <th>{!! trans('panel.global.created_at') !!}</th>
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
<div class="modal fade bd-example-modal-lg" id="createbranch" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.branch.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route' => 'branches.store','id' => 'createBranchForm','files'=>true ]) !!}
        <div class="row">
          <div class="col-md-12">
            <div class="input_sectuin">
              <label class="col-form-label">{!! trans('panel.branch.fields.branch_name') !!} <span class="text-danger"> *</span></label>           
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="branch_name" class="form-control" id="branch_name" value="{!! old( 'branch_name') !!}" maxlength="200" required>
                  @if ($errors->has('branch_name'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('branch_name') }}</p></div>
                  @endif
              </div>
            </div>
            </div>
            <div class="col-md-12">
                <div class="input_sectuin">
                  <label class="col-form-label">{!! trans('panel.branch.fields.branch_code') !!} <span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" id="branch_code" name="branch_code" class="form-control" value="{!! old( 'branch_code') !!}" maxlength="200" required>
                      @if ($errors->has('branch_code'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('branch_code') }}</p></div>
                      @endif
                  </div>
                </div>
              </div>
            <div class="col-md-12">
                <div class="input_sectuin">
                  <label class="col-form-label">Ware House<span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <select name="warehouse_id" class="form-control" id="warehouse_id" required>
                        <option value="">Select Ware House</option>
                        @if($warehouses->count() > 0)
                        @foreach($warehouses as $warehouse)
                          <option value="{!! $warehouse->id !!}">{!! $warehouse->warehouse_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                      @if ($errors->has('warehouse_id'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('warehouse_id') }}</p></div>
                      @endif
                  </div>
                </div>
              </div>

        </div>
        <div class="clearfix"></div>
        <div class="pull-right">
          <input type="hidden" name="id" id="branch_id" />
          {{ Form::submit('Submit', array('class' => 'btn btn-info save mt-4')) }}
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
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getbranch').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('branches.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false},
            {data: 'active', name: 'active',"defaultContent": '', orderable: false, searchable: false},
            {data: 'branch_name', name: 'branch_name',"defaultContent": ''},
            {data: 'branch_code', name: 'branch_code',"defaultContent": ''},
            {data: 'getuser.name', name: 'getuser.name',"defaultContent": ''},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
    
      var branchId = $(this).data('branch-id');
      // Make AJAX request to update data
      $.ajax({
         url: "{{ url('branches') }}/" + branchId + '/edit',
         type: 'GET',
         dataType: 'json',
         data: {
            // Include any data you want to update here
            // For example: name: 'New Branch Name'
         },
         success: function (data) {
               $('#branch_name').val(data.branch_name);
               $('#branch_code').val(data.branch_code);
               $('#warehouse_id').val(data.warehouse_id);
	        $('#branch_id').val(data.id);
	        var title = '{!! trans('panel.global.edit') !!}' ;
	        $('.modal-title').text(title);
	        $('#action_button').val('Edit');
	        $('#createbranch').modal('show');
         },
         error: function (error) {
            console.error('Error updating branch data:', error);
         }
      });
 
     });
    $('body').on('click', '.branchActive', function () {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if(active == 'Y')
        {
          status = 'Incative ?';
        }
        else
        {
           status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want "+status)) {
           return false;
        }
        $.ajax({
            url: "{{ url('branches') }}/" + id,
            type: 'PATCH',
            data: {_token: token,id: id,active:active},
            success: function (data) {
              $('.message').empty();
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
              table.draw();
            },
        });
    });
    $('.create').click(function () { 
        $('#branch_id').val('');
        $('#createBranchForm').trigger("reset");
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
 
    
    $('body').on('click', '.delete', function () { 
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('branches') }}"+'/'+id,
            type: 'DELETE',
            data: {_token: token,id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
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
