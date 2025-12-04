<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('camera_web')) {
            Schema::create('camera_web', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedInteger('room_id')->default(0);
                $table->unsignedInteger('timestamp');
                $table->string('url', 128)->default('');
                $table->boolean('visible')->default(true);

                $table->index('user_id');

                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';
            });
        } else {
            Schema::table('camera_web', function (Blueprint $table) {
                if (!Schema::hasColumn('camera_web', 'visible')) {
                    $table->boolean('visible')->default(true)->after('url');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('camera_web', function (Blueprint $table) {
            if (Schema::hasColumn('camera_web', 'visible')) {
                $table->dropColumn('visible');
            }
        });
    }
};