<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // ✅ Connexion de l'admin
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 🔍 Vérifier que l'utilisateur est un admin
        $admin = Utilisateur::where('email', $validated['email'])
    ->whereIn('role', ['admin', 'super_admin'])
    ->first();

        if (!$admin || !Hash::check($validated['password'], $admin->password)) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // 🔐 Créer un token d'accès
        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'admin' => $admin
        ]);
    }

    // 🚪 Déconnexion de l'admin
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    // 👤 Récupérer le profil de l'admin connecté
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
// super_admin
    public function register(Request $request)
{
    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'email' => 'required|email|unique:utilisateurs,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $admin = Utilisateur::create([
        'nom' => $validated['nom'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'admin',
    ]);

    return response()->json([
        'message' => 'Admin créé avec succès',
        'admin' => $admin
    ]);
}
// mise a jour d'un admin
public function updateAdmin(Request $request, $id)
{
    $admin = Utilisateur::where('role', 'admin')->findOrFail($id);

    $validated = $request->validate([
        'nom' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:utilisateurs,email,' . $id,
        'password' => 'sometimes|string|min:6|confirmed',
    ]);

    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

    $admin->update($validated);

    return response()->json(['message' => 'Admin mis à jour', 'admin' => $admin]);
}
// suppression d'un admin
public function deleteAdmin($id)
{
    $admin = Utilisateur::where('role', 'admin')->findOrFail($id);
    $admin->delete();

    return response()->json(['message' => 'Admin supprimé avec succès']);
}
public function listAdmins()
{
    $admins = Utilisateur::where('role', 'admin')->get();
    return response()->json($admins);
}

public function showAdmin($id)
{
    $admin = Utilisateur::where('role', 'admin')->findOrFail($id);
    return response()->json($admin);
}

// 🔧 Mise à jour du profil du super admin connecté
public function updateProfile(Request $request)
{
    $admin = $request->user(); // récupère l'utilisateur connecté

    $validated = $request->validate([
        'email' => 'sometimes|email|unique:utilisateurs,email,' . $admin->id,
        'password' => 'sometimes|string|min:6|confirmed',
    ]);

    if (isset($validated['email'])) {
        $admin->email = $validated['email'];
    }

    if (isset($validated['password'])) {
        $admin->password = Hash::make($validated['password']);
    }

    $admin->save();

    return response()->json(['message' => 'Profil mis à jour', 'admin' => $admin]);
}

}
