<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (columnExists('roles', 'job_description')) {
                Schema::dropColumns('roles', 'job_description');
            }

            if (columnExists('roles', 'staff_color')) {
                Schema::dropColumns('roles', 'staff_color');
            }

            $table->string('job_description')->default('Here to help')->after('hidden_rank');
            $table->string('staff_color', 8)->default('#327fa8')->after('job_description');
        });
    }
};
