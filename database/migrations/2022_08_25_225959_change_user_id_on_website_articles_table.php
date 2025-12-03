<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_articles', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('players')->cascadeOnDelete();
        });
    }
};
