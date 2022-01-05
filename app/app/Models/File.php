<?php

namespace App\Models;

use App\Models\Upload;
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
}
