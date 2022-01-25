<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\UploadLink;
use App\Models\DownloadLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function deleteFile(Request $request)
    {
        $files = File::with('upload.uploadLink')
            ->whereIn('id', $request->id)
            ->whereHas('upload.uploadLink', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->get();

        if ($files->isEmpty()) {
            return back()->withErrors(['error' => \MessageConstants::ERROR['fileNotFound']]);
        }

        foreach ($files as $file) {
            $uploadId = $file->upload->id;
            $file->delete();
        }

        // ファイルがすべて選ばれた場合はuploadLinkとuploadもdownloadLink削除
        if (File::where('upload_id', $uploadId)->count() === 0) {
            $file->upload->uploadLink->delete();
            return redirect()->route('user.dashboard');
        }

        return back()->withErrors(['error' => \MessageConstants::SUCCESS['fileDeleted']]);
    }

    public function deleteUploadLink(Request $request)
    {
        $uploadLinks = UploadLink::whereIn('id', $request->id)
            ->where('user_id', Auth::user()->id)->get();

        if ($uploadLinks->isEmpty()) {
            $uploadLinks->each(fn ($uploadLink) => $uploadLink->delete());

            return redirect()->route('user.dashboard');
        }

        return redirect()->route('user.dashboard')
            ->withErrors(['error' => \MessageConstants::ERROR['linkNotFound']]);
    }

    public function deleteDownloadLink(Request $request)
    {
        $downloadLinks = DownloadLink::with('uploadLink')
            ->whereIn('id', $request->id)
            ->whereHas('uploadLink', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->get();

        // Linkの存在を確認して、userの権限を確認
        if ($downloadLinks->isEmpty()) {
            return back()->withErrors(['error' => \MessageConstants::ERROR['fileNotFound']]);
        }

        $downloadLinks->each(fn ($downloadLink) => $downloadLink->delete());

        return back()->withErrors(['error' => \MessageConstants::SUCCESS['fileDeleted']]);
    }
}
