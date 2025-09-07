<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chambre extends Model
{
        protected $table = 'chambres'; // Nom de la table en base

    protected $fillable = [
        'titre',
        'description',
        'prix_par_nuit',
        'type',
        'disponible',
        'wifi',
        'image_principale',
    ];

    // Relation : une chambre peut avoir plusieurs réservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Relation : une chambre peut avoir plusieurs équipements
    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'chambre_equipement')
                    ->withPivot('image_equipement')
                    ->withTimestamps();
    }

    public function images()
{
    return $this->hasMany(Image::class);
}
}
