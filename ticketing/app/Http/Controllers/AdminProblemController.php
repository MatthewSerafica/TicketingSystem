<?php

namespace App\Http\Controllers;

use App\Events\ProblemCreated;
use App\Http\Requests\ProblemRequest;
use App\Models\Log;
use App\Models\Problem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminProblemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $request->validate([
                'search' => 'nullable|string|max:255',
            ]);

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
        } catch (ValidationException $e) {
            return inertia('Admin/Problems/Index', [
                'error' => $e->validator->errors()->all(),
            ]);
        }
    }

    public function create()
    {
        return inertia('Admin/Problems/Create');
    }

    public function store(ProblemRequest $request)
    {
        $problemData = $request->only('problem');

        DB::beginTransaction();
        try {
            $problem = Problem::create($problemData);
            event(new ProblemCreated($problem));
            $this->logAction($problem->problem, "Added a new problem");

            DB::commit();
            return redirect()->to('/admin/problems')->with('success', 'Titles Created!')->with('message', $request->problem . ' is added to the Titles!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create problem!')->with('message', $e->getMessage());
        }
    }

    public function update(ProblemRequest $request, $problem_id)
    {
        $problem = Problem::findOrFail($problem_id);
        $old_problem = $problem->problem;

        DB::beginTransaction();
        try {
            $problem->problem = $request->problem;
            $problem->save();
            $message = "Updated " . $old_problem . " problem to " . $request->problem;
            $this->logAction($problem->problem, $message);

            return redirect()->back()->with('success', 'Titles Updated!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update problem!')->with('message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $problem = Problem::findOrFail($id);
            $name = $problem->problem;
            $problem->delete();


            $message = "Deleted " . $name . " problem";
            $this->logAction($problem->problem, $message);

            return redirect()->back()->with('success', 'Problem Deleted!')->with('message', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update problem!')->with('message', $e->getMessage());
        }
    }
    private function logAction($problem, $action)
    {
        $auth = Auth::user();
        if ($auth) {
            Log::create([
                'name' => $auth->name,
                'user_type' => $auth->user_type,
                'actions_taken' => $action . ': ' . $problem,
            ]);
        } else {
            Log::create([
                'name' => request()->ip(),
                'user_type' => request()->header('User-Agent'),
                'actions_taken' => $action . ': ' . $problem,
            ]);
            return redirect()->back()->with('error', 'Attempt to log action without authenticated user');
        }
    }
}
