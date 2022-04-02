<?php

namespace App\Models;

use App\Models\UploadLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'upload_link_id',
    ];

    public function uploadLink()
    {
        return $this->hasMany(UploadLink::class);
    }
}
