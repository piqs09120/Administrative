<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\FacilityReservation;

class DigitalPassGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;
    public $passData;

    public function __construct(FacilityReservation $reservation, array $passData)
    {
        $this->reservation = $reservation;
        $this->passData = $passData;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Digital Pass Generated for Facility Access')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A digital pass has been generated for your facility reservation.')
            ->line('**Facility:** ' . $this->reservation->facility->name)
            ->line('**Visit Date:** ' . $this->reservation->start_time->format('m/d/Y'))
            ->line('**Visitor:** ' . $this->passData['visitor_name'])
            ->line('**Pass ID:** ' . $this->passData['pass_id'])
            ->action('View Pass Details', url('/facility_reservations/' . $this->reservation->id))
            ->line('Please keep this pass information secure.');
    }

    public function toArray($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->id,
            'facility_name' => $this->reservation->facility->name,
            'pass_data' => $this->passData,
            'generated_at' => now()->toISOString()
        ];
    }
}
