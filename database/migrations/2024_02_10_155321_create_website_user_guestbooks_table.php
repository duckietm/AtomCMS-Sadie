<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_user_guestbooks', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('profile_id');
            $table->bigInteger('user_id');
            $table->string('message');

            $table->timestamps();

            $table->foreign('profile_id')->references('id')->on('players')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('players')->cascadeOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_user_guestbooks');
    }
};
