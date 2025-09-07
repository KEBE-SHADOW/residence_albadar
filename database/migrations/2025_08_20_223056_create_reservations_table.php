<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
           $table->id();
    $table->foreignId('chambre_id')->constrained('chambres')->onDelete('cascade');
    $table->string('prenom_client'); // Nouveau champ
    $table->string('nom_client');    // Nom de famille
    $table->string('email_client');  // Nouveau champ
    $table->string('contact_client'); // Téléphone
    $table->date('date_arrivee');
    $table->date('date_depart');
    $table->enum('statut', ['en attente', 'confirmée', 'annulée'])->default('en attente');
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
        Schema::dropIfExists('reservations');
    }
}
