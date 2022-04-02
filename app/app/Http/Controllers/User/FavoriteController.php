<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Favorite::where('favorites.user_id', Auth::user()->id)
            ->leftJoin('upload_links', 'favorites.upload_link_id', '=', 'upload_links.id')
            ->leftJoin('uploads', 'upload_links.id', '=', 'uploads.upload_link_id')
            ->select(
                'upload_links.id as link_id',
                'upload_links.path',
                'upload_links.title',
                'upload_links.created_at',
                'upload_links.expire_date',
                'uploads.id as upload_id',
                'uploads.expire_date as file_expire_date'
            )
            ->paginate(10);

        return view('user.favorite', [
            'favorites' => $favorites,
        ]);
    }

    public function register(Request $request)
    {
        $content = $request->getContent() ?: '';
        $json = json_decode($content, true) ?? [];
        $linkId = $json['id'] ?? [];

        $favorite = Favorite::where('upload_link_id', $linkId)->first();

        if ($favorite && $favorite->user_id === Auth::user()->id) {
            $favorite->delete();
        } else {
            Favorite::create([
                'user_id' => Auth::user()->id,
                'upload_link_id' => $linkId,
            ]);
        }

        return response()->json(['message' => $favorite]);
    }
}
