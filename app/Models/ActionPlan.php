<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionPlan extends Model
{
    use HasUuid;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['department', 'pic', 'changeRequest'];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function pic()
    {
        return $this->belongsTo(Employee::class, 'pic_id', 'id');
    }

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class);
    }

    public function impactCategory()
    {
        return $this->belongsTo(FollowUpImplementationDetail::class, 'follow_up_implementation_detail_id', 'id');
    }


    public function completionProofFiles()
    {
        return $this->hasMany(CompletionProofFile::class);
    }
}
