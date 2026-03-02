<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChangeRequest;
use App\Traits\ResponseOutput;
use App\Models\ImpactOfChangeAssesment;
use App\Http\Requests\PPICAssessmentRequest;
use App\Repositories\ChangeRequest\ChangeRequestRepository;

class PPICAssessmentController extends Controller
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

        return inertia('PPICAssessment/Index', [
            'changeRequestId' => $id,
            'impact_of_change_assessments' => ImpactOfChangeAssesment::where('change_request_id', $id)->first(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PPICAssessmentRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $changeRequest = ChangeRequest::find($data['change_request_id']);
            $this->changeRequestRepository->ppicAssessment($request, $changeRequest);

            return redirect()
                ->route('change-requests.show', $data['change_request_id'])
                ->with('success', __('PPIC Assessment saved successfully.'));
        });
    }
}
