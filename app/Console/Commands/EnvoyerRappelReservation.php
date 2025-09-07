<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use App\Mail\RappelReservation;
use Carbon\Carbon;

class EnvoyerRappelReservation extends Command
{
    protected $signature = 'reservation:rappel';
    protected $description = 'Envoie un rappel aux clients la veille de leur arrivée';

    public function handle()
    {
        $demain = Carbon::tomorrow();

        $reservations = Reservation::whereDate('date_arrivee', $demain)
            ->where('statut', 'confirmée')
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('Aucune réservation confirmée pour demain.');
            return;
        }

        foreach ($reservations as $reservation) {
            Mail::to($reservation->email_client)->send(new RappelReservation($reservation));
            $this->info('Rappel envoyé à : ' . $reservation->email_client);
        }

        $this->info('Tous les rappels ont été envoyés avec succès.');
    }
}
