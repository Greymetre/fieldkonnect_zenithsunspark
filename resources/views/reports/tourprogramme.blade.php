<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
    <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <form method="GET" action="{{ URL::to('tourProgramme-download') }}">
            <div class="row">
              <div class="col-md-6">
                <h4 class="card-title ">Tour Programme Report</h4>
              </div>
              <div class="col-md-5">
                <div class="form-group has-default bmd-form-group">
                <select class="form-control select2" name="user_id" onchange="tourProgrammeData()">
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
              <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Name Of ASM: </strong> <span class="asmName"></span> &nbsp; 
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
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Date: {!! date('Y-m-d') !!}</strong> </li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Month :</strong> {!! date('M Y') !!}</li>
             </ul>
          </div>
        </div>
        <div class="row">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-xxs font-weight-bolder opacity-7">Date</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Town</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Category</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Objectives</th>
                <th class="text-xxs font-weight-bolder opacity-7 ps-2">Last Visit Date</th>
              </tr>
            </thead>
            <tbody id="tourProgrammeData">
            </tbody>
          </table>
        </div>
        <div class="row p-5">
          <div class="col-12 col-md-6 col-xl-4 position-relative text-center">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">Signature of ASM</h6>
            <hr class="horizontal gray-light">
          </div>
          <div class="col-12 col-md-6 col-xl-4 position-relative text-center">
            <hr class="horizontal gray-light">
            <h6 class="mb-0">Sanctioned by</h6>
            <h6 class="mb-0">ZSM</h6>
            <hr class="horizontal gray-light">
          </div>
          <div class="col-12 col-md-6 col-xl-4 position-relative text-center">
              <hr class="horizontal gray-light">
              <h6 class="mb-0">Approved by</h6>
              <h6 class="mb-0">NSM</h6>
              <hr class="horizontal gray-light">
          </div>
        </div>
      </div>
    </div>
    <button class="btn btn-info float-right" onclick="generatePDF()">Excel</button>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
   $( document ).ready(function() {
       tourProgrammeData();
   })

   function tourProgrammeData(){
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('tourProgrammeReportData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", user_id : user_id},
         success: function(res){
            $('#tourProgrammeData').empty();
            $(".asmName").empty();
            $(".asmLocation").empty();
            $(".asmZone").empty();
            $('.asmName').append(res.users.name);
            $('.asmLocation').append(res.users.location);
            $('.asmZone').append(res.users.location);
            $.each(res.tours,function(index,item){ 
                $('#tourProgrammeData').append(
                  '<tr>'+
                    '<td>'+item.date+'</td>'+
                    '<td>'+item.town+'</td>'+
                    '<td>'+item.category+'</td>'+
                    '<td>'+item.objectives+'</td>'+
                    '<td>'+item.last_visit_date+'</td>'+
                 '</tr>');

            });
         }
      });
   }
</script>
</x-app-layout>
