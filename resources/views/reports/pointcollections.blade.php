<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <form method="GET" action="{{ URL::to('pointCollection-download') }}">
            <div class="row">
              <div class="col-md-6">
                <h4 class="card-title ">Point Collections Report</h4>
              </div>
              <div class="col-md-5">
                <div class="form-group has-default bmd-form-group">
                <select class="form-control select2" name="user_id" onchange="pointCollectionData()">
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
                    <button class="btn btn-just-icon btn-theme" title="Point Collections Report Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </span>
              </div>
            </div>
          <form>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-12 col-xl-12 position-relative">
            <h6 class="mb-0 text-center">CITY / MECHANIC WISE POINT COLLECTIONS DURING THE MONTH </h6>
          </div>
        </div>
        <div class="row">
          <ul class="list-group ps-0 pt-0 ">
              <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Name Of ASM:</strong> &nbsp; <span class="asmName"></span>
              </li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Location:</strong> &nbsp; <span class="asmLocation"></span> </li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Zone:</strong> &nbsp; <span class="asmZone"></span> </li>
            </ul>
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-xxs font-weight-bolder opacity-7">N0</th>
                <th class="text-xxs font-weight-bolder opacity-7">Name of Mechanic</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">City</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Mobile No.</th>
                <th colspan="2"> 
                    <table> 
                      <tr class="text-center"> <th colspan="2">Gift Points Coupon</th></tr> 
                      <tr>
                        <td>Prior</td>
                        <td>Cummlative</td>
                      </tr>
                    </table>
                </th>
                <th colspan="2"> 
                    <table> 
                      <tr class="text-center"> <th colspan="2">MRP Label Value</th></tr> 
                      <tr>
                        <td>Prior</td>
                        <td>Cummlative</td>
                      </tr>
                    </table>
                </th>
              </tr>
            </thead>
            <tbody id="pointCollectionData">
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4">
                  <p class="text-xs font-weight-normal mb-0">Total</p>
                </th>
                <th id="total_total_quantity"></th>
                <th></th>
                <th></th>
                <th id="total_total_points"></th>
              </tr>
            </tfoot>
          </table>
        </div>
        <br>
        <div class="row">
          <div class="col-12 col-md-6 col-xl-4 position-relative text-center">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">ASM</h6>
            <hr class="horizontal gray-light">
          </div>
          <div class="col-12 col-md-6 col-xl-4 position-relative text-center">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">ZSM</h6>
            <hr class="horizontal gray-light">
          </div>
          <div class="col-12 col-md-6 col-xl-4 position-relative text-center">
              <hr class="horizontal gray-light">
              <h6 class="mb-0">NSM</h6>
              <hr class="horizontal gray-light">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
   $( document ).ready(function() {
       pointCollectionData();
   })

   function pointCollectionData(){
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('pointCollectionReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
            $('#pointCollectionData').empty();
            $(".asmName").empty();
            $(".asmLocation").empty();
            $(".asmZone").empty();
            $('.asmName').append(res.users.name);
            $('.asmLocation').append(res.users.location);
            $('.asmZone').append(res.users.location);
            $('#total_total_quantity').empty();
            $('#total_total_points').empty();
            $('#total_total_quantity').append(res.total.total_quantity);
            $('#total_total_points').append(res.total.total_points);
            $.each(res.points,function(index,item){ 
              var address = item.customers.customeraddress ;

                $('#pointCollectionData').append(
                  '<tr>'+
                    '<td>'+item.customer_id+'</td>'+
                    '<td>'+item.customers.name+'</td>'+
                    '<td>'+item.city_name +'</td>'+
                    '<td>'+item.customers.mobile+'</td>'+
                    '<td>'+item.total_quantity+'</td>'+
                    '<td>'+item.total_points+'</td>'+
                    '<td>'+item.total_quantity+'</td>'+
                    '<td>'+item.total_points+'</td>'+
                 '</tr>');

            });
         }
      });
   }
</script>
</x-app-layout>
