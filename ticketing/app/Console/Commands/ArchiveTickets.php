<?php

namespace App\Console\Commands;

use App\Models\ArchivedTicket;
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
        $ticketsToArchive = Ticket::where('created_at', '<' , $monthAgo)->get();

        foreach ($ticketsToArchive as $ticket) {
            ArchivedTicket::create($ticket->toArray());
            $ticket->delete();
        }
        $this->info('Tickets archived successfully!');
    }
}
