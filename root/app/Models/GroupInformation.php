<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupInformation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'information_id',
    ];

    protected $table = 'groups_information';
}
