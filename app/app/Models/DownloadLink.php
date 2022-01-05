<?php

namespace App\Models;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadLink extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'file_id',
        'query',
        'message',
        'expire_date',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
