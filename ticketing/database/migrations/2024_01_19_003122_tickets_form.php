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
        Schema::create('tickets_form', function (Blueprint $table) {
            $table->id('ticket_number'); 
            $table->string('employee_name');
            $table->string('employee_department');
            $table->string('issue');
            $table->string('description');
            $table->unsignedBigInteger('technician_id');
            $table->foreign('technician_id')->default('Unassigned')->references('technician_number')->on('technician');
            $table->string('status');
            $table->date('date_created'); 
            $table->date('date_updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets_form');
    }
};
