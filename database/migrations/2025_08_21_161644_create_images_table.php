<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
             $table->id();
    $table->foreignId('chambre_id')->constrained()->onDelete('cascade');
    $table->string('url'); // lien ou chemin de l’image
    $table->string('type')->default('principale'); // ex: salle_de_bain, cuisine, balcon
    

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
