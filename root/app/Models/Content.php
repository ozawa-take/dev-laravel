<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'admin_id',
        'title',
        'text',
        'youtube_video_id',
        'remarks',
        'position',
    ];

    protected $casts = [
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function contentsLogs()
    {
        return $this->hasMany(ContentsLog::class);
    }

    public function getLog($user)
    {
        $content_id = $this->id;
        if (ContentsLog::where('user_id', $user->id)->where('content_id', $content_id)->exists()) {
            return ContentsLog::where('user_id', $user->id)->where('content_id', $content_id)->first();
        } else {
            return null;
        }
    }

    protected static function boot()
    {
        parent::boot();

        // 並び替え時に使用
        static::creating(function ($model) {
            $model->position = Content::max('position') + 1;
        });
    }
}
