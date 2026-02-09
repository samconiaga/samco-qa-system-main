<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegulatoryAssesment extends Model
{
  use HasUuid, SoftDeletes;
  protected $guarded = [];
  protected $keyType = 'string';
  public $incrementing = false;

  public function changeRequest()
  {
    return $this->belongsTo(ChangeRequest::class, 'change_request_id', 'id');
  }
}
