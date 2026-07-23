<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">Stock Update </h4>
      </div>
      <div class="card-body">
        @if(count($errors) > 0)
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
          </span>
        </div>
        @endif

         <form method="POST" action="{{ route('orders.submitexpecteddelivery') }}" enctype="multipart/form-data">
              @csrf
          <div class="row">
            <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
               <thead>
                  <tr class="card-header-warning text-white">
                     <th width="3%"> # </th>
                     <th class="text-center">City Name</th>
                     <th class="text-center">Pincode</th>
                     <th width="15%">Expected-Day</th>
                     <th></th>
                  </tr>
               </thead>
               <tbody>
                <datalist id="cityList">
                    @if($cities->isNotEmpty())
                          @foreach ($cities as $city)
                            <option value="{{ $city['city_name'] }}"/>
                          @endforeach
                     @endif
                </datalist>
                @if($palaces && isset($palaces))
                  @foreach($palaces as $key => $rows )
                    <tr id='addr0' value="{!! $key+1 !!}">
                   <td>{!! $key+1 !!}</td>
                   <td>       
                      <input class="form-control city rowchange" placeholder="City Name" name="detail[{!! $key+1 !!}][city_name]" list="cityList" value="{!! $rows['city_name'] !!}" required/>
                   </td>
                   <td>
                      <input class="form-control pincode rowchange" placeholder="Enter Pincode" name="detail[{!! $key+1 !!}][pincode]" value="{!! $rows['pincode'] !!}" required/>
                   </td>
                   <td>
                      <input type="number" name='detail[{!! $key+1 !!}][days]' class="form-control days rowchange" step="0" min="0" value="{!! $rows['days'] !!}" required/>
                      <div class='error-quantity'></div>
                   </td>
                   <td class="td-actions text-center"><a class="remove btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td>
                </tr>
                  @endforeach
                @else
                <tr id='addr0' value="1">
                   <td>1</td>
                   <td>       
                      <input class="form-control city rowchange" placeholder="City Name" name="detail[1][city_name]" list="cityList" required/>
                   </td>
                   <td>
                      <input class="form-control pincode rowchange" placeholder="Enter Pincode" name="detail[1][pincode]" required/>
                   </td>
                   <td>
                      <input type="number" name='detail[1][days]' class="form-control days rowchange" step="0" min="0" required/>
                      <div class='error-quantity'></div>
                   </td>
                   <td class="td-actions text-center"><a class="remove btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td>
                </tr>
                @endif
               </tbody>
            </table>
            </div>
            <div class="row clearfix">
              <div class="col-md-12">
                 <table>
                    <tbody>
                       <tr>
                          <td class="td-actions text-center">
                             <a href="#" title="" class="btn btn-success btn-xs add-rows"> <i class="fa fa-plus"></i> </a>
                          </td>
                       </tr>
                    </tbody>
                 </table>
                 
              </div>
           </div>

            <button class="btn btn-info"> Update</button>
            </form>
          </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
   $(document).ready(function(){
    var $table = $('table.kvcodes-dynamic-rows-example'),
         counter = $('#tab_logic tr:last').attr('value');
      $('a.add-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr value="'+counter+'"> <td>'+counter+'</td>'+
                  '<td><input class="form-control city rowchange" placeholder="City Name" name="detail[' + counter + '][city_name]" list="cityList" required/></td>' +
                  '<td><input class="form-control pincode rowchange" placeholder="Enter Pincode" name="detail[' + counter + '][pincode]" required/></td>'+
                  '<td><input type="text" name="detail[' + counter + '][days]" class="form-control days rowchange" required/></td>' +
                  '<td class="td-actions text-center"><a href="#" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
          $table.append(newRow);
      });
   
      $table.on('click', '.remove-rows', function() {
          $(this).closest('tr').remove();
      });
   });
</script>
</x-app-layout>

