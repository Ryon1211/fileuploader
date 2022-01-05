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
        'query',
        'message',
        'expire_date',
    ];

    public function upload()
    {
        return $this->hasOne(Upload::class, 'upload_link_id', 'id');
    }
}
