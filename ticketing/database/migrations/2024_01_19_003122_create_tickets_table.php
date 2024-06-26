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
            $table->text('request_type')->nullable();
            $table->text('rr_no')->nullable();
            $table->text('ms_no')->nullable();
            $table->text('rs_no')->nullable();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('tickets');
    }
};
