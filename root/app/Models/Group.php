<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group_name',
        'remarks',
        'updated_at',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'groups_courses', 'group_id', 'course_id')->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_groups', 'group_id', 'user_id')->withTimestamps();
    }

    public function informations()
    {
        return $this->belongsToMany(Information::class, 'groups_information', 'group_id', 'information_id')
        ->withTimestamps();
    }

    // ユーザーが削除された時に、IDに紐づく中間テーブルの値も削除される
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($group) {
            $group->usersGroupsTable()->delete();
        });

        static::deleting(function ($group) {
            $group->groupsCoursesTable()->delete();
        });
    }

    // users_groupsテーブルとのリレーション
    public function usersGroupsTable()
    {
        return $this->hasMany(UsersGroup::class, 'group_id', 'id');
    }

    // groups_coursesテーブルとのリレーション
    public function groupsCoursesTable()
    {
        return $this->hasMany(GroupsCourse::class, 'group_id', 'id');
    }

    // groups_informationテーブルとのリレーション
    public function groupsInformationTable()
    {
        return $this->hasMany(GroupInformation::class, 'group_id', 'id');
    }
}
