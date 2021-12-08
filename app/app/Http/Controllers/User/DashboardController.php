<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UploadLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $uploadLinks = UploadLink::where('user_id', Auth::user()->id)->get();
        return view('user.dashboard', ['upload_links' => $uploadLinks]);
    }
}
