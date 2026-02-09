<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionPlanOverdueRequest extends Model
{
    use HasUuid;
    protected $guarded = ['id'];
    protected $keyType = 'string';
    public $incrementing = false;
}
