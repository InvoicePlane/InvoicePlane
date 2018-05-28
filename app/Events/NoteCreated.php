<?php

namespace FI\Events;

use FI\Modules\Notes\Models\Note;
use Illuminate\Queue\SerializesModels;

class NoteCreated extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Note $note)
    {
        $this->note = $note;
    }
}
