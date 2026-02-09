<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedDepartment extends Model
{
    use HasUuid, SoftDeletes;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
