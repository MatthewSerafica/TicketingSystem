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
        Schema::create('service_reports', function (Blueprint $table) {
            $table->id();
            $table->string('service_id')->unique();
            $table->date('date_started');
            $table->time('time_started');
            $table->unsignedBigInteger('ticket_number');
            $table->foreign('ticket_number')->references('ticket_number')->on('tickets');
            $table->unsignedBigInteger('technician');
            $table->foreign('technician')->references('technician_id')->on('technicians')->onUpdate('cascade')->onDelete('cascade');
            $table->string('requesting_office'); 
            $table->string('equipment_no');
            $table->string('issue');
            $table->string('action');
            $table->string('recommendation');
            $table->date('date_done');
            $table->time('time_done'); 
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reports');
    }
};
