<?php

namespace App\Http\Controllers\Capa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Capa\IssueRequest;
use App\Models\CapaType;
use App\Models\Department;
use App\Models\Issue;
use App\Repositories\Issue\IssueRepository;
use App\Traits\ResponseOutput;
use Illuminate\Http\Request;

class CIssue extends Controller
{
    use ResponseOutput;
    protected $issueRepository;
    public function __construct(IssueRepository $issueRepository)
    {
        $this->issueRepository = $issueRepository;
    }

    public function index()
    {
        return inertia('Capa/Index', [
            'title' => __("Issues")
        ]);
    }
    public function create()
    {
        return inertia('Capa/Partials/Form/IssueForm', [
            'departments' => Department::cursor(),
            'capaTypes' => CapaType::cursor(),
        ]);
    }
    public function edit(Issue $issue)
    {
        return inertia('Capa/Partials/Form/IssueForm', [
            'departments' => Department::cursor(),
            'capaTypes' => CapaType::cursor(),
            'issue' => $issue
        ]);
    }
    public function store(IssueRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $this->issueRepository->issueStore($data);
            if (isset($data['id'])) {
                return redirect()->route('capa.issues.index')->with('success', __("Issue updated successfully"));
            } else {
                return redirect()->route('capa.issues.index')->with('success', __("Issue created successfully"));
            }
        });
    }
    public function show(Issue $issue)
    {
        $issue->load(['department:id,name', 'capaType:id,name', 'resolution', 'approvalHistory.approver'])->append('issueResolutionFileUrls');
        return inertia('Capa/Show', [
            'title' => __("Issue Detail"),
            'issue' => $issue
        ]);
    }

    public function accept(Issue $issue)
    {
        return $this->safeInertiaExecute(function () use ($issue) {
            $issue->update(['status' => "In Progress"]);
            return back()->with('success', __("Issue updated successfully"));
        });
    }

    public function print(Issue $issue)
    {
        return $this->safeExecute(function () use ($issue) {
            $issue->load(['department:id,name', 'capaType:id,name', 'resolution', 'approvalHistory.approver'])->append('issueResolutionFileUrls');
            return $this->responseSuccess(__("Successfully Obtaining Data"), $issue);
        });
    }
}
