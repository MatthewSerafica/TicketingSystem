<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminGenerateReportController extends Controller
{
    function index() {
        return inertia('Admin/Reports/GenerateReports/Index');
    }
}
