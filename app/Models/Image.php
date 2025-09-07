<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'chambre_id',
        'url',
        'type',
    ];

    // 🔗 Relation : une image appartient à une chambre
    public function chambre()
    {
        return $this->belongsTo(Chambre::class);
    }
}
