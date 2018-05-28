<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Notes\Controllers;

use FI\Events\NoteCreated;
use FI\Http\Controllers\Controller;
use FI\Modules\Notes\Models\Note;

class NoteController extends Controller
{
    public function create()
    {
        $model = request('model');

        $object = $model::find(request('model_id'));

        $note = $object->notes()->create(['note' => request('note'), 'user_id' => auth()->user()->id, 'private' => request('isPrivate')]);

        if (auth()->user()->client_id)
        {
            event(new NoteCreated($note));
        }

        return view('notes._notes_list')
            ->with('object', $object)
            ->with('showPrivateCheckbox', request('showPrivateCheckbox'));
    }

    public function delete()
    {
        Note::destroy(request('id'));
    }
}