<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ConfirmationReservation;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // 📥 Création d'une réservation par le client
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chambres' => 'required|array|min:1',
            'chambres.*' => 'exists:chambres,id',
            'prenom_client' => 'required|string|max:255',
            'nom_client' => 'required|string|max:255',
            'email_client' => 'required|email',
            'contact_client' => 'required|string|max:255',
            'date_arrivee' => 'required|date|after_or_equal:today',
            'date_depart' => 'required|date|after:date_arrivee',
        ]);

        $chambresIndisponibles = [];

        foreach ($validated['chambres'] as $chambreId) {
            if (!$this->estDisponible($chambreId, $validated['date_arrivee'], $validated['date_depart'])) {
                $chambresIndisponibles[] = $chambreId;
            }
        }

        if (!empty($chambresIndisponibles)) {
            return response()->json([
                'message' => 'Certaines chambres ne sont pas disponibles.',
                'chambres_indisponibles' => $chambresIndisponibles
            ], 409);
        }

        $reservations = [];

        foreach ($validated['chambres'] as $chambreId) {
            $reservation = Reservation::create([
                'chambre_id' => $chambreId,
                'prenom_client' => $validated['prenom_client'],
                'nom_client' => $validated['nom_client'],
                'email_client' => $validated['email_client'],
                'contact_client' => $validated['contact_client'],
                'date_arrivee' => $validated['date_arrivee'],
                'date_depart' => $validated['date_depart'],
                'statut' => 'en attente',
                'code_annulation' => Str::uuid(),
            ]);

           try {
            Mail::to($validated['email_client'])->send(new ConfirmationReservation($reservation));
        } catch (\Exception $e) {
            \Log::error("Erreur d'envoi d'email pour la réservation ID {$reservation->id}: " . $e->getMessage());
        }

        $reservations[] = $reservation;
    }

    return response()->json([
        'message' => 'Réservations enregistrées avec succès.',
        'reservations' => $reservations
    ], 201);
}

    // 📋 Liste des réservations (admin uniquement)
    public function index()
    {
        $reservations = Reservation::with('chambre')->orderBy('date_arrivee')->get();
        return response()->json($reservations);
    }

    // 🔍 Détails d'une réservation
    public function show($id)
    {
        $reservation = Reservation::with('chambre')->findOrFail($id);
        return response()->json($reservation);
    }

    // ✏️ Mise à jour des infos client ou dates (admin uniquement)
    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validated = $request->validate([
            'prenom_client' => 'sometimes|string|max:255',
            'nom_client' => 'sometimes|string|max:255',
            'email_client' => 'sometimes|email',
            'contact_client' => 'sometimes|string|max:255',
            'date_arrivee' => 'sometimes|date|after_or_equal:today',
            'date_depart' => 'sometimes|date|after:date_arrivee',
        ]);

        $reservation->update($validated);

        return response()->json([
            'message' => 'Réservation mise à jour.',
            'reservation' => $reservation
        ]);
    }

    // 🔁 Mise à jour du statut (admin uniquement)
    public function updateStatut(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $request->validate([
            'statut' => 'required|in:en attente,confirmée,annulée',
        ]);

        $reservation->statut = $request->statut;
        $reservation->save();

        return response()->json([
            'message' => 'Statut mis à jour.',
            'reservation' => $reservation
        ]);
    }

    // ❌ Suppression d'une réservation (admin uniquement)
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'message' => 'Réservation supprimée.'
        ]);
    }

    // 🛑 Annulation par l'admin
    public function annuler($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->statut = 'annulée';
        $reservation->save();

        return response()->json([
            'message' => 'Réservation annulée.',
            'reservation' => $reservation
        ]);
    }

    // 🧾 Annulation par le client via code unique
    public function annulerParCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $reservation = Reservation::where('code_annulation', $request->code)->first();

        if (!$reservation) {
            return response()->json(['message' => 'Code invalide.'], 404);
        }

        if ($reservation->statut === 'annulée') {
            return response()->json(['message' => 'Réservation déjà annulée.'], 400);
        }

        $reservation->statut = 'annulée';
        $reservation->save();

        return response()->json(['message' => 'Réservation annulée avec succès.']);
    }

    // 📅 Vérification de disponibilité
    public function verifierDisponibilite(Request $request)
    {
        $validated = $request->validate([
            'chambre_id' => 'required|exists:chambres,id',
            'date_arrivee' => 'required|date',
            'date_depart' => 'required|date|after:date_arrivee',
        ]);

        $reservationExistante = Reservation::where('chambre_id', $validated['chambre_id'])
            ->where('statut', '!=', 'annulée')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('date_arrivee', [$validated['date_arrivee'], $validated['date_depart']])
                      ->orWhereBetween('date_depart', [$validated['date_arrivee'], $validated['date_depart']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('date_arrivee', '<=', $validated['date_arrivee'])
                            ->where('date_depart', '>=', $validated['date_depart']);
                      });
            })
            ->exists();

        return response()->json([
            'disponible' => !$reservationExistante
        ]);
    }

    // 📊 Réservations par chambre
    public function reservationsParChambre($chambreId)
    {
        $reservations = Reservation::where('chambre_id', $chambreId)
    ->where('statut', 'confirmée')
    ->orderBy('date_arrivee')
    ->get();


        return response()->json($reservations);
    }

    // 🔍 Vérifie si une chambre est disponible (interne)
    private function estDisponible($chambreId, $dateArrivee, $dateDepart)
    {
        return !Reservation::where('chambre_id', $chambreId)
            ->where('statut', '!=', 'annulée')
            ->where(function ($query) use ($dateArrivee, $dateDepart) {
                $query->whereBetween('date_arrivee', [$dateArrivee, $dateDepart])
                      ->orWhereBetween('date_depart', [$dateArrivee, $dateDepart])
                      ->orWhere(function ($q) use ($dateArrivee, $dateDepart) {
                          $q->where('date_arrivee', '<=', $dateArrivee)
                            ->where('date_depart', '>=', $dateDepart);
                      });
            })
            ->exists();
    }

    // 👥 Liste des clients ayant des réservations confirmées
    public function listeClientsConfirmes()
    {
        $clients = Reservation::select(
                'prenom_client',
                'nom_client',
                'email_client',
                'contact_client'
            )
            ->where('statut', 'confirmée')
            ->groupBy(
                'prenom_client',
                'nom_client',
                'email_client',
                'contact_client'
            )
            ->get();

        return response()->json($clients);
    }

    public function plagesOccupees($chambreId)
{
    $reservations = Reservation::where('chambre_id', $chambreId)
        ->where('statut', 'confirmée')
        ->select('date_arrivee', 'date_depart')
        ->get();

    return response()->json($reservations);
}

}
