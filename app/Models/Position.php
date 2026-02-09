<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasUuid, SoftDeletes;

    protected $guarded = ['id']; // Aman, department_id otomatis bisa diisi
    protected $keyType = 'string';
    public $incrementing = false;

    // Tambahkan ini agar bisa memanggil $position->department->name
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}