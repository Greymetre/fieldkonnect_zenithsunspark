<x-app-layout>

<div class="row mt-4">
      <div class="col-lg-12">
		<div class="card mt-4" data-animation="true">
			<div class="card-body">
         @if(session()->has('message_success'))
         <div class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span>
          {{ session()->get('message_success') }}
          </span>
        </div>
         @endif
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
				<h5 class="font-weight-normal mt-4"> Schedule Beat</h5>
            <div class="row p-3">
            <div class="table-responsive">
            <a href="javascript:;" title="Add a row" class="btn btn-just-icon btn-success pull-right add-schedule-rows" onclick="getScheduleUserlist()">+</a>
                  <form action="{{ URL::to('updateschedule') }}" class="form-horizontal" method="post">
                     {{ csrf_field() }}
                     <input type="hidden" name="beat_id" id="beat_id" value="{!! old( 'beat_id', $beats->id) !!}">
                     <table class="table beat-schedule-rows" id="tab_beat_schedule">
                        <thead>
                           <tr class="item-row">
                              <th></th>
                              <th>User Name</th>
                              <th>Date</th>
                              <th></th>
                           </tr>
                        </thead>
                        <tbody>
              
                        </tbody>
                     </table>
                     <button class="btn btn-theme pull-right"> Add</button>
                     </form>
                  </div>
               </div>
			</div>
		</div>
	</div>
</div>
<script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
<script src="{{ url('/').'/'.asset('assets/js/jquery.beat.js') }}"></script>
</x-app-layout>