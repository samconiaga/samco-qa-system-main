<?php

namespace App\Models;

use App\Traits\HasUuid;
use App\Models\ChangeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImpactOfChangeAssesment extends Model
{

    use HasUuid, SoftDeletes;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'third_party_involved' => 'boolean',
        'is_informed_to_toll_manufacturing' => 'boolean',
        'is_approval_required_from_toll_manufacturing' => 'boolean',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class, 'change_request_id', 'id');
    }
}
