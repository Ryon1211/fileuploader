<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function getUploadStatusAttribute(): ?bool
    {
        $uploadStatus = null;

        if ($this->expire_date !== null) {
            $nowDate = Carbon::now();
            $expiredDate = Carbon::parse($this->expire_date);

            $uploadStatus = $nowDate->lte($expiredDate);
        } else {
            $uploadStatus = true;
        }

        return $uploadStatus;
    }
}
