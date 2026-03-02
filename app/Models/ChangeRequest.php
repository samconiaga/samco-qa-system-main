<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ChangeRequest extends Model
{
    use HasUuid, SoftDeletes;
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;
    protected $with = ['impactOfChangeAssesment'];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeOfChange()
    {
        return $this->belongsToMany(
            ScopeOfChange::class,
            'change_request_scopes', // nama tabel pivot
            'change_request_id',     // foreign key di pivot untuk model ini
            'scope_of_change_id'     // foreign key di pivot untuk model tujuan
        )->using(ChangeRequestScope::class);
    }
    public function typeOfChange()
    {
        return $this->hasMany(ChangeRequestType::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function relatedDepartments()
    {
        return $this->hasMany(RelatedDepartment::class);
    }
    public function supportingAttachments()
    {
        return $this->hasMany(ChangeRequestSupportingAttachment::class);
    }
    public function proposedChangeFiles()
    {
        return $this->hasMany(ProposedChangeFile::class);
    }
    public function currentStatusFiles()
    {
        return $this->hasMany(CurrentStatusFile::class);
    }
    public function impactRiskAssesment()
    {
        return $this->hasOne(ImpactRiskAssesment::class);
    }
    public function impactOfChangeAssesment()
    {
        return $this->hasOne(ImpactOfChangeAssesment::class);
    }
    public function affectedProducts()
    {
        return $this->belongsToMany(Product::class, 'affected_products')
            ->withTimestamps();
    }
    public function qaRiskAssesment()
    {
        return $this->hasOne(QaRiskAssesment::class);
    }
    public function actionPlans()
    {
        return $this->hasMany(ActionPlan::class);
    }
    public function approvals()
    {
        return $this->hasMany(ChangeRequestApproval::class);
    }
    public function closing()
    {
        return $this->hasOne(ChangeRequestClosing::class);
    }

    public function lastApproval()
    {
        return $this->hasOne(ChangeRequestApproval::class)
            ->where('decision', '<>', 'Pending')
            ->latest()
            ->limit(1);
    }

    public function followUpImplementations()
    {
        return $this->hasMany(FollowUpImplementation::class);
    }

    public function followUpImplementationDetails()
    {
        return $this->hasManyThrough(
            FollowUpImplementationDetail::class,
            FollowUpImplementation::class,
            'change_request_id', // Foreign key on FollowUpImplementation table
            'follow_up_implementation_id', // Foreign key on FollowUpImplementationDetail table
            'id', // Local key on ChangeRequest table
            'id'  // Local key on FollowUpImplementation table
        );
    }

    public function regulatory()
    {
        return $this->hasOne(RegulatoryAssesment::class);
    }
    public function getCurrentStatusFileUrlsAttribute()
    {
        if (! $this->relationLoaded('currentStatusFiles')) {
            $this->load('currentStatusFiles');
        }

        return $this->currentStatusFiles
            ->map(function ($attachment) {
                return Storage::url($attachment->file_path);
            })
            ->toArray();
    }
    public function getProposedChangeFileUrlsAttribute()
    {
        if (!$this->relationLoaded('proposedChangeFiles')) {
            $this->load('proposedChangeFiles');
        }

        return $this->proposedChangeFiles
            ->map(function ($attachment) {
                return Storage::url($attachment->file_path);
            })
            ->toArray();
    }
    public function getSupportingAttachmentUrlsAttribute()
    {
        if (! $this->relationLoaded('supportingAttachments')) {
            $this->load('supportingAttachments');
        }

        return $this->supportingAttachments
            ->map(function ($attachment) {
                return Storage::url($attachment->file_path);
            })
            ->toArray();
    }

    public function getCurrentStatusFilesMetaAttribute()
    {
        if (! $this->relationLoaded('currentStatusFiles')) {
            $this->load('currentStatusFiles');
        }

        return $this->currentStatusFiles->map(function ($file) {
            return [
                'id'   => $file->id,
                'name' => $file->original_name ?? basename($file->file_path),
                'url'  => Storage::url($file->file_path),
            ];
        })->values();
    }

    public function getProposedChangeFilesMetaAttribute()
    {
        if (! $this->relationLoaded('proposedChangeFiles')) {
            $this->load('proposedChangeFiles');
        }

        return $this->proposedChangeFiles->map(function ($file) {
            return [
                'id'   => $file->id,
                'name' => $file->original_name ?? basename($file->file_path),
                'url'  => Storage::url($file->file_path),
            ];
        })->values();
    }

    public function getSupportingAttachmentsMetaAttribute()
    {
        if (! $this->relationLoaded('supportingAttachments')) {
            $this->load('supportingAttachments');
        }

        return $this->supportingAttachments->map(function ($file) {
            return [
                'id'   => $file->id,
                'name' => $file->original_name ?? basename($file->file_path),
                'url'  => Storage::url($file->file_path),
            ];
        })->values();
    }

    public function scopeByEmployee($query)
    {
        $user = User::where('id', Auth::id())->first();
        $employeeId = $user?->employee?->id;
        $departmentId = $user?->employee?->department_id;

        // ✅ Privilege hanya untuk QA SPV, QA Manager, dan Administrator
        $isPrivileged = $user->hasRole('Administrator') ||
            $user->permissions()->where(function ($q) {
                $q->where('name', 'like', 'Approve QA SPV%')
                    ->orWhere('name', 'like', 'Approve QA Manager%')
                    ->orWhere('name', 'like', 'Approve Plant Manager%');
            })->exists();

        // Ambil daftar permission Review dan Approve untuk stage approval
        $approvalPermissions = $user->permissions()
            ->where(function ($q) {
                $q->where('name', 'like', 'Approve%')
                    ->orWhere('name', 'like', 'Review%');
            })
            ->pluck('name');

        return $query->where(function ($q) use ($user, $employeeId, $departmentId, $approvalPermissions, $isPrivileged) {
            // 1️⃣ Privileged full akses
            if ($isPrivileged) {
                $q->orWhereRaw('1=1');
            }

            // 2️⃣ Create Change Control sendiri
            if ($user->can('Create Change Control') && $employeeId) {
                $q->orWhere('employee_id', $employeeId);
            }

            // 3️⃣ Approver/Reviewer sesuai permission dan employee_id
            if ($approvalPermissions->isNotEmpty() && $employeeId) {
                $q->orWhereHas('approvals', function ($a) use ($employeeId, $approvalPermissions) {
                    $a->where('approver_id', $employeeId)
                        ->whereIn('stage', $approvalPermissions);
                });
            }

            // 4️⃣ Manager bisa lihat Draft jika ada Approve Manager Pending
            if ($employeeId && $user->permissions()->where('name', 'like', 'Approve Manager%')->exists()) {
                $q->orWhere(function ($sub) use ($employeeId) {
                    $sub->where('overall_status', 'Draft')
                        ->whereHas('approvals', function ($a) use ($employeeId) {
                            $a->where('approver_id', $employeeId)
                                ->where('stage', 'Approve Manager');
                        });
                });
            }
            // 5️⃣ PIC Action Plan sesuai department
            if ($user->can('PIC Action Plan') && $departmentId) {
                $q->orWhereHas('actionPlans.changeRequest', function ($cr) use ($departmentId) {
                    $cr->where('overall_status', 'Approved')
                        ->whereHas('actionPlans', function ($ap) use ($departmentId) {
                            $ap->where('department_id', $departmentId);
                        });
                });
            }

            // 6️⃣ Departemen terkait (relatedDepartments)
            if ($departmentId) {
                $q->orWhereHas('relatedDepartments', function ($rd) use ($departmentId) {
                    $rd->where('department_id', $departmentId);
                });
            }

            // 7️⃣ PPIC Head bisa lihat CR yang melibatkan third party
            if ($user->can('Review PPIC Manager')) {
                $q->orWhereHas('impactOfChangeAssesment', function ($ioc) {
                    $ioc->where('third_party_involved', true);
                });
            }

            // 7️⃣ Jika tidak punya privilege & akses lain, kosongkan
            if (
                !$isPrivileged &&
                !$user->can('Create Change Control') &&
                !$user->can('PIC Action Plan') &&
                $approvalPermissions->isEmpty() &&
                !$departmentId
            ) {
                $q->whereRaw('1=0');
            }
        });
    }
}
