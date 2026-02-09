<?php

namespace App\Http\Controllers\Master\Attributes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\Attributes\ScopeOfChangeRequest;
use App\Models\ScopeOfChange;
use App\Repositories\App\AppRepository;


class CScopeOfChange extends Controller
{
    protected $appRepository;
    public function __construct(AppRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Master/Attribute-Types/ScopeOfChanges', [
            'title' => __('Scope of Changes'),
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
    public function store(ScopeOfChangeRequest $request)
    {
        try {
            $data = $request->validated();
            $this->appRepository->insertOneModel(new ScopeOfChange(), $data);
            return redirect()->back()->with('success', __('Scope of Change created successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to save: ') . $e->getMessage());
        }
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
    public function update(ScopeOfChangeRequest $request, ScopeOfChange $scopeOfChange)
    {
        try {
            $data = $request->validated();
            $this->appRepository->updateOneModel($scopeOfChange, $data);
            return redirect()->back()->with('success', __('Scope of Change updated successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update: ') . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScopeOfChange $scopeOfChange)
    {
        try {
            $this->appRepository->deleteOneModel($scopeOfChange);
            return redirect()->back()->with('success', __('Scope of Change deleted successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to delete: ') . $e->getMessage());
        }
    }
}
