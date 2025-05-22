<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_project',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
