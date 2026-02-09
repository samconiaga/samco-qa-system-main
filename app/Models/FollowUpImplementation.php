<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUpImplementation extends Model
{
    use HasUuid, SoftDeletes;
    protected $guarded = [];
    protected $table = 'follow_up_implementations';
    protected $keyType = 'string';
    public $incrementing = false;

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'assesment_by', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function detail()
    {
        return $this->hasMany(FollowUpImplementationDetail::class);
    }
}
