<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UploadLink as Link;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $uploadLinks = [];

        $relationalUploadInformation = Link::with('uploads')
            ->where('user_id', Auth::user()->id)
            ->get();

        foreach ($relationalUploadInformation as $uploadInformation) {
            $upload_status = null;

            if ($uploadInformation->uploads) {
                $nowDate = Carbon::now();
                $expiredDate =
                    Carbon::parse($uploadInformation->uploads->expired_date);

                $upload_status = $nowDate->lte($expiredDate);
            }

            $uploadLinks[$uploadInformation->query] = [
                'query' => $uploadInformation->query,
                'title' => $uploadInformation->message,
                'expire_date' => $uploadInformation->expire_date,
                'created_at' => $uploadInformation->created_at,
                'upload_status' => $upload_status,
            ];
        }

        return view('user.dashboard', [
            'upload_links' => $uploadLinks,
        ]);
    }
}
