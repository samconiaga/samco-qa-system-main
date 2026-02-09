<?php

namespace App\Http\Controllers\ChangeRequest;

use App\Http\Controllers\Controller;
use App\Models\ActionPlan;
use Illuminate\Http\Request;
use App\Traits\ResponseOutput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\FollowUpImplementation;
use App\Models\ImpactOfChangeCategory;
use App\Repositories\App\AppRepository;
use App\Models\FollowUpImplementationDetail;
use App\Http\Requests\ChangeRequest\FollowUpImplementationRequest;


class CFollowUpImplementation extends Controller
{
    use ResponseOutput;
    protected $appRepository;

    public function __construct(AppRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = $request->query('id');
        if (!$id) abort(404);
        //    dd(FollowUpImplementation::find($id));
        return inertia('FollowUpImplementation/Index', [
            'change_request_id' => $id,
            'impactOfChangeCategories' => ImpactOfChangeCategory::cursor(),
            'followUpImplementation' => FollowUpImplementation::with('changeRequest')->find($id)
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
    public function store(FollowUpImplementationRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {

            DB::transaction(function () use ($request) {
                $data = $request->validated();
                $impactCategory = $data['impact_of_change_category'] ?? $data['custom_category'] ?? null;
                $followUpImplementationDetail = $this->appRepository->updateOrCreateOneModel(
                    new FollowUpImplementationDetail(),
                    [
                        'follow_up_implementation_id' => $data['id'],
                        'impact_of_change_category' => $request->old_impact_category,

                    ],
                    ['impact_of_change_category' => $impactCategory, 'created_at' => now(), 'updated_at' => now()]
                );

                $this->appRepository->updateOrCreateOneModel(
                    new ActionPlan(),
                    [
                        'id' => $data['action_plan_id'] ?? null,
                        'change_request_id' => $data['change_request_id'],
                        'follow_up_implementation_detail_id' => $followUpImplementationDetail->id,
                    ],
                    [
                        'impact_of_change_description' => $data['impact_of_change_description'],
                        'deadline' => $data['deadline'],
                        'pic_id' => Auth::user()?->employee?->id,
                        'department_id' => Auth::user()?->employee?->department_id,
                    ]
                );
                return redirect()->back()->with('success', __('The follow up implementation has been created successfully.'));
            });
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
    public function destroy($id)
    {
        return $this->safeInertiaExecute(function () use ($id) {
            $actionPlan = ActionPlan::find($id);
            if ($actionPlan) {
                // hapus follow-up detail terkait
                $followUpDetail = FollowUpImplementationDetail::find($actionPlan->follow_up_implementation_detail_id);
                if ($followUpDetail) {
                    $followUpDetail->forceDelete();
                }

                // hapus action plan
                $actionPlan->forceDelete();
            }

            // redirect dengan id follow-up implementation
            return redirect()
                ->back()
                ->with('success', __('The follow up implementation has been deleted successfully.'));
        });
    }
}
