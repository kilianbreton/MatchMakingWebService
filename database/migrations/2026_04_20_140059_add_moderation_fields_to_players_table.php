<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false)->after('refresh');
            $table->timestamp('banned_until')->nullable()->after('is_banned');
            $table->text('ban_reason')->nullable()->after('banned_until');

            $table->boolean('is_muted')->default(false)->after('ban_reason');
            $table->timestamp('muted_until')->nullable()->after('is_muted');
            $table->text('mute_reason')->nullable()->after('muted_until');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'is_banned',
                'banned_until',
                'ban_reason',
                'is_muted',
                'muted_until',
                'mute_reason',
            ]);
        });
    }
};