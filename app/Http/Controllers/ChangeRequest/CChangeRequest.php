<?php

namespace App\Http\Controllers\ChangeRequest;

use App\Models\Product;
use App\Models\ActionPlan;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ChangeRequest;
use App\Models\ScopeOfChange;
use App\Traits\ResponseOutput;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CompletionProofFile;
use App\Http\Controllers\Controller;
use App\Services\Fpdi\PdfWithFooter;
use App\Models\FollowUpImplementation;
use App\Models\ImpactOfChangeCategory;
use App\Models\ActionPlanOverdueRequest;
use App\Http\Requests\ChangeRequest\OverdueRequest;
use App\Http\Requests\ChangeRequest\ProofOfWorkRequest;
use App\Http\Requests\ChangeRequest\ChangeRequestRequest;
use App\Http\Requests\ChangeRequest\ApproveOverdueRequest;
use App\Http\Requests\ChangeRequest\ImpactOfChangeRequest;
use App\Repositories\ChangeRequest\ChangeRequestRepository;
use App\Http\Requests\ChangeRequest\ChangeInitiationRequest;
use App\Http\Requests\ChangeRequest\ChangeRequestDraftRequest;
use App\Http\Requests\ChangeRequest\ImpactRiskAssesmentRequest;
use App\Repositories\SendNotification\SendNotificationRepository;

class CChangeRequest extends Controller
{
    use ResponseOutput;
    protected $changeRequestRepository, $sendNotificationRepository;

    public function __construct(ChangeRequestRepository $changeRequestRepository, SendNotificationRepository $sendNotificationRepository)
    {
        $this->changeRequestRepository = $changeRequestRepository;
        $this->sendNotificationRepository = $sendNotificationRepository;
    }


    // MAIN MODULES

    public function index()
    {

        return inertia('Change-Request/Index', [
            'title' => __('Change Requests'),

        ]);
    }

    public function create()
    {
        return inertia('Change-Request/Form', [
            'title' => __('Create Change Request'),
            'scopes' => ScopeOfChange::cursor(),
            'departments' => Department::orderBy('name', 'asc')->cursor(),
            'products' => Product::cursor(),
        ]);
    }
    public function edit(ChangeRequest $changeRequest)
    {
        return inertia('Change-Request/Form', [
            'title' => __('Edit Change Request'),
            'scopes' => ScopeOfChange::cursor(),
            'products' => Product::cursor(),
            'departments' => Department::orderBy('name', 'asc')->cursor(),
            'changeRequest' => $changeRequest->load([
                'scopeOfChange',
                'affectedProducts',
                'typeOfChange',
                'impactRiskAssesment',
                'relatedDepartments',
                'employee.user' => fn($q) => $q->select('id', 'email')
            ])->append(['current_status_file_urls', 'proposed_change_file_urls', 'supporting_attachment_urls', 'current_status_files_meta', 'proposed_change_files_meta', 'supporting_attachments_meta'])
        ]);
    }
    public function qaApproval(ChangeRequest $changeRequest)
    {
        return inertia('Change-Request/FormQA', [
            'title' => __('QA Approval'),
            'changeRequest' => $changeRequest->load('impactRiskAssesment')
        ]);
    }

    public function changeInitiation(ChangeInitiationRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $request->validated();
            return back()->with('success', 'Step one saved');
        });
    }
    public function impactRiskAssesment(ImpactRiskAssesmentRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $request->validated();
            if ($request->isLastStep) {
                $changeRequest = ChangeRequest::find($request->change_request_id);
                $this->changeRequestRepository->saveQaRiskAssessment($changeRequest, $request);
                $this->changeRequestRepository->closeChangeRequest($changeRequest, $request);
                return redirect()->route('change-requests.index')->with('success', __('Change Request has been signed'));
            }
            return back()->with('success', 'Impact Risk assessment saved');
        });
    }

    public function closeRequest(ChangeRequest $changeRequest, Request $request)
    {
        return $this->safeInertiaExecute(function () use ($changeRequest, $request) {
            $this->changeRequestRepository->closeChangeRequest($changeRequest, $request);
            return redirect()->route('change-requests.index')->with('success', __('Change Request has been signed'));
        });
    }

    public function ImpactOfChangeAssesment(ImpactOfChangeRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $request->validated();
            return back()->with('success', 'Impact of Change assessment saved');
        });
    }
    public function store(ChangeRequestRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $this->changeRequestRepository->StoreChangeRequest($data);
            if (isset($data['id'])) {
                return redirect()->route('change-requests.index')->with('success', __("Change Request updated successfully"));
            } else {
                return redirect()->route('change-requests.index')->with('success', __("Change Request created successfully"));
            }
        });
    }

    public function saveAsDraft(ChangeRequestDraftRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $this->changeRequestRepository->saveAsDraft($data);
            return redirect()->route('change-requests.index')->with('success', __("Change Request Saved as Draft successfully"));
        });
    }

    public function show(ChangeRequest $changeRequest)
    {

        return inertia('Change-Request/Show', [
            'title' => __('Change Request Details'),
            'impactOfChangeCategories' => ImpactOfChangeCategory::cursor(),
            'changeRequest' => $changeRequest->load([
                'employee.user',
                'affectedProducts',
                'scopeOfChange',
                'typeOfChange',
                'impactRiskAssesment',
                'qaRiskAssesment',
                'department',
                'regulatory',
                'relatedDepartments.department:id,name',
                'followUpImplementations',
                'closing',
                'approvals',
                'actionPlans' => fn($q) => $q->with(['department', 'impactCategory']),
            ])->append([
                'current_status_file_urls',
                'proposed_change_file_urls',
                'supporting_attachment_urls',
                'current_status_files_meta',
                'proposed_change_files_meta',
                'supporting_attachments_meta'
            ])
        ]);
    }

    function destroy(ChangeRequest $changeRequest)
    {
        return $this->safeInertiaExecute(function () use ($changeRequest) {
            $changeRequest->delete();
            return redirect()->back()->with('success', __('Change Request deleted successfully'));
        });
    }


    // Review & Approval

    public function reviewRnd(ChangeRequest $changeRequest)
    {

        return inertia('Regulatory', [
            'changeRequest' => $changeRequest->load('employee.user', 'scopeOfChange', 'typeOfChange', 'impactRiskAssesment', 'qaRiskAssesment', 'actionPlans.department', 'department', 'followUpImplementations')->append(['current_status_file_urls', 'proposed_change_file_urls', 'supporting_attachment_urls']),
        ]);
    }

    public function approveValidate(Request $request)
    {
        $request->validate([
            'decision' => 'required|in:Approved,Rejected,Agree,Disagree,Agree Not Impacted',
            'comments' => 'required|string|max:1000',
        ]);
    }
    public function approve(ChangeRequest $changeRequest, Request $request)
    {

        return $this->safeInertiaExecute(function () use ($request, $changeRequest) {
            if ($changeRequest->overall_status == 'Pending') {
                $this->changeRequestRepository->StoreChangeRequest($request->except('decision', 'comments'));
            }
            $this->changeRequestRepository->approve($request, $changeRequest);
            if ($request->input('decision') === "Approved") {
                return redirect()->route('change-requests.index')->with('success', __('Change Request approved successfully'));
            } else {
                return redirect()->route('change-requests.index')->with('success', __('Change Request rejected successfully'));
            }
        });
    }

    public function reviewStore(ChangeRequest $changeRequest, Request $request)
    {
        return $this->safeInertiaExecute(function () use ($request, $changeRequest) {
            $followUpImplementation =  $this->changeRequestRepository->reviewStore($request, $changeRequest);
            if ($request->decision == "Agree") {
                return redirect()->route('change-requests.follow-up-implementations.index', ['id' => $followUpImplementation->id])->with('success', __('Change Request reviewed successfully'));
            }
            return redirect()->back()->with('success', __('Change Request reviewed successfully'));
        });
    }

    // Action Plan

    public function savePendingActionPlan(Request $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $this->changeRequestRepository->savePendingActionPlan($request);
            return back()->with('success', __("Follow Up Implementation Has Been Submitted"));
        });
    }

    public function proofUpload(ProofOfWorkRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $actionPlan = ActionPlan::find($data['id']);
            if ($request->hasFile('completion_proof_file')) {

                $files = [];

                foreach ($request->file('completion_proof_file') as $file) {
                    $path = $file->store('proof-of-work', 'public');
                    $files[] = [
                        'id' => Str::uuid(),
                        'action_plan_id' => $actionPlan->id,
                        'file_path'      => $path,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }

                // 1 QUERY SAJA!
                CompletionProofFile::insert($files);
            }
            $actionPlan->save();
            $this->changeRequestRepository->submitActionPlan($request);
            return redirect()->back()->with('success', __("Follow-up implementation has been successfully uploaded."));
        });
    }

    public function approveActionPlan(ActionPlan $actionPlan)
    {
        return $this->safeInertiaExecute(function () use ($actionPlan) {
            $this->changeRequestRepository->approveActionPlan($actionPlan);
            return redirect()->back()->with('success', __("Follow Up Implementation Has Been Approved"));
        });
    }

    public function rejectActionPlan(ActionPlan $actionPlan)
    {
        return $this->safeInertiaExecute(function () use ($actionPlan) {
            $this->changeRequestRepository->rejectActionPlan($actionPlan);
            return redirect()->back()->with('success', __("Follow Up Implementation Has Been Rejected"));
        });
    }


    public function requestOverdue(OverdueRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $actionPlanId = $request->id;

            // Hitung overdue yang sudah ada untuk action plan ini
            $existingOverdueCount = ActionPlanOverdueRequest::where('action_plan_id', $actionPlanId)
                ->whereIn('status', ['Pending', 'Approved', 'Rejected'])
                ->count();

            if ($existingOverdueCount >= 2) {
                return redirect()->back()->with('error', __('Maximum 2 overdue requests allowed for this Action Plan'));
            }
            $this->changeRequestRepository->requestOverdue($request);
            return redirect()->back()->with('success', __("Overdue Request Has Been Sent"));
        });
    }
    public function approveOverdue(ApproveOverdueRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {

            $this->changeRequestRepository->approveOverdue($request);
            return redirect()->back()->with('success', __("Overdue Request Has Been Approved"));
        });
    }

    public function rejectOverdue(Request $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $this->changeRequestRepository->rejectOverdue($request);
            return redirect()->back()->with('success', __("Overdue Request Has Been Rejected"));
        });
    }



    // Related Department Assessment Module
    public function updateRelatedDepartmentAssessment(Request $request, FollowUpImplementation $relatedDepartmentAssessment)
    {
        return $this->safeInertiaExecute(function () use ($request, $relatedDepartmentAssessment) {
            $data = $request->validate([
                'comments' => 'nullable|string|max:1000',
                'evaluation_status' => 'required|in:Agree,Disagree,Agree Not Impacted',
            ]);
            $relatedDepartmentAssessment->update($data);
            return redirect()->back()->with('success', __('Related Department Assessment updated successfully'));
        });
    }

    // Print

    // public function print(ChangeRequest $changeRequest)
    // {

    //     $version = $changeRequest->updated_at->timestamp;

    //     $cachePath = storage_path(
    //         "app/pdf/change-request-{$changeRequest->id}-{$version}.pdf"
    //     );
    //     foreach (glob(storage_path("app/pdf/change-request-{$changeRequest->id}-*.pdf")) as $old) {
    //         @unlink($old);
    //     }

    //     if (file_exists($cachePath)) {
    //         return response()->file($cachePath);
    //     }

    //     $changeRequest->load([
    //         'employee.user',
    //         'affectedProducts',
    //         'scopeOfChange',
    //         'typeOfChange',
    //         'impactRiskAssesment',
    //         'qaRiskAssesment',
    //         'department',
    //         'regulatory',
    //         'relatedDepartments.department',
    //         'followUpImplementations',
    //         'closing',
    //         'approvals',
    //         'actionPlans' => fn($q) => $q->with(['department', 'impactCategory']),
    //     ])->append([
    //         'current_status_file_urls',
    //         'proposed_change_file_urls',
    //         'supporting_attachment_urls',
    //     ]);

    //     $mainPdf = Pdf::loadView(
    //         'print.change-request.index',
    //         compact('changeRequest')
    //     )->setPaper('A4');

    //     $tmpDir = storage_path('app/tmp');
    //     if (!is_dir($tmpDir)) {
    //         mkdir($tmpDir, 0755, true);
    //     }

    //     $tmpMainPath = $tmpDir . '/main-' . uniqid() . '.pdf';
    //     file_put_contents($tmpMainPath, $mainPdf->output());

    //     $pdf = new Fpdi();

    //     // main pdf
    //     $pageCount = $pdf->setSourceFile($tmpMainPath);
    //     for ($i = 1; $i <= $pageCount; $i++) {
    //         $tpl = $pdf->importPage($i);
    //         $pdf->AddPage();
    //         $pdf->useTemplate($tpl);
    //     }

    //     // attachments
    //     $attachments = array_merge(
    //         $changeRequest->current_status_file_urls ?? [],
    //         $changeRequest->proposed_change_file_urls ?? [],
    //         $changeRequest->supporting_attachment_urls ?? []
    //     );

    //     foreach ($attachments as $file) {
    //         $path = is_array($file) ? ($file['path'] ?? null) : $file;
    //         if (!$path || !str_ends_with(strtolower($path), '.pdf')) continue;

    //         if (str_starts_with($path, 'http')) {
    //             $path = parse_url($path, PHP_URL_PATH);
    //         }

    //         $path = str_replace('/storage/', '', $path);
    //         $fullPath = storage_path('app/public/' . ltrim($path, '/'));

    //         if (!file_exists($fullPath)) continue;

    //         $pageCount = $pdf->setSourceFile($fullPath);
    //         for ($i = 1; $i <= $pageCount; $i++) {
    //             $tpl = $pdf->importPage($i);
    //             $pdf->AddPage();
    //             $pdf->useTemplate($tpl);
    //         }
    //     }

    //     @unlink($tmpMainPath);

    //     if (!is_dir(dirname($cachePath))) {
    //         mkdir(dirname($cachePath), 0755, true);
    //     }

    //     $pdf->Output('F', $cachePath);

    //     return response()->file(
    //         $cachePath,
    //         [
    //             'Content-Type'        => 'application/pdf',
    //             'Content-Disposition' => 'inline; filename="change-request-' . $changeRequest->request_number . '.pdf"',
    //         ]
    //     );
    // }

    public function print(ChangeRequest $changeRequest)
    {
        $version   = $changeRequest->updated_at->timestamp;
        $cacheDir  = storage_path('app/pdf');
        $cachePath = "{$cacheDir}/change-request-{$changeRequest->id}-{$version}.pdf";

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        // if (!app()->isLocal() && file_exists($cachePath) && filesize($cachePath) > 0) {
        //     return response()->file($cachePath, [
        //         'Content-Type'        => 'application/pdf',
        //         'Content-Disposition' =>
        //         'inline; filename="change-request-' . $changeRequest->request_number . '.pdf"',
        //     ]);
        // }


        foreach (glob("{$cacheDir}/change-request-{$changeRequest->id}-*.pdf") as $old) {
            if ($old !== $cachePath) {
                @unlink($old);
            }
        }

        $changeRequest->load([
            'employee.user',
            'affectedProducts',
            'scopeOfChange',
            'typeOfChange',
            'impactRiskAssesment',
            'qaRiskAssesment',
            'department',
            'regulatory',
            'relatedDepartments.department',
            'followUpImplementations',
            'impactOfChangeAssesment',
            'closing',
            'approvals',
            'actionPlans' => fn($q) => $q->with(['department', 'impactCategory', 'pic.user']),
        ])->append([
            'current_status_file_urls',
            'proposed_change_file_urls',
            'supporting_attachment_urls',
        ]);



        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0755, true);
        $kopPortraitPath  = "{$tmpDir}/kop-portrait.pdf";
        $kopLandscapePath = "{$tmpDir}/kop-landscape.pdf";

        Pdf::loadView('print.kop')->setPaper('A4', 'portrait')->save($kopPortraitPath);
        Pdf::loadView('print.kop')->setPaper('A4', 'landscape')->save($kopLandscapePath);
        $pdf = new PdfWithFooter();
        $pdf->AliasNbPages();
        $pdf->docNumber = $changeRequest->request_number;
        $pdf->docDate   = $changeRequest->requested_date;
        $pdf->setSourceFile($kopPortraitPath);
        $kopPortraitTpl = $pdf->importPage(1);

        $pdf->setSourceFile($kopLandscapePath);
        $kopLandscapeTpl = $pdf->importPage(1);

        $render = function ($view, $orientation) use ($changeRequest, $tmpDir, $pdf, $kopPortraitTpl, $kopLandscapeTpl) {
            $tmp = "{$tmpDir}/" . uniqid() . ".pdf";

            Pdf::loadView($view, compact('changeRequest'))
                ->setPaper('A4', $orientation)
                ->save($tmp);

            $pages = $pdf->setSourceFile($tmp);

            for ($i = 1; $i <= $pages; $i++) {
                $tpl = $pdf->importPage($i);
                if ($orientation === 'landscape') {
                    $pdf->AddPage('L');
                    $pdf->useTemplate($kopLandscapeTpl, 0, 0, 297);
                    $pdf->useTemplate($tpl, 0, 20, 297);
                } elseif ($orientation === 'portrait') {
                    $pdf->AddPage('P');
                    $pdf->useTemplate($kopPortraitTpl, 0, 0, 210);
                    $pdf->useTemplate($tpl, 0, 20, 210);
                } else {
                    $pdf->AddPage('P');
                    $pdf->useTemplate($kopPortraitTpl, 0, 0, 210);
                    $pdf->useTemplate($tpl, 0, 20, 210);
                }
            }

            @unlink($tmp);
        };

        $render('print.change-request.change-initiation', 'portrait');
        $render('print.change-request.risk-assessment', 'landscape');

        // Group action plans by impact of change category
        $groupedActionPlans = $changeRequest->actionPlans
            ->sortBy(function ($ap) {
                return $ap->impactCategory?->impact_of_change_category ?? '';
            })
            ->groupBy(function ($ap) {
                return $ap->impactCategory?->impact_of_change_category ?? 'Uncategorized';
            });

        // Pass groupedActionPlans to the view by encoding it into the changeRequest object or a separate variable if the view supports it.
        // Since the render function only takes compact('changeRequest'), we can attach it to the changeRequest object as a temporary property.
        $changeRequest->groupedActionPlans = $groupedActionPlans;

        $render('print.change-request.other', 'potrait');

        foreach (
            array_merge(
                $changeRequest->current_status_file_urls ?? [],
                $changeRequest->proposed_change_file_urls ?? [],
                $changeRequest->supporting_attachment_urls ?? []
            ) as $file
        ) {

            $path = is_array($file) ? ($file['path'] ?? null) : $file;
            if (!$path || !str_ends_with(strtolower($path), '.pdf')) continue;

            if (str_starts_with($path, 'http')) {
                $path = parse_url($path, PHP_URL_PATH);
            }

            $path = str_replace('/storage/', '', $path);
            $fullPath = storage_path('app/public/' . ltrim($path, '/'));

            if (!file_exists($fullPath)) continue;

            $pages = $pdf->setSourceFile($fullPath);
            for ($i = 1; $i <= $pages; $i++) {
                $tpl = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tpl);
            }
        }

        $pdf->Output('F', $cachePath);

        return response()->file($cachePath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' =>
            'inline; filename="change-request-' . $changeRequest->request_number . '.pdf"',
        ]);
    }
}
