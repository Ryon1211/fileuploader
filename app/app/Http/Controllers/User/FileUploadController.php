<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadLinkRequest;
use App\Http\Requests\UploadFilesRequest;
use App\Mail\SendUploadLinkEmail;
use App\Models\File;
use App\Models\Upload;
use App\Models\UploadLink;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
        $uploadLink = UploadLink::create([
            'user_id' => Auth::user()->id,
            'path' => $key,
            'title' => $request->title,
            'expire_date' => \ExpireDateUtil::generateExpireDatetime($request->expire_date),
        ]);

        $uploadUrl = route('user.upload', ['key' => $key]);
        $title = 'アップロードリンクを新規作成しました';
        $message = 'ファイルをアップロードしてほしい人に、以下のリンクを教えてあげましょう。';

        $userId = $request->user;
        $userMessage = $request->message ?? '';
        if ($userId && $uploadLink) {
            $toSendUser = User::where('id', $userId)
                ->select('name', 'email')->first();

            $toName = $toSendUser->name;
            Mail::to($toSendUser->email)
                ->send(new SendUploadLinkEmail(
                    $toName,
                    Auth::user()->name,
                    $userMessage,
                    $uploadUrl
                ));

            $message = "{$toName}さんに、リンクを掲載したメールが送信されました。";
        }

        $request->session()->regenerateToken();

        return redirect()
            ->route('user.create.upload')
            ->with('options', \DateOptionsConstants::EXPIRE_OPTIONS)
            ->with('url', $uploadUrl)
            ->with('title', $title)
            ->with('message', $message)
            ->with('userMessage', $userMessage);
    }

    public function showUploadForm(string $key)
    {
        // linkのレコードを取得
        $link = UploadLink::where('path', $key)->first();

        if ($link) {
            $upload = Upload::where('upload_link_id', $link->id)->first();
            $expiredDate = \ExpireDateUtil::checkExpireDate($link->expire_date);

            $showForm = $expiredDate && !$upload;
            $message = !$expiredDate ? \MessageConstants::ERROR['linkExpired'] : '';

            if ($upload) {
                $files = File::where('upload_id', $upload->id)->get();
                $upload['expire_date'] = $upload->expire_date;
            }
        }

        return view('user.upload', [
            'options' => \DateOptionsConstants::EXPIRE_OPTIONS,
            'showForm' => $showForm ?? false,
            'path' => $key ?? '',
            'upload_information' => $upload ?? [],
            'files' => $files ?? [],
            'message' => $message ?? \MessageConstants::ERROR['linkDisabled'],
        ]);
    }

    public function uploadFiles(UploadFilesRequest $request, string $key)
    {
        // database Uploadに情報を登録
        DB::beginTransaction();
        try {
            $upload = Upload::create([
                'upload_link_id' => UploadLink::where('path', $key)->first()->id,
                'sender' => $request->sender,
                'message' => $request->message,
                'expire_date' => \ExpireDateUtil::generateExpireDatetime($request->expire_date),
            ]);

            foreach ($request->file('file') as $file) {
                $originalName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $fileSize = $file->getSize();
                $filePath = Storage::putFile('public/upload', $file);

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
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            abort(500);
        }

        $request->session()->regenerateToken();

        return redirect()->route('user.upload', ['key' => $key]);
    }
}
