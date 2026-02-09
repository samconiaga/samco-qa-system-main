<?php

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Traits\ResponseOutput;
use App\Http\Controllers\Controller;
use App\Repositories\App\AppRepository;
use App\Http\Requests\Master\CapaTypeRequest;
use App\Models\CapaType;

class CCapaType extends Controller
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
        return inertia('Master/CapaType');
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
    public function store(CapaTypeRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $this->appRepository->insertOneModel(new CapaType(), $data);
            return redirect()->back()->with('success', __('Capa Type created successfully'));
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
    public function update(CapaTypeRequest $request, CapaType $capaType)
    {
        return $this->safeInertiaExecute(function () use ($request, $capaType) {
            $data = $request->validated();
            $this->appRepository->updateOneModel($capaType, $data);
            return redirect()->back()->with('success', __('Capa Type updated successfully'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CapaType $capaType)
    {
        return $this->safeInertiaExecute(function () use ($capaType) {
            $this->appRepository->deleteOneModel($capaType);
            return redirect()->back()->with('success', __('Capa Type deleted successfully'));
        });
    }


    public function trash()
    {
        return inertia('Master/Trash/CapaType', [
            'title' => __('Trash'),
        ]);
    }
    public function delete(Request $request)
    {
       
        return $this->safeInertiaExecute(function () use ($request) {
            CapaType::onlyTrashed()->whereIn('id', $request->id)->forceDelete();
            return redirect()->back()->with('success', __('Capa Type deleted successfully'));
        });
    }
    public function restore(Request $request)
    { 
        return $this->safeInertiaExecute(function () use ($request) {
            CapaType::onlyTrashed()->whereIn('id', $request->id)->restore();
            return redirect()->back()->with('success', __('Capa Type restored successfully'));
        });
    }
}
