<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\DownloadLink;
use App\Models\UploadLink;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function showFiles(String $key)
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

    public function downloadFile(Request $request, int $id)
    {
        $file = File::find($id);

        if($file){
            $upload = $file->upload()->first();
            $userId = $upload->uploadLink()->first()->user_id;
            $expiredDate = $this->checkExpireDate($upload->expire_date);
            $download = DownloadLink::where('query', $request->input('k'))->first();
            $authorizedUserId = Auth::user()->id ?? '';
            // upload_linkを発行したユーザーであれば、getパラメータなしで判定
            // それ以外のユーザーはgetパラメータなしでは、ダウンロード不可
            if($expiredDate && ($authorizedUserId === $userId || $download)){
                $path = $file->path;
                $name = $file->name;
                $mimeType = Storage::mimeType($path);
                $headers =[['Content-Type' => $mimeType]];
                return Storage::download($path, $name, $headers);
            }
        }
        return abort(404);
    }

    private function checkExpireDate(?string $dateTime): bool
    {
        $nowDate = Carbon::now();
        $expiredDate = Carbon::parse($dateTime);

        return $nowDate->lte($expiredDate);
    }

    private function formatShowExpireDatetime(?string $date): ?string
    {
        return $date ? Carbon::parse($date) : null;
    }
}
