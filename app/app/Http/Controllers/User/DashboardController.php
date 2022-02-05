<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\UploadLink as Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $files = null;
        $uploadLinks = null;

        if ($request->target === 'file') {
            $files = File::authUser(Auth::user()->id)
                ->leftJoin('uploads', 'files.upload_id', '=', 'uploads.id')
                ->leftJoin('upload_links', 'uploads.upload_link_id', '=', 'upload_links.id')
                ->select(
                    'files.id',
                    'files.name',
                    'upload_links.query',
                    'uploads.created_at',
                    'uploads.expire_date',
                    'uploads.id as upload_id'
                )
                ->searchKeyword($request->search)
                ->sortOrder($request->orderby)
                ->paginate(10);
            $files->appends($this->appendQueryParams($request));
        } else {
            $uploadLinks = Link::where('user_id', Auth::user()->id)
                ->leftJoin('uploads', 'upload_links.id', '=', 'uploads.upload_link_id')
                ->select(
                    'upload_links.id',
                    'upload_links.query',
                    'upload_links.message',
                    'upload_links.created_at',
                    'upload_links.expire_date',
                    'uploads.id as upload_id',
                    'uploads.expire_date as file_expire_date'
                )
                ->searchKeyword($request->search)
                ->sortOrder($request->orderby)
                ->paginate(10);

            $uploadLinks->appends($this->appendQueryParams($request));
        }

        return view('user.dashboard', [
            'upload_links' => $uploadLinks,
            'files' => $files,
        ]);
    }

    public function appendQueryParams(Request $request): array
    {
        $queryParams = [];

        if ($request->target) {
            $queryParams['target'] = $request->target;
        }

        if ($request->search) {
            $queryParams['search'] = $request->search;
        }

        if ($request->orderby) {
            $queryParams['orderby'] = $request->orderby;
        }

        return $queryParams;
    }
}
