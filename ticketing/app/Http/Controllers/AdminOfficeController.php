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
}
