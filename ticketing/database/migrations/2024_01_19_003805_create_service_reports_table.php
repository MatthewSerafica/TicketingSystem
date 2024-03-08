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
            $table->date('date_started')->nullable();
            $table->time('time_started')->nullable();
            $table->unsignedBigInteger('ticket_number')->nullable();
            $table->foreign('ticket_number')->references('ticket_number')->on('tickets');
            $table->unsignedBigInteger('technician')->nullable();
            $table->foreign('technician')->references('technician_id')->on('technicians')->onUpdate('cascade')->onDelete('cascade');
            $table->string('requesting_office')->nullable();
            $table->string('equipment_no')->nullable();
            $table->string('issue')->nullable();
            $table->string('action')->nullable();
            $table->string('recommendation')->nullable();
            $table->date('date_done')->nullable();
            $table->time('time_done')->nullable();
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
