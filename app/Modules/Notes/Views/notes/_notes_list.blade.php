@foreach ($object->notes()->protect(auth()->user())->orderBy('created_at', 'desc')->get() as $note)
    <div class="direct-chat-msg" id="note-{{ $note->id }}">

        <div class="d-flex">

            <div class="d-none d-sm-block mr-3 mr-lg-4 h-100">
                <img class="direct-chat-img" src="{{ profileImageUrl($note->user) }}" alt="message user image">
            </div>

            <div class="h-auto">

                <div class="direct-chat-text">
                    {!! $note->formatted_note !!}
                </div>

                <div class="direct-chat-info d-flex small text-muted mt-3 mt-lg-4">

                    <div class="direct-chat-name mr-3 mr-lg-4">{{ $note->user->name }}</div>

                    <div class="direct-chat-timestamp mr-3 mr-lg-4">
                        {{ $note->formatted_created_at }}
                    </div>

                    <div class="direct-chat-scope mr-3 mr-lg-4">
                        @if (!auth()->user()->client_id)
                            <a href="javascript:void(0)" class="delete-note" data-note-id="{{ $note->id }}">
                                @lang('ip.delete')
                            </a>
                        @endif
                    </div>

                    @if (isset($showPrivateCheckbox) and $showPrivateCheckbox == true)
                        <div class="direct-chat-scope">
                            @if ($note->private)
                                <div class="badge badge-danger">@lang('ip.private')</div>
                            @else
                                <div class="badge badge-success">@lang('ip.public')</div>
                            @endif
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>
@endforeach
