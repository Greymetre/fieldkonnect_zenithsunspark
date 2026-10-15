    @if(count($media_items) > 0)
        <ul class="">
            @foreach($media_items as $media)
            <li class="d-flex flex-row justify-content-between align-items-center mb-3">
                <a href="{{ $media->getFullUrl() }}" target="_blank">
                    <div class="imagebox">
                        <div class="d-flex flex-row align-items-center">
                            @php
                                $mime = $media->mime_type;
                            @endphp

                            @if(str_contains($mime, 'image'))
                                <img loading="lazy" decoding="async" src="{{ $media->getFullUrl() }}" alt="{{ $media->name }}" width="50" height="50">
                            @elseif(str_contains($mime, 'pdf'))
                                <img loading="lazy" decoding="async" src="{{ asset('assets/img/pdf-icon.jpg') }}" alt="PDF" width="40"> 
                            @elseif(str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel'))
                                <img loading="lazy" decoding="async" src="{{ asset('assets/img/excel-icon.png') }}" alt="Excel" width="40"> 
                            @else
                                <img loading="lazy" decoding="async" src="{{ asset('images/file-icon.png') }}" alt="File" width="40"> 
                            @endif

                            <div class="imagedata">
                                <p>{{ $media->file_name }}</p>
                                <p>{{ strtoupper($media->mime_type) }} • {{ number_format($media->size / 1024, 1) }} KB</p>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="buttonbox">
                    <a href="{{ route('leads-deleteMedia') }}?media_id={{ $media->id }}"
                    onclick="return confirm('Do you really want to delete?')">
                    <img loading="lazy" decoding="async" src="{{ asset('assets/img/deletbox.svg') }}">
                    </a>
                </div>
            </li>
            @endforeach 
        </ul>
        @endif