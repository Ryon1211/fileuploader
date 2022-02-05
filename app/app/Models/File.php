<?php

namespace App\Models;

use App\Models\Upload;
use App\Models\Download;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'upload_id',
        'path',
        'name',
        'type',
        'size'
    ];

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function download()
    {
        return $this->hasMany(Download::class);
    }

    public function scopeAuthUser($query, $userId)
    {
        return $query->whereHas('upload.uploadLink', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    public function scopeBeforeExpired($query)
    {
        return $query->whereHas('upload', function ($query) {
            $query->where('uploads.expire_date', '>=', date('Y-m-d H:i:s'))
                ->orWhere('uploads.expire_date', null);
        });
    }

    public function scopeSearchKeyword($query, $keyword)
    {
        $convertKeyword = mb_convert_kana($keyword, 's');
        $keywords = preg_split('/[\s]+/', $convertKeyword, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($keywords as $word) {
            $query->where('files.name', 'like', "%$word%");
        }
    }

    public function scopeSortOrder($query, $order)
    {
        if ($order === null || $order === 'create_asc') {
            $query->orderBy('uploads.created_at');
        } elseif ($order === 'create_desc') {
            $query->orderByDesc('uploads.created_at');
        }

        if ($order === 'title_asc') {
            $query->orderBy('name');
        } elseif ($order === 'title_desc') {
            $query->orderByDesc('name');
        }

        if ($order === 'status_asc') {
            $query->orderByRaw(
                "case
                     when uploads.expire_date is not null
                     and uploads.id is not null then '1900-01-01 00:00:00'
                     when uploads.expire_date is null
                     and uploads.id is null then '9999-12-31 00:00:00'
                     else uploads.expire_date end"
            )->orderByDesc('uploads.expire_date');
        } elseif ($order === 'status_desc') {
            $query->orderByRaw(
                "case
                     when uploads.expire_date is null
                     and uploads.id is not null then '9999-12-31 00:00:00'
                     else uploads.expire_date end"
            )->orderBy('uploads.expire_date');
        }

        if ($order === 'expire_asc') {
            $query->orderBy('uploads.expire_date');
        } elseif ($order === 'expire_desc') {
            $query->orderByDesc('uploads.expire_date');
        }
    }
}
