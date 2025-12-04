<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (columnExists('roles', 'hidden_rank')) {
                Schema::dropColumns('roles', 'hidden_rank');
            }

            $table->boolean('hidden_rank')->after('hidden_staff')->default(false);
        });
    }
};
