<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipement extends Model
{
    protected $table = 'equipements'; // Nom de la table

    protected $fillable = [
        'nom',
        'description',
    ];

    // Relation : un équipement peut appartenir à plusieurs chambres
    public function chambres()
    {
        return $this->belongsToMany(Chambre::class, 'chambre_equipement')
                    ->withPivot('image_equipement')
                    ->withTimestamps();
    }
}
