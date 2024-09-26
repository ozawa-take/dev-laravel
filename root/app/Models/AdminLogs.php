<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLogs extends Model
{
    use HasFactory;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected $table = 'admin_logs';
}
