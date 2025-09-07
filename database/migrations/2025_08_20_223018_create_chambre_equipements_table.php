<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChambreEquipementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chambre_equipement', function (Blueprint $table) {
    $table->id(); // Identifiant unique
    $table->foreignId('chambre_id')->constrained('chambres')->onDelete('cascade'); // Référence à la chambre
    $table->foreignId('equipement_id')->constrained('equipements')->onDelete('cascade'); // Référence à l’équipement
    $table->string('image_equipement')->nullable(); // Image spécifique de l’équipement
    $table->timestamps(); // Dates de création et de mise à jour
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chambre_equipements');
    }
}
