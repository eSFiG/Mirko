<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class FileController
{
    public function create(FileRequest $request)
    {
        $user = Auth::user();
        $file = $request->file('file');

        $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('attachments/'.$user->id, $fileName);
        $fileModel = new File();
        $fileModel->fill([
            'original_name' => $file->getClientOriginalName(),
            'generated_name' => $fileName,
            'user_id' => $user->id,
        ])->save();

        return response(['file_id' => $fileModel->id]);
    }

    public function download(File $file_id)
    {
        if (!Gate::allows('file-action', $file_id)) {
            return response('It\'s not your file', 403);
        }

        $user = Auth::user();
        $filePath = storage_path("app/attachments/{$user->id}/{$file_id->generated_name}");
        if (file_exists($filePath)) {
            return response()->download($filePath, $file_id->original_name);
        }
        else {
            return response('File not found', 404);
        }
    }

    public function update(File $file_id, FileRequest $request)
    {
        if (!Gate::allows('file-action', $file_id)) {
            return response('It\'s not your file', 403);
        }

        $user = Auth::user();
        $file = $request->file('file');

        $filePath = storage_path("app/attachments/{$user->id}/{$file_id->generated_name}");
        if (file_exists($filePath)) {
            if ($file_id->extension() != $file->getClientOriginalExtension()) {
                return response('File extensions are different');
            }

            $file->storeAs('attachments/'.$user->id, $file_id->generated_name);
            $file_id->original_name = $file->getClientOriginalName();
            $file_id->update();

            return response('File updated');
        }
        else {
            return response('File not found', 404);
        }
    }

    public function delete(File $file_id)
    {
        if (!Gate::allows('file-action', $file_id)) {
            return response('It\'s not your file', 403);
        }

        $user = Auth::user();
        $filePath = storage_path("app/attachments/{$user->id}/{$file_id->generated_name}");
        if (file_exists($filePath)) {
            unlink($filePath);
            $file_id->delete();
            return response('File deleted');
        }
        else {
            return response('File not found', 404);
        }

    }
}
