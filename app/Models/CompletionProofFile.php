<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompletionProofFile extends Model
{
    use HasUuid, SoftDeletes;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    public function actionPlan()
    {
        return $this->belongsTo(ActionPlan::class);
    }
}
