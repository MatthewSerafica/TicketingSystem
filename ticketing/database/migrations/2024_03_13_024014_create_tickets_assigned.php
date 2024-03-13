<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assigned_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_number');
            $table->unsignedBigInteger('technician');
            $table->foreign('ticket_number')->references('ticket_number')->on('tickets')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('technician')->references('technician_id')->on('technicians')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_tickets');
    }
};
