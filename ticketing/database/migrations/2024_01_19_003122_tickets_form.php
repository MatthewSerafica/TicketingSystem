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
            $table->bigIncrements('ticket_number')->unsigned()->primary(); 
            $table->foreignName('employee_name')->constrained();
            $table->foreigndepartment('employee_department')->constrained();
            $table->string('issue');
            $table->string('description');
            $table->string('assigned_to');
            $table->string('status');
            $table->date('date_created'); 
            $table->date('date_updated');
            $table->timestamps();
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
