<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Information extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'text',
        'admin_id',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'groups_information', 'information_id', 'group_id');
    }
    // groups_informationテーブルとのリレーション
    public function groupsInformationTable()
    {
        return $this->hasMany(GroupInformation::class, 'information_id', 'id');
    }
}
