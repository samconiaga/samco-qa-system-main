<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use HasUuid, SoftDeletes;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function resolution()
    {
        return $this->hasOne(IssueResolution::class);
    }

    public function capaType()
    {
        return $this->belongsTo(CapaType::class, 'capa_type_id', 'id');
    }
    public function approvalHistory()
    {
        return $this->hasMany(IssueApprovalHistory::class);
    }
    public function issueResolutionFile()
    {
        return $this->hasMany(IssueResolutionFile::class);
    }

    public function getIssueResolutionFileUrlsAttribute()
    {
        if (! $this->relationLoaded('issueResolutionFile')) {
            $this->load('issueResolutionFile');
        }

        return $this->issueResolutionFile
            ->map(function ($attachment) {
                return Storage::url($attachment->file_path);
            })
            ->toArray();
    }
    public function scopeByEmployee($query)
    {
        $user = User::find(Auth::id());
        $employee = $user->employee;
        if ($user->hasRole('Administrator') || $user->can('Approve QA Manager')) {
            return $query;
        }
        // Selain itu → hanya lihat issue yang sesuai department_id
        return $query->where('department_id', $employee->department_id);
    }
}
