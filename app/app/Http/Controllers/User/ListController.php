<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Mylist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('mylists.user_id', Auth::user()->id)
            ->leftJoin('mylists', 'users.id', '=', 'mylists.registered_user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'mylists.user_id',
            )
            ->sortOrder($request->orderby)
            ->distinct()
            ->paginate(10);

        $users->appends(\QueryParamsUtil::appendQueryParams($request));

        return view('user.list', ['users' => $users]);
    }

    public function register(Request $request)
    {
        $content = $request->getContent() ?: '';
        $json = json_decode($content, true) ?? [];
        $userId = $json['id'] ?? [];

        $list = MyList::where('registered_user_id', $userId)
            ->where('user_id', Auth::user()->id)->first();

        if ($list) {
            $list->delete();
        } else {
            MyList::create([
                'user_id' => Auth::user()->id,
                'registered_user_id' => $userId,
            ]);
        }

        return response()->json(['message' => $list]);
    }

    public function search(Request $request)
    {
        $keyword = $request->keyword ?? '';
        if (empty($keyword)) {
            return redirect(route('user.list'));
        }

        $users = User::whereNotIn('users.id', [Auth::user()->id])
            ->where(function ($query) use ($keyword) {
                $query
                    ->orWhere('email', $keyword)
                    ->searchName($keyword);
            })
            ->leftJoin('mylists', function ($query) {
                $query->on('users.id', '=', 'mylists.registered_user_id')
                    ->where('mylists.user_id', Auth::user()->id);
            })
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'mylists.user_id',
            )
            ->sortOrder($request->orderby)
            ->distinct()
            ->paginate(10);

        return view('user.list', ['users' => $users, 'keyword' => $keyword]);
    }

    public function registeredUserSearch(Request $request)
    {
        $content = $request->getContent() ?: '';
        $json = json_decode($content, true) ?? [];
        $keyword = $json['search'] ?? [];

        $users = User::where('mylists.user_id', Auth::user()->id)
            ->where(function ($query) use ($keyword) {
                if (!empty($keyword)) {
                    return $query->where(function ($query) use ($keyword) {
                        $query
                            ->orWhere('email', $keyword)
                            ->searchName($keyword);
                    });
                }
            })
            ->leftJoin('mylists', 'users.id', '=', 'mylists.registered_user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
            )
            ->distinct()
            ->get();

        return response()->json(['users' => $users]);
    }
}
