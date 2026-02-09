<?php

namespace App\Http\Controllers\Master;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\ResponseOutput;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\App\AppRepository;
use App\Http\Requests\Master\ProductRequest;

class CProduct extends Controller
{

    use ResponseOutput;
    protected $appRepository;
    public function __construct(AppRepository $appRepository)
    {
        $this->appRepository = $appRepository;
    }
    public function index()
    {

        return inertia('Master/Products');
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
    public function store(ProductRequest $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            $data = $request->validated();
            $this->appRepository->insertOneModel(new Product(), $data);
            return redirect()->back()->with('success', __('Product created successfully'));
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
    public function update(ProductRequest $request, Product $product)
    {
        return $this->safeInertiaExecute(function () use ($request, $product) {
            $data = $request->validated();
            $this->appRepository->updateOneModel($product, $data);
            return redirect()->back()->with('success', __('Product updated successfully'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return $this->safeInertiaExecute(function () use ($product) {
            DB::transaction(function () use ($product) {
                $this->appRepository->deleteOneModel($product);
            });
            return redirect()->back()->with('success', __('Product deleted successfully'));
        });
    }

    public function trash()
    {
        return inertia('Master/Trash/Products', [
            'title' => __('Trash'),
        ]);
    }
    public function delete(Request $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            Product::onlyTrashed()->whereIn('product_code', $request->product_code)->forceDelete();
            return redirect()->back()->with('success', __('Product deleted successfully'));
        });
    }
    public function restore(Request $request)
    {
        return $this->safeInertiaExecute(function () use ($request) {
            Product::onlyTrashed()->whereIn('product_code', $request->product_code)->restore();
            return redirect()->back()->with('success', __('Product restored successfully'));
        });
    }
}
