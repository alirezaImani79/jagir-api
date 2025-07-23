<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FileUploadController
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'attachmentable_type' => ['required', Rule::in('accommodation', 'user')],
            'attachmentable_id' => ['required', 'integer']
        ]);

        $path = $request->file('file')->store(sprintf('uploads/%s', $request->input('attachmentable_type')));

        $attachmentableClass = null;
        switch($request->input('attachmentable_type')) {
            case 'user':
                $attachmentableClass = User::class;
                break;
            case 'accommodation':
                $attachmentableClass = Accommodation::class;
                break;
            default:
                $attachmentableClass = Accommodation::class;
        }

        $attachment = Attachment::create([
            'path' => $path,
            'type' => 'image',
            'attachmentable_type' => $attachmentableClass,
            'attachmentable_id' => $request->input('attachmentable_id')
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'attachment' => $attachment,
        ]);
    }
}
