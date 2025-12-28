<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Tag::withCount('products');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $tags = $query->orderBy('name')->paginate(20);

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
        ], [
            'name.required' => 'اسم التاغ مطلوب',
            'name.unique' => 'اسم التاغ مستخدم بالفعل',
        ]);

        Tag::create($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'تم إنشاء التاغ بنجاح');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tags,name,' . $tag->id],
        ], [
            'name.required' => 'اسم التاغ مطلوب',
            'name.unique' => 'اسم التاغ مستخدم بالفعل',
        ]);

        $tag->update($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'تم تحديث التاغ بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'تم حذف التاغ بنجاح');
    }

    /**
     * Check if tag exists (for AJAX requests).
     */
    public function checkDuplicate(Request $request)
    {
        $name = $request->input('name');
        
        if (!$name) {
            return response()->json(['exists' => false], 400);
        }

        $exists = Tag::where('name', $name)->exists();

        return response()->json([
            'exists' => $exists,
            'tag' => $exists ? Tag::where('name', $name)->first(['id', 'name']) : null,
        ]);
    }
}
