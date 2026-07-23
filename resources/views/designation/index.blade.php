<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.designation.title_singular') !!} {!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn"> 
                  <div class="next-btn">
                
                  @if(auth()->user())
                  <a data-toggle="modal" data-target="#createdesignation" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.branch.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
              <th>{!! trans('panel.designation.fields.designation_name') !!}</th>
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
<div class="modal fade bd-example-modal-lg" id="createdesignation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> {!! trans('panel.designation.title_singular') !!}
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        {!! Form::open(['route' => 'designation.store','id' => 'createDesignationForm','files'=>true ]) !!}
        <div class="row">
          <div class="col-md-12">
            <div class="input_section">
              <label class="col-form-label">{!! trans('panel.designation.fields.designation_name') !!} <span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" name="designation_name" class="form-control" id="designation_name" value="{!! old( 'designation_name') !!}" maxlength="200" required>
                  @if ($errors->has('designation_name'))
                    <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('designation_name') }}</p></div>
                  @endif
                </div>
            </div>
            </div>
               <div class="clearfix"></div>
        <div class="pull-right col-md-12">
          <input type="hidden" name="id" id="brand_id" />
          {{ Form::submit('Submit', array('class' => 'btn btn-info save')) }}
          {{ Form::close() }}
        </div>
           
            </div>
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
    var table = $('#getbrand').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('designation.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false},
            {data: 'active', name: 'active',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'designation_name', name: 'designation_name',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var designationId = $(this).data('designation-id');
      $('#form_result').html('');
      $.ajax({
       url: "{{ url('designation') }}/" + designationId + '/edit',
       type: 'GET',
       dataType:"json",
       success:function(data)
       {
        if(data.brand_image)
        {
          var image = data.brand_image ;
        }
        else
        {
          var image = "{!! asset('assets/img/placeholder.jpg') !!}" ;
        }
        
        $('#designation_name').val(data.designation_name);
        $('#brand_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createdesignation').modal('show');
       }
      })
     });
    $('body').on('click', '.designationActive', function () {
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
           url: "{{ url('designation') }}/" + id,
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
        $('#brand_id').val('');
        $('#createDesignationForm').trigger("reset");
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('designation') }}"+'/'+id,
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
