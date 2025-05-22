<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManPower extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'jabatan',
        'no_telepon',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'man_power');
    }
}
