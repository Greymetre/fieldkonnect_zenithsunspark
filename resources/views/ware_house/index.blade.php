<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">Ware House {!! trans('panel.global.list') !!}
              <span class="">
                <div class="btn-group header-frm-btn">
                    <div class="next-btn">
                  @if(auth()->user()->can(['category_upload']))
                  {{-- <form action="{{ URL::to('categories-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.category.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form> --}}
                  @endif
                
                  @if(auth()->user()->can(['category_download']))
                  {{-- <a href="{{ URL::to('categories-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.category.title') !!}"><i class="material-icons">cloud_download</i></a> --}}
                  @endif
                  @if(auth()->user()->can(['category_template']))
                  {{-- <a href="{{ URL::to('categories-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.category.title_singular') !!}"><i class="material-icons">text_snippet</i></a> --}}
                  @endif
                  @if(auth()->user()->can(['ware_house_create']))
                   <a data-toggle="modal" data-target="#createWarehouse" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.category.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
          <span>
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
          <table id="getcategory" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>ID</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>Ware House Code</th>
              <th>Ware House Name</th>
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
<div class="modal fade bd-example-modal-lg" id="createWarehouse" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">{!! trans('panel.global.add') !!}</span> Ware House
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('ware_house.store') }}" enctype="multipart/form-data" id="createWarehouseForm">
        @csrf
        <div class="row">
            <div class="col-md-9">
                <div class="input_section">
                  <label class="col-form-label">Ware House Code <span class="text-danger"> *</span></label>
                  
                  <div class="form-group has-default bmd-form-group">
                    <input type="text" name="warehouse_code" id="warehouse_code" class="form-control" value="{!! old( 'warehouse_code') !!}" maxlength="200" required>
                    @if ($errors->has('warehouse_code'))
                    <div class="error"><p class="text-danger">{{ $errors->first('warehouse_code') }}</p></div>
                    @endif
                  </div>
                  <label class="col-form-label">Ware House Name <span class="text-danger"> *</span></label>
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="warehouse_name" id="warehouse_name" class="form-control" value="{!! old( 'warehouse_name') !!}" maxlength="200" required>
                      @if ($errors->has('warehouse_name'))
                        <div class="error"><p class="text-danger">{{ $errors->first('warehouse_name') }}</p></div>
                      @endif
                  </div>
                </div>
              </div>
          </div>
        <div class="clearfix"></div>
        <div class="pull-right">
          <input type="hidden" name="id" id="warehouse_id" />
          <button class="btn btn-info save"> Submit</button>
        </form>
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
    var table = $('#getcategory').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('ware_house.index') }}",
        columns: [
            { data: 'id', name: 'id', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',orderable: false, searchable: false},
             {data: 'warehouse_code', name: 'warehouse_code',"defaultContent": '', orderable: false, searchable: false},
            {data: 'warehouse_name', name: 'warehouse_name',"defaultContent": '', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $.ajax({
        url: base_url + '/ware_house/'+id,
       dataType:"json",
       success:function(data)
       {
        console.log(data);
        $('#warehouse_code').val(data.warehouse_code);
        $('#warehouse_name').val(data.warehouse_name);
        $('#warehouse_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createWarehouse').modal('show');
       }
      })
     });
    
    
    $('.create').click(function () {
        $('#createWarehouseForm').trigger("reset");
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
    var id = $(this).attr("value");
    var token = $("meta[name='csrf-token']").attr("content");

    if (!confirm("Are you sure you want to delete?")) {
        return false;
    }

    $.ajax({
        url: '/ware_house/' + id,
        type: 'DELETE',          
        data: {
            _token: token,        
            id: id                
        },
        success: function (data) {
            $('.alert').show();
            if (data.status === 'success') {
                $('.alert').addClass("alert-success").text(data.message);
            } else {
                $('.alert').addClass("alert-danger").text(data.message);
            }
            table.draw();
        },
        error: function (xhr) {
            $('.alert').show().addClass("alert-danger").text("An error occurred.");
        }
    });
});

     
    });
</script>

</x-app-layout>
