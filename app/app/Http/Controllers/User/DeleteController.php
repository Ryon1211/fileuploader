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
        foreach ($request->id as $id) {
            $file = File::with('upload.uploadLink')->find($id);
            $uploadId = $file->upload->id;
            $userId = $file->upload->uploadLink->user_id;

            if ($file && $userId !== Auth::user()->id) {
                return back()->withErrors(['error' => \MessageConstants::ERROR['fileNotFound']]);
            }

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

        if (empty($uploadLinks->items)) {
            $uploadLinks->each(fn ($uploadLink) => $uploadLink->delete());

            return redirect()->route('user.dashboard');
        }

        return redirect()->route('user.dashboard')
            ->withErrors(['error' => \MessageConstants::ERROR['linkNotFound']]);
    }

    public function deleteDownloadLink(Request $request)
    {
        $downloadLinks = DownloadLink::with('uploadLink')
            ->whereIn('id', $request->id)->get();
        $userIds = $downloadLinks->pluck('uploadLink.user_id');

        // Linkの存在を確認して、userの権限を確認
        if (!empty($downloadLinks->items)) {
            return back()->withErrors(['error' => \MessageConstants::ERROR['fileNotFound']]);
        }

        foreach ($userIds as $id) {
            if ($id !== Auth::user()->id) {
                return back()->withErrors(['error' => \MessageConstants::ERROR['fileNotFound']]);
            }
        }

        $downloadLinks->each(fn ($downloadLink) => $downloadLink->delete());

        return back()->withErrors(['error' => \MessageConstants::SUCCESS['fileDeleted']]);
    }
}
