<ul class="list-group list-group-flush list-task contact-list">
    @foreach($lead_tasks as $lead_task)
    <li> 
        <div class="d-flex flex-row justify-content-between">
            <div class="wer">
                <p>{{$lead_task->description??''}}</p>
                <p><img src="{{ asset('assets/img/leaddate.svg') }}"> <span class="date">{{date("d F Y",strtotime($lead_task->date))}}</span>
                </p>
            </div>

            <div class="d-flex flex-row per">
                <div class="editbox" onclick='editTask(@json($lead_task))'> <img src="{{ asset('assets/img/ph_note-pencil-fill.svg') }}">
                </div>

                <div class="deletebox">
                    <form action="{{ route('lead-tasks.destroy', $lead_task->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                        @csrf
                        @method('DELETE')
                        <div type="submit" class="editbox">
                            <button class="deletebtn" type="submit"><img src="{{ asset('assets/img/deletbox.svg') }}"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </li>
    @endforeach
</ul>