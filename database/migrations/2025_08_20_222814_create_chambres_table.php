<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChambresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chambres', function (Blueprint $table) {
            $table->id(); // Identifiant unique
    $table->string('titre'); // Titre de la chambre
    $table->text('description')->nullable(); // Description
    $table->decimal('prix_par_nuit', 8, 2); // Prix par nuit
    $table->string('type'); // Type de chambre
    $table->boolean('disponible')->default(true); // Disponibilité
    $table->boolean('wifi')->default(false); // Présence du wifi
    $table->string('image_principale')->nullable(); // Image principale
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
        Schema::dropIfExists('chambres');
    }
}
