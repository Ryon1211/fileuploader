<?php

namespace App\Models;

use App\Models\Upload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadLink extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'path',
        'message',
        'expire_date',
    ];

    public function upload()
    {
        return $this->hasOne(Upload::class);
    }

    public function scopeSearchKeyword($query, $keyword)
    {
        $convertKeyword = mb_convert_kana($keyword, 's');
        $keywords = preg_split('/[\s]+/', $convertKeyword, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($keywords as $word) {
            $query->where('upload_links.message', 'like', "%$word%");
        }
    }

    public function scopeSortOrder($query, $order)
    {
        if ($order === null || $order === 'create_asc') {
            $query->orderBy('created_at');
        } elseif ($order === 'create_desc') {
            $query->orderByDesc('created_at');
        }

        if ($order === 'title_asc') {
            $query->orderBy('message');
        } elseif ($order === 'title_desc') {
            $query->orderByDesc('message');
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
            $query->orderBy('expire_date');
        } elseif ($order === 'expire_desc') {
            $query->orderByDesc('expire_date');
        }
    }
}
