<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        return view('superadmin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('superadmin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'interval' => 'required|in:monthly,yearly',
            'max_students' => 'nullable|integer',
            'max_staff' => 'nullable|integer',
            'max_storage_size' => 'nullable|string',
        ]);

        $data = $request->all();
        if(isset($data['features']) && is_string($data['features'])) {
             // Split by new line if textarea
             $data['features'] = array_filter(array_map('trim', explode("\n", $data['features'])));
        }
        $data['is_active'] = $request->has('is_active');

        Package::create($data);

        return redirect()->route('superadmin.packages.index')->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        return view('superadmin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'interval' => 'required|in:monthly,yearly',
            'max_students' => 'nullable|integer',
            'max_staff' => 'nullable|integer',
            'max_storage_size' => 'nullable|string',
        ]);

        $data = $request->all();
        if(isset($data['features']) && is_string($data['features'])) {
             $data['features'] = array_filter(array_map('trim', explode("\n", $data['features'])));
        }
        $data['is_active'] = $request->has('is_active');

        $package->update($data);

        return redirect()->route('superadmin.packages.index')->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('superadmin.packages.index')->with('success', 'Package deleted successfully.');
    }
}
