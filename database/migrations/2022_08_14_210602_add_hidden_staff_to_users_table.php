<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (columnExists('roles', 'hidden_staff')) {
                Schema::dropColumns('roles', 'hidden_staff');
            }

            $table->boolean('hidden_staff')->after('name')->default(false);
        });
    }
};
