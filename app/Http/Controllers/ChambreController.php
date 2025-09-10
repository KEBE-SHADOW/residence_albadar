<?php

namespace App\Http\Controllers;

use App\Models\Chambre;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChambreController extends Controller
{
    // 📋 Lister toutes les chambres avec leur disponibilité
    public function index()
    {
        $aujourdHui = Carbon::today();
        $chambres = Chambre::with('equipements')->get();

        $chambresAvecDisponibilite = $chambres->map(function ($chambre) use ($aujourdHui) {
            $reservationEnCours = Reservation::where('chambre_id', $chambre->id)
                ->where('statut', 'confirmée')
                ->where('date_depart', '>=', $aujourdHui)
                ->orderBy('date_depart', 'asc')
                ->first();

            if (!$reservationEnCours || $reservationEnCours->date_arrivee > $aujourdHui) {
                $chambre->statut = 'disponible';
                $chambre->disponible_a_partir = $aujourdHui->toDateString();
            } else {
                $chambre->statut = 'occupée';
                $chambre->disponible_a_partir = Carbon::parse($reservationEnCours->date_depart)->addDay()->toDateString();
            }

            return $chambre;
        });

        return response()->json($chambresAvecDisponibilite);
    }

    //Ajouter une nouvelle chambre
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_par_nuit' => 'required|numeric|min:0',
            'type' => 'required|string',
            'wifi' => 'required|boolean',
            'image_principale' => 'nullable|string',
        ]);

        $chambre = Chambre::create($validated);

        return response()->json([
            'message' => 'Chambre ajoutée avec succès.',
            'chambre' => $chambre
        ], 201);
    }

    //Modifier une chambre existante
    public function update(Request $request, $id)
    {
        $chambre = Chambre::findOrFail($id);

        $validated = $request->validate([
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'prix_par_nuit' => 'sometimes|numeric|min:0',
            'type' => 'sometimes|string',
            'wifi' => 'sometimes|boolean',
            'image_principale' => 'nullable|string',
        ]);

        $chambre->update($validated);

        return response()->json([
            'message' => 'Chambre mise à jour avec succès.',
            'chambre' => $chambre
        ]);
    }

    //Supprimer une chambre
    public function destroy($id)
    {
        $chambre = Chambre::findOrFail($id);
        $chambre->delete();

        return response()->json([
            'message' => 'Chambre supprimée avec succès.'
        ]);
    }

    //Voir les équipements d’une chambre
    public function equipements($id)
    {
        $chambre = Chambre::with('equipements')->findOrFail($id);
        return response()->json($chambre->equipements);
    }

    //Lier des équipements à une chambre avec image personnalisée
    public function lierEquipements(Request $request, $id)
    {
        $chambre = Chambre::findOrFail($id);

        $validated = $request->validate([
            'equipements' => 'required|array',
            'equipements.*.id' => 'required|exists:equipements,id',
            'equipements.*.image_equipement' => 'nullable|string',
        ]);

        $pivotData = [];

        foreach ($validated['equipements'] as $equipement) {
            $pivotData[$equipement['id']] = [
                'image_equipement' => $equipement['image_equipement'] ?? null,
            ];
        }

        $chambre->equipements()->sync($pivotData);

        return response()->json([
            'message' => 'Équipements liés à la chambre avec succès.',
            'chambre' => $chambre->load('equipements')
        ]);
    }

    // Filtrer les chambres par disponibilité, type et équipements
    public function filtrerChambres(Request $request)
    {
        $validated = $request->validate([
            'date_arrivee' => 'required|date',
            'date_depart' => 'required|date|after:date_arrivee',
            'type' => 'nullable|string',
            'equipements' => 'nullable|array',
            'equipements.*' => 'exists:equipements,id',
        ]);

        $query = Chambre::query();

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (!empty($validated['equipements'])) {
            $query->whereHas('equipements', function ($q) use ($validated) {
                $q->whereIn('equipement_id', $validated['equipements']);
            }, '=', count($validated['equipements']));
        }

        $query->whereDoesntHave('reservations', function ($q) use ($validated) {
            $q->where('statut', 'confirmée')
              ->where(function ($r) use ($validated) {
                  $r->whereBetween('date_arrivee', [$validated['date_arrivee'], $validated['date_depart']])
                    ->orWhereBetween('date_depart', [$validated['date_arrivee'], $validated['date_depart']])
                    ->orWhere(function ($s) use ($validated) {
                        $s->where('date_arrivee', '<=', $validated['date_arrivee'])
                          ->where('date_depart', '>=', $validated['date_depart']);
                    });
              });
        });

        $chambres = $query->with('equipements')->get();

        return response()->json($chambres);
    }

    // 🔍 Détails d’une chambre avec disponibilité
public function show($id)
{
    $chambre = Chambre::with(['equipements', 'images'])->findOrFail($id);
    $aujourdHui = Carbon::today();

    // 🔍 Vérifier la réservation en cours
    $reservationEnCours = Reservation::where('chambre_id', $chambre->id)
        ->where('statut', 'confirmée')
        ->where('date_depart', '>=', $aujourdHui)
        ->orderBy('date_depart', 'asc')
        ->first();
        $statut = $reservationEnCours && $reservationEnCours->date_arrivee <= $aujourdHui
            ? 'occupée'
            : 'disponible';

        $disponibleAPartir = $reservationEnCours
            ? Carbon::parse($reservationEnCours->date_depart)->addDay()->toDateString()
            : $aujourdHui->toDateString();

    // 🖼️ Regrouper les images secondaires par type
    $imagesParType = $chambre->images->groupBy('type');

    // ➕ Ajouter l'image principale dans le groupe "principale"
    if ($chambre->image_principale) {
        $imagesParType->put('principale', collect([
            [
                'id' => null,
                'url' => '/storage/images/chambres/' . $chambre->image_principale,
                'type' => 'principale'
            ]
        ]));
    }

    // 🧾 Réponse JSON complète
    return response()->json([
        'chambre' => $chambre->makeHidden('images'),
        'images' => $imagesParType,
        'equipements' => $chambre->equipements,
        'statut' => $statut,
        'disponible_a_partir' => $disponibleAPartir
    ]);
}

}
