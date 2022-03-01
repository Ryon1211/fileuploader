<?php

namespace App\Models;

use App\Models\File;
use App\Models\UploadLink as Link;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'upload_link_id',
        'sender',
        'message',
        'expire_date',
    ];

    protected $appends = [
        'upload_status'
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function uploadLink()
    {
        return $this->belongsTo(Link::class);
    }
}
