<?php


namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Chambre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    // 📋 Lister toutes les images d’une chambre
    public function index($chambreId)
    {
        $chambre = Chambre::with('images')->findOrFail($chambreId);

        // Regrouper les images par type (ex: salle_de_bain, cuisine)
        $imagesParType = $chambre->images->groupBy('type');

        return response()->json([
            'chambre_id' => $chambre->id,
            'images' => $imagesParType
        ]);
    }

    // 📥 Uploader une image locale pour une chambre
    public function uploadImage(Request $request, $chambreId)
    {
        $chambre = Chambre::findOrFail($chambreId);

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'type' => 'required|string|max:50', // ex: salle_de_bain, cuisine, principale
        ]);

        // Stocker l’image dans storage/app/public/images/chambres
        $path = $request->file('image')->store('images/chambres', 'public');

        // Enregistrer l’image dans la base
        $image = $chambre->images()->create([
            'url' => Storage::url($path), // renvoie /storage/images/chambres/nom.jpg
            'type' => $validated['type'],
        ]);

        return response()->json([
            'message' => 'Image uploadée avec succès.',
            'image' => $image
        ], 201);
    }

    public function update(Request $request, $id)
{
    $image = Image::findOrFail($id);

    $validated = $request->validate([
        'type' => 'required|string'
    ]);

    $image->update($validated);

    return response()->json([
        'message' => 'Type d’image mis à jour.',
        'image' => $image
    ]);
}


    // 🗑️ Supprimer une image (base + fichier)
    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        // Supprimer le fichier physique si présent
        $path = str_replace('/storage/', '', $image->url); // Convertir l’URL en chemin relatif
        Storage::disk('public')->delete($path);

        // Supprimer l’entrée en base
        $image->delete();

        return response()->json([
            'message' => 'Image supprimée avec succès.'
        ]);
    }
}

