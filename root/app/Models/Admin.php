<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'password',
        'mail_address',
        'is_system_admin'
    ];

    public function loginLogs()
    {
        return $this->hasMany(AdminLogs::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    public function adminMessages()
    {
        return $this->hasMany(AdminMessage::class);
    }

    public function userMessages()
    {
        return $this->hasMany(UserMessage::class);
    }
}
