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
        Schema::create('technician', function (Blueprint $table) {
            $table->bigIncrements('technician_number')->unsigned()->primary(); 
            $table->foreignId('technician_id')->constrained();
            $table->boolean('is_working')->default(true);
            $table->integer('tickets_assigned')->default(0);
            $table->integer('tickets_resolved')->default(0);
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician');
    }
};
