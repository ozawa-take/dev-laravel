<?php

namespace App\Models;

use App\Enums\ActionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AdminMessage extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'admin_id',
        'user_id',
        'title',
        'text',
        'action',
        'is_hidden',
        'is_replied',
        'reply_message_id',
    ];

    protected $casts = [
        'action'    => ActionEnum::class,
        'is_hidden' => 'boolean',
        'is_replied' => 'boolean',
    ];
}
