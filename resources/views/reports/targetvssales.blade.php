<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title">ASM Target VS Secondary Sales
          <span class="pull-right">
            <div class="btn-group">
              <a href="{{ URL::to('targetAchievement-download') }}" class="btn btn-just-icon btn-theme" title="Target Achievement Report Download"><i class="material-icons">cloud_download</i></a>
            </div>
          </span>
        </h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="table-responsive">
            <table class="table align-items-center">
              <thead>
                <tr>
                  <th class="text-xxs font-weight-bolder opacity-7">ZSM</th>
                  <th class="text-xxs font-weight-bolder opacity-7">No</th>
                  <th >ASM Name</th>
                  <th >Base Location</th>
                  <th colspan="4"> 
                    <table> 
                      <tr class="text-center"> <th colspan="4">GAJRA GEARS</th></tr> 
                      <tr>
                        <td>Target</td>
                        <td>10th</td>
                        <td>20th</td>
                        <td>30th</td>
                      </tr>
                    </table>
                  </th>
                  <th colspan="4"> 
                    <table> 
                      <tr class="text-center"> <th colspan="4">Product Division</th></tr> 
                      <tr>
                        <td>Target</td>
                        <td>10th</td>
                        <td>20th</td>
                        <td>30th</td>
                      </tr>
                    </table>
                  </th>
                  <th colspan="4"> 
                    <table> 
                      <tr class="text-center"> <th colspan="4">Differential</th></tr> 
                      <tr>
                        <td>Target</td>
                        <td>10th</td>
                        <td>20th</td>
                        <td>30th</td>
                      </tr>
                    </table>
                  </th>
                  <th colspan="4"> 
                    <table> 
                      <tr class="text-center"> <th colspan="4">Group</th></tr> 
                      <tr>
                        <td>Target</td>
                        <td>10th</td>
                        <td>20th</td>
                        <td>30th</td>
                      </tr>
                    </table>
                  </th>
                </tr>
              </thead>
              <tbody id="targetSaleReportData">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
   $( document ).ready(function() {
       targetSaleReportData();
   })

   function targetSaleReportData(){
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('targetvsSaleReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
            $('#targetSaleReportData').empty();
            $.each(res,function(index,item){ 
                $('#targetSaleReportData').append(
                  '<tr>'+
                    '<td>'+item.zsm_name+'</td>'+
                    '<td>'+ parseInt(++index)+'</td>'+
                    '<td>'+item.name+'</td>'+
                    '<td>'+item.location +'</td>'+
                    '<td>'+item.ggl_targets+'</td>'+
                    '<td>'+item.ggl_achievement_10th+'</td>'+
                    '<td>'+item.ggl_achievement_20th+'</td>'+
                    '<td>'+item.ggl_achievement_30th+'</td>'+
                    '<td>'+item.gpd_targets+'</td>'+
                    '<td>'+item.gpd_achievement_10th+'</td>'+
                    '<td>'+item.gpd_achievement_20th+'</td>'+
                    '<td>'+item.gpd_achievement_30th+'</td>'+
                    '<td>'+item.diff_targets+'</td>'+
                    '<td>'+item.diff_achievement_10th+'</td>'+
                    '<td>'+item.diff_achievement_20th+'</td>'+
                    '<td>'+item.diff_achievement_30th+'</td>'+
                    '<td>'+item.targets+'</td>'+
                    '<td>'+item.achievement_10th+'</td>'+
                    '<td>'+item.achievement_20th+'</td>'+
                    '<td>'+item.achievement_30th+'</td>'+
                 '</tr>');

            });
         }
      });
   }
</script>
</x-app-layout>
