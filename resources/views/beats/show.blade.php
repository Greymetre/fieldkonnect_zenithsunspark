<x-app-layout>
<div class="card  mt-0 p-0 new_bg_card">

		<div class="card-header card-header-icon card-header-theme">
	
				<h4 class="card-title">{!! $beats['beat_name'] !!}</h4> 
		
	</div>
	<div class="card-body h-100 mb-5">
	
	<div class="allcard_div">
		<div class="row mt-3">
			<div class="col-12 col-md-6 col-xl-4 mt-md-0 mt-4 position-relative">
				<div class="card card-plain h-100">
					<div class="card-header pt-0">
						<div class="manu_row">
							<div class="d-flex align-items-center justify-content-between">
								<h4 class="mb-0 font-weight-bolder">Beat Information</h4> 
							<a href="javascript:;" style="color: #fff;">
								 <!-- <i class="fas fa-user-edit text-sm" data-bs-toggle="tooltip" data-bs-placement="top" aria-hidden="true" aria-label="Edit Profile"></i> -->
								 <i class="material-icons icon" data-bs-toggle="tooltip" data-bs-placement="top" aria-hidden="true" aria-label="Edit Profile">edit</i></a></div>
						</div>
					
					<div class="card-body">
						<p class="text-sm"> {!! $beats['description'] !!} </p>
							<hr class="horizontal" style="color: #fff; background: #fff;">
						<ul class="list-group"> @if($beats['statename']['state_name'])
							<li class="list-group-item p-0 mb-3"><strong class="">State:</strong>  {!! $beats['statename']['state_name'] !!}</li> @endif @if($beats['district_name'])
							<li class="list-group-item p-0 mb-3"><strong class="">District:</strong>  {!! $beats['district_name'] !!}</li>@endif @if($beats['city_name'])
							<li class="list-group-item p-0 mb-3"><strong class="">City:</strong>  {!! $beats['city_name'] !!}</li> @endif @if($beats['createdbyname']['name'])
							<li class="list-group-item p-0 mb-3"><strong class="">Created By:</strong>  {!! $beats['createdbyname']['name'] !!}</li> @endif </ul>
						<hr class="horizontal" style="color: #fff; background: #fff;">
						<h6 class="text-uppercase text-body text-xs font-weight-bolder">Users</h6>
						<ul class="list-group"> @if($beats->exists && isset($beats['beatusers'])) @foreach($beats['beatusers'] as $key => $user )
							<li class="list-group-item p-0 mb-3"><strong class="">{!! $user['users']['name'] !!}</strong>  </li> @endforeach @endif </ul>
					</div>
				</div>
					</div>
				<hr class="vertical dark"> </div>
			<div class="col-12 col-md-6 col-xl-4 position-relative">
				<div class="card card-plain h-100">
					<div class="card-header pt-0">
							<div class="manu_row">
							<div class="d-flex align-items-center justify-content-between">
								<h4 class="mb-0 font-weight-bolder">Customers</h4>  
						

								</div>
						</div>
						


				
					<div class="card-body">
						<ul class="list-group"> 
              @if($customers) 
              @foreach($customers as $key => $customer )
							<li class="list-group-item p-0 mb-3 "><strong class="">{!! $customer['customers']['name'] !!}</strong> </li> 
              @endforeach 
              @endif 
            </ul>
            	</div>
					</div>
				</div>
				<hr class="vertical dark"> </div>
			<div class="col-12 col-xl-4 mt-xl-0 mt-4">
				<div class="card card-plain h-100">
					<div class="card-header pt-0">
						<h4 class="mb-0 font-weight-bolder">Beat Scheduled</h4> </div>
					<div class="card-body">
						<ul class="list-group"> @if($beats->exists && isset($schedules)) @foreach($schedules as $key => $schedule )
							<li class="list-group-item p-0 mb-3">
								<div class="avatar me-3"> @if($schedule['users']['profile_image'])<img src="{!! $schedule['users']['profile_image'] !!}" alt="kal" class="rounded-circle" width="50px"> @endif </div>
								<div class="d-flex align-items-start flex-column justify-content-center">
									<h6 class="mb-0 text-sm">{!! $schedule['users']['name'] !!}</h6>
									<p class="mb-0 text-xs">{!! $schedule['users']['mobile'] !!}</p>
								</div>
								<p class="btn btn-link ho_Btn" href="javascript:;">{!! $schedule['beat_date']!!}</p>
							</li> @endforeach @endif </ul>
					</div>
				</div>
		
		</div>
			</div>
	</div>
	</div>
</div> 
</x-app-layout>