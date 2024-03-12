<?php

namespace App\Console\Commands;

use App\Models\ArchivedTicket;
use App\Models\Employee;
use App\Models\Technician;
use App\Models\Ticket;
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
            $tech1 = Technician::where('technician_id', $ticket->technician1)->first();
            $tech2 = Technician::where('technician_id', $ticket->technician2)->first();
            $tech3 = Technician::where('technician_id', $ticket->technician3)->first();
            $technician1Name = ($tech1 && $tech1->user) ? $tech1->user->name : '';
            $technician2Name = ($tech2 && $tech2->user) ? '/' . $tech2->user->name : '';
            $technician3Name = ($tech3 && $tech3->user) ? '/' . $tech3->user->name : '';
            ArchivedTicket::create([
                'ticket_number' => $ticket->ticket_number,
                'rr_no' => $ticket->rr_no,
                'ms_no' => $ticket->ms_no,
                'rs_no' => $ticket->rs_no,
                'employee' => $employee->user->name,
                'technicians' => $technician1Name . $technician2Name . $technician3Name,
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

        $this->info('Tickets archived successfully!');
    }
}
