<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketMade extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Ticket $ticket,
        private $technicians,
        private $name,
        private $office,
        private $department,
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
        /* return ['mail']; */
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
            'service' => $this->ticket->service,
            'status' => $this->ticket->status,
            'resolved_at' => $this->ticket->resolved_at,
            'technicians' => $this->technicians,
            'name' => $this->name,
            'office' => $this->office,
            'department' => $this->department,
        ];
    }
}
