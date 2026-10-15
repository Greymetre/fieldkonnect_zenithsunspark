<ul class="list-group list-group-flush list-task cn-list appurt">
    @foreach($lead_opportunities  as $lead_opportunity)
    <li>
        <div class="contact-person">
            <div class="d-flex flex-row justify-content-between gh">
                <div class="">
                    <h5>{{$lead_opportunity->note}}</h5>
                    <p>{{$lead_opportunity->amount}}</p>
                </div>
                <div class="d-flex flex-row sdm">
                    <div class="editsource" onclick='editOpportunity(@json($lead_opportunity))'><img src="{{ asset ('assets/img/ph_note-pencil-fill.svg') }}">
                    </div>
                    <div class="deletebox">
                        <form action="{{ route('lead-opportunities.destroy', $lead_opportunity->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                            @csrf
                            @method('DELETE')
                            <button class="deletebtn editsource" type="submit" class="">
                                <img src="{{ asset ('assets/img/deletbox.svg') }}">
                            </button>
                        </form>
                    </div>

                </div>
            </div>


        </div>
    </li>
    @endforeach
</ul>