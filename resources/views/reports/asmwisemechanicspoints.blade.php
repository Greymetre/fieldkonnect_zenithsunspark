<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <form method="GET" action="{{ URL::to('mechanicsPoints-download') }}">
            <div class="row">
              <div class="col-md-6">
                <h4 class="card-title ">ASM Wise Mechanics Points Reports</h4>
              </div>
              <div class="col-md-5">
                <div class="form-group has-default bmd-form-group">
                <select class="form-control select2" name="user_id" onchange="mechanicsPointsData()">
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
                    <button class="btn btn-just-icon btn-theme" title="Mechanics Points Report Download"><i class="material-icons">cloud_download</i></button>
                    </div>
                  </span>
              </div>
            </div>
          <form>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6 col-md-6 col-xl-6 position-relative">
            <h6 class="mb-0">Branch : </h6>
          </div>
          <div class="col-6 col-md-6 col-xl-6 position-relative">
            <h6 class="mb-0">Name of ASM : </h6>
          </div>
        </div>
        <div class="row">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th>N0</th>
                  <th width="250px">Name of ASM </th>
                  <th>Month</th>
                  <th>H.Q </th>
                  <th>Territory</th>
                  <th>Name of ZSM</th>
                  <th colspan="3"> 
                    <table> 
                      <tr class="text-center"> <th colspan="3">No. of Mech. in </th></tr> 
                      <tr>
                        <td>Territory</td>
                        <td>Coupon Scheme</td>
                        <td>MRP Scheme </td>
                      </tr>
                    </table>
                  </th>
                  <th colspan="2"> 
                    <table> 
                      <tr class="text-center"> <th colspan="2">Collection till Month End</th></tr> 
                      <tr>
                        <td>Coupons value</td>
                        <td>MRP Value</td>
                      </tr>
                    </table>
                  </th>
                  <th colspan="2"> 
                    <table> 
                      <tr class="text-center"> <th colspan="2">Collection in {!! date('M') !!}.</th></tr> 
                      <tr>
                        <td>Coupons value</td>
                        <td>MRP Value</td>
                      </tr>
                    </table>
                  </th>
                  <th colspan="4"> 
                    <table> 
                      <tr class="text-center"> <th colspan="4">Secondary Sales</th></tr> 
                      <tr>
                        <td>GGL</td>
                        <td>GPD</td>
                        <td>Dif</td>
                        <td>Total</td>
                      </tr>
                    </table>
                  </th>
                </tr>
              </thead>
              <tbody id="mechanicsPointsData">
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="6">
                    <p class="text-xs font-weight-normal mb-0">Total</p>
                  </th>
                  <th id="mech_territory"></th>
                  <th id="under_coupon_scheme"></th>
                  <th id="under_mrp_scheme"></th>
                  <th id="total_coupon_value"></th>
                  <th id="total_mrp_value"></th>
                  <th id="collection_coupons_value"></th>
                  <th id="collection_mrp_value"></th>
                  <th id="secondary_sales_ggl"></th>
                  <th id="secondary_sales_gpd"></th>
                  <th id="total_diff"></th>
                  <th id="total_total"></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
   $( document ).ready(function() {
       mechanicsPointsData();
   })

   function mechanicsPointsData(){
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('asmWiseMechanicsPointsReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
            $('#mechanicsPointsData').empty();
            $(".asmName").empty();
            $(".asmLocation").empty();
            $(".asmZone").empty();
            $('.asmName').append(res.users.name);
            $('.asmLocation').append(res.users.location);
            $('.asmZone').append(res.users.location);
            $('#mech_territory').empty();
            $('#under_coupon_scheme').empty();
            $('#under_mrp_scheme').empty();
            $('#total_coupon_value').empty();
            $('#total_mrp_value').empty();
            $('#collection_coupons_value').empty();
            $('#collection_mrp_value').empty();
            $('#secondary_sales_ggl').empty();
            $('#secondary_sales_gpd').empty();
            $('#total_diff').empty();
            $('#total_total').empty();
            $('#mech_territory').append(res.total.mech_territory);
            $('#under_coupon_scheme').append(res.total.under_coupon_scheme);
            $('#under_mrp_scheme').append(res.total.under_mrp_scheme);
            $('#total_coupon_value').append(res.total.total_coupon_value);
            $('#total_mrp_value').append(res.total.total_mrp_value);
            $('#collection_coupons_value').append(res.total.collection_coupons_value);
            $('#collection_mrp_value').append(res.total.collection_mrp_value);
            $('#secondary_sales_ggl').append(res.total.secondary_sales_ggl);
            $('#secondary_sales_gpd').append(res.total.secondary_sales_gpd);
            $('#total_diff').append(res.total.diff);
            $('#total_total').append(res.total.total);
            $.each(res.perams,function(index,item){ 
                $('#mechanicsPointsData').append(
                  '<tr>'+
                    '<td>'+ parseInt(++index)+'</td>'+
                    '<td>'+item.user_name+'</td>'+
                    '<td>'+item.month +'</td>'+
                    '<td>'+item.location+'</td>'+
                    '<td>'+item.state+'</td>'+
                    '<td>'+item.reporting+'</td>'+
                    '<td>'+item.mech_territory+'</td>'+
                    '<td>'+item.under_coupon_scheme+'</td>'+
                    '<td>'+item.under_mrp_scheme+'</td>'+
                    '<td>'+item.total_coupon_value+'</td>'+
                    '<td>'+item.total_mrp_value+'</td>'+
                    '<td>'+item.collection_coupons_value+'</td>'+
                    '<td>'+item.collection_mrp_value+'</td>'+
                    '<td>'+item.secondary_sales_ggl+'</td>'+
                    '<td>'+item.secondary_sales_gpd+'</td>'+
                    '<td>'+item.diff+'</td>'+
                    '<td>'+item.total+'</td>'+
                 '</tr>');
            });
         }
      });
   }
</script>
</x-app-layout>
