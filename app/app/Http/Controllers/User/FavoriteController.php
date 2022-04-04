<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function register(Request $request)
    {
        $content = $request->getContent() ?: '';
        $json = json_decode($content, true) ?? [];
        $linkId = $json['id'] ?? [];

        $favorite = Favorite::where('upload_link_id', $linkId)
            ->where('user_id', Auth::user()->id)->first();

        if ($favorite) {
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
