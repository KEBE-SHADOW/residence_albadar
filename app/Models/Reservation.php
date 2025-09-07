<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
     protected $table = 'reservations'; // Nom de la table en base

    protected $fillable = [
        'chambre_id',
        'prenom_client',
        'nom_client',
        'email_client',
        'contact_client',
        'date_arrivee',
        'date_depart',
        'statut',
    ];

    // Relation : une réservation appartient à une chambre
    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }

    // Relation : une réservation peut avoir une notification
    public function notification()
    {
        return $this->hasOne(Notification::class);
    
}
}
