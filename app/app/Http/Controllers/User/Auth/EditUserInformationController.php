<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EditUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditUserInformationController extends Controller
{
    /**
     * Display the user information view.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('user.account', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\EditUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(int $id, EditUserRequest $request)
    {
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()
            ->route('user.account', ['id' => $id])
            ->with('message', 'アカウント情報を更新しました');
    }
}
