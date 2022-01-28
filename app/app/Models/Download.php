<?php

namespace App\Models;

use App\Models\DownloadLink;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'download_link_id',
        'file_id',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function downloadLink()
    {
        return $this->belongsTo(DownloadLink::class);
    }
}
