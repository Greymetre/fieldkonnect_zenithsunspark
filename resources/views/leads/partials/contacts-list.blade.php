<ul class="list-group list-group-flush list-task cn-list">
    @foreach($lead_contacts as $lead_contact)
    <li>
        <div class="contact-person">
            <h5>{{$lead_contact->name??''}}</h5>
            <p>{{$lead_contact->title??''}}</p>
        </div>
        <div class="contac-link">

<!-- <p><img src="{{ asset('assets/img/whatsup.svg') }}"></p> -->
    @if($lead_contact->email)
        <p>
            <a href="mailto:{{ $lead_contacts[0]->email }}">
                <img src="{{ asset('assets/img/mail.svg') }}" alt="Mail icon" title="{{$lead_contact->email}}" data-toggle="tooltip" >
            </a>
        </p>
    @endif
    @if($lead_contact->phone_number)
        <p>
            <a href="tel:{{ $lead_contacts[0]->phone_number }}">
                <img src="{{ asset('assets/img/phone.svg') }}" alt="Mail icon" title="{{$lead_contact->phone_number}}" data-toggle="tooltip">
            </a>
        </p>
    @endif
    </div>
    <div class="editdelte d-flex flex-row ">

        <div class="contact_edit" onclick='editContact(@json($lead_contact))'><img src="{{ asset('assets/img/ph_note-pencil-fill.svg') }}"></div>
        <div class="contact_edit">
            <form action="{{ route('lead-contacts.destroy', $lead_contact->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Do you really want to delete?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="deletebtn">
                    <img src="{{ asset('assets/img/deletbox.svg') }}">
                </button>
            </form>
        </div>
    </div>
</li>
@endforeach
</ul>