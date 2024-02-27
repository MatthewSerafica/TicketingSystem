<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class AdminOfficeController extends Controller
{
    public function index(Request $request)
    {
        $offices = Office::paginate(8);
        return inertia('Admin/Office/Index', [
            'offices' => $offices,
        ]);
    }

    public function create()
    {
        $offices = Office::get();
        return inertia('Admin/Office/Create', [
            'offices' => $offices,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'office' => 'required',
        ]);


        $officeData = [
            'office' => $request->office,
        ];

        Office::create($officeData);

        return redirect()->to('/admin/office')->with('success', 'Office Created');
    }

    public function update(Request $request, $office_id)
    {
        $request->validate([
            'office' => 'required',
        ]);

        $office = Office::findOrFail($office_id);
        $office->office = $request->office;
        $office->save();

        return redirect()->back()->with('success', 'Office updated successfully');
    }

}
