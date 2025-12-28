<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Representatives\StoreRepresentativeRequest;
use App\Http\Requests\Representatives\UpdateRepresentativeRequest;
use App\Models\Representative;
use App\Services\Representatives\RepresentativeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepresentativeController extends Controller
{
    public function __construct(
        protected RepresentativeService $representativeService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('representatives.view');

        $filters = $request->only(['is_active', 'search', 'per_page']);
        $representatives = $this->representativeService->getAllRepresentatives($filters);

        // Calculate statistics
        $totalRepresentatives = Representative::count();
        $totalActive = Representative::where('is_active', true)->count();
        $totalInactive = Representative::where('is_active', false)->count();

        if ($request->expectsJson()) {
            return response()->json($representatives);
        }

        return view('admin.representatives.list', compact('representatives', 'totalRepresentatives', 'totalActive', 'totalInactive'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('representatives.create');

        return view('admin.representatives.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRepresentativeRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('representatives.create');

        $representative = $this->representativeService->createRepresentative($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم إنشاء المندوب بنجاح',
                'representative' => $representative,
            ], 201);
        }

        return redirect()->route('admin.representatives.index')
            ->with('success', 'تم إنشاء المندوب بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Representative $representative): View|JsonResponse
    {
        $this->authorize('representatives.view');

        if (request()->expectsJson()) {
            return response()->json($representative);
        }

        return view('admin.representatives.show', compact('representative'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Representative $representative): View
    {
        $this->authorize('representatives.update');

        return view('admin.representatives.edit', compact('representative'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRepresentativeRequest $request, Representative $representative): RedirectResponse|JsonResponse
    {
        $this->authorize('representatives.update');

        $representative = $this->representativeService->updateRepresentative($representative, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'تم تحديث المندوب بنجاح',
                'representative' => $representative,
            ]);
        }

        return redirect()->route('admin.representatives.index')
            ->with('success', 'تم تحديث المندوب بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Representative $representative): RedirectResponse|JsonResponse
    {
        $this->authorize('representatives.delete');

        $this->representativeService->deleteRepresentative($representative);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'تم حذف المندوب بنجاح',
            ]);
        }

        return redirect()->route('admin.representatives.index')
            ->with('success', 'تم حذف المندوب بنجاح');
    }
}
