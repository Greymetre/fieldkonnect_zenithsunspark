        @php
        $previousDate = null;
        @endphp
        @foreach($combined as $lead_note)
        @php
        $noteDate = \Carbon\Carbon::parse($lead_note->created_at)->format('d M Y'); // e.g., 28 Feb 2024
        $noteTime = \Carbon\Carbon::parse($lead_note->created_at)->format('h:i a'); // e.g., 10:25 pm
        @endphp
        <div>
            @if($noteDate !== $previousDate)
            <h6 class="text-muted date_listing mt-3"> <img src="{{ asset('assets/img/leaddate.svg') }}"> {{ $noteDate }}</h6>
            @php
            $previousDate = $noteDate;
            @endphp
            @endif
<!-- code 28 feb 2024  -->

<!--new div--->
<div class="card mb-2 p-0">
    <div class="main-div">
        <div class="list-one">
            <div class="image_box">
                <div class="greenbox">
                    @if($lead_note->type == 'note')
                    <img src="{{asset('assets/img')}}/cup.svg">
                    @elseif($lead_note->type == 'task')
                    <img src="{{asset('assets/img')}}/task_logo.png" width="35">
                    @elseif($lead_note->type == 'log')
                    <img src="{{asset('assets/img')}}/log.png" width="35">
                    @else
                    <img src="{{asset('assets/img')}}/opportunity.png?" width="25">
                    @endif
                </div>
            </div>
            <div class="listdata">
                @if($lead_note->type == 'note')
                <h5>Note</h5>
                <h5>{!! $lead_note->note??''!!}</h5>
                <p>Created by: {!! $lead_note->createdby? $lead_note->createdby->name : ''!!}</p>
                @elseif($lead_note->type == 'task')
                <h5>Task <span class="badge badge-info ml-1" style="font-family: 'Poppins', sans-serif;font-size: 10px;font-weight:700;border-radius: 0px 10px !important;"> {{$lead_note->priority??''}}</span></h5>
                <h5>{!! $lead_note->description??''!!} ({{date('d M Y', strtotime($lead_note->date))}}, {{date('h:i A', strtotime($lead_note->time))}})</h5>
                <p>Created by: {!! $lead_note->createdby? $lead_note->createdby->name : ''!!}</p>
                @elseif($lead_note->type == 'log')
                <h5>Log </h5>
                <h5>{!! $lead_note->message??''!!}</h5>
                @else
                <h5>Opportunity <span class="badge badge-info ml-1" style="font-family: 'Poppins', sans-serif;font-size: 10px;font-weight:700;border-radius: 0px 10px !important;"> {{$lead_note->status_is ? $lead_note->status_is->status_name :''}}</span></h5>
                <h5>{!! $lead_note->note??''!!} </h5>
                <p>Estimated close date: {{date('d M Y', strtotime($lead_note->estimated_close_date))}}</p>
                <p>Confidence: {{$lead_note->confidence??0}}%</p>
                <p>Created by: {!! $lead_note->createdby? $lead_note->createdby->name : ''!!}</p>
                @endif

                <p></p>

                <p class="date-list">{{$noteTime}}</p>
            </div>
        </div>
        @if($lead_note->type == 'noteee')
        <div class="list-two">
            <div class="dropdown">
                <button class="dropdown-toggle" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{asset('assets/img')}}/point.svg">

            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <div class="dropdown-item" onclick='editNote(@json($lead_note))'>
                    <a>Edit</a>
                </div>
                <div class="dropdown-item">
                    <form action="{{ route('lead-notes.destroy', $lead_note->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="">
                            delete
                        </button>
                    </form>
                </div>



            </div>
        </div>

    </div>
    @endif
</div>

</div>
</div>
@endforeach