<?php

namespace App\Mail;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmationReservation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservations;

    public function __construct($reservations)
    {
        $this->reservations = $reservations;
    }

    public function build()
    {
        return $this->subject('Confirmation de votre réservation')
                    ->view('emails.confirmation');
    }
}