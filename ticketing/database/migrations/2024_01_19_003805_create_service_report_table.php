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
        Schema::create('service_report', function (Blueprint $table) {
            $table->id('service_number'); 
            $table->date('date_started');
            $table->time('time_started');
            $table->string('technician_name');
            $table->foreignId('employee_id')->references('employee_id')->on('employees');
            $table->string('issue');
            $table->string('action');
            $table->string('recommendation');
            $table->string('status_update');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_report');
    }
};
