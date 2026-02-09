<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\PositionRequest;
use App\Models\Position;
use App\Repositories\App\AppRepository;
use App\Traits\ResponseOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CPosition extends Controller
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

        return inertia('Master/Positions');
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
    public function store(PositionRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $this->appRepository->insertOneModel(new Position(), $data);
            return redirect()->back()->with('success', __('Position created successfully'));
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
    public function update(PositionRequest $request, Position $position)
    {
        return $this->safeInertiaExecute(function () use ($request, $position) {
            $data = $request->validated();
            $this->appRepository->updateOneModel($position, $data);
            return redirect()->back()->with('success', __('Position updated successfully'));
        });
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        return $this->safeInertiaExecute(function () use ($position) {
            $this->appRepository->deleteOneModel($position);
            return redirect()->back()->with('success', __('Position deleted successfully'));
        });
    }
}
