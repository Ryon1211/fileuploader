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
}
