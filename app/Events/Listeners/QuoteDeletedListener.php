<?php

namespace FI\Events\Listeners;

use FI\Events\QuoteDeleted;
use FI\Modules\Groups\Models\Group;

class QuoteDeletedListener
{
    public function __construct()
    {
        //
    }

    public function handle(QuoteDeleted $event)
    {
        foreach ($event->quote->items as $item) {
            $item->delete();
        }

        foreach ($event->quote->activities as $activity) {
            $activity->delete();
        }

        foreach ($event->quote->mailQueue as $mailQueue) {
            $mailQueue->delete();
        }

        foreach ($event->quote->notes as $note) {
            $note->delete();
        }

        $event->quote->custom()->delete();
        $event->quote->amount()->delete();

        $group = Group::where('id', $event->quote->group_id)
            ->where('last_number', $event->quote->number)
            ->first();

        if ($group) {
            $group->next_id = $group->next_id - 1;
            $group->save();
        }
    }
}
