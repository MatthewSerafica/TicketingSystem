<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProblemController extends Controller
{
    public function index(Request $request)
    {
        $problems = Problem::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('problem', 'like', '%' . $search . '%');
            })
            ->orderByDesc('id')
            ->paginate(10);
        $filters = $request->only(['search']);
        return inertia('Admin/Problems/Index', [
            'problems' => $problems,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $problems = Problem::get();
        return inertia('Admin/Problems/Create', [
            'problems' => $problems,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'problem' => 'required',
        ]);


        $problemData = [
            'problem' => $request->problem,
        ];

        Problem::create($problemData);
        
        $auth = Auth::user();
        $action_taken = "Added a new problem encountered " .  $request->problem;
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->to('/admin/problems')->with('success', 'Titles Created!')->with('message', $request->problem . ' is added to the Titles!');
    }

    public function update(Request $request, $problem_id)
    {
        $request->validate([
            'problem' => 'required',
        ]);

        $problem = Problem::findOrFail($problem_id);
        $old_problem = $problem->problem;
        $problem->problem = $request->problem;
        $problem->save();

        $auth = Auth::user();
        $action_taken = "Updated a problem from " . $old_problem . " to " .  $request->problem;
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->back()->with('success', 'Titles Updated!')->with('message', $old_problem . ' is updated to ' . $request->problem);
    }

    public function destroy($id)
    {
        $problem = Problem::findOrFail($id);
        $name = $problem->problem;
        $problem->delete();

        $auth = Auth::user();
        $action_taken = "Removed " . $name . " problem";
        $log_data = [
            'name' => $auth->name,
            'user_type' => $auth->user_type,
            'actions_taken' => $action_taken,
        ];
        Log::create($log_data);

        return redirect()->back()->with('success', 'Title Deleted!')->with('message', $name . ' has been deleted from Titles');
    }
}