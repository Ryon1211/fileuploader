<?php

namespace App\Models;

use App\Models\Download;
use App\Models\UploadLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadLink extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'upload_link_id',
        'path',
        'expire_date',
    ];

    public function download()
    {
        return $this->hasMany(Download::class);
    }

    public function uploadLink()
    {
        return $this->belongsTo(UploadLink::class);
    }

    public function scopeAuthUser($query, $userId)
    {
        return $query->whereHas('uploadLink', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}
