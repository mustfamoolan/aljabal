<?php

namespace App\Http\Controllers\Api\Representatives;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\StoreRepresentativeRequest;
use App\Http\Requests\Representatives\UpdateRepresentativeRequest;
use App\Http\Resources\Representatives\RepresentativeResource;
use App\Models\Representative;
use App\Services\Representatives\RepresentativeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RepresentativeController extends Controller
{
    public function __construct(
        protected RepresentativeService $representativeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('representatives.view');

        $filters = $request->only(['is_active', 'search', 'per_page']);
        $representatives = $this->representativeService->getAllRepresentatives($filters);

        return response()->json([
            'data' => RepresentativeResource::collection($representatives->items()),
            'meta' => [
                'current_page' => $representatives->currentPage(),
                'last_page' => $representatives->lastPage(),
                'per_page' => $representatives->perPage(),
                'total' => $representatives->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRepresentativeRequest $request): JsonResponse
    {
        $this->authorize('representatives.create');

        $representative = $this->representativeService->createRepresentative($request->validated());

        return response()->json([
            'message' => 'تم إنشاء المندوب بنجاح',
            'data' => new RepresentativeResource($representative),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Representative $representative): JsonResponse
    {
        $this->authorize('representatives.view');

        return response()->json([
            'data' => new RepresentativeResource($representative),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRepresentativeRequest $request, Representative $representative): JsonResponse
    {
        $this->authorize('representatives.update');

        $representative = $this->representativeService->updateRepresentative($representative, $request->validated());

        return response()->json([
            'message' => 'تم تحديث المندوب بنجاح',
            'data' => new RepresentativeResource($representative),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Representative $representative): JsonResponse
    {
        $this->authorize('representatives.delete');

        $this->representativeService->deleteRepresentative($representative);

        return response()->json([
            'message' => 'تم حذف المندوب بنجاح',
        ]);
    }
}
