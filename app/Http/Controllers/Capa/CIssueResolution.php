<?php

namespace App\Http\Controllers\Capa;

use App\Models\Issue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Capa\IssueResolutionRequest;
use App\Models\IssueResolution;
use App\Repositories\Issue\IssueRepository;
use App\Traits\ResponseOutput;

class CIssueResolution extends Controller
{
    use ResponseOutput;
    protected $issueRepository;
    public function __construct(IssueRepository $issueRepository)
    {
        $this->issueRepository = $issueRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $issue = Issue::with('resolution')
            ->where('id', $request->id)
            ->firstOrFail()
            ->append('issueResolutionFileUrls');
        return inertia('Capa/Partials/Form/IssueResolutionForm', [
            'issue' => $issue
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IssueResolutionRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $issue = Issue::findOrFail($data['id']);
            $this->issueRepository->resolutionStore($data, $issue);
            return redirect()->route('capa.issues.show', $issue->id)->with('success', __("Issue Resolution Submitted Successfully"));
        });
    }
    public function approve(Request $request, Issue $issue)
    {
        return $this->safeInertiaExecute(function () use ($request, $issue) {
            $this->issueRepository->approveResolution($issue);
            return redirect()->route('capa.issues.show', $issue->id)->with('success', __("Issue Resolution Resolved Successfully"));
        });
    }
    public function approveValidate(Request $request)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);
        return $request->all();
    }
    public function reject(Request $request, Issue $issue)
    {
        return $this->safeInertiaExecute(function () use ($request, $issue) {
            $data = $this->approveValidate($request);
            $this->issueRepository->rejectResolution($data, $issue);
            return redirect()->route('capa.issues.show', $issue->id)->with('success', __("Issue Resolution Has Been Rejected"));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
