<?php

namespace App\Console\Commands;

use App\Models\ArchivedTicket;
use App\Models\AssignedTickets;
use App\Models\Employee;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\ArchivedTickets;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ArchiveTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:archive-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move tickets to archived tickets table if a month has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $monthAgo = Carbon::now()->subMonth();
        $ticketsToArchive = Ticket::where('created_at', '<', $monthAgo)->get();


        foreach ($ticketsToArchive as $ticket) {
            $employee = Employee::where('employee_id', $ticket->employee)->first();
            $assigned = AssignedTickets::where('ticket_number', $ticket->ticket_number)->with('technician.user')->get()->toJson();
            $technicianNames = [];
            $assignedArray = json_decode($assigned, true);

            foreach ($assignedArray as $item) {
                $technicianNames[] = $item['technician'][0]['user']['name'];
            }

            $formattedTechnicianNames = implode(' / ', $technicianNames);
            $technicians = $formattedTechnicianNames;
            ArchivedTicket::create([
                'ticket_number' => $ticket->ticket_number,
                'rr_no' => $ticket->rr_no,
                'ms_no' => $ticket->ms_no,
                'rs_no' => $ticket->rs_no,
                'employee' => $employee->user->name,
                'technicians' => $technicians,
                'issue' => $ticket->issue,
                'service' => $ticket->service,
                'description' => $ticket->description,
                'complexity' => $ticket->complexity,
                'sr_no' => $ticket->sr_no,
                'remarks' => $ticket->remarks,
                'status' => $ticket->status,
                'resolved_at' => $ticket->resolved_at,
                'created_at' => $ticket->created_at,
                'updated_at' => $ticket->updated_at,
                'archived_at' => now(),
            ]);
            $ticket->delete();
        }

        $admins = User::where('user_type', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(
                new ArchivedTickets($ticketsToArchive[0], $monthAgo)
            );
        }

        $this->info('Tickets archived successfully!');
    }
}
