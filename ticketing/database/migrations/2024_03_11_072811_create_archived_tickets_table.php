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
            $table->id('ticket_number');
            $table->unsignedBigInteger('rr_no')->nullable();
            $table->unsignedBigInteger('ms_no')->nullable();
            $table->unsignedBigInteger('rs_no')->nullable();
            $table->unsignedBigInteger('employee');
            $table->unsignedBigInteger('technician1')->nullable();
            $table->unsignedBigInteger('technician2')->nullable();
            $table->unsignedBigInteger('technician3')->nullable();
            $table->foreign('employee')->references('employee_id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('technician1')->references('technician_id')->on('technicians')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('technician2')->references('technician_id')->on('technicians')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('technician3')->references('technician_id')->on('technicians')->onUpdate('cascade')->onDelete('cascade');
            $table->string('issue');
            $table->string('service')->nullable();
            $table->text('description')->nullable();
            $table->string('complexity')->nullable();
            $table->string('sr_no')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('archived_tickets');
    }
};
