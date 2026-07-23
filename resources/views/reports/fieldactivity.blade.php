<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title">Field Activity Report
          <span class="pull-right">
            <div class="btn-group">
              <a href="{{ URL::to('fieldActivity-download') }}" class="btn btn-just-icon btn-theme" title="Field Activity Report Download"><i class="material-icons">cloud_download</i></a>
            </div>
          </span>
        </h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
           <table class="table">
              <thead>
                 <tr>
                     <td>ID</td>
                     <td>Name of ASM</td>
                     <td>Month</td>
                     <th colspan="4"> 
                       <table> 
                         <tr class="text-center"> <th colspan="4">Total No.of Stations</th></tr> 
                         <tr>
                              <td>A</td>
                              <td>B</td>
                              <td>C</td>
                              <td>Total</td>
                         </tr>
                       </table>
                     </th>
                     <th colspan="4"> 
                       <table> 
                         <tr class="text-center"> <th colspan="4">Stations Visited</th></tr> 
                         <tr>
                              <td>A</td>
                              <td>B</td>
                              <td>C</td>
                              <td>Total</td>
                         </tr>
                       </table>
                     </th>
                     <th colspan="3"> 
                       <table> 
                         <tr class="text-center"> <th colspan="3">Dealer Visited</th></tr> 
                         <tr>
                              <td>New</td>
                             <td>Existing</td>
                             <td>Total</td>
                         </tr>
                       </table>
                     </th>
                     <th colspan="3"> 
                       <table> 
                         <tr class="text-center"> <th colspan="3">Mechanic Visited</th></tr> 
                         <tr>
                              <td>New</td>
                             <td>Existing</td>
                             <td>Total</td>
                         </tr>
                       </table>
                     </th>
                     <th colspan="2"> 
                       <table> 
                         <tr class="text-center"> <th colspan="2">Points Collected</th></tr> 
                         <tr>
                              <td>Garage</td>
                              <td>Dealer Point</td>
                         </tr>
                       </table>
                     </th>
                     <th colspan="2"> 
                       <table> 
                         <tr class="text-center"> <th colspan="2">Three months Stations Visited</th></tr> 
                         <tr>
                            <td>(A) Stations</td>
                            <td>Total ABC </td>
                         </tr>
                       </table>
                     </th>
                     <th colspan="2"> 
                       <table> 
                         <tr class="text-center"> <th colspan="2">Six months Stations Visited</th></tr> 
                         <tr>
                            <td>(A) Stations</td>
                            <td>Total ABC </td>
                         </tr>
                       </table>
                     </th>
                     <th colspan="6"> 
                       <table> 
                         <tr class="text-center"> <th colspan="6">Activites & Dates</th></tr> 
                         <tr>
                             <td>Name of Station</td>
                             <td>Sales Blitz</td>
                             <td>Nukkad</td>
                             <td>Road Show</td>
                             <td>Van Campaign</td>
                             <td>Activity Expenses</td>
                         </tr>
                       </table>
                     </th>
                     <td>Remarks</td>
                 </tr>
              </thead>
              <tbody id="activityReportData">
              </tbody>
           </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
   $( document ).ready(function() {
       fieldActivityData();
   })

   function fieldActivityData(){
      var fromdate = $("input[name=fromdate]").val();
      var todate = $("input[name=todate]").val();
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('fieldActivityReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}"},
         success: function(res){
            $('#activityReportData').empty();
            $.each(res,function(index,item){ 
                $('#activityReportData').append(
                  '<tr>'+
                    '<td>'+ parseInt(++index)+'</td>'+
                    '<td>'+item.name+'</td>'+
                    '<td>'+item.month+'</td>'+
                    '<td>'+item.stations_in_territory_a+'</td>'+
                    '<td>'+item.stations_in_territory_b+'</td>'+
                    '<td>'+item.stations_in_territory_c+'</td>'+
                    '<td>'+item.stations_in_territory_total+'</td>'+
                    '<td>'+item.stations_visited_month_a+'</td>'+
                    '<td>'+item.stations_visited_month_b+'</td>'+
                    '<td>'+item.stations_visited_month_c+'</td>'+
                    '<td>'+item.stations_visited_month_total+'</td>'+
                    '<td>'+item.new_dealer_visited+'</td>'+
                    '<td>'+item.existing_dealer_visited+'</td>'+
                    '<td>'+item.total_dealer_visited+'</td>'+
                    '<td>'+item.new_mechanic_visited+'</td>'+
                    '<td>'+item.existing_mechanic_visited+'</td>'+
                    '<td>'+item.total_mechanic_visited+'</td>'+
                    '<td>'+item.points_collected_garage+'</td>'+
                    '<td>'+item.points_collected_dealer+'</td>'+
                    '<td>'+item.stations_visited_last_three_months+'</td>'+
                    '<td>'+item.stations_visited_last_three_months_abc+'</td>'+
                    '<td>'+item.stations_visited_last_six_months+'</td>'+
                    '<td>'+item.stations_visited_last_six_months_abc+'</td>'+
                    '<td>'+item.stations_activity_name+'</td>'+
                    '<td>'+item.sales_blitz_activity_date+'</td>'+
                    '<td>'+item.nukkad_activity_date+'</td>'+
                    '<td>'+item.road_show_activity_date+'</td>'+
                    '<td>'+item.van_campaign_activity_date+'</td>'+
                    '<td>'+item.activity_expenses+'</td>'+
                    '<td>'+item.remarks+'</td>'+
                 '</tr>');

            });
         }
      });
   }
</script>
</x-app-layout>
