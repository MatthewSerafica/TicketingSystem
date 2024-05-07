<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class AdminLogsController extends Controller
{
    public function index() {
        $logs = Log::orderByDesc('id')->paginate(10);
        return inertia('Admin/Logs/Index', [
            'logs' => $logs,
        ]);
    }
}
