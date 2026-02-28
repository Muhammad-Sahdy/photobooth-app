<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FilterController extends Controller
{
    public function index()
    {
        $filters = Filter::all();
        return view('admin.filters.index', compact('filters'));
    }

    public function store(Request $request)
    {
        // 1. Validasi disesuaikan HANYA untuk "The Golden 8"
        $request->validate([
            'name'       => 'required|string|max:255',
            'brightness' => 'nullable|integer',
            'contrast'   => 'nullable|integer',
            'highlights' => 'nullable|integer',
            'shadows'    => 'nullable|integer',
            'gamma'      => 'nullable|numeric|between:0.1,9.9',
            'warmth'     => 'nullable|integer',
            'tint'       => 'nullable|integer',
            'sharpen'    => 'nullable|integer',
        ]);

        // 2. Simpan parameter dengan nilai default 0 atau 1.0 jika dikosongkan
        Filter::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parameters' => [
                'brightness' => (int)($request->brightness ?? 0),
                'contrast'   => (int)($request->contrast ?? 0),
                'highlights' => (int)($request->highlights ?? 0),
                'shadows'    => (int)($request->shadows ?? 0),
                'gamma'      => (float)($request->gamma ?? 1.0),
                'warmth'     => (int)($request->warmth ?? 0),
                'tint'       => (int)($request->tint ?? 0),
                'sharpen'    => (int)($request->sharpen ?? 0),
                'greyscale'  => $request->has('greyscale'),
            ]
        ]);

        return back()->with('success', 'Filter premium berhasil ditambah!');
    }

    public function destroy($id)
    {
        Filter::findOrFail($id)->delete();
        return back()->with('success', 'Filter dihapus!');
    }
}
