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
        Schema::create('archived_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_number');
            $table->unsignedBigInteger('rr_no')->nullable();
            $table->unsignedBigInteger('ms_no')->nullable();
            $table->unsignedBigInteger('rs_no')->nullable();
            $table->string('employee');
            $table->string('department');
            $table->string('office');
            $table->text('technicians')->nullable();
            $table->string('issue');
            $table->string('service')->nullable();
            $table->text('description')->nullable();
            $table->string('sr_no')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('archived_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_tickets');
    }
};
