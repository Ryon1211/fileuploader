<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\File;
use App\Models\DownloadLink;
use App\Models\UploadLink;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileDownloadController extends Controller
{
    public function showCreateForm(Request $request)
    {
        $fileIds = $request->id ?? [];
        $options = \DateOptionsConstants::EXPIRE_OPTIONS;

        $files = File::join('uploads', 'uploads.id', '=', 'files.upload_id')
            ->join('upload_links', 'upload_links.id', '=', 'uploads.upload_link_id')
            ->whereIn('files.id', $fileIds)
            ->where('upload_links.user_id', Auth::user()->id)
            ->where(
                function ($query) {
                    $query->orWhere('uploads.expire_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhere('uploads.expire_date', null);
                }
            )
            ->select(
                'files.id as file_id',
                'files.name',
                'files.type',
                'uploads.expire_date',
                'upload_links.user_id'
            )
            ->orderBy('file_id', 'asc')
            ->get();

        $status = count($fileIds) === count($files);

        if (!empty($fileIds) && $files[0]->expire_date !== null) {
            $nowDate = Carbon::now();
            $targetDate = Carbon::parse($files[0]->expire_date->expire_date);
            $diffDate = $nowDate->diffInDays($targetDate);

            $options = [$files[0]->expire_date => 'ファイルの有効期限まで'];
            foreach (\DateOptionsConstants::EXPIRE_OPTIONS as $key => $val) {
                if ((int)$key <= $diffDate && (int)$key !== 0) {
                    $options[$key] = $val;
                }
            }
        }

        return view(
            'user.create-download-link',
            [
                'status' => $status,
                'files' => $files,
                'options' => $options,
            ]
        );
    }

    public function checkBeforeCreateLink(Request $request)
    {
        $content = $request->getContent();
        $json = json_decode($content, true) ?? [];
        $fileIds = $json['file'] ?? [];
        $requestFileLength = count($fileIds);
        $fileCount = 0;

        // $fileInfoでエラー起きた場合の処理を考える
        if (0 < $requestFileLength) {
            foreach ($fileIds as $id) {
                $fileInfo = $this->getFileInfo((int)$id);
                $permission = false;
                // if ファイルが存在しているか
                if ($fileInfo) {
                    $upload = $fileInfo->upload;
                    $permission = $this->checkAuthUserEqCreateUser($upload->uploadLink->user_id);
                    $expireDate = $upload->expire_date ?? 0;
                }
                // if ファイルがアップロードされる際に使われたリンク作成したユーザーなのか
                if ($permission) {
                    if ($this->checkExpireDate($expireDate)) {
                        //  ダウンロード処理
                        $fileCount++;
                    } else {
                        return response()
                            ->json(['message' =>  \MessageConstants::ERROR['fileExpired']], 404);
                    }
                }
            }

            if ($fileCount === $requestFileLength) {
                return response()->json([]);
            }
        }

        return response()
            ->json(['message' =>  \MessageConstants::ERROR['fileNotFound']], 404);
    }

    public function createLink(Request $request)
    {
        $fileIds = $request->file ?? [];
        $requestFileLength = count($fileIds);
        $fileCount = 0;

        $key = Str::random(20);
        $expireDate = $this->generateExpireDatetime($request->expire_date);

        if (0 < $requestFileLength) {
            // DB処理
            foreach ($fileIds as $id) {
                $fileInfo = $this->getFileInfo((int)$id);
                $permission = false;
                // if ファイルが存在しているか
                if ($fileInfo) {
                    $upload = $fileInfo->upload;
                    $permission = $this->checkAuthUserEqCreateUser($upload->uploadLink->user_id);
                    $fileExpireDate = $upload->expire_date ?? 0;
                }

                // if ファイルがアップロードされる際に使われたリンク作成したユーザーなのか
                if ($permission && $this->checkExpireDate($fileExpireDate)) {
                    //  ダウンロードリンク作成
                    if (!$fileCount) {
                        $download_link = DownloadLink::create([
                            'upload_link_id' => $upload->uploadLink->id,
                            'query' => $key,
                            'expire_date' => $expireDate,
                        ]);
                    }

                    Download::create([
                        'download_link_id' => $download_link->id,
                        'file_id' => $id,
                    ]);
                    $fileCount++;
                }
            }

            if ($fileCount === $requestFileLength) {
                session()->flash('downloadUrl', route('user.download', ['key' => $key]));

                return redirect()
                    ->route('user.download', ['key' => $key]);
            }

            // fix! エラーのリダイレクト先を変更
            return redirect()
                ->route('user.create.download');
        }
    }

    public function showFiles(string $key)
    {
        // file一覧を取得する
        $link = UploadLink::with('upload.files')->where('query', $key)->first();
        // upload_link_idに紐付いたレコードかつ有効期銀内のデータのみ取得する
        $downloads = DownloadLink::with('download.file')
            ->where('upload_link_id', $link->id)
            ->where(
                function ($query) {
                    $query->orWhere('expire_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhere('expire_date', null);
                }
            )->paginate(5);

        // 有効期限内であればダウンロードできるようにする
        $expireStatus = $this->checkExpireDate($link->upload->expire_date);
        $expiredDate = $this->formatShowExpireDatetime($link->upload->expire_date) ?? '期限なし';

        return view('user.upload-detail', [
            'upload' => $link->upload,
            'expire_status' => $expireStatus,
            'expire_date' => $expiredDate,
            'download_links' => $downloads,
        ]);
    }

    public function showDownload(string $key)
    {
        // file一覧を取得する
        $links = DownloadLink::with('download.file.upload.uploadLink')->where('query', $key)->get();
        // 有効期限内であればダウンロードできるようにする
        $expireStatus = $this->checkExpireDate($links[0]->expire_date);
        $expiredDate = $this->formatShowExpireDatetime($links[0]->expire_date) ?? '期限なし';

        return view('user.download', [
            'upload' => $links[0]->download[0],
            'files' => $links[0]->download,
            'expire_status' => $expireStatus,
            'expire_date' => $expiredDate,
        ]);
    }

    public function downloadFile(Request $request): mixed
    {
        $json = json_decode($request->getContent(), true) ?? [];
        $fileIds = $json['file'] ?? [];
        $downloadKey = $json['key'] ?? '';
        $requestFileLength = count($fileIds);

        if ($requestFileLength === 1) {
            $fileInfo = $this->getFileInfo((int)$fileIds[0]);
            $permission = false;
            // if ファイルが存在しているか
            if ($fileInfo) {
                $upload = $fileInfo->upload;
                $permission = $this->checkAuthUserEqCreateUser($upload->uploadLink->user_id);
                $expireDate = $upload->expire_date ?? 0;

                if (!$permission) {
                    $downloadInfo = $this->getDownloadInfo($fileInfo->id, $downloadKey);
                    $permission = $downloadInfo;
                    $expireDate = $downloadInfo->expire_date ?? 0;
                }
            }
            // if ファイルがアップロードされる際に使われたリンク作成したユーザーなのか
            if ($permission) {
                if ($this->checkExpireDate($expireDate)) {
                    //  ダウンロード処理
                    return response()->download(
                        Storage::path($fileInfo->path),
                        $fileInfo->name,
                        ['Content-Type' => Storage::mimeType($fileInfo->path)]
                    );
                }
                return response()
                    ->json(['message' =>  \MessageConstants::ERROR['fileExpired']], 404);
            }
        }

        if (1 < $requestFileLength) {
            // ファイルを開いてzipを作成
            $fileName = 'download_' . Carbon::now()->format('Y-m-d_H-i-s') . '_' . Str::random(5) . '.zip';
            $savePath = storage_path('app/public/temp/' . $fileName);

            //zipファイル作成
            $zip = new \ZipArchive();
            $zip->open($savePath, \ZipArchive::CREATE);
            $fileCount = 0;

            foreach ($fileIds as $id) {
                $fileInfo = $this->getFileInfo((int)$id);
                $permission = false;
                // if ファイルが存在しているか
                if ($fileInfo) {
                    $upload = $fileInfo->upload;
                    $permission = $this->checkAuthUserEqCreateUser($upload->uploadLink->user_id);
                    $expireDate = $upload->expire_date ?? 0;

                    if (!$permission) {
                        $downloadInfo = $this->getDownloadInfo($fileInfo->id, $downloadKey);
                        $permission = $downloadInfo;
                        $expireDate = $downloadInfo->expire_date ?? 0;
                    }
                }
                // if ファイルがアップロードされる際に使われたリンク作成したユーザーなのか
                if ($permission) {
                    if ($this->checkExpireDate($expireDate)) {
                        //  ダウンロード処理
                        $zip->addFile(Storage::path($fileInfo->path), $fileInfo->name);
                        $fileCount++;
                    } else {
                        return response()
                            ->json(['message' =>  \MessageConstants::ERROR['fileExpired']], 404);
                    }
                }
            }

            if ($fileCount === $requestFileLength) {
                $zip->close();

                return response()
                    ->download(
                        $savePath,
                        $fileName,
                        ['Content-Type' => 'application/zip']
                    )
                    ->deleteFileAfterSend();
            }
        }

        return response()
            ->json(['message' =>  \MessageConstants::ERROR['fileNotFound']], 404);
    }

    private function getFileInfo(int $id): File | bool
    {
        return File::with('upload.uploadLink')->find($id) ?? false;
    }

    private function getDownloadInfo(int $id, string $key): DownloadLink | bool
    {
        return DownloadLink::where([['file_id', '=', $id], ['query', '=', $key]])->first()
            ?? false;
    }

    private function checkAuthUserEqCreateUser(int $userId): bool
    {
        $authorizedUserId = Auth::user()->id ?? '';

        return $authorizedUserId === $userId;
    }

    private function checkExpireDate(?string $dateTime): bool
    {
        $nowDate = Carbon::now();
        $expiredDate = Carbon::parse($dateTime);

        return $nowDate->lte($expiredDate);
    }

    private function generateExpireDatetime(?string $date): ?string
    {
        if (array_key_exists($date, \DateOptionsConstants::EXPIRE_OPTIONS)) {
            return $date ? Carbon::now()->addDay($date) : null;
        }

        return $date;
    }

    private function formatShowExpireDatetime(?string $date): ?string
    {
        return $date ? Carbon::parse($date) : null;
    }
}
