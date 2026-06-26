<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FacilityController extends Controller
{
    // ─── Public: List all facilities ────────────────────────────────────────

    public function index(Request $request): View
    {
        $search = trim($request->query('search', ''));
        $status = $request->query('status', '');

        $query = Facility::with(['equipment', 'feedbacks'])->latest();

        if ($search !== '') {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== '' && in_array($status, ['available', 'not_available'])) {
            $query->where('status', $status);
        }

        $facilities = $query->paginate(12)->appends($request->query());

        return view('facilities.index', compact('facilities', 'search', 'status'));
    }

    // ─── Public: Show single facility detail ────────────────────────────────

    public function show(Facility $facility): View
    {
        $facility->load('equipment');
        return view('facilities.show', compact('facility'));
    }

    // ─── Rental Officer: Create form ────────────────────────────────────────

    public function create(): View
    {
        return view('facilities.create');
    }

    // ─── Rental Officer: Store new facility ─────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                      => ['required', 'string', 'max:255'],
            'price'                     => ['required', 'numeric', 'min:0'],
            'status'                    => ['required', 'in:available,not_available'],
            'image'                     => ['nullable', 'image', 'max:2048'],
            'required_participants'     => ['required', 'integer', 'min:1', 'max:50'],

            'equipment'                 => ['nullable', 'array'],
            'equipment.*.name'          => ['required_with:equipment', 'string', 'max:255'],
            'equipment.*.price'         => ['required_with:equipment', 'numeric', 'min:0'],
            'equipment.*.status'        => ['required_with:equipment', 'in:available,not_available'],
            'equipment.*.quantity'      => ['required_with:equipment', 'integer', 'min:1'],
            'equipment.*.image'         => ['nullable', 'image', 'max:2048'],
        ]);

        $facilityImagePath = null;
        if ($request->hasFile('image')) {
            $facilityImagePath = $request->file('image')->store('facilities', 'public');
        }

        $facility = Facility::create([
            'name'                  => $data['name'],
            'price'                 => $data['price'],
            'status'                => $data['status'],
            'image'                 => $facilityImagePath,
            'required_participants' => $data['required_participants'],
        ]);

        if (!empty($data['equipment'])) {
            foreach ($data['equipment'] as $index => $eq) {
                $eqImagePath = null;
                if ($request->hasFile("equipment.{$index}.image")) {
                    $eqImagePath = $request->file("equipment.{$index}.image")
                                           ->store('equipment', 'public');
                }
                $facility->equipment()->create([
                    'name'     => $eq['name'],
                    'price'    => $eq['price'],
                    'status'   => $eq['status'],
                    'quantity' => $eq['quantity'],
                    'image'    => $eqImagePath,
                ]);
            }
        }

        return redirect()->route('facilities.index')
                         ->with('success', "Facility \"{$facility->name}\" has been created.");
    }

    // ─── Rental Officer: Edit form ───────────────────────────────────────────

    public function edit(Facility $facility): View
    {
        $facility->load('equipment');
        return view('facilities.edit', compact('facility'));
    }

    // ─── Rental Officer: Update facility ────────────────────────────────────

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $data = $request->validate([
            'name'                      => ['required', 'string', 'max:255'],
            'price'                     => ['required', 'numeric', 'min:0'],
            'status'                    => ['required', 'in:available,not_available'],
            'image'                     => ['nullable', 'image', 'max:2048'],
            'required_participants'     => ['required', 'integer', 'min:1', 'max:50'],

            'equipment'                 => ['nullable', 'array'],
            'equipment.*.id'            => ['nullable', 'integer', 'exists:equipment,id'],
            'equipment.*.name'          => ['required_with:equipment', 'string', 'max:255'],
            'equipment.*.price'         => ['required_with:equipment', 'numeric', 'min:0'],
            'equipment.*.status'        => ['required_with:equipment', 'in:available,not_available'],
            'equipment.*.quantity'      => ['required_with:equipment', 'integer', 'min:1'],
            'equipment.*.image'         => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($facility->image) Storage::disk('public')->delete($facility->image);
            $facility->image = $request->file('image')->store('facilities', 'public');
        }

        $facility->update([
            'name'                  => $data['name'],
            'price'                 => $data['price'],
            'status'                => $data['status'],
            'image'                 => $facility->image,
            'required_participants' => $data['required_participants'],
        ]);

        $submittedIds = collect($data['equipment'] ?? [])->pluck('id')->filter()->toArray();
        $facility->equipment()->whereNotIn('id', $submittedIds)->each(function (Equipment $eq) {
            if ($eq->image) Storage::disk('public')->delete($eq->image);
            $eq->delete();
        });

        foreach ($data['equipment'] ?? [] as $index => $eq) {
            if (!empty($eq['id'])) {
                $equipment   = Equipment::find($eq['id']);
                $eqImagePath = $equipment->image;
                if ($request->hasFile("equipment.{$index}.image")) {
                    if ($equipment->image) Storage::disk('public')->delete($equipment->image);
                    $eqImagePath = $request->file("equipment.{$index}.image")->store('equipment', 'public');
                }
                $equipment->update([
                    'name'     => $eq['name'],
                    'price'    => $eq['price'],
                    'status'   => $eq['status'],
                    'quantity' => $eq['quantity'],
                    'image'    => $eqImagePath,
                ]);
            } else {
                $eqImagePath = null;
                if ($request->hasFile("equipment.{$index}.image")) {
                    $eqImagePath = $request->file("equipment.{$index}.image")->store('equipment', 'public');
                }
                $facility->equipment()->create([
                    'name'     => $eq['name'],
                    'price'    => $eq['price'],
                    'status'   => $eq['status'],
                    'quantity' => $eq['quantity'],
                    'image'    => $eqImagePath,
                ]);
            }
        }

        return redirect()->route('facilities.index')
                         ->with('success', "Facility \"{$facility->name}\" has been updated.");
    }

    // ─── Rental Officer: Delete facility ────────────────────────────────────

    public function destroy(Facility $facility): RedirectResponse
    {
        if ($facility->image) Storage::disk('public')->delete($facility->image);
        foreach ($facility->equipment as $eq) {
            if ($eq->image) Storage::disk('public')->delete($eq->image);
        }

        $name = $facility->name;
        $facility->delete();

        return redirect()->route('facilities.index')
                         ->with('success', "Facility \"{$name}\" has been deleted.");
    }
}