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
        Schema::create('tagged_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('post_id');
            $table->foreign('post_id')->references('id')->on('post')->onUpdate('cascade')->onDelete('cascade');
            $table->uuid('comment_id')->nullable();
            $table->foreign('comment_id')->references('id')->on('comment')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagged_users');
    }
};
