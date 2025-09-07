<?php

namespace App\Mail;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RappelReservation extends Mailable

    {
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Crée une nouvelle instance du Mailable.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
{
    return $this->subject('Rappel : votre arrivée est prévue demain')
                ->view('emails.rappel');
}
}
