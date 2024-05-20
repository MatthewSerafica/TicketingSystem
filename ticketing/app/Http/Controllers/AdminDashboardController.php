<?php

namespace App\Http\Controllers;

use App\Models\ArchivedTicket;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('employee.user', 'assigned.technician.user')
            ->whereDate('created_at', today())
            ->latest()
            ->take(3)
            ->get();

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

        $ticket_data = Ticket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            });

        /* $archived_ticket_data = ArchivedTicket::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('M');
            })
            ->map(function ($grouped_tickets) {
                return $grouped_tickets->count();
            }); */

        // Merge the data from both models
        $yearly_data = $ticket_data;

        $ordered_data = collect([]);
        foreach ($months as $month) {
            $ordered_data[$month] = $yearly_data->get($month, 0);
        }
        return $ordered_data;
    }

    private function getType()
    {
        $ticket_types = Ticket::distinct('service')->pluck('service');
        /* $archived_ticket_types = ArchivedTicket::distinct('service')->pluck('service'); */

        $types = $ticket_types->unique();

        $typeCounts = [];

        foreach ($types as $type) {
            $ticket_count = Ticket::where('service', $type)->count();
            $archived_ticket_count = ArchivedTicket::where('service', $type)->count();

            $total_count = $ticket_count + $archived_ticket_count;

            $typeCounts[$type] = $total_count;
        }

        return $typeCounts;
    }
}
