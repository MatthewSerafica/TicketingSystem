<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('employee.user', 'assigned.technician.user')->whereDate('created_at', today())->orderByDesc('created_at')->take(3)->get();
        $yearly_data = $this->getYearlyData();
        $service = $this->getType();
        return inertia('Admin/Dashboard/Index', [
            'tickets' => $tickets,
            'yearly_data' => $yearly_data,
            'service' => $service,
        ]);
    }

    private function getYearlyData()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $yearly_data = Ticket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            });
        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getType()
    {
        $types = Ticket::distinct('service')->pluck('service');
        $typeCounts = [];

        foreach ($types as $type) {
            $count = Ticket::where('service', $type)->count(); // Count tickets for each type
            $typeCounts[$type] = $count;
        }

        return $typeCounts;
    }
}
