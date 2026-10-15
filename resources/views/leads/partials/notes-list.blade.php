        @php
        $previousDate1 = null;
        @endphp
        @foreach($lead_notes as $lead_note)
        @php
        $noteDate = \Carbon\Carbon::parse($lead_note->created_at)->format('d M Y'); // e.g., 28 Feb 2024
        $noteTime = \Carbon\Carbon::parse($lead_note->created_at)->format('h:i a'); // e.g., 10:25 pm
        @endphp
        <div>
            @if($noteDate !== $previousDate1)
            <h6 class="text-muted date_listing mt-3"> <img src="{{ asset('assets/img/leaddate.svg') }}"> {{ $noteDate }}</h6>
            @php
            $previousDate1 = $noteDate;
            @endphp
            @endif
<!-- code 28 feb 2024  -->

<!--new div--->
<div class="card mb-2 p-0">
    <div class="main-div">
        <div class="list-one">
            <div class="image_box">
                <div class="greenbox">
                    <img src="{{asset('assets/img')}}/cup.svg">
                </div>
            </div>
            <div class="listdata">
                <h5>{!! $lead_note->note??''!!}</h5>

                <p></p>

                <p class="date-list">{{$noteTime}}</p>
            </div>
        </div>
        <div class="list-two">
            <div class="dropdown">
                <button class="dropdown-toggle" type="button" id="dropdownMenuButton"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{asset('assets/img')}}/point.svg">

            </button>

            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <div class="dropdown-item">
                    <a onclick='editNote(@json($lead_note))'>Edit</a>
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
</div>

</div>
</div>
@endforeach