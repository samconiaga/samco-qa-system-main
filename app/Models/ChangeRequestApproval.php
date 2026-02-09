<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeRequestApproval extends Model
{
    use HasUuid, SoftDeletes;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['approver'];


    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id', 'id');
    }
}
