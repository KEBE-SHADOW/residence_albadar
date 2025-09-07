<?php


namespace App\Http\Controllers;

use App\Models\Equipement;
use Illuminate\Http\Request;

class EquipementController extends Controller
{
    //Lister tous les équipements
    public function index()
    {
        return response()->json(Equipement::all());
    }

    // ➕ Ajouter un équipement
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $equipement = Equipement::create($validated);

        return response()->json([
            'message' => 'Équipement ajouté avec succès.',
            'equipement' => $equipement
        ], 201);
    }

    //  Modifier un équipement
    public function update(Request $request, $id)
    {
        $equipement = Equipement::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $equipement->update($validated);

        return response()->json([
            'message' => 'Équipement mis à jour.',
            'equipement' => $equipement
        ]);
    }

    //Supprimer un équipement
    public function destroy($id)
    {
        $equipement = Equipement::findOrFail($id);
        $equipement->delete();

        return response()->json([
            'message' => 'Équipement supprimé.'
        ]);
    }
}

