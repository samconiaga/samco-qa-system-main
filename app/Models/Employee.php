<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasUuid, SoftDeletes;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['department', 'position'];
    protected $appends = ['sign_url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function getSignUrlAttribute()
    {
        if ($this->sign && File::exists(storage_path("app/public/" . $this->sign))) {
            return url("storage/" . $this->sign);
        }

        return null;
    }
}
