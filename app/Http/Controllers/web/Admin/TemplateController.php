<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::all(); // Laravel auto-cast slots

        // ✅ DEBUG - cek casting jalan
        logger('Index templates', [
            'count' => $templates->count(),
            'first_slots_raw' => $templates->first()?->getRawOriginal('slots'),
            'first_slots_array' => is_array($templates->first()?->slots)
        ]);

        return view('admin.templates.index', compact('templates'));
    }


    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        // --- Di dalam Method store atau update ---

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => $request->isMethod('post') ? 'required|image|mimes:jpeg,png,jpg|max:2048' : 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'slot_count' => 'required|integer|min:1', // Ubah dari between:2,4 ke min:1
        ]);

        $slotCount = (int) $request->slot_count;
        $slots = [];

        for ($i = 1; $i <= $slotCount; $i++) {
            // Validasi dinamis untuk setiap slot yang ada
            $request->validate([
                "slot{$i}_x"      => 'required|integer|min:0',
                "slot{$i}_y"      => 'required|integer|min:0',
                "slot{$i}_width"  => 'required|integer|min:10',
                "slot{$i}_height" => 'required|integer|min:10',
            ]);

            $slots[] = [
                'x'      => (int) $request["slot{$i}_x"],
                'y'      => (int) $request["slot{$i}_y"],
                'width'  => (int) $request["slot{$i}_width"],
                'height' => (int) $request["slot{$i}_height"],
            ];
        }

        $templatePath = $request->file('file')->store('templates/master', 'public');
        $thumbnailPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('templates/thumbnails', 'public')
            : null;

        // ✅ NO json_encode()
        Template::create([
            'name' => $request->name,
            'file_path' => $templatePath,
            'thumbnail_path' => $thumbnailPath,
            'slot_count' => $slotCount,
            'slots' => $slots,  // ← ARRAY LANGSG
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Template berhasil!');
    }

    public function edit(Template $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        // --- Di dalam Method store atau update ---

        $request->validate([
            'name' => 'required|string|max:255',
            'file' => $request->isMethod('post') ? 'required|image|mimes:jpeg,png,jpg|max:2048' : 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'slot_count' => 'required|integer|min:1', // Ubah dari between:2,4 ke min:1
        ]);

        $slotCount = (int) $request->slot_count;
        $slots = [];

        for ($i = 1; $i <= $slotCount; $i++) {
            // Validasi dinamis untuk setiap slot yang ada
            $request->validate([
                "slot{$i}_x"      => 'required|integer|min:0',
                "slot{$i}_y"      => 'required|integer|min:0',
                "slot{$i}_width"  => 'required|integer|min:10',
                "slot{$i}_height" => 'required|integer|min:10',
            ]);

            $slots[] = [
                'x'      => (int) $request["slot{$i}_x"],
                'y'      => (int) $request["slot{$i}_y"],
                'width'  => (int) $request["slot{$i}_width"],
                'height' => (int) $request["slot{$i}_height"],
            ];
        }

        // Update files
        $data = ['name' => $request->name];
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($template->file_path);
            $data['file_path'] = $request->file('file')->store('templates/master', 'public');
        }
        if ($request->hasFile('thumbnail')) {
            if ($template->thumbnail_path) {
                Storage::disk('public')->delete($template->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }

        // ✅ NO json_encode() - Laravel casts handle otomatis
        $template->update(array_merge($data, [
            'slot_count' => $slotCount,
            'slots' => $slots,  // ← ARRAY LANGSG, bukan json_encode()
        ]));

        return redirect()->route('admin.templates.index')
            ->with('success', "Template '{$request->name}' berhasil diupdate dengan {$slotCount} slot!");
    }


    public function destroy(Template $template)
    {
        $template->delete();

        return redirect()
            ->route('admin.templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }
}
