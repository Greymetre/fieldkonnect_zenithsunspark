<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <form method="GET" action="{{ URL::to('monthlyMovement-download') }}">
            <div class="row">
              <div class="col-md-6">
                <h4 class="card-title ">Tour Programme Report</h4>
              </div>
              <div class="col-md-5">
                <div class="form-group has-default bmd-form-group">
                <select class="form-control select2" name="user_id" onchange="monthlyMovementData()">
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
                    <button class="btn btn-just-icon btn-theme" title="Tour Programme Report Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </span>
              </div>
            </div>
          <form>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-6 col-xl-4 position-relative">
            <h6 class="mb-0">Gajra Gears Pvt. Ltd.</h6>
            <hr class="horizontal gray-light">
            <h6 class="mb-0">Marketing Dept. </h6>
            <hr class="horizontal gray-light">
            <ul class="list-group ps-0 pt-0 ">
              <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Name Of ASM:</strong> &nbsp; <span class="asmName"></span>
              </li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Location:</strong> &nbsp; <span class="asmLocation"></span> </li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Zone:</strong> &nbsp; <span class="asmZone"></span> </li>
            </ul>
          </div>
          <div class="col-12 col-md-6 col-xl-4 position-relative">
            <h6 class="mb-0 text-center">Tour Programme</h6>
          </div>
          <div class="col-12 col-md-6 col-xl-4 position-relative">
              <ul class="list-group">
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Doc. No. AP04:</strong> (419) 300-21 </li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Date:</strong> {!! date('Y-m-d') !!}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Month :</strong> {!! date('M Y') !!}</li>
             </ul>
          </div>
        </div>
        <div class="row">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-xxs font-weight-bolder opacity-7">Date</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Planned</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Actual</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Category</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Dealer</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Mech</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">STU</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Fleet Owner</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Total Visit</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">No.of Coupons</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Total Points</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">No.of Gifts</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Value</th>
              </tr>
            </thead>
            <tbody id="monthlyMovementReportData">
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5">
                  <p class="text-xs font-weight-normal mb-0">Total</p>
                </td>
                <td id="total_dealer_visited"></td>
                <td id="total_mechanic_visited"></td>
                <td id="total_stu_visited"></td>
                <td id="total_fleet_owner_visited"></td>
                <td id="total_total_visited"></td>
                <td id="total_no_of_coupons"></td>
                <td id="total_total_points"></td>
                <td id="total_no_of_gifts"></td>
                <td id="total_gift_value"></td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div class="row">
          <div class="col-12 col-md-6 col-xl-6 position-relative">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">Status</h6>
            <hr class="horizontal gray-light">
            <ul class="list-group ps-0 pt-0 ">
              <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">‘T’</strong> &nbsp; Tour (other than Suburb) 
              </li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">‘O’</strong> &nbsp; Office Work </li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">‘S’ </strong> &nbsp; Suburban </li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">‘C’</strong> &nbsp; Central Market</li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">‘H’</strong> &nbsp; Holiday</li>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">‘L’</strong> &nbsp; Leave (CL/ EL)</li>
            </ul>
          </div>
          <div class="col-12 col-md-6 col-xl-6 position-relative">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">Remarks</h6>
            <hr class="horizontal gray-light">
            <ul class="list-group ps-0 pt-0 ">
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">1.  For change of tour plan, ZSM to give the remarks :</strong> &nbsp;  
              </li>
              <br>
              <br>
              <br>
              <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">2.  If tour days (T) are below 15 days,  ZSM to give remarks :</strong> &nbsp; </li>
            </ul>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-6 col-xl-6 position-relative text-center">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">Signature of  ASM / Date</h6>
            <hr class="horizontal gray-light">
          </div>
          <div class="col-12 col-md-6 col-xl-6 position-relative text-center">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">Signature of ZSM</h6>
            <hr class="horizontal gray-light">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
   $( document ).ready(function() {
       monthlyMovementData();
   })

   function monthlyMovementData(){
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('monthlyMovementReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
            $('#monthlyMovementReportData').empty();
            $('#total_dealer_visited').empty();
            $('#total_mechanic_visited').empty();
            $('#total_stu_visited').empty();
            $('#total_fleet_owner_visited').empty();
            $('#total_total_visited').empty();
            $('#total_no_of_coupons').empty();
            $('#total_total_points').empty();
            $('#total_no_of_gifts').empty();
            $('#total_gift_value').empty();
            $('#total_dealer_visited').append(res.total.dealer_visited);
            $('#total_mechanic_visited').append(res.total.mechanic_visited);
            $('#total_stu_visited').append(res.total.stu_visited);
            $('#total_fleet_owner_visited').append(res.total.fleet_owner_visited);
            $('#total_total_visited').append(res.total.total_visited);
            $('#total_no_of_coupons').append(res.total.no_of_coupons);
            $('#total_total_points').append(res.total.total_points);
            $('#total_no_of_gifts').append(res.total.no_of_gifts);
            $('#total_gift_value').append(res.total.gift_value);
            $(".asmName").empty();
            $(".asmLocation").empty();
            $(".asmZone").empty();
            $('.asmName').append(res.users.name);
            $('.asmLocation').append(res.users.location);
            $('.asmZone').append(res.users.location);
            $.each(res.tours,function(index,item){ 
                $('#monthlyMovementReportData').append(
                  '<tr>'+
                    '<td>'+item.date+'</td>'+
                    '<td>'+item.town+'</td>'+
                    '<td>'+item.actual_visited+'</td>'+
                    '<td>'+item.grade+'</td>'+
                    '<td>'+item.type+'</td>'+
                    '<td>'+item.dealer_visited+'</td>'+
                    '<td>'+item.mechanic_visited+'</td>'+
                    '<td>'+item.stu_visited+'</td>'+
                    '<td>'+item.fleet_owner_visited+'</td>'+
                    '<td>'+item.total_visited+'</td>'+
                    '<td>'+item.no_of_coupons+'</td>'+
                    '<td>'+item.total_points+'</td>'+
                    '<td>'+item.no_of_gifts+'</td>'+
                    '<td>'+item.gift_value+'</td>'+
                 '</tr>');

            });
         }
      });
   }
</script>
</x-app-layout>
