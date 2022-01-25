<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadLinkRequest;
use App\Http\Requests\UploadFilesRequest;
use App\Models\File;
use App\Models\Upload;
use App\Models\UploadLink;
use Carbon\Carbon;
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
            'expire_date' => $this->generateExpireDatetime($request->expire_date),
        ]);

        // URL生成しユーザーに通知
        session()->flash('uploadUrl', route('user.upload', ['key' => $key]));

        return redirect()
            ->route('user.create.upload')
            ->with('options', \DateOptionsConstants::EXPIRE_OPTIONS);
    }

    public function showUploadForm(string $key)
    {
        $message = '';
        $showForm = false;
        $uploadInformation = [];
        $filesInformation = '';
        $link = UploadLink::where('query', $key)->first();

        if ($link) {
            // linkの有効性を確認(期限)
            $formExpired = $this->checkExpireDate($link->expire_date);
            if ($formExpired) {
                $showForm = $formExpired;
                $message = \MessageConstants::ERROR['linkExpired'];
            }
            // linkの有効性を確認(過去のアップロード) & アップロード済みのファイル名を表示
            $upload = Upload::where('upload_link_id', $link->id)->first();
            if ($upload) {
                $files = File::where('upload_id', $upload->id)->get();
                $uploadInformation = $upload;
                $uploadInformation['expire_date'] =
                    $this->formatShowExpireDatetime($upload->expire_date)
                    ?? \DateOptionsConstants::EXPIRE_OPTIONS['0'];
                $filesInformation = $files;
                $showForm = false;
            }
        } else {
            $message = \MessageConstants::ERROR['linkDisabled'];
        }

        return view('user.upload', [
            'options' => \DateOptionsConstants::EXPIRE_OPTIONS,
            'showForm' => $showForm,
            'query' => $key,
            'upload_information' => $uploadInformation,
            'files' => $filesInformation,
            'message' => $message,
        ]);
    }

    public function uploadFiles(UploadFilesRequest $request, string $key)
    {
        // database Uploadに情報を登録
        $upload = Upload::create([
            'upload_link_id' => UploadLink::where('query', $key)->first()->id,
            'sender' => $request->sender,
            'message' => $request->message,
            'expire_date' => $this->generateExpireDatetime($request->expire_date),
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

    private function generateExpireDatetime(?string $date): ?string
    {
        return $date ? Carbon::now()->addDay($date) : null;
    }

    private function formatShowExpireDatetime(?string $date): ?string
    {
        return $date ? Carbon::parse($date) : null;
    }

    private function checkExpireDate(?string $dateTime): bool
    {
        $nowDate = Carbon::now();
        $expiredDate = Carbon::parse($dateTime);
        if ($nowDate->lte($expiredDate)) {
            return true;
        }

        return false;
    }
}
