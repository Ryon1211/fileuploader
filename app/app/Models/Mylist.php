<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mylist extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'registered_user_id',
    ];

    public function scopeSearchKeyword($query, $keyword)
    {
        $convertKeyword = mb_convert_kana($keyword, 's');
        $keywords = preg_split('/[\s]+/', $convertKeyword, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($keywords as $word) {
            $query->where('name', 'like', "%$word%")
                ->orWhere('email', 'like', "%$word%");
        }
    }

    public function scopeAuthUser($query, $userId)
    {
        return $query->whereHas('upload.uploadLink', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}
