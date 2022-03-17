<?php

namespace App\Models;

use App\Notifications\PasswordResetNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeSearchName($query, $keyword)
    {
        $convertKeyword = mb_convert_kana($keyword, 's');
        $keywords = preg_split('/[\s]+/', $convertKeyword, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($keywords as $word) {
            $query->orWhere('users.name', 'like', "%$word%");
        }
    }

    public function scopeSortOrder($query, $order)
    {
        if ($order === null || $order === 'name_asc') {
            $query->orderBy('name');
        } elseif ($order === 'name_desc') {
            $query->orderByDesc('name');
        }

        if ($order === 'email_asc') {
            $query->orderBy('email');
        } elseif ($order === 'email_desc') {
            $query->orderByDesc('email');
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }
}
