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
        Schema::create('history_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_number');
            $table->foreign('ticket_number')->references('ticket_number')->on('tickets')->onUpdate('cascade')->onDelete('restrict');
            $table->text('rr_no')->nullable();
            $table->text('ms_no')->nullable();
            $table->text('rs_no')->nullable();
            $table->string('sr_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_numbers');
    }
};
