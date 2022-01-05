<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
    private const MESSAGE = [
        'expired' => 'ファイルの有効期限が切れています。',
        'notFound' => 'ファイルが指定されていないか、存在していません。',
    ];

    private const OPTIONS = [
        '0' => '期限なし',
        '7' => '7日',
        '5' => '5日',
        '3' => '3日',
        '1' => '1日',
    ];

    public function showCreateForm(Request $request)
    {
        $fileIds = $request->id;
        $files = [];
        $status = false;
        $options = self::OPTIONS;

        if (!empty($fileIds)) {
            foreach ($fileIds as $id) {
                $fileInfo = $this->getFileInfo((int)$id);
                $upload = $fileInfo->upload;
                $userId = $upload->uploadLink->user_id ?? 0;
                $expiredDate = $this->checkExpireDate($upload->expire_date);

                // fileの期限を確認
                if ($expiredDate && $this->checkAuthUserEqCreateUser($userId)) {
                    $files[] = $fileInfo;
                }
            }

            if (count($fileIds) === count($files)) {
                $status = true;
            }

            if ($upload->expire_date !== null) {
                $nowDate = Carbon::now();
                $targetDate = Carbon::parse($upload->expire_date);
                $diffDate = $nowDate->diffInDays($targetDate);

                $options = [
                    $upload->expire_date => 'ファイルの有効期限まで',
                ];
                foreach (self::OPTIONS as $key => $val) {
                    if ((int)$key <= $diffDate && (int)$key !== 0) {
                        $options[$key] = $val;
                    }
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
                            ->json(['message' =>  self::MESSAGE['expired']], 404);
                    }
                }
            }

            if ($fileCount === $requestFileLength) {
                return response()->json([]);
            }
        }

        return response()
            ->json(['message' =>  self::MESSAGE['notFound']], 404);
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
                // dd($request, $expireDate, $requestFileLength);
                $fileInfo = $this->getFileInfo((int)$id);
                $permission = false;
                // if ファイルが存在しているか
                if ($fileInfo) {
                    $upload = $fileInfo->upload;
                    $permission = $this->checkAuthUserEqCreateUser($upload->uploadLink->user_id);
                    $fileExpireDate = $upload->expire_date ?? 0;
                }


                // if ファイルがアップロードされる際に使われたリンク作成したユーザーなのか
                if ($permission) {
                    if ($this->checkExpireDate($fileExpireDate)) {
                        //  ダウンロードリンク作成
                        DownloadLink::create([
                            'user_id' => Auth::user()->id,
                            'file_id' => $id,
                            'query' => $key,
                            'expire_date' => $expireDate,
                        ]);
                        $fileCount++;
                    }
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
        $link = UploadLink::where('query', $key)->first();
        $upload = $link->upload()->where('upload_link_id', $link->id)->first();
        $files = $upload->files()->get();


        // 有効期限内であればダウンロードできるようにする
        $expireStatus = $this->checkExpireDate($upload->expire_date);
        $expiredDate = $this->formatShowExpireDatetime($upload->expire_date) ?? '期限なし';

        return view('user.upload-detail', [
            'upload' => $upload,
            'files' => $files,
            'expire_status' => $expireStatus,
            'expire_date' => $expiredDate,
        ]);
    }

    public function showDownload(string $key)
    {
        // file一覧を取得する
        $links = DownloadLink::with('file.upload')->where('query', $key)->get();
        // 有効期限内であればダウンロードできるようにする
        $expireStatus = $this->checkExpireDate($links[0]->expire_date);
        $expiredDate = $this->formatShowExpireDatetime($links[0]->expire_date) ?? '期限なし';

        return view('user.download', [
            'upload' => $links[0]->file->upload,
            'files' => $links,
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
                    ->json(['message' =>  self::MESSAGE['expired']], 404);
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
                            ->json(['message' =>  self::MESSAGE['expired']], 404);
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
            ->json(['message' =>  self::MESSAGE['notFound']], 404);
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
        if (array_key_exists($date, self::OPTIONS)) {
            return $date ? Carbon::now()->addDay($date) : null;
        }

        return $date;
    }

    private function formatShowExpireDatetime(?string $date): ?string
    {
        return $date ? Carbon::parse($date) : null;
    }
}
