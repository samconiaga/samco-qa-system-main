<?php

namespace App\Repositories\Issue;

use App\Models\User;
use App\Models\Issue;
use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\IssueResolution;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Models\IssueResolutionFile;
use Illuminate\Support\Facades\Bus;
use App\Models\IssueApprovalHistory;
use Illuminate\Support\Facades\Auth;
use App\Repositories\App\AppRepository;
use Illuminate\Support\Facades\Storage;
use LaravelEasyRepository\Implementations\Eloquent;

class IssueRepositoryImplement extends Eloquent implements IssueRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $appRepository;
    protected Issue $model;

    public function __construct(AppRepository $appRepository, Issue $model)
    {
        $this->appRepository = $appRepository;
        $this->model = $model;
    }

    public function issueStore($data)
    {
        return DB::transaction(function () use ($data) {
            $issueNumber = $this->generateIssueNumber($data);
            if ($issueNumber) $data['issue_number'] = $issueNumber;
            $issue = $this->appRepository->updateOrCreateOneModel($this->model, ['id' => $data['id']], $data);
            if ($issue->wasRecentlyCreated) {
                $recipients = User::whereRelation('employee', 'department_id', $data['department_id'])->get();
                if ($recipients->isEmpty()) return;
                $this->sendNotification($recipients, [
                    'issue_number' => $issue->issue_number,
                    'subject' => $data['subject'],
                    'finding' => strip_tags($data['finding']),
                    'criteria' => $data['criteria'],
                    'deadline' => $data['deadline']
                ], 'new-issue-notification.txt', __("Action Required: New Issue"));
            }
            return $issue;
        });
    }

    public function resolutionStore($data, $issue)
    {
        return DB::transaction(function () use ($data, $issue) {
            $files = $data['issue_resolution_file'] ?? [];
            $data = Arr::except($data, [
                'issue_resolution_file'
            ]);
            $this->appRepository->updateOrCreateOneModel(new IssueResolution(), ['issue_id' => $data['id']], $data);

            $this->saveIssueResolutionFile($issue, $files);
            $issue->update(['status' => 'Submitted']);
            $issue->refresh();
            $historyData = [
                'issue_id' => $data['id'],
                'status' => $issue->status,
                'approver_id' => Auth::user()?->employee?->id
            ];
            $this->appRepository->insertOneModel(new IssueApprovalHistory, $historyData);

            $recipients =  User::whereHas('permissions', function ($q) {
                $q->where('name', 'Create CAPA');
            })->get();

            $this->sendNotification($recipients, [
                'issue_number' => $issue->issue_number,
                'subject' => $issue->subject,
                'finding' => strip_tags($issue->finding),
                'criteria' => $issue->criteria,
                'deadline' => $issue->deadline
            ], 'submit-issue-notification.txt', __("Action Required: Submitted Issue Resolution"));
            return $issue;
        });
    }


    public function approveResolution($issue)
    {
        DB::transaction(function () use ($issue) {
            $issue->update(['status' => 'Resolved']);
            $issue->refresh();
            $historyData = [
                'issue_id' => $issue->id,
                'status' => $issue->status,
                'approver_id' => Auth::user()?->employee?->id
            ];
            $this->appRepository->insertOneModel(new IssueApprovalHistory, $historyData);
            $recipients = User::whereRelation('employee', 'department_id', $issue->department_id)->get();
            if ($recipients->isEmpty()) return;
            $this->sendNotification($recipients, [
                'issue_number' => $issue->issue_number,
                'subject' => $issue->subject,
                'finding' => strip_tags($issue->finding),
                'criteria' => $issue->criteria,
                'deadline' => $issue->deadline
            ], 'resolve-issue-notification.txt', __("Issue Has Been Resolved"));
        });
    }
    public function rejectResolution($data, $issue)
    {
        DB::transaction(function () use ($data, $issue) {
            $issue->update(['status' => 'Rejected']);
            $issue->refresh();
            $historyData = [
                'issue_id' => $issue->id,
                'status' => $issue->status,
                'comment' => $data['comment'],
                'approver_id' => Auth::user()?->employee?->id
            ];
            $this->appRepository->insertOneModel(new IssueApprovalHistory, $historyData);
            $recipients = User::whereRelation('employee', 'department_id', $issue->department_id)->get();
            if ($recipients->isEmpty()) return;
            $this->sendNotification($recipients, [
                'issue_number' => $issue->issue_number,
                'subject' => $issue->subject,
                'finding' => strip_tags($issue->finding),
                'criteria' => $issue->criteria,
                'deadline' => $issue->deadline
            ], 'reject-issue-notification.txt', __("Issue Resolution Has Been Rejected"));
        });
    }

    private function saveIssueResolutionFile(Issue $issue, array $files)
    {
        $issue->loadMissing('issueResolutionfile');

        // Hapus file lama satu per satu
        foreach ($issue->issueResolutionfile as $attachment) {
            if (Storage::exists($attachment->file_path)) {
                Storage::delete($attachment->file_path);
            }
        }
        // Hapus record lama di DB
        $issue->issueResolutionfile()->forceDelete();
        foreach ($files as $file) {
            $path = $file->store('issue_resolution_file');
            IssueResolutionFile::create([
                'id' => Str::uuid(),
                'issue_id' => $issue->id,
                'file_path' => $path
            ]);
        }
    }
    private function generateIssueNumber($data)
    {
        if (isset($data['id']) && Issue::find($data['id'])) {
            return null;
        }
        $year = now()->format('Y');
        $count = Issue::whereYear('created_at', $year)->count();
        return sprintf("ISS-%s-%04d", $year, $count + 1);
    }



    private function sendNotification($recipients, $data, $template, $subject)
    {
        $user = $recipients->filter();
        Bus::batch(
            $user->map(
                fn($u) =>
                new SendNotificationJob(
                    $u,
                    array_merge(['name' => $u->name], $data),
                    $template,
                    $subject
                )
            )
        )->onQueue('notifications')->dispatch();
    }
}
