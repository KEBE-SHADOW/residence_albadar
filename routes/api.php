<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChambreController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
//Client
Route::get('/chambres', [ChambreController::class, 'index']);
Route::get('/chambres/{id}', [ChambreController::class, 'show']);
Route::post('/chambres/filtrer', [ChambreController::class, 'filtrerChambres']);
Route::get('/chambres/{id}/equipements', [ChambreController::class, 'equipements']);

Route::post('/reservations', [ReservationController::class, 'store']);
Route::post('/reservations/annuler-par-code', [ReservationController::class, 'annulerParCode']);
Route::post('/reservations/disponibilite', [ReservationController::class, 'verifierDisponibilite']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::get('/chambres/{id}/occupations', [ReservationController::class, 'plagesOccupees']);



//Admin
Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/admin/me', [AdminAuthController::class, 'me']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::apiResource('/chambres', ChambreController::class)->except(['index', 'show']);
    Route::post('/chambres/{id}/equipements', [ChambreController::class, 'lierEquipements']);
    Route::apiResource('/equipements', EquipementController::class);
    Route::apiResource('/reservations', ReservationController::class)->except(['store']);
    Route::put('/reservations/{id}/statut', [ReservationController::class, 'updateStatut']);
    Route::patch('/reservations/{id}/annuler', [ReservationController::class, 'annuler']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/chambres/{chambreId}/images/upload', [ImageController::class, 'uploadImage']);
    Route::delete('/images/{id}', [ImageController::class, 'destroy']);
    Route::get('/admin/statistiques', [DashboardController::class, 'statistiques']);
    Route::get('/admin/clients', [ReservationController::class, 'listeClientsConfirmes']);
    Route::middleware('auth:sanctum')->put('/admin/profile', [AdminAuthController::class, 'updateProfile']);
    Route::put('/images/{id}', [ImageController::class, 'update']);
    Route::get('/reservations/chambre/{chambreId}', [ReservationController::class, 'reservationsParChambre']);


});
Route::middleware(['auth:sanctum', 'is_super_admin'])->group(function () {
    Route::post('/admin/register', [AdminAuthController::class, 'register']);
    Route::put('/admin/{id}', [AdminAuthController::class, 'updateAdmin']);
    Route::delete('/admin/{id}', [AdminAuthController::class, 'deleteAdmin']);
    Route::get('/admin/list', [AdminAuthController::class, 'listAdmins']);
    Route::get('/admin/{id}', [AdminAuthController::class, 'showAdmin']);
    Route::put('/admin/profile', [AdminAuthController::class, 'updateProfile']);

    


});

