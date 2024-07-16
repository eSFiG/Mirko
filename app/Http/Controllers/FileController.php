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

    public function download(File $file)
    {
        if (!Gate::allows('file-action', $file)) {
            return response('It\'s not your file', 403);
        }

        $user = Auth::user();
        $filePath = storage_path("app/attachments/{$user->id}/{$file->generated_name}");
        if (file_exists($filePath)) {
            return response()->download($filePath, $file->original_name);
        }

        return response('File not found', 404);
    }

    public function update(File $file, FileRequest $request)
    {
        if (!Gate::allows('file-action', $file)) {
            return response('It\'s not your file', 403);
        }

        $user = Auth::user();
        $file_request = $request->file('file');

        $filePath = storage_path("app/attachments/{$user->id}/{$file->generated_name}");
        if (file_exists($filePath)) {
            if ($file->extension() != $file_request->getClientOriginalExtension()) {
                return response('File extensions are different');
            }

            $file_request->storeAs('attachments/'.$user->id, $file->generated_name);
            $file->original_name = $file_request->getClientOriginalName();
            $file->update();

            return response('File updated');
        }

        return response('File not found', 404);
    }

    public function delete(File $file)
    {
        if (!Gate::allows('file-action', $file)) {
            return response('It\'s not your file', 403);
        }

        $user = Auth::user();
        $filePath = storage_path("app/attachments/{$user->id}/{$file->generated_name}");
        if (file_exists($filePath)) {
            unlink($filePath);
            $file->delete();
            return response('File deleted');
        }

        return response('File not found', 404);
    }
}
