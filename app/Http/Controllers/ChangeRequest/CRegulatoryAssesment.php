<?php

namespace App\Http\Controllers\ChangeRequest;

use Illuminate\Http\Request;
use App\Models\ChangeRequest;
use App\Traits\ResponseOutput;
use Illuminate\Support\Facades\DB;
use App\Models\RegulatoryAssesment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ChangeRequestApproval;
use App\Models\ImpactOfChangeAssesment;
use App\Repositories\App\AppRepository;
use App\Http\Requests\ChangeRequest\RegulatoryAssessmentRequest;
use App\Repositories\ChangeRequest\ChangeRequestRepository;

class CRegulatoryAssesment extends Controller
{
    use ResponseOutput;
    protected $changeRequestRepository;

    public function __construct(ChangeRequestRepository $changeRequestRepository)
    {
        $this->changeRequestRepository = $changeRequestRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = $request->query('id');
        if (!$id) abort(404);
        return inertia('RegulatoryAssessment/Form', [
            'changeRequestId' => $id,
            'status' => ChangeRequest::find($id)->pluck('overall_status'),
            'impact_of_change_assessments' => ImpactOfChangeAssesment::where('change_request_id', $id)->first(),
            'regulatory_assessments' => RegulatoryAssesment::where('change_request_id', $id)->first()
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
    public function store(RegulatoryAssessmentRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $changeRequest = ChangeRequest::find($data['change_request_id']);
            $this->changeRequestRepository->regulatoryAssessment($request,$changeRequest);

            return redirect()
                ->route('change-requests.show', $data['change_request_id'])
                ->with('success', __('Regulatory Assessment saved successfully.'));
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
