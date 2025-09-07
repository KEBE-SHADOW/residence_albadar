<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chambre;
use App\Models\Reservation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function statistiques()
    {
        $totalChambres = Chambre::count();

        $aujourdHui = Carbon::today();

        // 🔍 Chambres occupées aujourd’hui (confirmées)
        $chambresOccupees = Reservation::whereDate('date_debut', '<=', $aujourdHui)
            ->whereDate('date_fin', '>=', $aujourdHui)
            ->where('statut', 'confirmée')
            ->count();

        // 📊 Taux d’occupation
        $tauxOccupation = $totalChambres > 0
            ? round(($chambresOccupees / $totalChambres) * 100, 2)
            : 0;

        // 🛏️ Chambres libres
        $chambresLibres = $totalChambres - $chambresOccupees;

        // ⏳ Réservations en attente
        $reservationsEnAttente = Reservation::where('statut', 'en attente')->count();

        // 📅 Réservations confirmées du jour
        $reservationsConfirmeesDuJour = Reservation::whereDate('created_at', $aujourdHui)
            ->where('statut', 'confirmée')
            ->count();

        return response()->json([
            'total_chambres' => $totalChambres,
            'chambres_libres' => $chambresLibres,
            'taux_occupation' => $tauxOccupation . '%',
            'reservations_en_attente' => $reservationsEnAttente,
            'reservations_confirmees_du_jour' => $reservationsConfirmeesDuJour,
        ]);
    }
}
