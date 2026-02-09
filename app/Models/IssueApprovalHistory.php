<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssueApprovalHistory extends Model
{
    use HasUuid;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
}
