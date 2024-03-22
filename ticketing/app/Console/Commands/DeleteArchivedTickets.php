<?php

namespace App\Console\Commands;

use App\Models\ArchivedTicket;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\DeletedTickets;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteArchivedTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-archived-tickets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the archived tickets after two years';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $years = Carbon::now()->subYears(2);

        $tickets = ArchivedTicket::whereNotNull('archived_at')
            ->where('archived_at', '<=', $years)
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->delete();
        }

        $this->info('Deleted ' . $tickets->count() . ' archived tickets older than two years');
    }
}
