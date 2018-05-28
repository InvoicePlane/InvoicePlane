<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Attachments\Controllers;

use FI\Events\CheckAttachment;
use FI\Http\Controllers\Controller;
use FI\Modules\Attachments\Models\Attachment;

class AttachmentController extends Controller
{
    public function download($urlKey)
    {
        $attachment = Attachment::where('url_key', $urlKey)->firstOrFail();

        return response()->download($attachment->attachable->attachment_path . '/' . $attachment->filename);
    }

    public function ajaxList()
    {
        $model = request('model');

        $object = $model::find(request('model_id'));

        return view('attachments._table')
            ->with('model', request('model'))
            ->with('object', $object);
    }

    public function ajaxDelete()
    {
        Attachment::destroy(request('attachment_id'));
    }

    public function ajaxModal()
    {
        return view('attachments._modal_attach_files')
            ->with('model', request('model'))
            ->with('modelId', request('model_id'));
    }

    public function ajaxUpload()
    {
        $model = request('model');

        $object = $model::find(request('model_id'));

        event(new CheckAttachment($object));
    }

    public function ajaxAccessUpdate()
    {
        $attachment = Attachment::find(request('attachment_id'));

        $attachment->client_visibility = request('client_visibility');

        $attachment->save();
    }
}