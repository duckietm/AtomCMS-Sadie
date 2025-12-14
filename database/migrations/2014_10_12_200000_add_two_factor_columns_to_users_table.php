<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Fortify\Fortify;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('players')) {
            Schema::create('players', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('username', 50);
                $table->string('email', 50);
                $table->string('password', 60);
                $table->dateTime('created_at', 6);
            });
        }

        Schema::table('players', function (Blueprint $table) {
            if (Schema::hasColumn('players', 'two_factor_secret')) {
                $table->dropColumn('two_factor_secret');
            }
            if (Schema::hasColumn('players', 'two_factor_recovery_codes')) {
                $table->dropColumn('two_factor_recovery_codes');
            }

            $table->text('two_factor_secret')
                ->after('password')
                ->nullable();

            $table->text('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            if (Fortify::confirmsTwoFactorAuthentication()) {
                if (Schema::hasColumn('players', 'two_factor_confirmed_at')) {
                    $table->dropColumn('two_factor_confirmed_at');
                }

                $table->timestamp('two_factor_confirmed_at')
                    ->after('two_factor_recovery_codes')
                    ->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
