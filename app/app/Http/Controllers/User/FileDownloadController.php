<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\File;
use App\Models\DownloadLink;
use App\Models\UploadLink;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileDownloadController extends Controller
{
    public function showCreateForm(Request $request)
    {
        $fileIds = $request->id ?? [];
        $options = \DateOptionsConstants::EXPIRE_OPTIONS;

        $files = File::with('upload.uploadLink')
            ->whereIn('id', $fileIds)
            ->AuthUser(Auth::user()->id)
            ->BeforeExpired()->get();

        $fileExists = !$files->isEmpty();

        if ($fileExists && $files[0]->upload->expire_date !== null) {
            $diffDate = Carbon::now()
                ->diffInDays(Carbon::parse($files[0]->upload->expire_date));
            $options = [$files[0]->upload->expire_date => 'ファイルの有効期限まで'];

            foreach (\DateOptionsConstants::EXPIRE_OPTIONS as $key => $val) {
                if ((int)$key <= $diffDate && (int)$key !== 0) {
                    $options[$key] = $val;
                }
            }
        }

        return view(
            'user.create-download-link',
            [
                'status' => $fileExists,
                'files' => $files,
                'options' => $options,
            ]
        );
    }

    public function checkBeforeCreateLink(Request $request)
    {
        $content = $request->getContent() ?: '';
        $json = json_decode($content, true) ?? [];
        $fileIds = $json['file'] ?? [];

        $files =  File::with('upload.uploadLink')
            ->whereIn('id', $fileIds)->AuthUser(Auth::user()->id)->get();

        if ($files->isEmpty()) {
            return response()
                ->json(['message' => \MessageConstants::ERROR['fileNotFound']], 404);
        }

        foreach ($files as $file) {
            if (!\ExpireDateUtil::checkExpireDate($file->upload->expire_date)) {
                return response()
                    ->json(['message' => \MessageConstants::ERROR['fileExpired']], 404);
            }
        }

        return response()->json([]);
    }

    public function createLink(Request $request)
    {
        $fileIds = $request->file ?? [];

        $key = Str::random(20);
        $expireDate = \ExpireDateUtil::generateExpireDatetime($request->expire_date);

        $files = File::with('upload.uploadLink')
            ->whereIn('id', $fileIds)
            ->AuthUser(Auth::user()->id)
            ->BeforeExpired()->get();

        if ($files->isEmpty()) {
            return back();
        }

        DB::beginTransaction();
        try {
            $downloadLink = DownloadLink::create([
                'upload_link_id' => $files[0]->upload->uploadLink->id,
                'path' => $key,
                'expire_date' => $expireDate,
            ]);

            foreach ($files as $file) {
                Download::create([
                    'download_link_id' => $downloadLink->id,
                    'file_id' => $file->id,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }

        session()->flash('downloadUrl', route('user.download', ['key' => $key]));
        return redirect()->route('user.download', ['key' => $key]);
    }

    public function showFiles(string $key)
    {
        // file一覧を取得する
        $link = UploadLink::with('upload.files')->where('path', $key)->first();
        // upload_link_idに紐付いたレコードかつ有効期銀内のデータのみ取得する
        $downloads = DownloadLink::with('download.file')
            ->where('upload_link_id', $link->id)
            ->where(function ($query) {
                $query->where('expire_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhere('expire_date', null);
            })->paginate(5);

        // 有効期限内であればダウンロードできるようにする
        $expireStatus = \ExpireDateUtil::checkExpireDate($link->upload->expire_date);

        return view('user.upload-detail', [
            'upload' => $link->upload,
            'expire_status' => $expireStatus,
            'download_links' => $downloads,
        ]);
    }

    public function showDownload(string $key)
    {
        // file一覧を取得する
        $link = DownloadLink::with('download.file.upload.uploadLink')->where('path', $key)->first();
        // 有効期限内であればダウンロードできるようにする
        $expireStatus = \ExpireDateUtil::checkExpireDate($link->expire_date);

        return view('user.download', [
            'upload' => $link->download[0],
            'files' => $link->download,
            'expire_status' => $expireStatus,
            'expire_date' => $link->expire_date,
        ]);
    }

    public function downloadFile(Request $request): mixed
    {
        $json = json_decode($request->getContent(), true) ?? [];
        $fileIds = $json['file'] ?? [];
        $downloadKey = $json['key'] ?? '';
        $userId = Auth::user()->id ?? 0;

        $files = File::whereIn('id', $fileIds)
            ->where(
                function ($query) use ($userId, $downloadKey) {
                    $query
                        ->AuthUser($userId)
                        ->orWhereHas('download.downloadLink', function ($query) use ($downloadKey) {
                            $query->where('path', $downloadKey);
                        });
                }
            )
            ->BeforeExpired();

        $fileCounts = $files->count();

        if ($fileCounts === 0) {
            return response()
                ->json(['message' => \MessageConstants::ERROR['fileNotFound']], 404);
        }

        if ($fileCounts === 1) {
            $fileInfo = $files->first();
            $file = Storage::path($fileInfo->path);
            $name = $fileInfo->name;
            $headers =  ['Content-Type' => Storage::mimeType($fileInfo->path)];
            $afterDelete = false;
        }

        if (1 < $fileCounts) {
            $fileInfos = $files->get();
            $fileName = 'download_' . Carbon::now()->format('Y-m-d_H-i-s') . '_' . Str::random(5) . '.zip';
            $savePath = storage_path('app/public/temp/' . $fileName);
            $zip = new \ZipArchive();
            $zip->open($savePath, \ZipArchive::CREATE);

            foreach ($fileInfos as $file) {
                $zip->addFile(Storage::path($file->path), $file->name);
            }

            $zip->close();

            $file = $savePath;
            $name = $fileName;
            $headers = ['Content-Type' => 'application/zip'];
            $afterDelete = true;
        }

        return response()->download($file, $name, $headers)
            ->deleteFileAfterSend($afterDelete);
    }
}
