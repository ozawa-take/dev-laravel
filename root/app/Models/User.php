<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    public function adminMessages()
    {
        return $this->hasMany(AdminMessage::class);
    }

    public function userMessagess()
    {
        return $this->hasMany(UserMessage::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'mail_address',
    ];

    public function userLogs()
    {
        return $this->hasMany(UserLogs::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'users_courses', 'user_id', 'course_id');
    }

    public function records()
    {
        return $this->hasMany(Record::class);
    }

    // ユーザーが削除された時に、IDに紐づく中間テーブルの値も削除される
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $user->usersGroupsTable()->delete();
        });
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'users_groups', 'user_id', 'group_id')->withTimestamps();
    }

    // users_groupsテーブルとのリレーション
    public function usersGroupsTable()
    {
        return $this->hasMany(UsersGroup::class, 'user_id', 'id');
    }
}
