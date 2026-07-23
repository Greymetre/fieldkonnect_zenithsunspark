<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <form method="GET" action="{{ URL::to('territoryCoverage-download') }}">
            <div class="row">
              <div class="col-md-6">
                <h4 class="card-title ">Territory Coverage Report</h4>
              </div>
              <div class="col-md-5">
                <div class="form-group has-default bmd-form-group">
                <select class="form-control select2" name="user_id" onchange="territoryCoverageData()">
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
                    <button class="btn btn-just-icon btn-theme" title="Territory Coverage Report Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </span>
              </div>
            </div>
          <form>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-12 col-md-12 col-xl-12 position-relative">
            <h6 class="mb-0">Branch : <span class="asmLocation"></span></h6>
          </div>
          <div class="col-12 col-md-12 col-xl-12 position-relative">
            <h6 class="mb-0">Name of ASM : <span class="asmName"></h6>
          </div>
        </div>
        <div class="row">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-xxs font-weight-bolder opacity-7">N0</th>
                <th class="text-xxs font-weight-bolder opacity-7">City Name</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Catg</th>
                <th colspan="2"> 
                    <table> 
                      <tr class="text-center"> <th colspan="2">Total</th></tr> 
                      <tr>
                        <td>Dealer</td>
                        <td>Mech</td>
                      </tr>
                    </table>
                </th>
                <th colspan="4"> 
                    <table> 
                      <tr class="text-center"> <th colspan="4">Jan. Visit</th></tr> 
                      <tr>
                        <td>Dealer</td>
                        <td>Mech</td>
                        <td>Coupon</td>
                        <td>MRP</td>
                      </tr>
                    </table>
                </th>
                <th colspan="4"> 
                    <table> 
                      <tr class="text-center"> <th colspan="4">Total Visit</th></tr> 
                      <tr>
                        <td>Dealer</td>
                        <td>Mech</td>
                        <td>Coupon</td>
                        <td>MRP</td>
                      </tr>
                    </table>
                </th>
              </tr>
            </thead>
            <tbody id="territoryCoverageData">

            </tbody>
            <tfoot>
              <tr>
                <th colspan="3">
                  <p class="text-xs font-weight-normal mb-0">Total</p>
                </th>
                <th id="total_dealer"></th>
                <th id="total_mechanic"></th>
                <th id="dealer_visited"></th>
                <th id="mechanic_visited"></th>
                <th id="gift_coupons"></th>
                <th id="mrp_label"></th>
                <th id="total_dealer_visited"></th>
                <th id="total_mechanic_visited"></th>
                <th id="total_gift_coupons"></th>
                <th id="total_mrp_label"></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
   $( document ).ready(function() {
       territoryCoverageData();
   })

   function territoryCoverageData(){
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('territoryCoverageReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
            $('#territoryCoverageData').empty();
            $(".asmName").empty();
            $(".asmLocation").empty();
            $(".asmZone").empty();
            $('.asmName').append(res.users.name);
            $('.asmLocation').append(res.users.location);
            $('.asmZone').append(res.users.location);
            $('#total_dealer').empty();
            $('#total_mechanic').empty();
            $('#dealer_visited').empty();
            $('#mechanic_visited').empty();
            $('#gift_coupons').empty();
            $('#mrp_label').empty();
            $('#total_dealer_visited').empty();
            $('#total_mechanic_visited').empty();
            $('#total_gift_coupons').empty();
            $('#total_mrp_label').empty();
            $('#total_dealer').append(res.total.total_dealer);
            $('#total_mechanic').append(res.total.total_mechanic);
            $('#dealer_visited').append(res.total.dealer_visited);
            $('#mechanic_visited').append(res.total.mechanic_visited);
            $('#gift_coupons').append(res.total.gift_coupons);
            $('#mrp_label').append(res.total.mrp_label);
            $('#total_dealer_visited').append(res.total.total_dealer_visited);
            $('#total_mechanic_visited').append(res.total.total_mechanic_visited);
            $('#total_gift_coupons').append(res.total.total_gift_coupons);
            $('#total_mrp_label').append(res.total.total_mrp_label);
            $.each(res.cities,function(index,item){ 
                $('#territoryCoverageData').append(
                  '<tr>'+
                    '<td>'+ parseInt(++index)+'</td>'+
                    '<td>'+item.city_name+'</td>'+
                    '<td>'+item.grade+'</td>'+
                    '<td>'+item.total_dealer +'</td>'+
                    '<td>'+item.total_mechanic+'</td>'+
                    '<td>'+item.dealer_visited+'</td>'+
                    '<td>'+item.mechanic_visited+'</td>'+
                    '<td>'+item.gift_coupons+'</td>'+
                    '<td>'+item.mrp_label+'</td>'+
                    '<td>'+item.total_dealer_visited+'</td>'+
                    '<td>'+item.total_mechanic_visited+'</td>'+
                    '<td>'+item.total_gift_coupons+'</td>'+
                     '<td>'+item.total_mrp_label+'</td>'+
                 '</tr>');

            });
         }
      });
   }
</script>
</x-app-layout>
