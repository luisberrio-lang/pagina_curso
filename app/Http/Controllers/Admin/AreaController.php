<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::query()->ordered()->get();
        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.areas.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'        => 'required|string|max:120',
            'slug'        => 'nullable|string|max:140|unique:areas,slug',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'is_default'  => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_default'] = $r->boolean('is_default');

        DB::transaction(function () use ($data) {
            // Si marcaron default, apagamos todos antes
            if ($data['is_default']) {
                Area::query()->update(['is_default' => false]);
            }
            Area::create($data);
        });

        return redirect()->route('admin.areas.index')->with('ok', 'Área creada correctamente.');
    }

    public function edit(Area $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    public function update(Request $r, Area $area)
    {
        $data = $r->validate([
            'name'        => 'required|string|max:120',
            'slug'        => 'nullable|string|max:140|unique:areas,slug,' . $area->id,
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer',
            'is_default'  => 'nullable|boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_default'] = $r->boolean('is_default');

        DB::transaction(function () use ($data, $area) {
            if ($data['is_default']) {
                Area::query()->where('id', '!=', $area->id)->update(['is_default' => false]);
            }
            $area->update($data);
        });

        return redirect()->route('admin.areas.index')->with('ok', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area)
    {
        $area->delete();
        return back()->with('ok', 'Área eliminada.');
    }

    public function setDefault(Area $area)
    {
        Area::query()->update(['is_default' => false]);
        $area->update(['is_default' => true]);
        return back()->with('ok', 'Área por defecto actualizada.');
    }
}
