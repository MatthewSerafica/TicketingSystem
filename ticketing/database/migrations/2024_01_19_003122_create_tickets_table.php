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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('ticket_number');
            $table->unsignedBigInteger('employee');
            $table->unsignedBigInteger('technician')->nullable();
            $table->foreign('employee')->references('employee_id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('technician')->references('technician_id')->on('technicians')->onUpdate('cascade')->onDelete('cascade');
            $table->string('issue');
            $table->string('service')->nullable();
            $table->string('description');
            $table->string('status');
            $table->timestamps();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
