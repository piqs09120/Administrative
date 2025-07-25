<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\FacilityReservation;

class FacilityReservationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;

    public function __construct(FacilityReservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Facility Reservation Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your reservation for facility "' . $this->reservation->facility->name . '" has been ' . $this->reservation->status . '.')
            ->line('Remarks: ' . ($this->reservation->remarks ?? 'None'))
            ->action('View Reservation', url('/facility_reservations/' . $this->reservation->id))
            ->line('Thank you for using our system!');
    }

    public function toArray($notifiable)
    {
        return [
            'facility_id' => $this->reservation->facility_id,
            'status' => $this->reservation->status,
            'remarks' => $this->reservation->remarks,
        ];
    }
} 