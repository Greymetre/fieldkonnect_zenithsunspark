<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <form method="GET" action="{{ URL::to('performanceParameter-download') }}">
            <div class="row">
              <div class="col-md-6">
                <h4 class="card-title ">Performance Parameter Of Visit</h4>
              </div>
              <div class="col-md-5">
                <div class="form-group has-default bmd-form-group">
                <select class="form-control select2" name="user_id" onchange="performanceParameterData()">
                  <option selected disabled=""> Select Users</option>
                  @if(@isset($users ))
                    @foreach($users as $user)
                      <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                  @endif
                </select>
                </div>
              </div>
              <div class="col-md-1">
                  <span class="pull-right">
                    <div class="btn-group">
                    <button class="btn btn-just-icon btn-theme" title="Performance Parameter Report Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </span>
              </div>
            </div>
          <form>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6 col-md-6 col-xl-6 position-relative">
            <h6 class="mb-0">Branch : <span class="asmLocation"></span></h6>
          </div>
          <div class="col-6 col-md-6 col-xl-6 position-relative">
            <h6 class="mb-0">Name of ASM : <span class="asmName"></span> </h6>
          </div>
        </div>
        <div class="row">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-xxs font-weight-bolder opacity-7">N0</th>
                <th class="text-xxs font-weight-bolder opacity-7">Parameter Of Visit</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">JAN</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">FEB</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">MAR</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">APR</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">MAY</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">JUN</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">JUL</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">AUG</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">SEP</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">OCT</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">NOV</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">DEC</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">TOTAL</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">APM</th>
              </tr>
            </thead>
            <tbody id="performanceParameterData">

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
   $( document ).ready(function() {
       performanceParameterData();
   })

   function performanceParameterData(){
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('performanceParameterReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
            $('#performanceParameterData').empty();
            $(".asmName").empty();
            $(".asmLocation").empty();
            $(".asmZone").empty();
            $('.asmName').append(res.users.name);
            $('.asmLocation').append(res.users.location);
            $('.asmZone').append(res.users.location);
            $.each(res.perams,function(index,item){ 
                $('#performanceParameterData').append(
                  '<tr>'+
                    '<td>'+ parseInt(++index)+'</td>'+
                    '<td>'+item.parameter+'</td>'+
                    '<td>'+item.jan+'</td>'+
                    '<td>'+item.feb +'</td>'+
                    '<td>'+item.mar+'</td>'+
                    '<td>'+item.apr+'</td>'+
                    '<td>'+item.may+'</td>'+
                    '<td>'+item.jun+'</td>'+
                    '<td>'+item.jul+'</td>'+
                    '<td>'+item.aug+'</td>'+
                    '<td>'+item.sep+'</td>'+
                    '<td>'+item.oct+'</td>'+
                    '<td>'+item.nov+'</td>'+
                    '<td>'+item.dec+'</td>'+
                    '<td>'+item.total+'</td>'+
                    '<td>'+item.apm+'</td>'+
                 '</tr>');

            });
         }
      });
   }
</script>
</x-app-layout>
