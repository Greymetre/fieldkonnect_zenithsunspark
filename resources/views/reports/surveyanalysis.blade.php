<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <form method="GET" action="{{ URL::to('surveyAnalysis-download') }}">
            <div class="row">
              <div class="col-md-6">
                <h4 class="card-title ">Survey Analysis Report</h4>
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
            <div class="row" id="surveyData">
              
            </div>
            <!-- <div id="TATASurveyDataAnalysis" class="ct-chart"></div> -->
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
         url: "{{ url('surveyAnalysisReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
          var html = [];
            $.each(res,function(index,item){ 
              $('#surveyData').append(
                  '<div class="col-sm-10 p-4">'+
                    '<div class="d-flex">'+
                      '<div class="p-2"><h4 class="card-title">'+item.types+'</h4></div>'+
                      '<div class="ml-auto p-2"><h6>'+item.total+' responses</h6></div>'+
                    '</div>'+
                    '<div id="'+item.types.replace("M&M", "MM")+'SurveyDataAnalysis" class="ct-chart"></div>'+
                    '<hr class="my-3">'+
              '</div>')
              if(item.types === "Tractor")
              {
                var labels = ['Yes', 'No'] ;
                var targets = [item.total_tractor , item.total-item.total_tractor];
              } 
              else
              {
                var labels = ['HCV', "MAV", "LMV", "LCV","Other"] ;
                var targets = [item.total_hcv, item.total_mav , item.total_lmv , item.total_lcv, item.total_other];
              }   
              new Chartist.Bar('#'+item.types.replace("M&M", "MM")+'SurveyDataAnalysis', {
                  labels: labels,
                  series: [
                    targets
                  ]
                }, {
                  seriesBarDistance: 10,
                  reverseData: true,
                  horizontalBars: true,
                  axisY: {
                    offset: 70
                  },
                });
            });


         }
      });
   }
</script>
</x-app-layout>
