<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResidentRequest;
use App\Models\Resident;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Resident::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('purok')) {
            $query->where('purok', $request->purok);
        }

        $residents = $query->paginate(10)->withQueryString();
        $puroks    = Resident::whereNotNull('purok')->distinct()->pluck('purok');

        return view('admin.residents.index', compact('residents', 'puroks'));
    }

    public function store(ResidentRequest $request)
    {
        $resident = Resident::create($request->validated() + ['verified' => $request->boolean('verified')]);

        ActivityLogger::log(
            'created',
            "Resident '{$resident->first_name} {$resident->last_name}' added to the directory",
            'Resident', $resident->id
        );

        return back()->with('success', 'Resident added successfully.');
    }

    public function update(ResidentRequest $request, Resident $resident)
    {
        $resident->update($request->validated() + ['verified' => $request->boolean('verified')]);

        ActivityLogger::log(
            'updated',
            "Resident '{$resident->first_name} {$resident->last_name}' record updated",
            'Resident', $resident->id
        );

        return back()->with('success', 'Resident updated successfully.');
    }

    public function destroy(Resident $resident)
    {
        $name = "{$resident->first_name} {$resident->last_name}";
        $resident->delete();

        ActivityLogger::log('deleted', "Resident '{$name}' removed from the directory", 'Resident');

        return back()->with('success', 'Resident deleted.');
    }
}
