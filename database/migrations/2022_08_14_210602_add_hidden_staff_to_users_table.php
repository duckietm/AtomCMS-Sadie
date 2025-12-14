<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('name');
                $table->boolean('hidden_staff')->default(false);
                $table->boolean('hidden_rank')->default(false);
                $table->string('job_description', 255)->default('Here to help');
                $table->string('staff_color', 8)->default('#327fa8');
                $table->string('staff_background', 255)->default('staff-bg.png');
            });
        }

        Schema::table('roles', function (Blueprint $table) {

            if (Schema::hasColumn('roles', 'hidden_staff')) {
                $table->dropColumn('hidden_staff');
            }

            $table->boolean('hidden_staff')
                ->after('name')
                ->default(false);

            if (Schema::hasColumn('roles', 'hidden_rank')) {
                $table->dropColumn('hidden_rank');
            }

            $table->boolean('hidden_rank')
                ->after('hidden_staff')
                ->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
