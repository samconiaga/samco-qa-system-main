<?php

namespace App\Repositories\ChangeRequest;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use App\Models\ActionPlan;
use Illuminate\Support\Str;
use App\Models\ChangeRequest;
use App\Models\ScopeOfChange;
use App\Models\QaRiskAssesment;
use App\Jobs\SendNotificationJob;
use App\Models\CurrentStatusFile;
use App\Models\RelatedDepartment;
use Illuminate\Http\UploadedFile;
use App\Models\ProposedChangeFile;
use Illuminate\Support\Facades\DB;
use App\Models\ImpactRiskAssesment;
use App\Models\RegulatoryAssesment;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Models\ChangeRequestClosing;
use Illuminate\Support\Facades\Auth;
use App\Models\ChangeRequestApproval;
use App\Models\FollowUpImplementation;
use App\Models\ImpactOfChangeAssesment;
use Illuminate\Support\Facades\Storage;
use App\Models\ActionPlanOverdueRequest;
use App\Models\FollowUpImplementationDetail;
use App\Models\ChangeRequestSupportingAttachment;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Repositories\SendNotification\SendNotificationRepository;

class ChangeRequestRepositoryImplement extends Eloquent implements ChangeRequestRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */

    protected $sendNotificationRepository;

    public function __construct(SendNotificationRepository $sendNotificationRepository)
    {
        $this->sendNotificationRepository = $sendNotificationRepository;
    }

    // Main Methods
    public function StoreChangeRequest($data)
    {
        return DB::transaction(function () use ($data) {
            // 1️⃣ Create / Update main ChangeRequest
            $changeRequest = $this->saveMainChangeRequest($data);
            // 2️⃣ Sync scopes
            $this->syncScopes($changeRequest, $data['scopes'] ?? []);

            // 3️⃣ Sync types
            $this->syncTypes($changeRequest, $data['type_of_change'] ?? []);

            // 4️⃣ Update or create Impact Risk Assessment
            $this->saveImpactRiskAssessment($changeRequest, $data);

            // 5️⃣ Update Related Departments
            $this->saveRelatedDepartments($changeRequest, $data['related_departments'] ?? []);

            // 6️⃣ Add supporting attachments
            $this->saveSupportingAttachments($changeRequest, $data['supporting_attachment'] ?? [], $data['supporting_attachment_names'] ?? [], $data['supporting_attachment_keep_ids'] ?? []);
            // 7️⃣ Add current status file
            $this->saveCurrentStatusFile($changeRequest, $data['current_status_file'] ?? [], $data['current_status_file_names'] ?? [], $data['current_status_file_keep_ids'] ?? []);
            // 8️⃣ Add proposed change file
            $this->saveProposedChangeFile($changeRequest, $data['proposed_change_file'] ?? [], $data['proposed_change_file_names'] ?? [], $data['proposed_change_file_keep_ids'] ?? []);
            // 9️⃣ Create Impact Of Change Assessment
            $this->saveImpactOfChangeAssessment($changeRequest, $data);

            // 1️⃣0️⃣ Create First Approval
            if (Auth::user()?->employee?->id == $changeRequest->employee_id && ($changeRequest->wasRecentlyCreated || $changeRequest->overall_status == 'Pending')) {
                $this->createFirstApproval($changeRequest);
            }
            return $changeRequest;
        });
    }
    public function saveAsDraft($data)
    {
        return DB::transaction(function () use ($data) {
            $data['overall_status'] = 'Draft';

            // 1️⃣ Create / Update main ChangeRequest
            $changeRequest = $this->saveMainChangeRequest($data);
            // 2️⃣ Sync scopes
            $this->syncScopes($changeRequest, $data['scopes'] ?? []);

            // 3️⃣ Sync types
            $this->syncTypes($changeRequest, $data['type_of_change'] ?? []);

            // 4️⃣ Update or create Impact Risk Assessment
            $this->saveImpactRiskAssessment($changeRequest, $data);
            // 5️⃣ Update Related Departments
            $this->saveRelatedDepartments($changeRequest, $data['related_departments'] ?? []);

            // 6️⃣ Add supporting attachments
            $this->saveSupportingAttachments($changeRequest, $data['supporting_attachment'] ?? [], $data['supporting_attachment_names'] ?? [], $data['supporting_attachment_keep_ids'] ?? []);
            // 7️⃣ Add current status file
            $this->saveCurrentStatusFile($changeRequest, $data['current_status_file'] ?? [], $data['current_status_file_names'] ?? [], $data['current_status_file_keep_ids'] ?? []);
            // 8️⃣ Add proposed change file
            $this->saveProposedChangeFile($changeRequest, $data['proposed_change_file'] ?? [], $data['proposed_change_file_names'] ?? [], $data['proposed_change_file_keep_ids'] ?? []);
            // 9️⃣ Create Impact Of Change Assessment
            $this->saveImpactOfChangeAssessment($changeRequest, $data);
            return $changeRequest;
        });
    }

    public function approve($request, $changeRequest)
    {

        return DB::transaction(function () use ($request, $changeRequest) {
            $user = User::find(Auth::id());

            // Ambil approval aktif (pending)
            $approval = $changeRequest->approvals()
                ->where('decision', 'Pending')
                ->whereNull('deleted_at')
                ->where(function ($q) use ($user) {
                    $q->where('approver_id', $user->employee?->id)
                        ->orWhereNull('approver_id');
                })
                ->firstOrFail();

            // Cek permission user (langsung pakai stage sebagai nama permission)
            if (!$user->can($approval->stage)) {
                throw new \Exception("You don't have permission to approve this stage.");
            }
            if ($this->stageCanUpdateData($approval->stage)) {
                $this->storeChangeRequest($request->all());
            }
            // Update decision
            $approval->update([
                'decision'    => $request->input('decision'),
                'comments'    => $request->input('comments'),
                'approved_at' => now()
            ]);

            // Kalau Rejected → stop
            if ($request->input('decision') === 'Rejected') {
                $user = $changeRequest?->employee?->user;
                $this->sendNotification($user, [
                    'name' => $user->employee->name,
                    'title' => $changeRequest->title,
                    'initiator' => $changeRequest->initiator_name,
                ], 'change-request-rejected-notification.txt', __("Change Request Rejected"));
                $changeRequest->followUpImplementations()->delete();
                $changeRequest->update(['overall_status' => 'Rejected']);
                return $changeRequest;
            }

            // Kalau Approved → lanjut
            $this->moveToNextStage($changeRequest, $approval->stage, $request);

            return $changeRequest->fresh();
        });
    }

    public function reviewStore($request, $changeRequest)
    {
        return DB::transaction(function () use ($request, $changeRequest) {
            $data = $request->all();
            $user = Auth::user();
            $deptId = $user?->employee?->department_id;

            // Simpan/update hasil review
            $assessment = FollowUpImplementation::updateOrCreate(
                ['change_request_id' => $changeRequest->id, 'department_id' => $deptId],
                [
                    'evaluation_status' => $data['decision'] ?? '',
                    'comments'          => $data['comments'] ?? '',
                    'assesment_by'      => $user->employee->id,
                ]
            );

            // Ambil semua departemen terkait & review
            $relatedIds = $changeRequest->relatedDepartments()->pluck('department_id')->toArray();
            $reviews = FollowUpImplementation::where('change_request_id', $changeRequest->id)
                ->whereIn('department_id', $relatedIds)
                ->get();

            $allReviewed = empty(array_diff($relatedIds, $reviews->pluck('department_id')->toArray()));
            $regulatoryFilled = (bool) $changeRequest->regulatory;

            if ($allReviewed && $regulatoryFilled) {
                $agreeReviews = $reviews->filter(fn($r) => $r->evaluation_status === 'Agree');

                if ($agreeReviews->isEmpty()) {
                    // Tidak ada Agree → langsung move
                    $this->moveToNextStage($changeRequest, 'To SPV Review', $request);
                } else {
                    // Ada Agree → cek semua action plan mereka Pending
                    $agreeDepartmentIds = $agreeReviews->pluck('department_id')->toArray();
                    $pendingDepartments = ActionPlan::where('change_request_id', $changeRequest->id)
                        ->whereIn('department_id', $agreeDepartmentIds)
                        ->where('status', 'Pending')
                        ->pluck('department_id')
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();

                    if ($pendingDepartments === $agreeDepartmentIds) {
                        $this->moveToNextStage($changeRequest, 'To SPV Review', $request);
                    }
                }
            }

            return $assessment;
        });
    }


    public function regulatoryAssessment($request, $changeRequest)
    {
        return DB::transaction(function () use ($request, $changeRequest) {

            $data = $request->all();

            // 1️⃣ Simpan Impact Of Change Assessment
            ImpactOfChangeAssesment::updateOrCreate(
                ['change_request_id' => $data['change_request_id']],
                [
                    'facility_affected' => $data['facility_affected'] ?? null,
                    'product_affected'  => $data['product_affected'] ?? null,
                    'halal_status'      => $data['halal_status'] ?? null,
                ]
            );

            // 2️⃣ Simpan Regulatory Assessment
            $regulatory = RegulatoryAssesment::updateOrCreate(
                ['change_request_id' => $data['change_request_id']],
                [
                    'regulatory_change_type' => $data['regulatory_change_type'] ?? null,
                    'regulatory_variation'   => $data['regulatory_variation'] ?? null,
                    'reported_by'            => $data['reported_by'] ?? null,
                    'notification_date'      => $data['notification_date'] ?? null,
                ]
            );

            // 3️⃣ Auto-approve Prodev Manager jika masih Pending
            ChangeRequestApproval::where([
                ['change_request_id', '=', $data['change_request_id']],
                ['stage', '=', 'Review Prodev Manager'],
                ['decision', '=', 'Pending'],
            ])->update([
                'decision' => 'Approved',
            ]);

            // 4️⃣ Ambil semua department terkait
            $relatedIds = $changeRequest->relatedDepartments()
                ->pluck('department_id')
                ->toArray();

            // 5️⃣ Ambil semua review
            $reviews = FollowUpImplementation::where('change_request_id', $changeRequest->id)
                ->whereIn('department_id', $relatedIds)
                ->get();

            // 6️⃣ Semua department sudah review?
            $allReviewed = empty(array_diff(
                $relatedIds,
                $reviews->pluck('department_id')->toArray()
            ));

            // 7️⃣ Regulatory sudah diisi? (WAJIB pakai exists)
            $regulatoryFilled = $changeRequest->regulatory()->exists();

            // 8️⃣ VALIDASI KHUSUS evaluation_status = Agree
            $agreeReviews = $reviews->where('evaluation_status', 'Agree');
            $agreeReady = true;

            if ($agreeReviews->isNotEmpty()) {

                $agreeDepartmentIds = $agreeReviews->pluck('department_id')->toArray();

                // A. Department yang action plan-nya INVALID
                $invalidDepartments = ActionPlan::where('change_request_id', $changeRequest->id)
                    ->whereIn('department_id', $agreeDepartmentIds)
                    ->where(function ($q) {
                        $q->whereNull('status')
                            ->orWhere('status', '!=', 'Pending');
                    })
                    ->pluck('department_id')
                    ->unique()
                    ->toArray();

                if (!empty($invalidDepartments)) {
                    $agreeReady = false;
                }

                // B. Pastikan SEMUA Agree department punya action plan
                $departmentsWithPlans = ActionPlan::where('change_request_id', $changeRequest->id)
                    ->whereIn('department_id', $agreeDepartmentIds)
                    ->pluck('department_id')
                    ->unique()
                    ->toArray();

                if (!empty(array_diff($agreeDepartmentIds, $departmentsWithPlans))) {
                    $agreeReady = false;
                }
            }

            // 9️⃣ PINDAH STAGE JIKA SEMUA SYARAT TERPENUHI
            if ($allReviewed && $regulatoryFilled && $agreeReady) {
                $this->moveToNextStage($changeRequest, 'To SPV Review', $request);
            }

            return $regulatory;
        });
    }



    // Action Plan Methods
    public function requestOverdue($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $actionPlan = ActionPlan::find($data['id']);
            $actionPlan->status = 'Request Overdue';
            $actionPlan->save();
            ActionPlanOverdueRequest::create([
                'action_plan_id' => $actionPlan->id,
                'status'         => 'Pending',
                'reason'         => $data['reason'] ?? null,
            ]);
            $spv = Employee::find($this->findApproverId('Approve QA SPV'));
            $this->sendNotification($spv->user, [
                'name' => $spv->name,
                'impact_of_change_category' => $actionPlan?->impactCategory?->impact_of_change_category,
                'title' => $actionPlan->changeRequest->title,
                'pic' => $actionPlan->department->name,
                'request_number' => $actionPlan->changeRequest->request_number,
                'realization' => $actionPlan->realization,
                'initiator' => $actionPlan->changeRequest->initiator_name,
                'department' => $actionPlan->changeRequest->department->name,
            ], 'overdue-request.txt', __("Overdue Request"));
            return true;
        });
    }
    public function submitActionPlan($request)
    {
        return DB::transaction(function () use ($request) {
            $actionPlan = ActionPlan::find($request->input('id'));
            $actionPlan->realization = $request->input('realization');
            $actionPlan->status = 'Submitted';
            $actionPlan->save();
            $spv = Employee::whereHas('user.permissions', function ($q) {
                $q->where('name', 'Approve QA SPV');
            })
                ->first();


            $this->sendNotification($spv->user, [
                'name' => $spv->name,
                'title' => $actionPlan->changeRequest->title,
                'request_number' => $actionPlan->changeRequest->request_number,
                'impact_of_change_category' => $actionPlan?->impactCategory?->impact_of_change_category,
                'pic' => $actionPlan->department->name,
                'realization' => $actionPlan->realization,
            ], 'submit-follow-up-implementation.txt', __("New Submitted Follow Up Implementation"));
            return true;
        });
    }

    public function approveOverdue($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $actionPlan = ActionPlan::find($data['id']);
            $actionPlan->status = 'Overdue';
            $actionPlan->deadline = $data['deadline'] ?? '';
            $actionPlan->save();
            // dd($data['deadline']);
            ActionPlanOverdueRequest::where('action_plan_id', $actionPlan->id)
                ->where('status', 'Pending')
                ->update([
                    'status' => 'Approved',
                ]);
            $pics = Employee::where('department_id', $actionPlan->department_id)
                ->whereHas('user')
                ->get();
            $pics->chunk(50)->each(function ($chunk) use ($actionPlan, $data) {
                $jobs = $chunk->map(function ($pic) use ($actionPlan, $data) {
                    $notificationData = [
                        'name' => $pic->name,
                        'title' => $actionPlan->changeRequest->title,
                        'impact_of_change_category' => $actionPlan?->impactCategory?->impact_of_change_category,
                        'request_number' => $actionPlan->changeRequest->request_number,
                        'extend_timeline' => $data['deadline'] ?? '',
                        'realization' => $actionPlan->realization,
                    ];

                    return new SendNotificationJob(
                        $pic->user,
                        $notificationData,
                        'approve-overdue.txt',
                        __("Overdue Request Has Been Approved")
                    );
                })->toArray();
                Log::info('Jumlah jobs dalam batch: ' . count($jobs));

                Bus::batch($jobs)
                    ->name("Approve Overdue Notifications - Dept {$actionPlan->department_id}")
                    ->onQueue('notifications')
                    ->dispatch();
            });

            return true;
        });
    }
    public function rejectOverdue($request)
    {
        return DB::transaction(function () use ($request) {
            $actionPlan = ActionPlan::find($request->input('id'));

            $actionPlan->update(['status' => 'Open']);

            ActionPlanOverdueRequest::where('action_plan_id', $actionPlan->id)
                ->where('status', 'Pending')
                ->update(['status' => 'Rejected']);

            $pics = Employee::where('department_id', $actionPlan->department_id)
                ->whereHas('user')
                ->get();

            // 🔹 Bagi batch agar tidak terlalu berat
            $pics->chunk(50)->each(function ($chunk) use ($actionPlan) {
                $jobs = $chunk->map(function ($pic) use ($actionPlan) {
                    $notificationData = [
                        'name'            => $pic->name,
                        'title'           => $actionPlan->changeRequest->title,
                        'request_number'  => $actionPlan->changeRequest->request_number,
                        'realization' => $actionPlan->realization,
                        'impact_of_change_category' => $actionPlan?->impactCategory?->impact_of_change_category,

                    ];

                    return new SendNotificationJob(
                        $pic->user,
                        $notificationData,
                        'reject-overdue.txt',
                        __("Overdue Request Has Been Rejected")
                    );
                })->toArray();
                Log::info('Jumlah jobs dalam batch: ' . count($jobs));

                Bus::batch($jobs)
                    ->name("Reject Overdue Notifications - Dept {$actionPlan->department_id}")
                    ->onQueue('notifications')
                    ->dispatch();
            });

            return true;
        });
    }

    public function approveActionPlan($actionPlan)
    {
        return DB::transaction(function () use ($actionPlan) {
            $actionPlan->status = 'Close';
            $actionPlan->save();
            $pics = Employee::where('department_id', $actionPlan->department_id)
                ->whereHas('user')
                ->get();
            $pics->chunk(50)->each(function ($chunk) use ($actionPlan) {
                $jobs = $chunk->map(function ($pic) use ($actionPlan) {
                    $notificationData = [
                        'name' => $pic->name,
                        'title' => $actionPlan->changeRequest->title,
                        'impact_of_change_category' => $actionPlan?->impactCategory?->impact_of_change_category,
                        'request_number' => $actionPlan->changeRequest->request_number,
                        'realization' => $actionPlan->realization,
                        'pic' => $actionPlan->department->name,
                    ];

                    return new SendNotificationJob(
                        $pic->user,
                        $notificationData,
                        'approve-follow-up-implementation.txt',
                        __("Follow Up Implementation Has Been Approved")
                    );
                })->toArray();
                Bus::batch($jobs)
                    ->name("Approve Follow Up Implementation Notifications - Dept {$actionPlan->department_id}")
                    ->onQueue('notifications')
                    ->dispatch();
            });
            $this->checkAllClosed($actionPlan);
            return true;
        });
    }

    public function rejectActionPlan($actionPlan)
    {
        return DB::transaction(function () use ($actionPlan) {
            $filePaths = $actionPlan->completionProofFiles()
                ->pluck('file_path')
                ->toArray();

            // Hapus semua file dalam sekali eksekusi
            Storage::delete($filePaths);

            // Hapus semua record di DB 
            $actionPlan->completionProofFiles()->forceDelete();
            $actionPlan->status = 'Open';
            $actionPlan->save();
            $pics = Employee::where('department_id', $actionPlan->department_id)
                ->whereHas('user')
                ->get();
            $pics->chunk(50)->each(function ($chunk) use ($actionPlan) {
                $jobs = $chunk->map(function ($pic) use ($actionPlan) {
                    $notificationData = [
                        'name' => $pic->name,
                        'title' => $actionPlan->changeRequest->title,
                        'impact_of_change_category' => $actionPlan?->impactCategory?->impact_of_change_category,
                        'request_number' => $actionPlan->changeRequest->request_number,
                        'realization' => $actionPlan->realization,
                        'pic' => $actionPlan->department->name,
                    ];

                    return new SendNotificationJob(
                        $pic->user,
                        $notificationData,
                        'reject-follow-up-implementation.txt',
                        __("Follow Up Implementation Has Been Rejected")
                    );
                })->toArray();
                Bus::batch($jobs)
                    ->name("Reject Follow Up Implementation Notifications - Dept {$actionPlan->department_id}")
                    ->onQueue('notifications')
                    ->dispatch();
            });

            return true;
        });
    }

    public function savePendingActionPlan($request)
    {
        return DB::transaction(function () use ($request) {

            // 1️⃣ Update Action Plan department ini → Pending
            ActionPlan::where('change_request_id', $request->change_request_id)
                ->where('department_id', $request->department_id)
                ->update([
                    'status' => 'Pending',
                ]);

            $changeRequest = ChangeRequest::findOrFail($request->change_request_id);

            // 2️⃣ Regulatory sudah diisi?
            $regulatoryFilled = (bool) $changeRequest->regulatory;
            if (!$regulatoryFilled) {
                return true; // belum ada regulatory → stop
            }

            // 3️⃣ Semua department terkait
            $relatedDepartmentIds = $changeRequest->relatedDepartments()
                ->pluck('department_id')
                ->toArray();

            // 4️⃣ Semua FollowUpImplementation existing
            $existingFUIIds = FollowUpImplementation::where('change_request_id', $changeRequest->id)
                ->pluck('department_id')
                ->toArray();

            $allDepartmentsHaveFUI = empty(array_diff($relatedDepartmentIds, $existingFUIIds));
            if (!$allDepartmentsHaveFUI) {
                return true; // masih ada department yang belum submit FollowUpImplementation
            }

            // 5️⃣ Ambil department yang Agree
            $agreeDepartmentIds = FollowUpImplementation::where('change_request_id', $changeRequest->id)
                ->where('evaluation_status', 'Agree')
                ->pluck('department_id')
                ->unique()
                ->toArray();

            if (empty($agreeDepartmentIds)) {
                return true; // tidak ada yang Agree → stop
            }

            // 6️⃣ Semua Agree harus punya Action Plan Pending
            $submittedAgreeDepartments = ActionPlan::where('change_request_id', $changeRequest->id)
                ->whereIn('department_id', $agreeDepartmentIds)
                ->where('status', 'Pending')
                ->pluck('department_id')
                ->unique()
                ->toArray();

            $allAgreeSubmitted = empty(array_diff($agreeDepartmentIds, $submittedAgreeDepartments));
            if (!$allAgreeSubmitted) {
                return true; // masih ada yang belum submit Action Plan
            }

            // 7️⃣ Validasi detail Action Plan
            $hasInvalidAgreeDetail = FollowUpImplementation::where('change_request_id', $changeRequest->id)
                ->where('evaluation_status', 'Agree')
                ->whereDoesntHave('detail', function ($q) {
                    $q->whereNull('status')
                        ->orWhere('status', '!=', 'Pending');
                })
                ->exists();

            if ($hasInvalidAgreeDetail) {
                return true; // masih ada detail yang belum valid
            }

            // 8️⃣ SEMUA SYARAT TERPENUHI → pindah stage
            $this->moveToNextStage($changeRequest, 'To SPV Review', $request);

            return true;
        });
    }



    // Close Change Request Methods
    public function saveQaRiskAssessment($changeRequest, $request)
    {
        QaRiskAssesment::updateOrCreate(
            ['change_request_id' => $changeRequest->id],
            [
                'approver_id' => Auth::user()->employee->id,
                'source_of_risk' => $request->input('source_of_risk'),
                'impact_of_risk' => $request->input('impact_of_risk'),
                'severity' => $request->input('severity'),
                'cause_of_risk' => $request->input('cause_of_risk'),
                'probability' => $request->input('probability'),
                'control_implemented' => $request->input('control_implemented'),
                'detectability' => $request->input('detectability') ?? 0,
                'rpn' => $request->input('rpn') ?? 0,
                'risk_category' => $request->input('risk_category') ?? ''
            ]
        );
    }


    public function closeChangeRequest($changeRequest, $request)
    {
        return DB::transaction(function () use ($changeRequest, $request) {
            $user = User::find(Auth::id());
            $initiator = $changeRequest->employee?->user;
            $permissionToField = [
                'Approve QA Manager' => 'qa_manager_sign',
                'Approve QA SPV'     => 'qa_spv_sign',
            ];
            $fieldsToUpdate = collect($permissionToField)
                ->filter(fn($field, $permission) => $user->hasPermissionTo($permission))
                ->mapWithKeys(fn($field) => [$field => now()])
                ->all();
            $closing = ChangeRequestClosing::updateOrCreate(
                ['change_request_id' => $changeRequest->id],
                $fieldsToUpdate
            );

            if ($request->has('conclusion')) {
                $changeRequest->update(['conclusion' => $request->conclusion]);
            }
            if ($closing->qa_manager_sign && $closing->qa_spv_sign) {
                $changeRequest->update(['overall_status' => 'Closed']);
                $this->sendNotification($initiator, [
                    'name' => $initiator->name,
                    'number' => $changeRequest->request_number,
                    'title' => $changeRequest->title,
                    'initiator' => $initiator->name,
                ], 'change-request-closed-notification.txt', __("Change Request Closed"));
            }

            return true;
        });
    }
    /**
     * Protected Methods
     */
    protected function checkAllClosed($actionPlan)
    {
        $changeRequest = $actionPlan->changeRequest;

        // Load action plans sekali query
        $changeRequest->loadMissing('actionPlans');

        // Cek apakah masih ada action plan yang tidak close
        $allClosed = $changeRequest->actionPlans
            ->where('status', '!=', 'Close')
            ->isEmpty();

        if ($allClosed) {
            $this->moveToNextStage($changeRequest, 'To Waiting Close');
        }
    }






    protected function moveToNextStage($changeRequest, $currentStage)
    {
        // Helper untuk kirim notifikasi (baik single atau collection)
        $dispatchNotification = function ($recipients, $data, $template, $subject) {
            $users = collect($recipients)->filter();
            if ($users->isEmpty()) return;

            Bus::batch(
                $users->map(
                    fn($u) =>
                    new SendNotificationJob(
                        $u,
                        array_merge(['name' => $u->name], $data),
                        $template,
                        $subject
                    )
                )
            )->onQueue('notifications')->dispatch();
        };
        switch ($currentStage) {
            case 'Approve Manager':
                $approval = $changeRequest->approvals()->create([
                    'stage'       => 'Review Prodev Manager',
                    'decision'    => 'Pending',
                    'approver_id' => $this->findApproverId('Review Prodev Manager'),
                ]);

                $changeRequest->update(['overall_status' => 'Reviewed']);
                $recipients = collect();
                if ($user = $approval->approver?->user) {
                    $recipients->push($user);
                }
                $departmentIds = $changeRequest->relatedDepartments()->pluck('department_id');
                $relatedUsers = User::whereHas('employee', function ($q) use ($departmentIds) {
                    $q->whereIn('department_id', $departmentIds);
                })->get();
                $recipients = $recipients->merge($relatedUsers)->unique('id');
                // --- Kirim notifikasi ---
                if ($recipients->isNotEmpty()) {
                    $dispatchNotification(
                        $recipients,
                        [
                            'initiator'  => $changeRequest->initiator_name,
                            'title'      => $changeRequest->title,
                            'department' => $changeRequest->department->name,
                        ],
                        'new-change-request-notification.txt',
                        __("New Change Request")
                    );
                }
                break;
            case 'To SPV Review':
                $approval = $changeRequest->approvals()->create([
                    'stage'       => 'Approve QA SPV',
                    'decision'    => 'Pending',
                    'approver_id' => $this->findApproverId('Approve QA SPV'),
                ]);
                $changeRequest->update(['overall_status' => 'Waiting SPV Approval']);
                if ($user = $approval->approver?->user) {
                    $dispatchNotification(
                        [$user],
                        [
                            'initiator'  => $changeRequest->initiator_name,
                            'title'      => $changeRequest->title,
                            'department' => $changeRequest->department->name,
                        ],
                        'new-change-request-notification.txt',
                        __("New Change Request")
                    );
                }
                break;
            case 'Approve QA SPV':
                $approval = $changeRequest->approvals()->create([
                    'stage'       => 'Approve QA Manager',
                    'decision'    => 'Pending',
                    'approver_id' => $this->findApproverId('Approve QA Manager'),
                ]);
                $changeRequest->update(['overall_status' => 'Waiting QA Manager Approval']);
                if ($user = $approval->approver?->user) {
                    $dispatchNotification(
                        [$user],
                        [
                            'initiator'  => $changeRequest->initiator_name,
                            'title'      => $changeRequest->title,
                            'department' => $changeRequest->department->name,
                        ],
                        'new-change-request-notification.txt',
                        __("New Change Request")
                    );
                }
                break;
            case 'Approve QA Manager':
                $approval = $changeRequest->approvals()->create([
                    'stage'       => 'Approve Plant Manager',
                    'decision'    => 'Pending',
                    'approver_id' => $this->findApproverId('Approve Plant Manager'),
                ]);
                $changeRequest->update(['overall_status' => 'Waiting Plant Manager Approval']);
                if ($user = $approval->approver?->user) {
                    $dispatchNotification(
                        [$user],
                        [
                            'initiator'  => $changeRequest->initiator_name,
                            'title'      => $changeRequest->title,
                            'department' => $changeRequest->department->name,
                        ],
                        'new-change-request-notification.txt',
                        __("New Change Request")
                    );
                }
                break;
            case 'Approve Plant Manager':
                $changeRequest->update(['request_number' => $this->generateRequestNumber($changeRequest)]);

                $agreeDepartments = $changeRequest->followUpImplementations()
                    ->where('evaluation_status', 'Agree')
                    ->pluck('department_id')
                    ->unique();

                if ($agreeDepartments->isNotEmpty()) {
                    $changeRequest->update(['overall_status' => 'In Progress']);
                    $departmentUsers = Employee::whereIn('department_id', $agreeDepartments)
                        ->with('user')
                        ->get()
                        ->pluck('user');

                    $dispatchNotification(
                        $departmentUsers,
                        [
                            'title'          => $changeRequest->title,
                            'request_number' => $changeRequest->request_number,
                            'status'         => 'In Progress',
                        ],
                        'related-department-notification.txt',
                        __("Related Department Agreed on Change Request")
                    );
                    $changeRequest->actionPlans()->update([
                        'status' => 'Open'
                    ]);

                    return;
                }
                $changeRequest->update(['overall_status' => 'Waiting Close']);

                collect(['Approve QA SPV', 'Approve QA Manager'])->each(function ($stage) use ($changeRequest) {
                    $changeRequest->approvals()->create([
                        'stage'       => $stage,
                        'decision'    => 'Pending',
                        'approver_id' => $this->findApproverId($stage),
                    ]);
                });

                $qaUsers = User::whereHas(
                    'permissions',
                    fn($q) =>
                    $q->whereIn('permissions.name', ['Approve QA SPV', 'Approve QA Manager'])
                )->get();

                $dispatchNotification(
                    $qaUsers,
                    [
                        'title'          => $changeRequest->title,
                        'request_number' => $changeRequest->request_number,
                        'status'         => 'Waiting Close'
                    ],
                    'waiting-close.txt',
                    __("Action Required: Close Pending Change Request")
                );
                break;
            case 'To Waiting Close':
                $changeRequest->update(['overall_status' => 'Waiting Close']);

                foreach (['Approve QA SPV', 'Approve QA Manager'] as $stage) {
                    $changeRequest->approvals()->create([
                        'stage'       => $stage,
                        'decision'    => 'Pending',
                        'approver_id' => $this->findApproverId($stage),
                    ]);
                }

                $qaUsers = User::whereHas(
                    'permissions',
                    fn($q) =>
                    $q->whereIn('permissions.name', ['Approve QA SPV', 'Approve QA Manager'])
                )->get();

                $dispatchNotification(
                    $qaUsers,
                    [
                        'title'          => $changeRequest->title,
                        'request_number' => $changeRequest->request_number,
                        'status'         => 'Waiting Close',
                    ],
                    'waiting-close.txt',
                    __("Action Required: Close Pending Change Request")
                );
                break;
        }
    }


    protected function createReviewRelatedDepartmentApprovals($changeRequest, $type = "Approved")
    {
        // Ambil semua departemen unik dari related departments
        $departments = $changeRequest->relatedDepartments->pluck('department_id')->unique();

        foreach ($departments as $departmentId) {
            $manager = Employee::where('department_id', $departmentId)
                ->with('user')
                ->whereHas('user.permissions', fn($q) => $q->where('name', 'Review Action Plan'))
                ->first();

            if ($manager && $manager?->user) {
                // Cek kalau approval untuk manager ini belum ada
                $exists = $changeRequest->approvals()
                    ->where('stage', 'Review Action Plan')
                    ->where('approver_id', $manager->id)
                    ->exists();

                if (!$exists) {
                    $changeRequest->approvals()->create([
                        'stage'       => 'Review Action Plan',
                        'decision'    => 'Pending',
                        'approver_id' => $manager->id,
                    ]);
                }
            }
        }
    }

    protected function updateStatusOpenActionPlan($actionPlans)
    {
        $actionPlans =  $actionPlans->get();
        // Bulk update semua action plan jadi "Open"
        $actionPlans->each->update(['status' => 'Open']);

        // Ambil semua user PIC unik
        $users = User::whereHas('employee', function ($q) use ($actionPlans) {
            $q->whereIn('department_id', $actionPlans->pluck('department_id'));
        })
            ->permission('PIC Action Plan')
            ->distinct()
            ->get();
        $firstCR = $actionPlans->first()?->changeRequest;

        // Kirim notifikasi per user unik
        foreach ($users as $user) {
            $this->sendNotification($user, [
                'name'      => $user->name,
                'request_number' => $firstCR?->request_number,
                'title'     => $firstCR?->title,
                'initiator' => $firstCR?->initiator_name,
                'department' => $firstCR?->department->name,
            ], 'open-action-plan-notification.txt', __("Action Plan Opened"));
        }
    }

    protected function stageCanUpdateData(string $stage): bool
    {
        return in_array($stage, [
            'Approve Manager',
            'Approve QA SPV',
        ]);
    }


    // Private Methods
    private function saveMainChangeRequest(array $data)
    {
        $isDraft = filter_var($data['isDraft'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $status = $isDraft
            ? 'Draft'
            : (in_array($data['overall_status'] ?? null, ['Draft', 'Rejected'], true)
                ? 'Pending'
                : ($data['overall_status'] ?? 'Pending'));

        $changeRequest = ChangeRequest::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'request_number' => $this->generateFirstRequestNumber($data),
                'employee_id' => $data['employee_id'] ?? Auth::user()?->employee?->id,
                'department_id' => $data['department_id'] ?? Auth::user()?->employee?->department_id,
                'title' => $data['title'],
                'requested_date' => $data['requested_date'] ?? Carbon::now(),
                'initiator_name' => $data['initiator_name'] ?? Auth::user()->name,
                'current_status' => $data['current_status'] ?? 'Pending',
                'proposed_change' => $data['proposed_change'] ?? '',
                'reason' => $data['reason'] ?? '',
                'overall_status' => $status,
            ]
        );
        $changeRequest->save();
        return $changeRequest;
    }

    private function syncScopes(ChangeRequest $changeRequest, array $scopes)
    {
        $scopeIds = ScopeOfChange::whereIn('name', $scopes)->pluck('id')->toArray();
        $changeRequest->scopeOfChange()->sync($scopeIds);
    }

    private function syncTypes(ChangeRequest $changeRequest, array $types)
    {
        $changeRequest->typeOfChange()->delete();
        $typeData = [];
        foreach ($types as $type) {
            $typeData[] = ['id' => Str::uuid(), 'type_name' => $type];
        }
        $changeRequest->typeOfChange()->createMany($typeData);
    }

    private function saveImpactRiskAssessment(ChangeRequest $changeRequest, array $data)
    {
        ImpactRiskAssesment::updateOrCreate(
            ['change_request_id' => $changeRequest->id],
            [
                'source_of_risk' => $data['source_of_risk'] ?? '',
                'impact_of_risk' => $data['impact_of_risk'] ?? '',
                'severity' => $data['severity'] ?? 0,
                'cause_of_risk' => $data['cause_of_risk'] ?? '',
                'probability' => $data['probability'] ?? 0,
                'control_implemented' => $data['control_implemented'] ?? '',
                'detectability' => $data['detectability'] ?? 0,
                'rpn' => $data['rpn'] ?? 0,
                'risk_category' => $data['risk_category'] ?? ''
            ]
        );
    }



    private function saveRelatedDepartments(ChangeRequest $changeRequest, array $departments)
    {
        // Hapus related departments lama
        $changeRequest->relatedDepartments()->forceDelete();

        $relatedDepartments = [];
        foreach ($departments as $department) {
            $relatedDepartments[] = [
                'id' => Str::uuid(),
                'change_request_id' => $changeRequest->id,
                'department_id' => $department['department_id'] ?? null,
            ];
        }
        RelatedDepartment::insert($relatedDepartments);
    }
    private function saveImpactOfChangeAssessment(ChangeRequest $changeRequest, array $data)
    {
        ImpactOfChangeAssesment::updateOrCreate(
            ['change_request_id' => $changeRequest->id],
            [
                'product_affected' => $data['product_affected'] ?? null,

                'third_party_involved' => $data['third_party_involved'] ?? null,
                'third_party_name' => $data['third_party_name'] ?? null,
            ]
        );
        if (($data['product_affected'] ?? '') === 'Yes' && !empty($data['affected_products'])) {
            $changeRequest->affectedProducts()->sync($data['affected_products']);
        } else {
            $changeRequest->affectedProducts()->sync([]);
        }
    }
    private function saveSupportingAttachments(
        ChangeRequest $changeRequest,
        array $files,
        array $names = [],
        array $keepIds = []
    ) {
        $changeRequest->loadMissing('supportingAttachments');

        foreach ($changeRequest->supportingAttachments as $index => $attachment) {
            if (!in_array($attachment->id, $keepIds)) continue;

            $newName = $names[$index] ?? null;

            if ($newName && $attachment->original_name !== $newName) {
                $attachment->update([
                    'original_name' => $newName,
                ]);
            }
        }

        $changeRequest->supportingAttachments()
            ->whereNotIn('id', $keepIds)
            ->get()
            ->each(function ($attachment) {
                Storage::delete($attachment->file_path);
                $attachment->forceDelete();
            });

        foreach ($files as $i => $file) {
            if (!$file instanceof UploadedFile) continue;

            $frontendName = $names[$i] ?? $file->getClientOriginalName();

            $path = $this->storeWithFrontendName(
                $file,
                $frontendName,
                'supporting_attachment'
            );

            ChangeRequestSupportingAttachment::create([
                'id' => Str::uuid(),
                'change_request_id' => $changeRequest->id,
                'file_path' => $path,
                'original_name' => $frontendName,
            ]);
        }
    }

    private function saveCurrentStatusFile(
        ChangeRequest $changeRequest,
        array $files,
        array $names = [],
        array $keepIds = []
    ) {
        $changeRequest->loadMissing('currentStatusFiles');

        foreach ($changeRequest->currentStatusFiles as $index => $attachment) {
            if (!in_array($attachment->id, $keepIds)) continue;

            $newName = $names[$index] ?? null;

            if ($newName && $attachment->original_name !== $newName) {
                $attachment->update([
                    'original_name' => $newName,
                ]);
            }
        }

        $changeRequest->currentStatusFiles()
            ->whereNotIn('id', $keepIds)
            ->get()
            ->each(function ($attachment) {
                Storage::delete($attachment->file_path);
                $attachment->forceDelete();
            });

        foreach ($files as $i => $file) {
            if (!$file instanceof UploadedFile) continue;

            $frontendName = $names[$i] ?? $file->getClientOriginalName();

            $path = $this->storeWithFrontendName(
                $file,
                $frontendName,
                'current_status_files'
            );

            CurrentStatusFile::create([
                'id' => Str::uuid(),
                'change_request_id' => $changeRequest->id,
                'file_path' => $path,
                'original_name' => $frontendName,
            ]);
        }
    }

    private function saveProposedChangeFile(
        ChangeRequest $changeRequest,
        array $files,
        array $names = [],
        array $keepIds = []
    ) {
        $changeRequest->loadMissing('proposedChangeFiles');

        foreach ($changeRequest->proposedChangeFiles as $index => $attachment) {
            if (!in_array($attachment->id, $keepIds)) continue;

            $newName = $names[$index] ?? null;

            if ($newName && $attachment->original_name !== $newName) {
                $attachment->update([
                    'original_name' => $newName,
                ]);
            }
        }

        $changeRequest->proposedChangeFiles()
            ->whereNotIn('id', $keepIds)
            ->get()
            ->each(function ($attachment) {
                Storage::delete($attachment->file_path);
                $attachment->forceDelete();
            });

        foreach ($files as $i => $file) {
            if (!$file instanceof UploadedFile) continue;

            $frontendName = $names[$i] ?? $file->getClientOriginalName();

            $path = $this->storeWithFrontendName(
                $file,
                $frontendName,
                'proposed_change_files'
            );

            ProposedChangeFile::create([
                'id' => Str::uuid(),
                'change_request_id' => $changeRequest->id,
                'file_path' => $path,
                'original_name' => $frontendName,
            ]);
        }
    }

    private function storeWithFrontendName(UploadedFile $file, string $frontendName, string $directory): string
    {
        $extension = pathinfo($frontendName, PATHINFO_EXTENSION);
        $baseName = Str::slug(
            pathinfo($frontendName, PATHINFO_FILENAME),
            '_'
        );
        $uniqueSuffix = '_' . Carbon::now()->timestamp;
        $fileName = "{$baseName}{$uniqueSuffix}.{$extension}";

        return $file->storeAs($directory, $fileName);
    }



    private function createFirstApproval(ChangeRequest $changeRequest)
    {
        $manager = Employee::where('department_id', Auth::user()?->employee?->department_id)
            ->whereHas('user.permissions', function ($q) {
                $q->where('name', 'Approve Manager');
            })
            ->first();

        ChangeRequestApproval::create([
            'id' => Str::uuid(),
            'change_request_id' => $changeRequest->id,
            'stage' => 'Approve Manager',
            'approver_id' => $manager->id,
            'decision' => 'Pending'
        ]);
        $this->sendNotification($manager->user, [
            'name' => $manager->name,
            'initiator' => $changeRequest?->employee?->employee_code,
            'title' => $changeRequest->title,
        ], 'new-change-request-notification.txt', __("New Change Request"));
    }

    private function findApproverId(string $permissionName)
    {
        $user = User::permission($permissionName)->first();
        return $user?->employee?->id;
    }

    private function generateRequestNumber(ChangeRequest $changeRequest)
    {
        return DB::transaction(function () use ($changeRequest) {
            $year = $changeRequest->created_at->format('Y');

            $last = ChangeRequest::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderBy('request_number', 'desc')
                ->first();

            $next = $last
                ? ((int) substr($last->request_number, -4)) + 1
                : 1;

            return sprintf("CC-%s-%04d", $year, $next);
        });
    }
    private function generateFirstRequestNumber($data)
    {
        if (isset($data['id']) && $existing = ChangeRequest::find($data['id'])) {
            return $existing->request_number;
        }

        $year = now()->format('Y');
        $count = ChangeRequest::whereYear('created_at', $year)->count();

        return sprintf("CC-%04d", $count + 1);
    }


    // Protected Methods
    protected function sendNotification($user, $data, $messageFile, $subject)
    {
        // $this->sendNotificationRepository->sendEmailMessageWithAttachment(
        //     $user,
        //     $data,
        //     $messageFile,
        //     $subject
        // );

        $this->sendNotificationRepository->sendWhatsappMessage(
            $user->employee->phone,
            $data,
            $messageFile
        );
    }

    private function cleanFileName(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Hapus timestamp di akhir: _1234567890
        $name = preg_replace('/_\d+$/', '', $name);

        return $name;
    }
}
