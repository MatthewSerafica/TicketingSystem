<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArchivedTickets extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Ticket $ticket,
        private $date
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_number' => $this->ticket->ticket_number,
            'employee' => $this->ticket->employee,
            'issue' => $this->ticket->issue,
            'description' => $this->ticket->description,
            'sr_no' => $this->ticket->sr_no,
            'service' => $this->ticket->service,
            'status' => $this->ticket->status,
            'remarks' => $this->ticket->remarks,
            'resolved_at' => $this->ticket->resolved_at,
            'title' => 'Tickets Archived',
            'message' => 'The tickets from the previous month have been archived.',
            'message_two' => 'Please download and print the tickets generated before the tickets are deleted!',
            'date' => $this->date,
        ];
    }
}
