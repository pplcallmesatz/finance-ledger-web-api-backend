<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CategoryMaster;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryMasterResource;
use App\Http\Resources\CategoryMasterCollection;
use App\Http\Requests\CategoryMasterStoreRequest;
use App\Http\Requests\CategoryMasterUpdateRequest;

class CategoryMasterController extends Controller
{
    public function index(Request $request): CategoryMasterCollection
    {
        $this->authorize('view-any', CategoryMaster::class);

        $search = $request->get('search', '');

        $categoryMasters = CategoryMaster::search($search)
            ->latest()
            ->paginate();

        return new CategoryMasterCollection($categoryMasters);
    }

    public function store(
        CategoryMasterStoreRequest $request
    ): CategoryMasterResource {
        $this->authorize('create', CategoryMaster::class);

        $validated = $request->validated();

        $categoryMaster = CategoryMaster::create($validated);

        return new CategoryMasterResource($categoryMaster);
    }

    public function show(
        Request $request,
        CategoryMaster $categoryMaster
    ): CategoryMasterResource {
        $this->authorize('view', $categoryMaster);

        return new CategoryMasterResource($categoryMaster);
    }

    public function update(
        CategoryMasterUpdateRequest $request,
        CategoryMaster $categoryMaster
    ): CategoryMasterResource {
        $this->authorize('update', $categoryMaster);

        $validated = $request->validated();

        $categoryMaster->update($validated);

        return new CategoryMasterResource($categoryMaster);
    }

    public function destroy(
        Request $request,
        CategoryMaster $categoryMaster
    ): Response {
        $this->authorize('delete', $categoryMaster);

        $categoryMaster->delete();

        return response()->noContent();
    }
}
