<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UploadLink as Link;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $uploadLinks = Link::with('uploads')
            ->where('user_id', Auth::user()->id)
            ->paginate(5);

        return view('user.dashboard', [
            'upload_links' => $uploadLinks,
        ]);
    }
}
