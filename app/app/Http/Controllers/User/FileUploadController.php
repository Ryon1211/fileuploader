<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadLinkRequest;
use App\Http\Requests\UploadFilesRequest;
use App\Models\File;
use App\Models\Upload;
use App\Models\UploadLink;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function showCreateForm()
    {
        return view(
            'user.create-upload-link',
            ['options' => \DateOptionsConstants::EXPIRE_OPTIONS]
        );
    }

    public function createLink(UploadLinkRequest $request)
    {
        $key = Str::random(20);

        UploadLink::create([
            'user_id' => Auth::user()->id,
            'query' => $key,
            'message' => $request->message,
            'expire_date' => \ExpireDateUtil::generateExpireDatetime($request->expire_date),
        ]);

        return redirect()
            ->route('user.create.upload', [
                'options' => \DateOptionsConstants::EXPIRE_OPTIONS
            ])->with('uploadUrl', route('user.upload', ['key' => $key]));
    }

    public function showUploadForm(string $key)
    {
        // linkのレコードを取得
        $link = UploadLink::where('query', $key)->first();

        if ($link) {
            $upload = Upload::where('upload_link_id', $link->id)->first();
            $expiredDate = \ExpireDateUtil::checkExpireDate($link->expire_date);

            $showForm = $expiredDate && !$upload;
            $message = !$expiredDate ? \MessageConstants::ERROR['linkExpired'] : '';

            if ($upload) {
                $files = File::where('upload_id', $upload->id)->get();
                $upload['expire_date'] =
                    \ExpireDateUtil::formatShowExpireDatetime($upload->expire_date)
                    ?? \DateOptionsConstants::EXPIRE_OPTIONS['0'];
            }
        }

        return view('user.upload', [
            'options' => \DateOptionsConstants::EXPIRE_OPTIONS,
            'showForm' => $showForm ?? false,
            'query' => $key ?? '',
            'upload_information' => $upload ?? [],
            'files' => $files ?? [],
            'message' => $message ?? \MessageConstants::ERROR['linkDisabled'],
        ]);
    }

    public function uploadFiles(UploadFilesRequest $request, string $key)
    {
        // database Uploadに情報を登録
        $upload = Upload::create([
            'upload_link_id' => UploadLink::where('query', $key)->first()->id,
            'sender' => $request->sender,
            'message' => $request->message,
            'expire_date' => \ExpireDateUtil::generateExpireDatetime($request->expire_date),
        ]);

        foreach ($request->file('file') as $file) {
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            $hashName = $file->hashName();

            $filePath = $file->storeAs('public/upload', $hashName);

            if ($filePath) {
                //database Filesにファイル情報を保存
                File::create([
                    'upload_id' => $upload->id,
                    'path' => $filePath,
                    'name' => $originalName,
                    'type' => $mimeType,
                    'size' => $fileSize,
                ]);
            }
        }

        return redirect()->route('user.upload', ['key' => $key]);
    }
}
