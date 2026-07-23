<x-app-layout>
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <section class="content new_item">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-primary">
              <div class="card-header card-header-icon card-header-theme">
                <h3 class="card-title">Tour Create</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              @if($errors->any())
                  <div>
                      <ul class="alert alert-danger">
                          @foreach($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
                <div class="card-body ">
                  <div class="tab-content tab-space">
                    {!! Form::model($tours,[
                      'route' => $tours->exists ? ['tours.update', $tours->id] : 'tours.store',
                      'method' => $tours->exists ? 'PUT' : 'POST',
                      'id' => 'createCompany',
                      'files'=>true
                    ]) !!}
                      <div class="row">
                        <input type="hidden" name="id" id="id">
                        <div class="table-responsive w-100">
                          <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                             <thead>
                                <tr class="text-white">
                                  <th class="text-center">Date </th>
                                  <th>User</th>
                                  <th class="text-center">Town</th>
                                  <th class="text-center">Objectives</th>
                                  <th class="text-center"></th>
                                </tr>
                             </thead>
                             <tbody>
                              <tr id='addr0' value="1">
                                 <td>
                                  <div class="input_section"><input type="text" name="detail[1][date]" class="form-control datepicker" autocomplete="off"/>
                                  </div></td>
                                 <td>
                                <div class="input_section">
                                    <select class="form-control rowchange select2" name="detail[1][userid]">
                                       @if(@isset($users))
                                       <option value="">Select User</option>
                                       @foreach($users as $user )
                                       <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                                       @endforeach
                                       @endif
                                    </select>
                                  </div>
                                 </td>
                                 <td>
                                    <div class="input_section">
                                    <input type="text" name="detail[1][town]" class="form-control rowchange"/>
                                  </div>
                                 </td>
                                 <td>
                                    <div class="input_section">
                                    <input type="text" name="detail[1][objectives]" class="form-control rowchange"/>
                                  </div>
                                 </td>
                                 <td class="td-actions text-center"><a class="remove btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td>
                              </tr>
                             </tbody>
                          </table>
                       </div>
                      <table class="table">
                        <tbody>
                           <tr>
                              <td class="td-actions text-left">
                                 <a href="javascript:void(0)" class="btn  btn-xs add-rows" onclick="getUserlist()"> <i class="fa fa-plus"></i> </a>
                              </td>
                           </tr>
                        </tbody>
                      </table>
                      </div>
                    {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
                    {{ Form::close() }} 
                  </div>
                </div>
            </div>
            </div>
        </div>
      </div>
    </section>
<script type="text/javascript">
    $(document).ready(function(){
    var $table = $('table.kvcodes-dynamic-rows-example'),
         counter = $('#tab_logic tr:last').attr('value');
      $('a.add-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr value="'+counter+'">'+
                  '<td><div class="input_section"><input type="text" name="detail[' + counter + '][date]" class="form-control datepicker" autocomplete="off"/></div></td>' +
                  '<td><div class="input_section"><select class="form-control user select2" name="detail[' + counter + '][userid]"></select></div></td>'+
                  '<td><div class="inpu_section"><input type="text" name="detail[' + counter + '][town]" class="form-control rowchange"/></div></td>' +
                  '<td><div class="inpu_section"><input type="text" name="detail[' + counter + '][objectives]" class="form-control rowchange"/></div></td>' +
                  '<td class="td-actions text-center"><a href="javascript:void(0)" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
          $table.append(newRow);
          $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
          $('.select2').select2();
      });
   
      $table.on('click', '.remove-rows', function() {
          $(this).closest('tr').remove();
      });
  })

  function getUserlist()
  {
      var base_url =$('.baseurl').data('baseurl'); 
        $.ajax({
          url: "{{ url('getUserList') }}",
          dataType: "json",
          type: "POST",
          data:{ _token: "{{csrf_token()}}" },
          success: function(res){
            var table = document.getElementById(tab_logic),rIndex;
            if(res){
              $('#tab_logic tr:last').find(".product").empty();
                $('#tab_logic tr:last').find(".user").append('<option value="">Select User</option>');
              $.each(res,function(key,value){ 
                $('#tab_logic tr:last').find('.user').append('<option value="'+value.id+'">'+value.name+'</option>');
   
              });
            }
            else{
               row.find(".product").empty();
            }
          }
      });
    }
</script> 
</x-app-layout>
