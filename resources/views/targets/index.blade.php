<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-icon card-header-theme">
            <div class="card-icon">
               <i class="material-icons">perm_identity</i>
            </div>
            <h4 class="card-title ">
               Target List
               <span class="pull-right">
                  <div class="btn-group header-frm-btn">
                  <div class="next-btn">
                     <a data-toggle="modal" data-target="#createtargets" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.targets.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
               <table id="gettargets" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                  <thead class=" text-primary">
                     <th>{!! trans('panel.global.no') !!}</th>
                     <th>{!! trans('panel.global.action') !!}</th>
                     <th>User Name</th>
                     <th>Amount</th>
                     <th>From Date</th>
                     <th>To Date</th>
                     <th>Assign By</th>
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
<div class="modal fade bd-example-modal-lg" id="createtargets" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content card">
   <div class="card-header card-header-icon card-header-theme">
      <div class="card-icon">
         <i class="material-icons">perm_identity</i>
      </div>
      <h4 class="card-title"><span class="modal-title">Add </span> Target
         <span class="pull-right" >
         <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
         </span>
      </h4>
   </div>
   <div class="modal-body">
      <form method="POST" action="{{ route('targets.store') }}" enctype="multipart/form-data" id="createtargetsForm">
         @csrf
         <div class="row">
            <div class="col-md-6">
                      <label class="col-form-label">Select User</label>
               <div class="input_section">
                  <select class="form-control" name="userid" id="userid" style="width: 100%;" required >
                     <option value="" selected disabled>Select User</option>
                     @if(@isset($users ))
                     @foreach($users as $user)
                     <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                     @endforeach
                     @endif
                  </select>
                  @if ($errors->has('userid'))
                  <div class="error">
                     <p class="text-danger">{{ $errors->first('userid') }}</p>
                  </div>
                  @endif
               </div>
            </div>
            <div class="col-md-6">
               <div class="input_section">
                  <label class="col-form-label">From Date</label>
                 
                     <div class="form-group has-default bmd-form-group">
                        <input type="text" name="startdate" id="startdate" class="form-control datepicker" autocomplete="off" readonly>
                        @if($errors->has('startdate'))
                        <div class="invalid-feedback">
                           {{ $errors->first('startdate') }}
                        </div>
                        @endif
                     </div>
                  </div>
           
            </div>
            <div class="col-md-6">
               <div class="input_section">
                  <label class="col-form-label">To Date</label>
                
                     <div class="form-group has-default bmd-form-group">
                        <input type="text" name="enddate" id="enddate" class="form-control datepicker" autocomplete="off" readonly>
                        @if($errors->has('enddate'))
                        <div class="invalid-feedback">
                           {{ $errors->first('enddate') }}
                        </div>
                        @endif
                     </div>
                                 </div>
            </div>
            <div class="col-md-6">
               <div class="input_section">
                  <label class="col-form-label">Amount</label>
                 
                     <div class="form-group has-default bmd-form-group">
                        <input type="number" name="amount" id="amount" class="form-control">
                        @if($errors->has('amount'))
                        <div class="invalid-feedback">
                           {{ $errors->first('amount') }}
                        </div>
                        @endif
                     </div>
                  </div>
         
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 pull-right">
               <input type="hidden" name="id" id="targets_id" />
               <button class="btn btn-info save"> Submit</button>
      </form>
      </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(function () {
     $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           }
     });
     var table = $('#gettargets').DataTable({
         processing: true,
         serverSide: true,
         "order": [ [0, 'desc'] ],
         ajax: "{{ route('targets.index') }}",
         columns: [
             { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
             {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
             {data: 'users.name', name: 'users.name',"defaultContent": ''},
             {data: 'amount', name: 'amount',"defaultContent": ''},
             {data: 'startdate', name: 'startdate',"defaultContent": ''},
             {data: 'enddate', name: 'enddate',"defaultContent": ''},
             {data: 'createdbyname.name', name: 'createdbyname.name',"defaultContent": ''},
         ]
     });
          
     $(document).on('click', '.edit', function(){
       var base_url =$('.baseurl').data('baseurl');
       var id = $(this).attr('id');
       $.ajax({
         url: base_url + '/targets/'+id+'/edit',
        dataType:"json",
        success:function(data)
        {
         $('#amount').val(data.amount);
         $('#startdate').val(data.startdate);
         $('#enddate').val(data.enddate);
         $('#userid').val(data.userid);
         $('#targets_id').val(data.id);
         var title = '{!! trans('panel.global.edit') !!}' ;
         $('.modal-title').text(title);
         $('#action_button').val('Edit');
         $('#createtargets').modal('show');
        }
       })
      });
   
     $('body').on('click', '.activeRecord', function () {
         var id = $(this).attr("id");
         var active = $(this).attr("value");
         var targets = '';
         if(active == 'Y')
         {
           targets = 'Incative ?';
         }
         else
         {
            targets = 'Ative ?';
         }
         var token = $("meta[name='csrf-token']").attr("content");
         if(!confirm("Are You sure want "+targets)) {
            return false;
         }
         $.ajax({
             url: "{{ url('targets-active') }}",
             type: 'POST',
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
         $('#targets_id').val('');
         $('#createtargetsForm').trigger("reset");
         $('.modal-title').text('{!! trans('panel.global.add') !!}');
     });
     
     $('body').on('click', '.delete', function () {
         var id = $(this).attr("value");
         var token = $("meta[name='csrf-token']").attr("content");
         if(!confirm("Are You sure want to delete ?")) {
            return false;
         }
         $.ajax({
             url: "{{ url('targets') }}"+'/'+id,
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
</script>
</x-app-layout>