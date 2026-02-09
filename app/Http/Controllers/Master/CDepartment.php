<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\DepartmentRequest;
use App\Models\Department;
use App\Repositories\App\AppRepository;
use App\Traits\ResponseOutput;
use Illuminate\Http\Request;

class CDepartment extends Controller
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
    public function index()
    {
        return inertia('Master/Departments');
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
    public function store(DepartmentRequest $request)
    {
       return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $this->appRepository->insertOneModel(new Department(), $data);
            return redirect()->back()->with('success', __('Department created successfully'));
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
    public function update(DepartmentRequest $request, Department $department)
    {
        return $this->safeInertiaExecute(function () use ($request, $department) {
            $data = $request->validated();
            $this->appRepository->updateOneModel($department, $data);
            return redirect()->back()->with('success', __('Department updated successfully'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        return $this->safeInertiaExecute(function () use ($department) {
            $this->appRepository->deleteOneModel($department);
            return redirect()->back()->with('success', __('Department deleted successfully'));
        });
    }
}
